<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_user', 'email_verified_at')) {
                $table->timestamp('email_verified_at')->nullable()->after('email');
            }
            if (!Schema::hasColumn('tbl_user', 'last_login_at')) {
                $table->timestamp('last_login_at')->nullable()->after('email_verified_at');
            }
            if (!Schema::hasColumn('tbl_user', 'email_blast_sent_at')) {
                $table->timestamp('email_blast_sent_at')->nullable()->after('last_login_at');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_user', function (Blueprint $table) {
            $columns = ['email_verified_at', 'last_login_at', 'email_blast_sent_at'];
            foreach ($columns as $col) {
                if (Schema::hasColumn('tbl_user', $col)) {
                    $table->dropColumn($col);
                }
            }
        });
    }
};
