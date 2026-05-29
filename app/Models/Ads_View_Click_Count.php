<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ads_View_Click_Count extends Model
{
    use HasFactory;

    protected $table = 'tbl_ads_view_click_count';
    protected $guarded = array();

    protected $casts = [
        'id' => 'integer',
        'ads_type' => 'integer',
        'ads_id' => 'integer',
        'device_type' => 'integer',
        'device_token' => 'string',
        'content_id' => 'integer',
        'type' => 'integer',
        'total_coin' => 'integer',
        'admin_commission' => 'integer',
        'user_wallet_earning' => 'integer',
        'status' => 'integer',
    ];

    public function ads()
    {
        return $this->belongsTo(Ads::class, 'ads_id');
    }
}
