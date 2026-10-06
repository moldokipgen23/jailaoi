<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Validation\ValidationException;

class FirebaseIdentityVerifier
{
    public function verify(string $token): array
    {
        $invalid = fn () => ValidationException::withMessages(['identity_token' => 'Could not verify your identity. Please sign in again.']);
        $parts = explode('.', $token);
        if (count($parts) !== 3 || strlen($token) > 16384) throw $invalid();
        $decode = static function ($part) {
            if (!preg_match('/^[A-Za-z0-9_-]+$/D', $part)) return false;
            return base64_decode(strtr($part, '-_', '+/') . str_repeat('=', (4 - strlen($part) % 4) % 4), true);
        };
        $header = json_decode($decode($parts[0]) ?: '', true);
        $claims = json_decode($decode($parts[1]) ?: '', true);
        $signature = $decode($parts[2]);
        $project = config('services.firebase.project_id');
        if (!is_array($header) || !is_array($claims) || !$signature || !$project
            || ($header['alg'] ?? '') !== 'RS256' || !is_string($header['kid'] ?? null)
            || ($claims['aud'] ?? '') !== $project
            || ($claims['iss'] ?? '') !== 'https://securetoken.google.com/' . $project
            || !is_string($claims['sub'] ?? null) || $claims['sub'] === '' || strlen($claims['sub']) > 128
            || !is_numeric($claims['exp'] ?? null) || $claims['exp'] <= time()
            || !is_numeric($claims['iat'] ?? null) || $claims['iat'] > time()
            || !is_numeric($claims['auth_time'] ?? null) || $claims['auth_time'] > time()) throw $invalid();

        $certificates = Cache::remember('firebase_signing_certificates', 3600, function () {
            $response = Http::timeout(10)->get('https://www.googleapis.com/robot/v1/metadata/x509/securetoken@system.gserviceaccount.com');
            $response->throw();
            return $response->json();
        });
        $certificate = $certificates[$header['kid']] ?? null;
        if (!$certificate || openssl_verify($parts[0] . '.' . $parts[1], $signature, $certificate, OPENSSL_ALGO_SHA256) !== 1) throw $invalid();
        return $claims;
    }
}
