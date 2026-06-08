<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// JAILAOI: Seeds backup_email key into tbl_general_setting.
// Set this in Admin → System Settings or directly in DB to receive daily backup emails.
return new class extends Migration
{
    public function up(): void
    {
        if (!DB::table('tbl_general_setting')->where('key', 'backup_email')->exists()) {
            DB::table('tbl_general_setting')->insert([
                'key'   => 'backup_email',
                'value' => '',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->where('key', 'backup_email')->delete();
    }
};
