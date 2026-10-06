<?php

namespace Tests\Feature;

use App\Http\Middleware\ApiAuthentication;
use App\Models\User;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ApiIdentityTest extends TestCase
{
    protected function setUp(): void
    {
        parent::setUp();
        config(['database.default' => 'sqlite', 'database.connections.sqlite.database' => ':memory:']);
        Schema::create('tbl_user', function (Blueprint $table) {
            $table->id(); $table->integer('status'); $table->string('email')->nullable(); $table->string('password')->nullable();
            $table->integer('device_type')->nullable(); $table->string('device_token')->nullable(); $table->timestamps();
        });
        (require database_path('migrations/2019_12_14_000001_create_personal_access_tokens_table.php'))->up();
    }

    public function test_password_login_issues_a_real_session_token(): void
    {
        $user = User::create(['status' => 1, 'email' => 'listener@example.com', 'password' => \Illuminate\Support\Facades\Hash::make('valid-password')]);
        $this->app->bind(\App\Http\Controllers\Api\UserController::class, function () {
            $controller = new \App\Http\Controllers\Api\UserController;
            $controller->common = \Mockery::mock(\App\Models\Common::class)->makePartial();
            $controller->common->shouldReceive('imageNameToUrl')->andReturn([]);
            $controller->common->shouldReceive('is_any_package_buy')->andReturn(0);
            return $controller;
        });
        $response = $this->postJson('/api/login', ['type' => 4, 'email' => $user->email, 'password' => 'valid-password']);
        $response->assertOk()->assertJsonPath('status', 200);
        $token = \Laravel\Sanctum\PersonalAccessToken::findToken($response->json('token'));
        $this->assertSame($user->id, $token->tokenable_id);
        $this->assertTrue($token->can('listener'));
    }

    public function test_social_login_cannot_claim_an_email_without_identity_proof(): void
    {
        $this->postJson('/api/login', ['type' => 2, 'email' => 'listener@example.com'])->assertStatus(422);
    }

    public function test_sign_in_can_replace_an_expired_session(): void
    {
        $request = Request::create('/api/login', 'POST', ['user_id' => 987]);
        $request->headers->set('Authorization', 'Bearer expired-session');
        $response = (new ApiAuthentication)->handle($request, fn ($r) => response()->json(['id' => $r->user_id]));
        $this->assertSame(0, $response->getData(true)['id']);
    }

    public function test_guests_cannot_impersonate_an_account(): void
    {
        $this->postJson('/api/get_profile', ['user_id' => 123])->assertStatus(401);
        $this->postJson('/api/generate_portal_token', ['user_id' => 123])->assertStatus(401);
    }

    public function test_verified_token_overrides_client_user_id(): void
    {
        $user = User::create(['status' => 1]);
        $token = $user->createToken('test', ['listener'], now()->addMinute())->plainTextToken;
        $request = Request::create('/api/get_profile', 'POST', ['user_id' => 987]);
        $request->headers->set('Authorization', 'Bearer ' . $token);
        $response = (new ApiAuthentication)->handle($request, fn ($r) => response()->json(['id' => $r->user_id]));
        $this->assertSame($user->id, $response->getData(true)['id']);
    }

    public function test_expired_and_disabled_tokens_are_rejected(): void
    {
        $user = User::create(['status' => 1]);
        $token = $user->createToken('expired', ['listener'], now()->subMinute())->plainTextToken;
        $this->withToken($token)->postJson('/api/get_profile')->assertStatus(401);
        $token = $user->createToken('disabled', ['listener'], now()->addMinute())->plainTextToken;
        $user->update(['status' => 0]);
        $this->withToken($token)->postJson('/api/get_profile')->assertStatus(401);
    }

    public function test_logout_revokes_session(): void
    {
        $user = User::create(['status' => 1]);
        $token = $user->createToken('test', ['listener'], now()->addMinute())->plainTextToken;
        $this->withToken($token)->postJson('/api/logout')->assertOk();
        $this->withToken($token)->postJson('/api/get_profile')->assertStatus(401);
    }

    public function test_guest_catalog_request_discards_claimed_identity(): void
    {
        $request = Request::create('/api/get_artist', 'POST', ['user_id' => 987]);
        (new ApiAuthentication)->handle($request, function ($r) {
            $this->assertSame(0, $r->user_id);
            $this->assertNull($r->user());
            return response()->json([]);
        });
    }
    public function test_profile_cannot_claim_someone_elses_sign_in_email(): void
    {
        $user=User::create(['status'=>1,'email'=>'owner@example.com','password'=>\Illuminate\Support\Facades\Hash::make('valid-password')]);
        $token=$user->createToken('listener',['listener'],now()->addMinute())->plainTextToken;
        $this->withToken($token)->postJson('/api/update_profile',['email'=>'someone@example.com'])->assertStatus(422);
        $this->assertSame('owner@example.com',$user->fresh()->email);
    }
    public function test_verified_artist_request_overrides_impersonated_user_id(): void
    {
        $user=User::create(['status'=>1]);$token=$user->createToken('listener',['listener'],now()->addMinute())->plainTextToken;
        $request=Request::create('/api/generate_portal_token','POST',['user_id'=>987]);$request->headers->set('Authorization','Bearer '.$token);
        $response=(new ApiAuthentication)->handle($request,fn($r)=>response()->json(['id'=>$r->user_id]));
        $this->assertSame($user->id,$response->getData(true)['id']);
    }

    public function test_verified_phone_cannot_take_over_a_password_account_with_an_unverified_phone_field(): void
    {
        Schema::table('tbl_user',function(Blueprint $t){$t->integer('type')->nullable();$t->string('country_code')->nullable();$t->string('mobile_number')->nullable();});
        User::create(['status'=>1,'type'=>4,'country_code'=>'+91','mobile_number'=>'9999999999','email'=>'owner@example.com']);
        $verifier=\Mockery::mock(\App\Services\FirebaseIdentityVerifier::class);
        $verifier->shouldReceive('verify')->andReturn(['firebase'=>['sign_in_provider'=>'phone'],'phone_number'=>'+919999999999']);
        $this->app->instance(\App\Services\FirebaseIdentityVerifier::class,$verifier);
        $this->postJson('/api/login',['type'=>1,'country_code'=>'+91','mobile_number'=>'9999999999','country_name'=>'India','identity_token'=>'verified-test-proof'])->assertForbidden();
        $this->assertSame(0,\Laravel\Sanctum\PersonalAccessToken::count());
    }

}
