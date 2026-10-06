<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
return new class extends Migration {
    public function up(): void {
        Schema::create('tbl_revenue_entries', function(Blueprint $t) {
            $t->id(); $t->string('month',7)->index(); $t->string('kind',30); $t->bigInteger('amount_cents');
            $t->string('reference',190)->unique(); $t->text('note'); $t->unsignedBigInteger('created_by'); $t->timestamps();
        });
        Schema::create('tbl_revenue_reviews', function(Blueprint $t) {
            $t->id(); $t->string('month',7)->unique(); $t->string('status',20)->default('review');
            $t->longText('snapshot'); $t->string('fingerprint',64); $t->unsignedBigInteger('prepared_by')->nullable();
            $t->unsignedBigInteger('approved_by')->nullable(); $t->timestamp('approved_at')->nullable(); $t->timestamps();
        });
        Schema::create('tbl_artist_statements', function(Blueprint $t) {
            $t->id(); $t->unsignedBigInteger('artist_id'); $t->string('month',7); $t->unsignedBigInteger('stream_credits');
            $t->bigInteger('amount_cents'); $t->unsignedBigInteger('review_id'); $t->timestamps(); $t->unique(['artist_id','month']);
        });
    }
    public function down(): void {
        Schema::dropIfExists('tbl_artist_statements'); Schema::dropIfExists('tbl_revenue_reviews'); Schema::dropIfExists('tbl_revenue_entries');
    }
};
