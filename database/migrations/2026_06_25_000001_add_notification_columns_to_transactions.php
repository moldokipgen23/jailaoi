<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_transaction', function (Blueprint $table) {
            $table->tinyInteger('renewal_reminder_sent')->default(0)->after('expiry_date');
            $table->tinyInteger('expiry_notified')->default(0)->after('renewal_reminder_sent');
        });

        $seeds = [
            ['key' => 'renewal_reminder_days',     'value' => '7'],
            ['key' => 'renewal_reminder_enabled',   'value' => '1'],
            ['key' => 'expiry_notification_enabled','value' => '1'],
        ];

        foreach ($seeds as $seed) {
            $exists = DB::table('tbl_general_setting')->where('key', $seed['key'])->exists();
            if (!$exists) {
                DB::table('tbl_general_setting')->insert([
                    'key'        => $seed['key'],
                    'value'      => $seed['value'],
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }

    public function down(): void
    {
        Schema::table('tbl_transaction', function (Blueprint $table) {
            $table->dropColumn(['renewal_reminder_sent', 'expiry_notified']);
        });

        DB::table('tbl_general_setting')->whereIn('key', [
            'renewal_reminder_days', 'renewal_reminder_enabled', 'expiry_notification_enabled',
        ])->delete();
    }
};
