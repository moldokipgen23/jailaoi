<?php

namespace App\Console\Commands;

use App\Models\Batch;
use App\Models\General_Setting;
use App\Models\Section;
use App\Models\User_Summary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class createSections extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-sections';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'It generates sections for users based on their summary using an AI model.';

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

        $batch = Batch::where('status', 'completed')->first();
        if (!$batch) {
            return Command::SUCCESS;
        }

        $user_ids = [];

        if (!empty($batch->output_file_id)) {

            $output_file_id = $batch->output_file_id;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
            ])->get(
                'https://api.openai.com/v1/files/' . $output_file_id . '/content',
            );

            if ($response->successful()) {
                $result = explode("\n", $response->body());

                foreach ($result as $request) {

                    $request_data = json_decode($request, true);

                    if (!is_array($request_data) || empty($request_data)) {
                        continue;
                    }

                    if (isset($request_data['response']['body']['error'])) {
                        Log::error("createSections: Error for user {$request_data['custom_id']}:  {$request_data['response']['body']['error']['message']}");
                        continue;
                    }

                    $user_ids[] = $request_data['custom_id'] ?? 0;

                    $content = isset($request_data['response']['body']['choices'][0]['message']['content']) ? $request_data['response']['body']['choices'][0]['message']['content'] : null;
                    $content = json_decode($content, true);

                    if (isset($content) && is_array($content)) {

                        foreach ($content as  $key => $value) {

                            $sections = [];

                            if (!empty($value['sections'])) {
                                Section::where('user_id', $request_data['custom_id'])->where('section_type', 1)->delete();

                                foreach ($value['sections'] as $section) {
                                    $sections[] = [
                                        'user_id' => $request_data['custom_id'] ?? 0,
                                        'section_type' => 1,
                                        'title' => $section['t'] ?? "",
                                        'sub_title' => $section['st'] ?? "",
                                        'type' => $section['tp'] ?? 0,
                                        'artist_id' => $section['aid'] ?? 0,
                                        'category_id' => $section['cid'] ?? 0,
                                        'language_id' => $section['lid'] ?? 0,
                                        'city_id' => $section['cty'] ?? 0,
                                        'screen_layout' => $section['sl'] ?? "",
                                        'is_premium' => 0,
                                        'order_by_upload' => 1,
                                        'order_by_play' => 1,
                                        'is_paid' => 0,
                                        'is_title' => 1,
                                        'is_category' => 1,
                                        'is_artist_name' => 1,
                                        'no_of_content' => $section['noc'] ?? 0,
                                        'view_all' => 1,
                                        'sortable' => 0,
                                        'status' => 1,
                                        'created_at' => now(),
                                        'updated_at' => now(),
                                    ];
                                }
                            } else {
                                Log::error("createSections: No sections found in the content for user ID:  {$request_data['custom_id']}");
                            }

                            if (!empty($sections)) {
                                Section::insert($sections);
                            }
                        }
                    } else {
                        Log::error("Invalid content format for request {$request}:{$content}");
                    }
                }
            } else {
                Log::error("createSections: Failed to fetch output file content for batch ID {$batch->batch_id}. HTTP Status: {$response->status()}");
            }
        }
        if (!empty($batch->error_file_id)) {
            $error_file_id = $batch->error_file_id;

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
            ])->get(
                'https://api.openai.com/v1/files/' . $error_file_id . '/content',
            );

            if ($response->successful()) {

                $result = explode("\n", $response->body());

                foreach ($result as $request) {

                    $request_data = json_decode($request, true);
                    if (!is_array($request_data) || empty($request_data)) {
                        continue;
                    }

                    $user_ids[] = $request_data['custom_id'] ?? 0;
                    $user_id = $request_data['custom_id'] ?? 0;
                    $error_msg = $request_data['response']['body']['error']['message'] ?? "";

                    Log::info("createSections: {$user_id} - {$error_msg} ");
                }
            } else {
                Log::error("createSections: Failed to fetch error file content for batch ID {$batch->batch_id}. HTTP Status: {$response->status()}");
            }
        }

        User_Summary::whereIn('user_id', $user_ids)->delete();
        $batch->delete();

        return Command::SUCCESS;
    }
}
