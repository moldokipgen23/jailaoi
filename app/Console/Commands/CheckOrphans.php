<?php

namespace App\Console\Commands;

use App\Models\Content;
use Illuminate\Console\Command;

class CheckOrphans extends Command
{
    protected $signature = 'media:orphans';
    protected $description = 'Count music records with missing audio files';

    public function handle(): int
    {
        $total = Content::where('content_type', 2)->where('content', '!=', '')->count();
        $missing = 0;
        $ids = [];

        foreach (Content::where('content_type', 2)->where('content', '!=', '')->lazyById() as $r) {
            $path = storage_path('app/public/content/' . $r->content);
            if (!file_exists($path)) {
                $missing++;
                $ids[] = $r->id;
            }
        }

        $this->info("Total music records: {$total}");
        $this->info("Missing audio files: {$missing}");
        if ($missing > 0) {
            $this->info("IDs: " . implode(', ', $ids));
        }

        return Command::SUCCESS;
    }
}
