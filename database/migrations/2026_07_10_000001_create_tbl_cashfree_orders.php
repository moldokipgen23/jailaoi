<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_cashfree_orders')) {
            Schema::create('tbl_cashfree_orders', function (Blueprint $table) {
                $table->id();
                $table->string('order_id')->unique();
                $table->unsignedBigInteger('user_id');
                $table->unsignedBigInteger('package_id');
                $table->decimal('amount', 12, 2);
                $table->string('status')->default('created')->comment('created, paid, failed');
                $table->timestamp('paid_at')->nullable();
                $table->timestamps();
            });
        }
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_cashfree_orders');
    }
};
