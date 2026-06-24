<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $seeds = [
            ['key' => 'interstital_cooldown', 'value' => '60'],
            ['key' => 'ios_interstital_cooldown', 'value' => '60'],
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
        DB::table('tbl_general_setting')->whereIn('key', [
            'interstital_cooldown',
            'ios_interstital_cooldown',
        ])->delete();
    }
};
