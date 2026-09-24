<?php

namespace Tests\Feature;

use App\Models\Pengguna;
use App\Services\DatabaseBackupService;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Mail;
use Tests\TestCase;

class BackupTest extends TestCase
{
    use DatabaseTransactions;

    protected function tearDown(): void
    {
        // Clean up test backup files if created
        $backupDir = storage_path('app/backups');
        if (File::exists($backupDir)) {
            $files = File::files($backupDir);
            foreach ($files as $file) {
                if (str_starts_with($file->getFilename(), 'test-') || str_contains($file->getFilename(), 'backup-test')) {
                    File::delete($file->getRealPath());
                }
            }
        }

        parent::tearDown();
    }

    public function test_artisan_backup_command_executes_successfully(): void
    {
        $exitCode = Artisan::call('db:backup-email', ['--no-email' => true]);
        $this->assertEquals(0, $exitCode);

        $service = app(DatabaseBackupService::class);
        $backups = $service->listBackups();
        $this->assertNotEmpty($backups);
        $this->assertStringEndsWith('.sql.gz', $backups[0]['filename']);
    }

    public function test_admin_can_access_backup_page(): void
    {
        $admin = Pengguna::firstOrCreate(
            ['idpengguna' => 'admin_test'],
            [
                'nama' => 'Admin Test',
                'password' => bcrypt('password123'),
                'levelpengguna' => 0,
                'status' => 1,
            ]
        );

        $response = $this->actingAs($admin)->get('/backup');
        $response->assertStatus(200);
        $response->assertSee('Backup Database');
        $response->assertSee('Eksekusi Backup Manual');
    }

    public function test_backup_controller_can_run_manual_backup(): void
    {
        Mail::fake();

        $admin = Pengguna::firstOrCreate(
            ['idpengguna' => 'admin_test'],
            [
                'nama' => 'Admin Test',
                'password' => bcrypt('password123'),
                'levelpengguna' => 0,
                'status' => 1,
            ]
        );

        $response = $this->actingAs($admin)->post('/backup/run', [
            'email' => 'pramana.veni@gmail.com',
            'send_email' => '1',
        ]);

        $response->assertRedirect('/backup');
        $response->assertSessionHas('success');
    }

    public function test_backup_service_lifecycle(): void
    {
        $service = app(DatabaseBackupService::class);
        $meta = $service->createBackup();

        $this->assertFileExists($meta['path']);
        $this->assertGreaterThan(0, $meta['size_bytes']);
        $this->assertGreaterThan(0, $meta['tables_count']);

        $path = $service->getBackupPath($meta['filename']);
        $this->assertNotNull($path);

        $deleted = $service->deleteBackup($meta['filename']);
        $this->assertTrue($deleted);
        $this->assertFileDoesNotExist($meta['path']);
    }
}
