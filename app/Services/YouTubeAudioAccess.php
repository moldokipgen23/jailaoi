<?php
namespace App\Services;
use App\Models\Artist;
use App\Models\General_Setting;
class YouTubeAudioAccess
{
    public function settings(): array
    {
        $value = General_Setting::where('key', 'youtube_audio_access')->value('value');
        $data = json_decode($value ?? '{}', true);
        return ['enabled'=>(bool)($data['enabled'] ?? false), 'user_ids'=>array_map('intval', is_array($data['user_ids'] ?? null) ? $data['user_ids'] : [])];
    }
    public function allows(int $userId): bool
    {
        $settings = $this->settings();
        return $settings['enabled'] && in_array($userId, $settings['user_ids'], true)
            && Artist::where('user_id', $userId)->where('is_suspended', 0)->exists();
    }
}
