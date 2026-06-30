<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $exists = DB::table('tbl_language')->where('name', 'Zou')->exists();
        if (!$exists) {
            DB::table('tbl_language')->insert([
                'name'         => 'Zou',
                'storage_type' => 0,
                'image'        => '',
                'sort_order'   => 0,
                'status'       => 1,
            ]);
        }
    }

    public function down(): void
    {
        DB::table('tbl_language')->where('name', 'Zou')->delete();
    }
};
