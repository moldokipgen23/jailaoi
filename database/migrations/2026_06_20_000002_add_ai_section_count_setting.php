<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('tbl_general_setting')->where('key', 'ai_section_count')->exists();
        if (!$exists) {
            DB::table('tbl_general_setting')->insert([
                'key'   => 'ai_section_count',
                'value' => '2',
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->where('key', 'ai_section_count')->delete();
    }
};
