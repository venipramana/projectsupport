<?php

namespace App\Services;

use App\Mail\DatabaseBackupMail;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use PDO;

class DatabaseBackupService
{
    protected string $backupDir;

    public function __construct()
    {
        $this->backupDir = storage_path('app/backups');
        if (!File::exists($this->backupDir)) {
            File::makeDirectory($this->backupDir, 0755, true);
        }
    }

    /**
     * Generate a full database backup in compressed .sql.gz format.
     *
     * @return array
     * @throws Exception
     */
    public function createBackup(): array
    {
        $startTime = microtime(true);
        $connection = DB::connection();
        $dbName = $connection->getDatabaseName();
        $dbHost = config('database.connections.mysql.host', 'localhost');
        $timestamp = Carbon::now('Asia/Jakarta');
        $filename = 'backup-' . $dbName . '-' . $timestamp->format('Y-m-d_H-i-s') . '.sql.gz';
        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $filename;

        $gz = gzopen($filePath, 'w9');
        if (!$gz) {
            throw new Exception("Gagal membuka file backup untuk penulisan: {$filePath}");
        }

        // Header SQL
        $header = "-- ============================================================\n"
            . "-- Database Backup: {$dbName}\n"
            . "-- Host: {$dbHost}\n"
            . "-- Generated At: " . $timestamp->toDateTimeString() . " WIB\n"
            . "-- Application: " . config('app.name', 'Laravel') . "\n"
            . "-- ============================================================\n\n"
            . "SET NAMES utf8mb4;\n"
            . "SET FOREIGN_KEY_CHECKS = 0;\n"
            . "SET SQL_MODE = 'NO_AUTO_VALUE_ON_ZERO';\n"
            . "SET time_zone = '+00:00';\n\n";

        gzwrite($gz, $header);

        /** @var PDO $pdo */
        $pdo = $connection->getPdo();

        // Ambil semua base table
        $tablesStmt = $pdo->query("SHOW FULL TABLES WHERE Table_type = 'BASE TABLE'");
        $tables = $tablesStmt->fetchAll(PDO::FETCH_NUM);
        $tablesCount = count($tables);

        foreach ($tables as $tableRow) {
            $tableName = $tableRow[0];

            gzwrite($gz, "-- ------------------------------------------------------------\n");
            gzwrite($gz, "-- Table structure for `{$tableName}`\n");
            gzwrite($gz, "-- ------------------------------------------------------------\n");
            gzwrite($gz, "DROP TABLE IF EXISTS `{$tableName}`;\n");

            // Create table DDL
            $createTableStmt = $pdo->query("SHOW CREATE TABLE `{$tableName}`");
            $createTableRow = $createTableStmt->fetch(PDO::FETCH_NUM);
            if (isset($createTableRow[1])) {
                gzwrite($gz, $createTableRow[1] . ";\n\n");
            }

            // Dump data
            gzwrite($gz, "-- Data for `{$tableName}`\n");
            $rowsStmt = $pdo->query("SELECT * FROM `{$tableName}`");
            $rows = [];
            $batchSize = 250;

            while ($row = $rowsStmt->fetch(PDO::FETCH_ASSOC)) {
                $rows[] = $row;
                if (count($rows) >= $batchSize) {
                    $this->writeInsertBatch($gz, $tableName, $rows, $pdo);
                    $rows = [];
                }
            }

            if (count($rows) > 0) {
                $this->writeInsertBatch($gz, $tableName, $rows, $pdo);
            }

            gzwrite($gz, "\n");
        }

        // Footer SQL
        $footer = "SET FOREIGN_KEY_CHECKS = 1;\n"
            . "-- ============================================================\n"
            . "-- Backup completed at: " . Carbon::now('Asia/Jakarta')->toDateTimeString() . " WIB\n"
            . "-- ============================================================\n";

        gzwrite($gz, $footer);
        gzclose($gz);

        $duration = round(microtime(true) - $startTime, 2);
        $fileSizeBytes = filesize($filePath);

        $meta = [
            'filename' => $filename,
            'path' => $filePath,
            'database' => $dbName,
            'host' => $dbHost,
            'size_bytes' => $fileSizeBytes,
            'size_formatted' => $this->formatBytes($fileSizeBytes),
            'tables_count' => $tablesCount,
            'duration' => $duration,
            'created_at' => $timestamp->format('d/m/Y H:i:s') . ' WIB',
        ];

        Log::info("Database backup created successfully: {$filename} ({$meta['size_formatted']}, {$duration}s)");

        return $meta;
    }

    /**
     * Send backup file via email.
     *
     * @param string $filePath
     * @param array $meta
     * @param string|null $toEmail
     * @return void
     */
    public function sendBackupEmail(string $filePath, array $meta, ?string $toEmail = null): void
    {
        $recipient = $toEmail ?: config('mail.from.address') ?: 'pramana.veni@gmail.com';

        Mail::to($recipient)->send(new DatabaseBackupMail($filePath, $meta));

        Log::info("Database backup sent to email {$recipient} for file: " . ($meta['filename'] ?? basename($filePath)));
    }

    /**
     * List all available local backup files.
     *
     * @return array
     */
    public function listBackups(): array
    {
        if (!File::exists($this->backupDir)) {
            return [];
        }

        $files = File::files($this->backupDir);
        $backups = [];

        foreach ($files as $file) {
            if ($file->getExtension() === 'gz' || str_ends_with($file->getFilename(), '.sql.gz')) {
                $size = $file->getSize();
                $mtime = $file->getMTime();
                $backups[] = [
                    'filename' => $file->getFilename(),
                    'path' => $file->getRealPath(),
                    'size_bytes' => $size,
                    'size_formatted' => $this->formatBytes($size),
                    'created_at' => Carbon::createFromTimestamp($mtime, 'Asia/Jakarta')->format('d/m/Y H:i:s') . ' WIB',
                    'timestamp' => $mtime,
                ];
            }
        }

        // Sort newest first
        usort($backups, fn($a, $b) => $b['timestamp'] <=> $a['timestamp']);

        return $backups;
    }

    /**
     * Delete a backup file safely.
     *
     * @param string $filename
     * @return bool
     */
    public function deleteBackup(string $filename): bool
    {
        $cleanFilename = basename($filename);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.sql\.gz$/', $cleanFilename)) {
            return false;
        }

        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;
        if (File::exists($filePath)) {
            return File::delete($filePath);
        }

        return false;
    }

    /**
     * Get valid backup path for download.
     *
     * @param string $filename
     * @return string|null
     */
    public function getBackupPath(string $filename): ?string
    {
        $cleanFilename = basename($filename);
        if (!preg_match('/^[a-zA-Z0-9_\-\.]+\.sql\.gz$/', $cleanFilename)) {
            return null;
        }

        $filePath = $this->backupDir . DIRECTORY_SEPARATOR . $cleanFilename;
        return File::exists($filePath) ? $filePath : null;
    }

    /**
     * Format bytes into readable format.
     */
    public function formatBytes(int $bytes, int $precision = 2): string
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        $bytes /= pow(1024, $pow);

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

    /**
     * Write batch of insert statements.
     */
    protected function writeInsertBatch($gz, string $tableName, array $rows, PDO $pdo): void
    {
        if (empty($rows)) {
            return;
        }

        $columns = array_keys($rows[0]);
        $columnList = '`' . implode('`, `', $columns) . '`';

        $valuesList = [];
        foreach ($rows as $row) {
            $rowValues = [];
            foreach ($columns as $col) {
                $val = $row[$col] ?? null;
                if (is_null($val)) {
                    $rowValues[] = 'NULL';
                } elseif (is_numeric($val) && !is_string($val)) {
                    $rowValues[] = $val;
                } else {
                    $rowValues[] = $pdo->quote((string)$val);
                }
            }
            $valuesList[] = '(' . implode(', ', $rowValues) . ')';
        }

        $sql = "INSERT INTO `{$tableName}` ({$columnList}) VALUES\n" . implode(",\n", $valuesList) . ";\n";
        gzwrite($gz, $sql);
    }
}
