<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (!Schema::hasTable('tbl_page')) {
            Schema::create('tbl_page', function (Blueprint $table) {
                $table->increments('id');
                $table->string('title');
                $table->text('description');
                $table->string('icon')->default('');
                $table->integer('status')->default(1);
                $table->timestamps();
            });

            DB::table('tbl_page')->insert([
                ['title' => 'About Us',           'description' => '', 'icon' => '', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Privacy Policy',      'description' => '', 'icon' => '', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Terms & Conditions',  'description' => '', 'icon' => '', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
                ['title' => 'Refund Policy',       'description' => '', 'icon' => '', 'status' => 1, 'created_at' => now(), 'updated_at' => now()],
            ]);
        }
    }

    public function down(): void
    {
        // intentionally left empty — never drop live data
    }
};
