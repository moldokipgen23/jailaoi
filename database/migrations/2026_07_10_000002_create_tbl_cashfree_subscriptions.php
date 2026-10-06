<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_cashfree_subscriptions')) {
            Schema::create('tbl_cashfree_subscriptions', function (Blueprint $table) {
                $table->id();
                $table->string('subscription_id')->unique();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('package_id');
                $table->decimal('plan_amount', 12, 2);
                $table->string('status')->default('created')->comment('created, active, on_hold, cancelled, expired, failed, completed');
                $table->timestamp('next_schedule_date')->nullable();
                $table->timestamps();
            });
        }

        if (!Schema::hasColumn('tbl_transaction', 'cf_subscription_id')) {
            Schema::table('tbl_transaction', function (Blueprint $table) {
                $table->string('cf_subscription_id')->nullable()->after('transaction_id');
                $table->index('cf_subscription_id');
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_cashfree_subscriptions');
        if (Schema::hasColumn('tbl_transaction', 'cf_subscription_id')) {
            Schema::table('tbl_transaction', function (Blueprint $table) {
                $table->dropIndex(['cf_subscription_id']);
                $table->dropColumn('cf_subscription_id');
            });
        }
    }
};
