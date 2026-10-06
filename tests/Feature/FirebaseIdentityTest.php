<?php
namespace Tests\Feature;

use App\Services\FirebaseIdentityVerifier;
use Illuminate\Support\Facades\Cache;
use Illuminate\Validation\ValidationException;
use Tests\TestCase;

class FirebaseIdentityTest extends TestCase
{
    private function token(array $override = [], bool $forged = false): string
    {
        config(['services.firebase.project_id' => 'jailaoi']);
        $key = openssl_pkey_new(['private_key_bits' => 2048, 'private_key_type' => OPENSSL_KEYTYPE_RSA]);
        Cache::put('firebase_signing_certificates', ['test-key' => openssl_pkey_get_details($key)['key']], 60);
        $encode = fn ($v) => rtrim(strtr(base64_encode(json_encode($v)), '+/', '-_'), '=');
        $claims = array_replace(['aud' => 'jailaoi', 'iss' => 'https://securetoken.google.com/jailaoi', 'sub' => 'verified-user',
            'exp' => time() + 300, 'iat' => time() - 5, 'auth_time' => time() - 5], $override);
        $input = $encode(['alg' => 'RS256', 'kid' => 'test-key']) . '.' . $encode($claims);
        openssl_sign($input, $signature, $key, OPENSSL_ALGO_SHA256);
        if ($forged) $signature[0] = chr(ord($signature[0]) ^ 1);
        return $input . '.' . rtrim(strtr(base64_encode($signature), '+/', '-_'), '=');
    }
    public function test_valid_signature_and_project_are_accepted(): void
    {
        $this->assertSame('verified-user', (new FirebaseIdentityVerifier)->verify($this->token())['sub']);
    }
    public function test_modified_signature_is_rejected(): void
    {
        $token = $this->token([], true);
        $this->expectException(ValidationException::class);
        (new FirebaseIdentityVerifier)->verify($token);
    }
    public function test_token_for_another_project_is_rejected(): void
    {
        $token = $this->token(['aud' => 'another-project']);
        $this->expectException(ValidationException::class);
        (new FirebaseIdentityVerifier)->verify($token);
    }
    public function test_expired_identity_is_rejected(): void
    {
        $token = $this->token(['exp' => time() - 1]);
        $this->expectException(ValidationException::class);
        (new FirebaseIdentityVerifier)->verify($token);
    }
}
