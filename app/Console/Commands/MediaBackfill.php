<?php

namespace App\Console\Commands;

use App\Models\Common;
use App\Models\Content;
use Illuminate\Console\Command;

class MediaBackfill extends Command
{
    protected $signature = 'media:backfill
        {--dry-run : Show what would be done without making changes}';

    protected $description = 'Backfill duration and waveform for existing music records';

    public function handle(): int
    {
        $common = new Common;
        $folder = '/app/public/content/';

        $records = Content::where('content_type', 2)
            ->where('content', '!=', '')
            ->where('content_upload_type', 'server_video')
            ->get();

        $bar = $this->output->createProgressBar(count($records));
        $bar->start();

        $durationFixed = 0;
        $waveformFixed = 0;

        foreach ($records as $record) {
            $changed = false;

            if (empty($record->content_duration) || $record->content_duration == 0) {
                $dur = $common->ExtractDuration($record->content, $folder);
                if ($dur > 0) {
                    if (!$this->option('dry-run')) {
                        Content::where('id', $record->id)->update(['content_duration' => $dur]);
                    }
                    $durationFixed++;
                    $changed = true;
                }
            }

            if (empty($record->waveform_data)) {
                $wf = $common->generateWaveform($record->content, $folder);
                if ($wf) {
                    if (!$this->option('dry-run')) {
                        Content::where('id', $record->id)->update(['waveform_data' => $wf]);
                    }
                    $waveformFixed++;
                    $changed = true;
                }
            }

            if ($changed && $this->option('dry-run')) {
                $this->line("  Would fix: id={$record->id} {$record->title}");
            }

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Duration fixed: {$durationFixed}");
        $this->info("Waveforms generated: {$waveformFixed}");

        return Command::SUCCESS;
    }
}
