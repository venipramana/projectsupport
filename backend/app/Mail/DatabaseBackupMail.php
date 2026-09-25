<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class DatabaseBackupMail extends Mailable
{
    use Queueable, SerializesModels;

    public string $backupFilePath;
    public array $meta;

    /**
     * Create a new message instance.
     */
    public function __construct(string $backupFilePath, array $meta = [])
    {
        $this->backupFilePath = $backupFilePath;
        $this->meta = $meta;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $dbName = $this->meta['database'] ?? config('database.connections.mysql.database', 'pm');
        $date = $this->meta['created_at'] ?? now('Asia/Jakarta')->format('d/m/Y H:i');

        return new Envelope(
            subject: "[PM Backup] Database Backup ({$dbName}) - {$date}",
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.database_backup',
            with: [
                'meta' => $this->meta,
                'appName' => config('app.name', 'Project Support'),
            ]
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        if (file_exists($this->backupFilePath)) {
            return [
                Attachment::fromPath($this->backupFilePath)
                    ->as(basename($this->backupFilePath))
                    ->withMime('application/gzip'),
            ];
        }

        return [];
    }
}

