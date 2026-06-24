<?php

namespace App\Console\Commands;

use App\Mail\RenewalReminderMail;
use App\Mail\SubscriptionExpiredMail;
use App\Models\General_Setting;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Schema;

class SendSubscriptionNotifications extends Command
{
    protected $signature = 'app:send-subscription-notifications';

    protected $description = 'Send renewal reminders and expiry notifications for subscriptions';

    public function handle()
    {
        $this->info('Starting subscription notifications...');

        if (!Schema::hasColumn('tbl_transaction', 'renewal_reminder_sent')) {
            $this->warn('Migration not run yet. Run php artisan migrate first.');
            return Command::SUCCESS;
        }

        $now = date('Y-m-d H:i');
        $reminderDays = max(1, (int) (General_Setting::where('key', 'renewal_reminder_days')->value('value') ?? 7));
        $reminderEnabled = General_Setting::where('key', 'renewal_reminder_enabled')->value('value') ?? '1';
        $expiryEnabled = General_Setting::where('key', 'expiry_notification_enabled')->value('value') ?? '1';

        $this->info("reminder_days={$reminderDays} reminder_enabled={$reminderEnabled} expiry_enabled={$expiryEnabled}");

        $expiredCount = Transaction::where('status', 1)
            ->where('expiry_date', '<=', $now)
            ->update(['status' => 0, 'updated_at' => $now]);

        if ($expiredCount > 0) {
            $this->info("Expired {$expiredCount} overdue transaction(s)");
        }

        $sent = 0;

        if ($reminderEnabled === '1') {
            $threshold = date('Y-m-d H:i', strtotime("+{$reminderDays} days"));
            if ($threshold === false) {
                $this->error('Invalid reminder_days setting');
                return Command::FAILURE;
            }

            $transactions = Transaction::with('user', 'package')
                ->where('status', 1)
                ->where('renewal_reminder_sent', 0)
                ->where('expiry_date', '>=', $now)
                ->where('expiry_date', '<=', $threshold)
                ->get();

            foreach ($transactions as $transaction) {
                $user = $transaction->user;
                if (!$user || empty($user->email)) {
                    continue;
                }

                $packageName = $transaction->package ? $transaction->package->name : 'Subscription';

                try {
                    Mail::to($user->email)->send(new RenewalReminderMail(
                        $user->full_name ?: $user->user_name,
                        $packageName,
                        $transaction->expiry_date
                    ));

                    $transaction->renewal_reminder_sent = 1;
                    $transaction->save();
                    $sent++;
                    $this->info("Renewal reminder sent to {$user->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send renewal reminder to {$user->email}: {$e->getMessage()}");
                }
            }
        }

        if ($expiryEnabled === '1') {
            $yesterday = date('Y-m-d H:i', strtotime('-24 hours'));

            $transactions = Transaction::with('user', 'package')
                ->where('status', 0)
                ->where('expiry_notified', 0)
                ->where('expiry_date', '<=', $now)
                ->where('updated_at', '>=', $yesterday)
                ->get();

            foreach ($transactions as $transaction) {
                $user = $transaction->user;
                if (!$user || empty($user->email)) {
                    continue;
                }

                $packageName = $transaction->package ? $transaction->package->name : 'Subscription';

                try {
                    Mail::to($user->email)->send(new SubscriptionExpiredMail(
                        $user->full_name ?: $user->user_name,
                        $packageName,
                        $transaction->expiry_date
                    ));

                    $transaction->expiry_notified = 1;
                    $transaction->save();
                    $sent++;
                    $this->info("Expiry notification sent to {$user->email}");
                } catch (\Exception $e) {
                    $this->error("Failed to send expiry notification to {$user->email}: {$e->getMessage()}");
                }
            }
        }

        $this->info("Finished. Sent {$sent} notification(s).");
        return Command::SUCCESS;
    }
}
