<?php
namespace App\Console\Commands;
use App\Models\Batch;
use App\Models\General_Setting;
use App\Models\User_Summary;
use App\Services\AiSectionImporter;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class createSections extends Command
{
    protected $signature = 'app:create-sections';
    protected $description = 'Import validated personalized sections while retaining failed work for retry.';
    public function handle()
    {
        $key = General_Setting::where('key', 'ai_api_key')->value('value');
        if (!$key || General_Setting::where('key', 'ai_section')->value('value') != 1) return Command::SUCCESS;
        $batch = Batch::whereIn('status', ['completed', 'expired', 'cancelled'])->orderBy('id')->first();
        if (!$batch) return Command::SUCCESS;
        $count = (int) (General_Setting::where('key', 'ai_section_count')->value('value') ?? 2);
        try {
            $outputs = [];
            // Download all results before changing data; transient errors retain the batch.
            foreach (['output_file_id', 'error_file_id'] as $field) {
                if (!$batch->$field) continue;
                $response = Http::withToken($key)->timeout(30)->get('https://api.openai.com/v1/files/' . $batch->$field . '/content');
                if (!$response->successful()) {
                    Log::warning('AI result download failed', ['status'=>$response->status()]);
                    return Command::FAILURE;
                }
                foreach (explode("\n", trim($response->body())) as $line) {
                    if ($line === '') continue;
                    $row = json_decode($line, true);
                    if (!is_array($row) || !isset($row['custom_id'])) return Command::FAILURE;
                    $outputs[] = $row;
                }
            }
            if (!$outputs) return Command::FAILURE;
            foreach ($outputs as $row) {
                $userId = filter_var($row['custom_id'], FILTER_VALIDATE_INT);
                if (!$userId || $userId < 1) continue;
                $summary = User_Summary::where('user_id', $userId)->where('status', 1)->where('created_at', '<=', $batch->created_at)->first();
                if (!$summary) continue;
                $content = $row['response']['body']['choices'][0]['message']['content'] ?? null;
                if (($row['response']['status_code'] ?? 0) !== 200 || !is_string($content)) continue;
                if ((new AiSectionImporter)->replace($userId, $content, $count)) $summary->delete();
                else Log::warning('AI sections rejected; existing recommendations and summary preserved.');
            }
            $batch->update(['status'=>'processed']);
            return Command::SUCCESS;
        } catch (\Throwable $e) {
            Log::warning('AI section import failed', ['exception'=>get_class($e)]);
            return Command::FAILURE;
        }
    }
}
