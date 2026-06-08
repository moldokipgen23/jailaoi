<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_withdrawal_requests', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_withdrawal_requests', 'paid_at')) {
                $table->timestamp('paid_at')->nullable()->after('processed_at');
            }
            if (!Schema::hasColumn('tbl_withdrawal_requests', 'payment_note')) {
                $table->text('payment_note')->nullable()->after('admin_note');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_withdrawal_requests', function (Blueprint $table) {
            $table->dropColumn(['paid_at', 'payment_note']);
        });
    }
};
