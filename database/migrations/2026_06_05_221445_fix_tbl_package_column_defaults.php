<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $columns = DB::select("SHOW COLUMNS FROM `tbl_package`");
        foreach ($columns as $col) {
            $field = $col->Field;
            $null = $col->Null;
            $default = $col->Default;
            $type = strtolower($col->Type);

            // Skip auto-increment id and timestamps
            if (in_array($field, ['id', 'created_at', 'updated_at'])) continue;

            // If NOT NULL and no default, add a default
            if ($null === 'NO' && $default === null) {
                if (strpos($type, 'int') !== false) {
                    DB::statement("ALTER TABLE `tbl_package` MODIFY `{$field}` INT NOT NULL DEFAULT 0");
                } elseif (strpos($type, 'varchar') !== false || strpos($type, 'text') !== false) {
                    DB::statement("ALTER TABLE `tbl_package` MODIFY `{$field}` {$col->Type} NOT NULL DEFAULT ''");
                } elseif (strpos($type, 'timestamp') !== false) {
                    if ($field === 'created_at' || $field === 'updated_at') continue;
                    DB::statement("ALTER TABLE `tbl_package` MODIFY `{$field}` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP");
                }
            }
        }
    }

    public function down(): void
    {
        // Reverting individual defaults is not practical.
        // This migration is additive (sets defaults, doesn't remove columns).
    }
};
