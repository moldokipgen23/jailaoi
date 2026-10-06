<?php
namespace Tests\Feature;
use Tests\TestCase;
use App\Services\ArtistPayoutDetails;
use App\Services\KycDocuments;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Illuminate\Validation\ValidationException;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use App\Models\Music;
use App\Models\Content;
class ArtistWebRepairTest extends TestCase
{
    public function test_payout_details_are_method_validated_and_extra_fields_removed(): void
    {
        $details=(new ArtistPayoutDetails)->validate('upi',['upi_id'=>'artist@bank','account_name'=>'Artist','injected'=>'evil']);
        $this->assertSame(['upi_id'=>'artist@bank','account_name'=>'Artist'],$details);
        $this->expectException(ValidationException::class);
        (new ArtistPayoutDetails)->validate('bank','{"account_number":"not-an-account"}');
    }
    public function test_identity_documents_are_private_and_path_traversal_is_rejected(): void
    {
        $disk=\Mockery::mock(\Illuminate\Contracts\Filesystem\Filesystem::class);
        Storage::shouldReceive('disk')->with('local')->andReturn($disk);
        $disk->shouldReceive('putFileAs')->once()->withArgs(fn($path,$file,$name,$options)=>$path==='private/kyc')->andReturn('private/kyc/test-proof.jpg');
        $name=(new KycDocuments)->store(new class(__FILE__, 'proof.jpg', 'image/jpeg', null, true) extends UploadedFile { public function hashName($path = null) { return 'test-proof.jpg'; } });
        $this->assertSame('test-proof.jpg',$name);
        $disk->shouldReceive('exists')->with('private/kyc/'.$name)->andReturn(true);
        $disk->shouldReceive('path')->with('private/kyc/'.$name)->andReturn(__FILE__);
        $response=(new KycDocuments)->response($name);
        $this->assertStringContainsString('no-store',$response->headers->get('Cache-Control'));
        $this->expectException(\Symfony\Component\HttpKernel\Exception\HttpException::class);
        (new KycDocuments)->response('../proof.jpg');
    }
    public function test_music_edit_preserves_plays_premium_and_moderation(): void
    {
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        Schema::create('tbl_artist',function(Blueprint $t){$t->id();$t->integer('user_id');$t->timestamps();});
        Schema::create('tbl_music',function(Blueprint $t){
            $t->id();$t->integer('jailaoi_content_id')->unique();
            foreach(['title','artist_id','album_name','music','lyrics','description','portrait_img','landscape_img','ogtag_img'] as $c)$t->string($c)->default('');
            foreach(['category_id','language_id','duration','upload_type','is_premium','status','total_play'] as $c)$t->integer($c)->default(0);$t->timestamps();
        });
        \App\Models\Artist::create(['user_id'=>77]);
        $user=new \App\Models\User;$user->id=77;
        auth()->guard('user')->setUser($user);
        Music::create(['jailaoi_content_id'=>22,'total_play'=>99,'is_premium'=>1,'status'=>0]);
        $content=new Content(['id'=>22,'title'=>'Edited','content'=>'audio.mp3','landscape_img'=>'wide.jpg','status'=>1]);
        $controller=new \App\Http\Controllers\User\MusicController;
        $method=new \ReflectionMethod($controller,'mirrorToMusic');$method->setAccessible(true);
        $method->invoke($controller,$content,['content_upload_type'=>'server_video']);
        $music=Music::first();
        $this->assertSame(99,$music->total_play);$this->assertSame(1,$music->is_premium);
        $this->assertSame(0,$music->status);$this->assertSame('wide.jpg',$music->landscape_img);
        $this->assertSame('Edited',$music->title);
    }
    public function test_withdrawal_ignores_forged_destination_and_uses_approved_kyc(): void
    {
        config(['database.default'=>'sqlite','database.connections.sqlite.database'=>':memory:']);
        Schema::create('tbl_artist',function(Blueprint $t){$t->id();$t->integer('user_id');$t->decimal('wallet_balance',18,4);$t->timestamps();});
        Schema::create('tbl_artist_kyc',function(Blueprint $t){$t->id();$t->integer('user_id');$t->integer('artist_id');$t->string('status');$t->string('payment_method');$t->text('payment_details');$t->timestamps();});
        Schema::create('tbl_monetization_applications',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->string('status');$t->timestamps();});
        Schema::create('tbl_artist_earnings',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->decimal('amount',18,6);$t->string('settled_month')->nullable();$t->timestamps();});
        Schema::create('tbl_general_setting',function(Blueprint $t){$t->id();$t->string('key');$t->string('value');});
        Schema::create('tbl_withdrawal_requests',function(Blueprint $t){$t->id();$t->integer('artist_id');$t->integer('user_id');$t->decimal('amount',18,4);$t->string('payment_method');$t->text('payment_details');$t->string('status');$t->timestamps();});
        $artist=\App\Models\Artist::create(['user_id'=>77,'wallet_balance'=>500]);
        $user=new \App\Models\User;$user->id=77;auth()->guard('user')->setUser($user);
        \Illuminate\Support\Facades\DB::table('tbl_general_setting')->insert(['key'=>'min_streams_for_payout','value'=>'0']);
        \App\Models\ArtistEarning::create(['artist_id'=>$artist->id,'amount'=>300,'settled_month'=>'2026-09']);
        \App\Models\MonetizationApplication::create(['artist_id'=>$artist->id,'status'=>'approved']);
        \App\Models\ArtistKyc::create(['artist_id'=>$artist->id,'user_id'=>77,'status'=>'approved','payment_method'=>'upi','payment_details'=>json_encode(['upi_id'=>'approved@bank','account_name'=>'Artist'])]);
        $request=\Illuminate\Http\Request::create('/user/earnings/withdraw','POST',['amount'=>'200.00','payment_method'=>'bank','payment_details'=>'attacker account']);
        $result=(new \App\Http\Controllers\User\EarningsController)->requestWithdrawal($request);
        $this->assertSame(200,$result->getData(true)['status'],json_encode($result->getData(true)));
        $withdrawal=\App\Models\WithdrawalRequest::first();
        $this->assertSame('upi',$withdrawal->payment_method);
        $this->assertStringContainsString('approved@bank',$withdrawal->payment_details);
        $this->assertSame(300.0,(float)$artist->fresh()->wallet_balance);
    }

}
