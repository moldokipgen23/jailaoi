<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private array $columns = [
        ['section_type', 'integer', 0],
        ['type', 'integer', 0],
        ['sub_title', 'string', null, true],
        ['user_id', 'integer', 0],
        ['artist_id', 'integer', 0],
        ['category_id', 'integer', 0],
        ['language_id', 'integer', 0],
        ['city_id', 'integer', 0],
        ['screen_layout', 'string', 'landscape'],
        ['is_premium', 'integer', 2],
        ['order_by_upload', 'integer', 0],
        ['order_by_play', 'integer', 0],
        ['is_paid', 'integer', 0],
        ['is_title', 'integer', 1],
        ['is_category', 'integer', 0],
        ['is_artist_name', 'integer', 1],
        ['no_of_content', 'integer', 20],
        ['view_all', 'integer', 1],
        ['sortable', 'integer', 1],
        ['status', 'integer', 1],
    ];

    public function up(): void
    {
        if (!Schema::hasTable('tbl_section')) return;

        foreach ($this->columns as $col) {
            $name = $col[0];
            $type = $col[1];
            $default = $col[2];
            $nullable = $col[3] ?? false;

            if (Schema::hasColumn('tbl_section', $name)) continue;

            Schema::table('tbl_section', function (Blueprint $table) use ($name, $type, $default, $nullable) {
                $c = $type === 'integer' ? $table->integer($name)->default($default) : $table->string($name)->default($default ?? '');
                if ($nullable) $c->nullable();
            });
        }
    }

    public function down(): void
    {
    }
};
