<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Model;
class YouTubeAudioImport extends Model
{
    protected $table = 'artist_youtube_imports';
    public $incrementing = false;
    protected $keyType = 'string';
    protected $guarded = [];
    protected $casts = ['expires_at'=>'datetime', 'rights_confirmed_at'=>'datetime', 'duration_seconds'=>'integer'];
}
