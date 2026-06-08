<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\Ads;
use App\Models\Content;
use Exception;
use App\Models\Common;
use App\Models\Refer_Earn;
use App\Models\User_Badges_Bonus;

class DashboardController extends Controller
{
    private $folder_user = "user";
    private $folder_content = "content";
    private $folder_badges_bonus = "badges_bonus";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        // JAILAOI: dtradio dashboard replaced — redirect artists to earnings overview
        return redirect()->route('user.earnings.index');
    }
}
