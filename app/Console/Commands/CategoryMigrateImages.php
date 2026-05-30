<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CategoryMigrateImages extends Command
{
    protected $signature = 'category:migrate-images
        {--old-domain=https://jailaoi.com : Old DeepSound domain}';

    protected $description = 'Download and migrate category images from old DeepSound install';

    private array $oldCategories = [
        1 => 'upload/photos/2024/04/wM7pY1QcTzBdvuQnhMUo_06_5836c27594a88770508ed5140d7aa5c0_image.png',
        2 => 'upload/photos/2024/01/uM4VldveZdslDI9dm6Az_10_16458a15f5d72c0e25a0b5f922aa0086_image.png',
        3 => 'upload/photos/2024/01/WT5Oxbw9YKEl3qV1pOPl_10_13d195fe94969a9aac03a1a0260eb17f_image.png',
        4 => 'upload/photos/2024/01/tq2UZzP6WwQplJvE325N_10_72461996381beb0b8391d76064317dbf_image.png',
        5 => 'upload/photos/2024/01/N7aW55wfOHUxuT7kVKkn_10_4320ab7c71d0d776ca5500a68109d30a_image.png',
        6 => 'upload/photos/2024/01/AmVMLJywBNaP2DF1xtMt_10_bfa902d78eefd4c398c9705317d68222_image.png',
        7 => 'upload/photos/2024/01/CGJTyuc1bM5Afa3NCJgx_10_bc2d6eab154227000807d2cae5f6d7d8_image.png',
        8 => 'upload/photos/2024/01/TJAdIid7yLRTLdvPAisA_10_8c73f1997a37a3d8854f5e047f13974e_image.png',
        9 => 'upload/photos/2024/01/DdLtiMDA9G312P2X77aY_10_cc64261acce797f6bb70668c78026a8d_image.png',
    ];

    public function handle(): int
    {
        $oldDomain = rtrim($this->option('old-domain'), '/');
        $count = 0;
        $errors = 0;

        foreach ($this->oldCategories as $categoryId => $oldPath) {
            $category = DB::table('tbl_category')->where('id', $categoryId)->first();
            if (!$category) {
                $this->warn("Category ID $categoryId not found in DB, skipping");
                continue;
            }

            // Transform old path: strip "upload/photos/" prefix
            $relativePath = $this->transformPath($oldPath);

            // Get the year/month and filename
            $pathParts = explode('/', $relativePath);
            $year = $pathParts[0] ?? date('Y');
            $month = $pathParts[1] ?? date('m');
            $filename = $pathParts[2] ?? null;

            if (!$filename) {
                $this->warn("Could not parse filename from: $oldPath");
                continue;
            }

            $datePath = "$year/$month";
            $fullLocalDir = storage_path("app/public/category/$datePath");

            if (!is_dir($fullLocalDir)) {
                mkdir($fullLocalDir, 0777, true);
            }

            $localPath = "$datePath/$filename";

            // Check if file already exists locally
            if (Storage::disk('public')->exists("category/$localPath")) {
                $this->line("Already exists: category/$localPath");
                DB::table('tbl_category')->where('id', $categoryId)->update(['image' => $localPath]);
                $count++;
                continue;
            }

            // Download from old domain
            $oldUrl = "$oldDomain/$oldPath";
            $this->line("Downloading: $oldUrl");

            $content = @file_get_contents($oldUrl);
            if ($content === false) {
                $this->error("Failed to download: $oldUrl");
                $errors++;
                continue;
            }

            $written = file_put_contents("$fullLocalDir/$filename", $content);
            if ($written === false) {
                $this->error("Failed to write: $fullLocalDir/$filename");
                $errors++;
                continue;
            }

            DB::table('tbl_category')->where('id', $categoryId)->update(['image' => $localPath]);
            $this->info("Saved: category/$localPath (" . round(strlen($content) / 1024) . " KB)");
            $count++;
        }

        $this->newLine();
        $this->info("Done: $count images migrated, $errors errors");

        return $errors > 0 ? Command::FAILURE : Command::SUCCESS;
    }

    private function transformPath(string $oldPath): string
    {
        $prefixes = [
            'upload/photos/',
            'upload/audio/',
        ];

        foreach ($prefixes as $prefix) {
            if (str_starts_with($oldPath, $prefix)) {
                return substr($oldPath, strlen($prefix));
            }
        }

        return $oldPath;
    }
}
