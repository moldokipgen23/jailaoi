<?php

namespace App\Console\Commands;

use App\Models\Artist;
use App\Models\Content;
use App\Models\Music;
use Illuminate\Console\Command;

class MirrorContentToMusic extends Command
{
    protected $signature = 'mirror:content-to-music
        {--pretend : Dry-run — show what would be mirrored without inserting}';

    protected $description = 'Mirror existing tbl_content (content_type=2) records into tbl_music so the Flutter app can play them';

    public function handle(): int
    {
        $pretend = $this->option('pretend');
        $prefixes = ['upload/audio/', 'upload/photos/'];

        $contents = Content::where('content_type', 2)
            ->where('status', 1)
            ->whereNotNull('content')
            ->where('content', '!=', '')
            ->orderBy('id')
            ->get();

        $total = $contents->count();
        $mirrored = 0;
        $skipped = 0;
        $errors = 0;

        $this->info("Found {$total} content records to process.");
        if ($pretend) {
            $this->warn('--pretend mode: no changes will be made.');
        }

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        foreach ($contents as $content) {
            try {
                // Skip if already mirrored
                if (Music::where('jailaoi_content_id', $content->id)->exists()) {
                    $skipped++;
                    $bar->advance();
                    continue;
                }

                // Resolve artist via channel_id -> tbl_user -> tbl_artist
                $artist = Artist::where('user_id', function ($q) use ($content) {
                    $q->select('id')
                      ->from('tbl_user')
                      ->where('channel_id', $content->channel_id)
                      ->limit(1);
                })->first();

                if (!$artist) {
                    $this->line('');
                    $this->warn("Artist not found for content#{$content->id} (channel: {$content->channel_id}) — skipping");
                    $errors++;
                    $bar->advance();
                    continue;
                }

                // Strip old DeepSound prefixes from audio path
                $audioPath = $content->content;
                foreach ($prefixes as $p) {
                    if (str_starts_with($audioPath, $p)) {
                        $audioPath = substr($audioPath, strlen($p));
                        break;
                    }
                }

                // Strip old DeepSound prefixes from portrait image
                $portraitPath = $content->portrait_img ?? '';
                foreach ($prefixes as $p) {
                    if (str_starts_with($portraitPath, $p)) {
                        $portraitPath = substr($portraitPath, strlen($p));
                        break;
                    }
                }

                $duration = $content->content_duration ?? 0;

                if ($pretend) {
                    $this->line('');
                    $this->info("  [PRETEND] Would mirror content#{$content->id}: \"{$content->title}\"");
                    $this->info("           artist_id: {$artist->id}, audio: {$audioPath}, image: {$portraitPath}");
                    $bar->advance();
                    continue;
                }

                Music::create([
                    'jailaoi_content_id' => $content->id,
                    'title'              => $content->title ?? '',
                    'artist_id'          => (string) $artist->id,
                    'album_name'         => '',
                    'category_id'        => $content->category_id ?? 0,
                    'language_id'        => $content->language_id ?? 0,
                    'is_premium'         => 0,
                    'duration'           => $duration,
                    'upload_type'        => 1,
                    'music'              => $audioPath,
                    'description'        => $content->description ?? '',
                    'portrait_img'       => $portraitPath,
                    'landscape_img'      => '',
                    'ogtag_img'          => '',
                    'total_play'         => $content->total_view ?? $content->total_play ?? 0,
                    'status'             => 1,
                ]);

                $mirrored++;
            } catch (\Exception $e) {
                $this->line('');
                $this->error("Error mirroring content#{$content->id}: " . $e->getMessage());
                $errors++;
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine(2);

        $this->table(
            ['Status', 'Count'],
            [
                ['Mirrored', $mirrored],
                ['Skipped (already mirrored)', $skipped],
                ['Errors', $errors],
            ]
        );

        if ($pretend) {
            $this->warn('Dry-run complete — no records were inserted.');
        } else {
            $this->info("Done! {$mirrored} records mirrored into tbl_music.");
        }

        return Command::SUCCESS;
    }
}
