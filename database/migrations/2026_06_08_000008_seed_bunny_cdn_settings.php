<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// JAILAOI: Seeds Bunny CDN credential keys into tbl_general_setting so they
// can be managed from the admin panel instead of requiring .env edits.
return new class extends Migration
{
    public function up(): void
    {
        $settings = [
            'bunny_storage_zone'     => '',
            'bunny_storage_api_key'  => '',
            'bunny_storage_endpoint' => 'https://storage.bunnycdn.com',
            'bunny_cdn_url'          => '',
        ];

        foreach ($settings as $key => $default) {
            $exists = DB::table('tbl_general_setting')->where('key', $key)->exists();
            if (!$exists) {
                DB::table('tbl_general_setting')->insert([
                    'key'   => $key,
                    'value' => $default,
                ]);
            }
        }
    }

    public function down(): void
    {
        DB::table('tbl_general_setting')->whereIn('key', [
            'bunny_storage_zone',
            'bunny_storage_api_key',
            'bunny_storage_endpoint',
            'bunny_cdn_url',
        ])->delete();
    }
};
