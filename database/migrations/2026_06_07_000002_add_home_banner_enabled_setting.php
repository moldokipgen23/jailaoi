<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('tbl_general_setting')->where('key', 'home_banner_enabled')->exists();
        if (!$exists) {
            DB::table('tbl_general_setting')->insert([
                'key' => 'home_banner_enabled',
                'value' => '1',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->where('key', 'home_banner_enabled')->delete();
    }
};
