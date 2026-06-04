<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\General_Setting;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class checkStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:check-status';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $api_key = General_Setting::where('key', 'ai_api_key')->value('value');
        $ai_section = General_Setting::where('key', 'ai_section')->value('value');

        if ($api_key == "" || $ai_section != 1) {
            return Command::FAILURE;
        }

        $data = Batch::where('batch_id', '!=', "")->whereIn('status', ['validating', 'in_progress', 'finalizing'])->get();

        if ($data->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($data as $item) {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
            ])->get('https://api.openai.com/v1/batches/' . $item->batch_id . '');

            if (!$response->successful()) {
                Log::error("checkStatus: HTTP error with status {$response->status()}");
                continue;
            }

            $result = $response->json();

            if (isset($result['status'])) {
                Log::info("checkStatus: {$result['status']} for batch ID {$item->batch_id}");
                $item->update([
                    'output_file_id' => $result['output_file_id'] ?? "",
                    'error_file_id' => $result['error_file_id'] ?? "",
                    'status' => $result['status']
                ]);
            } else {
                Log::error("checkStatus: Failed to retrieve status for batch ID {$item->batch_id}. Response:  {$response->body()}");
            }
        }

        return Command::SUCCESS;
    }
}
