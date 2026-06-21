<?php

namespace App\Console\Commands;

use App\Models\Favorite;
use App\Models\User;
use App\Models\User_Action;
use App\Models\User_Summary;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Log;

class updateSummary extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:update-summary';

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
        User_Summary::truncate();

        $data = User_Action::all()
            ->groupBy(['user_id', 'content_type']);
        $ids = [];

        $sample_json = [
            'content_type_affinity' => [],
            'affinity_vectors' => [],
        ];

        $engagementWeight = 0.6;
        $frequencyWeight  = 0.4;

        foreach ($data as $user_id => $types) {

            // --- Get or create user summary ---
            $summary = User_Summary::where('user_id', $user_id)->where('status', 1)->first();
            if (!$summary) {
                $summary = User_Summary::create([
                    'user_id' => $user_id,
                    'score_json' => json_encode($sample_json, true),
                    'status' => 1,
                ]);
            }
            $profile = json_decode($summary['score_json'], true);

            foreach ($types as $contentType => $actions) {
                $ids[] = $actions->pluck('id')->toArray();

                // Raw counts
                $plays      = $actions->where('action', 1);
                $playCount  = $plays->count();
                $totalTime  = $plays->sum('time_spend');

                $favCount   = Favorite::where('user_id', $user_id)->where('type', $contentType)->count();
                $favBoost = min(0.4, log($favCount + 1) * 0.2);

                // If no data continue
                if (($playCount) == 0) {
                    continue;
                }

                // after 15 plays it always 1
                $frequency  = min(1, log($playCount + 1) / log(15));

                // completion ratio per content
                $completionSum = 0;
                $completionCount = 0;

                $groupedByContent = $plays->groupBy('content_id');
                foreach ($groupedByContent as $content) {
                    $contentDuration = max(1, $content->first()->content_duration);
                    $playedTime = min($contentDuration, $content->sum('time_spend'));
                    $completionSum += $playedTime / $contentDuration;
                    $completionCount++;
                }
                $engagement = $completionCount > 0 ? ($completionSum / $completionCount) : 0;

                //weekly score
                $weeklyScore =  ($engagement * $engagementWeight) +  ($frequency * $frequencyWeight) + $favBoost;
                $weeklyScore = max(-1, min(1, $weeklyScore));

                // score update
                if (!isset($profile['content_type_affinity'][$contentType])) {
                    $profile['content_type_affinity'][$contentType] = [
                        'score' => 0,
                        'raw'   => [],
                    ];
                }

                $newScore = round($weeklyScore, 4);

                $profile['content_type_affinity'][$contentType]['score'] =  $newScore;
                $profile['content_type_affinity'][$contentType]['raw'] = [
                    'plays'        => $playCount,
                    'time_spent'   => $totalTime,
                    'completion_ratio'   => round($engagement, 4),
                ];

                // dimensions array
                $dimensions = [
                    'top_category' => 'category_id',
                    'top_language' =>  'language_id',
                    'top_artist'   =>  'artist_id',
                    'top_city'     =>  'city_id',
                ];

                foreach ($dimensions as $dimName => $field) {

                    if ($dimName == 'top_artist' && $contentType == 3) {
                        $artistMap = [];
                        foreach ($actions as $action) {
                            if (empty($action->artist_id)) {
                                continue;
                            }
                            $artistIds = explode(',', $action->artist_id);
                            foreach ($artistIds as $artistId) {
                                $artistMap[$artistId][] = $action;
                            }
                        }
                        $grouped = collect($artistMap);
                    } else {
                        $grouped = $actions->groupBy($field);
                    }

                    foreach ($grouped as $id => $values) {

                        // if any dimension value is empty or zero skip
                        if ($id == "" || $id == 0) {
                            continue;
                        }

                        $values = collect($values);

                        // Raw counts
                        $dimPlays      = $values->where('action', 1);
                        $dimPlayCount  = $dimPlays->count();

                        // If no play data continue
                        if ($dimPlayCount == 0) {
                            continue;
                        }

                        $dimTotalTime  = $dimPlays->sum('time_spend');

                        // after 15 plays it always 1
                        $dimfrequency  = min(1, log($dimPlayCount + 1) / log(15));

                        // completetion ratio per dimension
                        $dimCompletionSum = 0;
                        $dimCompletionCount = 0;

                        $groupedByContent = $dimPlays->groupBy('content_id');
                        foreach ($groupedByContent as $content) {
                            $contentDuration = max(1, $content->first()->content_duration);
                            $playedTime = min($contentDuration, $content->sum('time_spend'));
                            $dimCompletionSum += $playedTime / $contentDuration;
                            $dimCompletionCount++;
                        }
                        $dimEngagement = $dimCompletionCount > 0 ? ($dimCompletionSum / $dimCompletionCount) : 0;

                        //weekly score
                        $dimWeeklyScore =  ($dimEngagement * $engagementWeight) +  ($dimfrequency * $frequencyWeight);
                        $dimWeeklyScore = max(-1, min(1, $dimWeeklyScore));

                        if (!isset($profile['affinity_vectors'][$contentType][$dimName][$id])) {
                            $profile['affinity_vectors'][$contentType][$dimName][$id] = [
                                'score' => 0,
                                'raw'   => [],
                            ];
                        }

                        $profile['affinity_vectors'][$contentType][$dimName][$id]['score'] = round($dimWeeklyScore, 4);

                        $profile['affinity_vectors'][$contentType][$dimName][$id]['raw'] = [
                            'plays'      => $dimPlayCount,
                            'time_spent' => $dimTotalTime,
                            'completion_ratio' => round($dimEngagement, 4),
                        ];
                    }
                }
            }

            if (empty($profile['content_type_affinity']) && empty($profile['affinity_vectors'])) {
                $summary->delete();
            } else {
                foreach ($profile['affinity_vectors'] as $type => $values) {
                    foreach ($values as $dimName => $dimValue) {

                        // sort array based on score in descing order
                        uasort($dimValue, fn($a, $b) => $b['score'] <=> $a['score']);

                        // get top 3 only 
                        $profile['affinity_vectors'][$type][$dimName] = array_slice($dimValue, 0, 3, true);
                    }
                }
                $summary->score_json = json_encode($profile);
                $summary->save();
            }
        }

        $ids = collect($ids)->flatten()->toArray();
        User_Action::whereIn('id', $ids)->delete();

        return Command::SUCCESS;
    }
}
