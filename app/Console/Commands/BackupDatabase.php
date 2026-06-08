<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

// JAILAOI: Daily DB backup command.
// Dumps the MySQL database to storage/app/backups/jailaoi_YYYY-MM-DD.sql.gz
// Keeps last 7 days only — older files are pruned automatically.
// Optionally emails the backup if BACKUP_EMAIL is set in .env or tbl_general_setting.
// Schedule: daily at 2am via cPanel Cron Jobs:
//   0 2 * * * cd /home/jailaoic/m.jailaoi.com && php artisan jailaoi:backup-db >> /dev/null 2>&1
class BackupDatabase extends Command
{
    protected $signature   = 'jailaoi:backup-db';
    protected $description = 'Dump MySQL DB to gzipped SQL file, keep last 7 days';

    public function handle(): int
    {
        $dbName = config('database.connections.mysql.database');
        $dbUser = config('database.connections.mysql.username');
        $dbPass = config('database.connections.mysql.password');
        $dbHost = config('database.connections.mysql.host', '127.0.0.1');
        $dbPort = config('database.connections.mysql.port', '3306');

        $backupDir = storage_path('app/backups');
        if (!is_dir($backupDir)) {
            mkdir($backupDir, 0755, true);
        }

        $date     = date('Y-m-d_H-i');
        $filename = "jailaoi_{$date}.sql.gz";
        $filepath = $backupDir . '/' . $filename;

        // Build mysqldump command — pipe straight through gzip
        // MYSQL_PWD avoids password in process list
        $cmd = sprintf(
            'MYSQL_PWD=%s mysqldump --host=%s --port=%s --user=%s --single-transaction --quick --lock-tables=false %s | gzip > %s 2>&1',
            escapeshellarg($dbPass),
            escapeshellarg($dbHost),
            escapeshellarg($dbPort),
            escapeshellarg($dbUser),
            escapeshellarg($dbName),
            escapeshellarg($filepath)
        );

        exec($cmd, $output, $exitCode);

        if ($exitCode !== 0 || !file_exists($filepath) || filesize($filepath) < 100) {
            $msg = 'JailaOi DB backup FAILED. Exit: ' . $exitCode . ' Output: ' . implode(' ', $output);
            Log::error($msg);
            $this->error($msg);
            return 1;
        }

        $sizeMb = round(filesize($filepath) / 1024 / 1024, 2);
        $this->info("Backup created: {$filename} ({$sizeMb} MB)");
        Log::info("DB backup OK: {$filename} ({$sizeMb} MB)");

        // Prune backups older than 7 days
        $this->pruneOldBackups($backupDir, 7);

        // Optional: email backup file
        $this->emailBackup($filepath, $filename, $sizeMb);

        return 0;
    }

    private function pruneOldBackups(string $dir, int $keepDays): void
    {
        $files = glob($dir . '/jailaoi_*.sql.gz') ?: [];
        $cutoff = time() - ($keepDays * 86400);
        $pruned = 0;

        foreach ($files as $file) {
            if (filemtime($file) < $cutoff) {
                @unlink($file);
                $pruned++;
            }
        }

        if ($pruned > 0) {
            $this->info("Pruned {$pruned} old backup(s).");
            Log::info("DB backup pruned {$pruned} old file(s).");
        }
    }

    private function emailBackup(string $filepath, string $filename, float $sizeMb): void
    {
        try {
            // Get backup email from tbl_general_setting or .env
            $email = \Illuminate\Support\Facades\DB::table('tbl_general_setting')
                ->where('key', 'backup_email')
                ->value('value');

            if (!$email) {
                $email = env('BACKUP_EMAIL', '');
            }

            if (!$email) return; // No email configured — skip silently

            $smtpOk = (new \App\Models\Common)->SetSmtpConfig();
            if (!$smtpOk) return;

            \Illuminate\Support\Facades\Mail::raw(
                "JailaOi daily DB backup: {$filename} ({$sizeMb} MB)\nDate: " . now()->toDateTimeString(),
                function ($msg) use ($email, $filename, $filepath) {
                    $msg->to($email)
                        ->subject("JailaOi DB Backup — {$filename}")
                        ->attach($filepath, ['as' => $filename, 'mime' => 'application/gzip']);
                }
            );

            $this->info("Backup emailed to {$email}");
            Log::info("DB backup emailed to {$email}");

        } catch (\Exception $e) {
            Log::warning('DB backup email failed: ' . $e->getMessage());
            // Not fatal — backup file still exists on server
        }
    }
}
