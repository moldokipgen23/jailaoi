<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Artist;
use App\Models\Batch;
use App\Models\Category;
use App\Models\City;
use App\Models\General_Setting;
use App\Models\Language;
use App\Models\User_Summary;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class createUploadBatchFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:create-upload-batch-file';

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

        $ai_section_count = max(1, (int) (General_Setting::where('key', 'ai_section_count')->value('value') ?? 2));

        User_Summary::select('id', 'user_id', 'score_json')->where('status', 1)->chunkById(5000, function ($rows) use ($api_key, $ai_section_count) {

            $filename = 'public/batch/input_' . now()->timestamp . "_" . uniqid() . '.jsonl';
            $filePath = storage_path('app/' . $filename);
            if (!is_dir(dirname($filePath))) {
                mkdir(dirname($filePath), 0755, true);
            }
            $file = fopen($filePath, 'w');

            foreach ($rows as $item) {
                $user_id = $item->user_id;
                $data = json_decode($item->score_json, true);

                if (!is_array($data)) {
                    Log::error("createUploadBatchFile: invalid score_json for user {$user_id}");
                    $item->delete();
                    continue;
                }

                $keys = ['top_category', 'top_language', 'top_artist', 'top_city'];
                $result = array_fill_keys($keys, []);

                foreach ($data['affinity_vectors'] ?? [] as $vector) {
                    if (!is_array($vector)) {
                        continue;
                    }
                    foreach ($keys as $key) {
                        foreach (array_keys($vector[$key] ?? []) as $id) {
                            $result[$key][$id] = true;
                        }
                    }
                }

                foreach ($result as $key => $value) {
                    $result[$key] = array_keys($value);
                }

                $category_ids = $result['top_category'];
                $language_ids = $result['top_language'];
                $artist_ids = $result['top_artist'];
                $city_ids = $result['top_city'];

                $content_mapping = [
                    '1' => "Radio",
                    '2' => "Podcast",
                    '3' => "Music",
                ];

                $category_mappings = Category::whereIn('id', $category_ids)->pluck('name', 'id')->toArray();
                $language_mappings = Language::whereIn('id', $language_ids)->pluck('name', 'id')->toArray();
                $artist_mappings = Artist::whereIn('id', $artist_ids)->pluck('name', 'id')->toArray();
                $city_mappings = City::whereIn('id', $city_ids)->pluck('name', 'id')->toArray();

                $final_result = [
                    'content_mappings' => $content_mapping,
                    'category_mappings' => $category_mappings,
                    'language_mappings' => $language_mappings,
                    'artist_mappings' => $artist_mappings,
                    'city_mappings' => $city_mappings,
                    'user_data' => $data,
                ];
                $final_result = json_encode($final_result);

                $prompt = <<<PROMPT
You are a streaming personalization engine. Return ONLY a valid JSON array. No explanations, no markdown.

TYPE CODES: 1=Radio, 2=Podcast, 8=Music
TYPE NORMALIZATION: in affinity_vectors, type=3 represents Music and MUST be treated as Music and output as tp=8; tp=3 MUST NEVER appear in sections.

INSTRUCTIONS:

1. Sort content types by user_data[uid].content_type_affinity[tp].score descending.
2. Compute gap_12 = top score − second score.
3. Produce exactly N={$ai_section_count} sections total. Distribute across content types by affinity score proportion:
   - Rank types by content_type_affinity score descending
   - Assign sections proportionally: top type gets the most, lower types get fewer
   - Minimum 1 section for the top-ranked type; lower types get sections only if N allows
   - If only 1 type has affinity data, all N sections use that type with different filter rotations
   - Round so total = exactly N
4. If a type has no affinity vectors, allocate **generic sections** for it.

FILTERS:

For each type build four isolated value lists from user_data[uid].affinity_vectors[tp] only:
- artist_pool = top_artist keys of that tp sorted by score descending
- category_pool = top_category keys of that tp sorted by score descending
- language_pool = top_language keys of that tp sorted by score descending
- city_pool = top_city keys of that tp sorted by score descending

Hard isolation: a value is only legal in a section if it comes from that section's own tp pool. A value from tp=8 pools cannot appear in tp=2 or tp=1 sections. A value from tp=2 pools cannot appear in tp=8 or tp=1 sections. A value from tp=1 pools cannot appear in tp=2 or tp=8 sections.

City rule: city_pool is only usable for tp=1. For tp=2 and tp=8 city is always 0.

Each section has a primary filter and an optional only one secondary filter. Maximum two filters per section. All unused filter fields are zero.

Primary filter: pick next dimension in SCORE-DRIVEN rotation order for that type. Assign highest unused value from that pool.
Secondary filter: allowed only if a different dimension has an unused value whose score is within five percent of the primary score. If allowed assign that value as secondary. Otherwise secondary is zero.
Both primary and secondary must come from that section's own tp pools only.

Rotation order per type is SCORE-DRIVEN:

For each tp, compute dimension_strength as:
- artist_strength = highest artist score in artist_pool (or 0 if empty)
- category_strength = highest category score in category_pool (or 0 if empty)
- language_strength = highest language score in language_pool (or 0 if empty)
- city_strength = highest city score in city_pool (or 0 if empty)

Sort dimensions by dimension_strength descending.
This sorted list defines the rotation order for that type.
Skip any dimension whose pool is empty.

SECTION ORDER:

- Output highest-affinity type sections first, then lower types
- Within a type, rotate filter dimensions by score strength

TITLE RULES:

- Maximum 4 words
- Simple, clean, professional streaming tone
- Modern and platform-like
- No heavy vocabulary
- No dramatic wording
- No emotional exaggeration
- No behavioral explanation
- No analytics language
- Forbidden words:
  favourite, favorite, followed, following, liked,
  your, because, based on
- Title must reflect the selected filter context naturally.
- Avoid robotic template phrasing.
- If multiple sections share the same tp but use different filters,
- DO NOT repeat the same grammatical structure.
- Vary phrasing structure across sections.
- Titles must feel editorial, not mechanical.
- Do not rely on a single pattern like repeating “in”, “by”, or similar connectors.
- Keep structure varied while staying concise.

SUBTITLE RULES:

- Maximum 6 words
- Neutral streaming tone
- Must NOT repeat filter term
- No explanation
- No analytics wording

LAYOUT:

- sl ∈ {landscape, square, small_square}, vary across sections
- noc ∈ [4,20], vary across sections

OUTPUT:

Return exactly:
[{"uid":number,"sections":[{"t":string,"st":string,"tp":number,"aid":number,"cid":number,"lid":number,"cty":number,"noc":number,"sl":string}]}]

FINAL RULES:

1. Always produce exactly {$ai_section_count} sections. No more, no less.
2. Never include explanations, markdown, or extra text.
PROMPT;

                $request = [
                    'custom_id' => (string)$user_id,
                    'method' => 'POST',
                    'url' => '/v1/chat/completions',
                    'body' => [
                        'model' => 'gpt-4o-mini',
                        'messages' => [
                            [
                                'role' => 'system',
                                'content' => $prompt
                            ],
                            [
                                'role' => 'user',
                                'content' => $final_result
                            ]
                        ],
                    ],

                ];
                fwrite($file, json_encode($request) . "\n");
            }
            fclose($file);
            $file = basename($filename);

            if (filesize($filePath) == 0) {
                Log::info("createUploadBatchFile: {$file} File Is Empty");
                // delete empty file
                Storage::delete($filename);
                return;
            }

            $fileStream = fopen($filePath, 'r');
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
            ])->attach(
                'file',
                $fileStream,
                basename($filePath)
            )->post(
                'https://api.openai.com/v1/files',
                [
                    'purpose' => 'batch',
                ]

            );
            fclose($fileStream);

            if (!$response->successful()) {
                Log::info("createUploadBatchFile: Failed To Upload File {$file}");

                // clear local storage
                Storage::delete($filename);
                return;
            }

            $result = $response->json();

            $input_file_id = $result['id'] ?? null;
            if ($input_file_id) {
                Log::info("createUploadBatchFile: {$input_file_id}");

                Batch::create([
                    'input_file_id' => $input_file_id,
                    'batch_id' => "",
                    'output_file_id' => "",
                    'error_file_id' => "",
                    'status' => "uploaded",
                ]);
            } else {
                Log::error("createUploadBatchFile: Failed To Upload Batch File: {$response->body()}");
            }

            // clear local storage
            Storage::delete($filename);
        });

        $batches = Batch::where('status', 'uploaded')->get();

        if ($batches->isEmpty()) {
            return Command::SUCCESS;
        }

        foreach ($batches as $batch) {

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $api_key,
            ])->post('https://api.openai.com/v1/batches', [
                'input_file_id' => $batch->input_file_id,
                'endpoint' => '/v1/chat/completions',
                'completion_window' => '24h',
            ]);

            if (!$response->successful()) {
                Log::info("retryUpload: Failed To Batch File having file id: {$batch->input_file_id}");
                continue;
            }

            $result = $response->json();

            $batch_id = $result['id'] ?? null;
            if ($batch_id) {
                $batch->update(['batch_id' => $batch_id, 'status' => $result['status']]);
                Log::info("sendBatchRequest: Created batch ID {$batch_id} for item ID {$batch->id}");
            } else {
                Log::error("sendBatchRequest: Failed to create batch for item ID {$batch->id}. Response: {$response->body()}");
            }
        }

        return Command::SUCCESS;
    }
}
