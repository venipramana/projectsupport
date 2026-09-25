<?php

namespace App\Console\Commands;

use App\Services\DatabaseBackupService;
use Exception;
use Illuminate\Console\Command;

class BackupDatabaseEmailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:backup-email 
                            {--email= : Recipient email address} 
                            {--no-email : Only generate backup file without sending email}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate full MySQL database backup (.sql.gz) and send it via email';

    /**
     * Execute the console command.
     */
    public function handle(DatabaseBackupService $backupService): int
    {
        $this->info('Memulai proses backup database MySQL...');

        try {
            $meta = $backupService->createBackup();

            $this->info("Backup berhasil dibuat: {$meta['filename']}");
            $this->line(" - Database: {$meta['database']} ({$meta['host']})");
            $this->line(" - Ukuran: {$meta['size_formatted']}");
            $this->line(" - Tabel: {$meta['tables_count']} tabel");
            $this->line(" - Durasi: {$meta['duration']} detik");

            if ($this->option('no-email')) {
                $this->warn('Opsi --no-email terdeteksi. Pengiriman email dilewati.');
                return Command::SUCCESS;
            }

            $recipient = $this->option('email') ?: config('mail.from.address') ?: 'pramana.veni@gmail.com';
            $this->info("Mengirimkan file backup ke email: {$recipient}...");

            $backupService->sendBackupEmail($meta['path'], $meta, $recipient);

            $this->info("Email backup database berhasil dikirimkan ke {$recipient}.");
            return Command::SUCCESS;
        } catch (Exception $e) {
            $this->error('Gagal melakukan backup database: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}

