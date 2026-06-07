<?php

namespace App\Console\Commands;

use App\Models\Common;
use App\Models\User;
use Exception;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\URL;

class BlastMigratedArtistEmails extends Command
{
    protected $signature = 'jailaoi:blast-migrated-artist-emails
        {--dry-run : Only list users that would be emailed, do not send}
        {--limit=100 : Max emails to send per run}';
    protected $description = 'Send "Welcome to JailaOi — set your password" email to migrated artists';

    public function handle()
    {
        $this->info('Starting migrated artist email blast...');
        $this->newLine();

        $dryRun = $this->option('dry-run');
        $limit = (int) $this->option('limit');

        $users = User::where('role', 'artist')
            ->where(function ($q) {
                $q->whereNull('email_blast_sent_at')
                    ->orWhere('email_blast_sent_at', '<', now()->subDays(7));
            })
            ->whereNotNull('email')
            ->limit($limit)
            ->get();

        if ($users->isEmpty()) {
            $this->warn('No eligible users found.');
            return Command::SUCCESS;
        }

        $this->info('Found ' . $users->count() . ' eligible artists.');
        $bar = $this->output->createProgressBar($users->count());
        $bar->start();

        $sent = 0;

        foreach ($users as $user) {
            if ($dryRun) {
                $this->line(' [DRY-RUN] Would send to: ' . $user->email . ' (' . ($user->full_name ?? 'no name') . ')');
                $bar->advance();
                continue;
            }

            try {
                $token = sha1($user->email . $user->id . now()->timestamp);
                \Illuminate\Support\Facades\DB::table('password_resets')->updateOrInsert(
                    ['email' => $user->email],
                    ['token' => bcrypt($token), 'created_at' => now()]
                );

                $resetUrl = url('/user/password/reset?' . http_build_query([
                    'token' => $token,
                    'email' => $user->email,
                ]));

                $common = new Common;
                $common->SetSmtpConfig();

                Mail::to($user->email)->send(
                    new \App\Mail\MigratedArtistWelcomeMail($user->full_name ?? 'there', $resetUrl)
                );

                $user->email_blast_sent_at = now();
                $user->save();

                $sent++;
            } catch (Exception $e) {
                Log::error('Blast email failed for ' . $user->email . ': ' . $e->getMessage());
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Sent {$sent} emails.");

        return Command::SUCCESS;
    }
}
