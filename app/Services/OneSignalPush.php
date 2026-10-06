<?php
namespace App\Services;

use App\Models\General_Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class OneSignalPush
{
    public function send(array $payload): array
    {
        $settings = General_Setting::whereIn('key', ['onesignal_apid', 'onesignal_rest_key'])->pluck('value', 'key');
        $appId = trim($settings['onesignal_apid'] ?? '');
        $key = trim($settings['onesignal_rest_key'] ?? '');
        if ($appId === '' || $key === '') return ['sent' => false, 'reason' => 'not_configured'];
        try {
            $response = Http::timeout(15)->withHeaders(['Authorization' => 'Key ' . $key])
                ->post('https://api.onesignal.com/notifications', array_merge($payload, ['app_id' => $appId]));
            if (!$response->successful() || !$response->json('id')) {
                Log::warning('Push request was not accepted.', ['http_status' => $response->status()]);
                return ['sent' => false, 'reason' => 'provider_rejected'];
            }
            // Accepted by provider is not proof that every device displayed it.
            return ['sent' => true, 'reason' => 'accepted'];
        } catch (\Exception $e) {
            Log::warning('Push delivery is temporarily unavailable.');
            return ['sent' => false, 'reason' => 'unavailable'];
        }
    }
}
