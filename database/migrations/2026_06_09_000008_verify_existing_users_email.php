<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

// JAILAOI: All users who registered before email verification was introduced
// have email_verified_at = NULL and get blocked at login.
// Mark all existing unverified users as verified using their created_at date.
return new class extends Migration
{
    public function up(): void
    {
        DB::table('tbl_user')
            ->whereNull('email_verified_at')
            ->update(['email_verified_at' => DB::raw('created_at')]);
    }

    public function down(): void
    {
        // Non-reversible — we do not want to un-verify users
    }
};
