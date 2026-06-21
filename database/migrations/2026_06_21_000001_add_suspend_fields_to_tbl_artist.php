<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_artist', function (Blueprint $table) {
            $table->tinyInteger('is_suspended')->default(0)->after('status');
            $table->text('suspend_reason')->nullable()->after('is_suspended');
            $table->timestamp('suspended_at')->nullable()->after('suspend_reason');
        });
    }

    public function down(): void
    {
        Schema::table('tbl_artist', function (Blueprint $table) {
            $table->dropColumn(['is_suspended', 'suspend_reason', 'suspended_at']);
        });
    }
};
