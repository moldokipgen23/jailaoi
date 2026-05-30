<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class MediaVerify extends Command
{
    protected $signature = 'media:verify
        {--fix : Fix DB paths by stripping embedded folder prefixes}
        {--check-exists : Check if files actually exist on disk}';

    protected $description = 'Verify and fix media file paths in the database';

    private array $tables = [
        'tbl_user' => [
            'columns' => ['image', 'cover_img'],
            'folder' => 'user',
        ],
        'tbl_content' => [
            'columns' => ['portrait_img', 'landscape_img', 'content'],
            'folder' => 'content',
        ],
        'tbl_artist' => [
            'columns' => ['image'],
            'folder' => 'artist',
        ],
        'tbl_category' => [
            'columns' => ['image'],
            'folder' => 'category',
        ],
    ];

    public function handle(): int
    {
        $fixed = 0;
        $missing = 0;
        $totalChecked = 0;

        foreach ($this->tables as $table => $config) {
            $folder = $config['folder'];
            $rows = DB::table($table)->get();

            foreach ($rows as $row) {
                foreach ($config['columns'] as $column) {
                    $value = $row->$column ?? '';
                    if (empty($value)) continue;

                    $totalChecked++;

                    // Check for embedded folder prefix
                    $stripped = $value;
                    if (str_starts_with($value, $folder . '/')) {
                        $stripped = substr($value, strlen($folder) + 1);
                        if ($this->option('fix')) {
                            DB::table($table)->where('id', $row->id)->update([$column => $stripped]);
                            $this->line("  Fixed: {$table}.{$column} '{$value}' → '{$stripped}'");
                            $fixed++;
                        } else {
                            $this->line("  Prefix found: {$table}.{$column} id={$row->id}: '{$value}'");
                        }
                    }

                    // Check if file exists on disk
                    if ($this->option('check-exists')) {
                        $exists = Storage::disk('public')->exists($folder . '/' . $stripped);
                        if (!$exists) {
                            $this->line("  MISSING: {$folder}/{$stripped} ({$table}.{$column} id={$row->id})");
                            $missing++;
                        }
                    }
                }
            }
        }

        $this->newLine();
        $this->info("Total paths checked: {$totalChecked}");
        if ($this->option('fix')) {
            $this->info("Fixed: {$fixed} paths");
        }
        if ($this->option('check-exists')) {
            $this->info("Missing files: {$missing}");
        }

        return Command::SUCCESS;
    }
}
