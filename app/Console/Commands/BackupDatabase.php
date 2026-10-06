<?php
namespace App\Console\Commands;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use App\Services\EncryptedBackup;

class BackupDatabase extends Command
{
    protected $signature='jailaoi:backup-db';
    protected $description='Create verified encrypted database and local-media backups; retain 30 days.';
    public function handle(): int
    {
        try {
            $manifest=app(EncryptedBackup::class)->create();
            Log::info('Encrypted backup completed', ['created_at'=>$manifest['created_at']]);
            $this->info('Encrypted database and local-media backup created.');
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::critical('Backup failed; recovery needs attention', ['exception'=>get_class($e)]);
            $this->error('Backup failed. Check the encryption certificate, database access and disk space.');
            return Command::FAILURE;
        }
    }
}
