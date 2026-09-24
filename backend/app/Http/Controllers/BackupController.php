<?php

namespace App\Http\Controllers;

use App\Services\DatabaseBackupService;
use Exception;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class BackupController extends Controller
{
    protected DatabaseBackupService $backupService;

    public function __construct(DatabaseBackupService $backupService)
    {
        $this->backupService = $backupService;
    }

    /**
     * Display a listing of database backups and manual trigger form.
     */
    public function index(): View
    {
        $backups = $this->backupService->listBackups();
        $defaultEmail = config('mail.from.address') ?: 'pramana.veni@gmail.com';
        $dbName = config('database.connections.mysql.database', 'pm');
        $dbHost = config('database.connections.mysql.host', 'mysql');
        $totalSize = array_sum(array_column($backups, 'size_bytes'));
        $totalSizeFormatted = $this->backupService->formatBytes($totalSize);

        return view('backup.index', compact(
            'backups',
            'defaultEmail',
            'dbName',
            'dbHost',
            'totalSizeFormatted'
        ));
    }

    /**
     * Trigger manual backup and optional email dispatch.
     */
    public function runBackup(Request $request): RedirectResponse
    {
        $request->validate([
            'email' => 'nullable|email',
            'send_email' => 'nullable',
        ]);

        $shouldSendEmail = $request->has('send_email');
        $recipient = $request->input('email') ?: config('mail.from.address') ?: 'pramana.veni@gmail.com';

        try {
            $meta = $this->backupService->createBackup();

            if ($shouldSendEmail) {
                $this->backupService->sendBackupEmail($meta['path'], $meta, $recipient);
                $message = "Backup database berhasil dibuat ({$meta['size_formatted']}, {$meta['tables_count']} tabel) dan file backup telah berhasil dikirimkan ke email {$recipient}.";
            } else {
                $message = "Backup database berhasil dibuat ({$meta['size_formatted']}, {$meta['tables_count']} tabel) dan tersimpan di server lokal.";
            }

            return redirect()->route('backup.index')->with('success', $message);
        } catch (Exception $e) {
            return redirect()->route('backup.index')->with('error', 'Gagal memproses backup database: ' . $e->getMessage());
        }
    }

    /**
     * Download a specific backup file.
     */
    public function downloadBackup(string $filename): BinaryFileResponse|RedirectResponse
    {
        $path = $this->backupService->getBackupPath($filename);

        if (!$path) {
            return redirect()->route('backup.index')->with('error', 'File backup tidak ditemukan atau nama file tidak valid.');
        }

        return response()->download($path, basename($path), [
            'Content-Type' => 'application/gzip',
        ]);
    }

    /**
     * Delete a specific backup file.
     */
    public function destroyBackup(string $filename): RedirectResponse
    {
        $deleted = $this->backupService->deleteBackup($filename);

        if ($deleted) {
            return redirect()->route('backup.index')->with('success', "File backup {$filename} berhasil dihapus.");
        }

        return redirect()->route('backup.index')->with('error', 'Gagal menghapus file backup.');
    }
}
