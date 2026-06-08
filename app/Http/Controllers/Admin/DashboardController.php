<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Song;
use App\Models\User;
use App\Models\Language;
use App\Models\Page;
use App\Models\Package;
use App\Models\Transaction;
use App\Models\City;
use App\Models\Category;
use App\Models\Podcast;
use App\Models\Common;
use App\Models\Event_Join_User;
use App\Models\Live_Event;
use App\Models\Music;
use Exception;
use Illuminate\Support\Facades\URL;

class DashboardController extends Controller
{
    private $folder_song = "radio";
    private $folder_city = "city";
    private $folder_language = "language";
    private $folder_podcast = "podcast";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index()
    {
        try {

            $params['UserCount'] = User::count();
            $params['ArtistCount'] = Artist::count();
            $params['SongCount'] = Song::count();
            $params['MusicCount'] = Music::count();
            $params['LanguageCount'] = Language::count();
            $params['CategoryCount'] = Category::count();
            $params['PodcastCount'] = Podcast::count();
            $params['CurrentMounthCount'] = Transaction::whereMonth('created_at', date('m'))->whereYear('created_at', date('Y'))->sum('price');
            $params['TransactionCount'] = Transaction::sum('price');
            $params['PackageCount'] = Package::count();
            $params['LiveEventCount'] = Live_Event::count();
            $params['LiveEventEarningCount'] = Event_Join_User::sum('price');

            // User Statistice
            $user_year = [];
            $user_month = [];
            $d = date('t', mktime(0, 0, 0, date('m'), 1, date('Y')));

            for ($i = 1; $i < 13; $i++) {
                $Sum = User::whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->count();
                $user_year['sum'][] = (int) $Sum;
            }
            for ($i = 1; $i <= $d; $i++) {

                $Sum = User::whereYear('created_at', date('Y'))->whereMonth('created_at', date('m'))->whereDay('created_at', $i)->count();
                $user_month['sum'][] = (int) $Sum;
            }
            $params['user_year'] = json_encode($user_year);
            $params['user_month'] = json_encode($user_month);

            // Best City
            $params['best_city'] = City::orderBy('id', 'desc')->take(8)->get();
            $this->common->imageNameToUrl($params['best_city'], 'image', $this->folder_city);

            // Most Play Song
            $params['most_play_song'] = Song::orderBy('total_play', 'desc')->latest()->take(6)->get();
            $this->common->imageNameToUrl($params['most_play_song'], 'image', $this->folder_song);

            // package 
            $subscription = Package::where('status', 1)->get();
            $pack_data = [];
            $pack_name = [];

            foreach ($subscription as $row) {
                $pack_name[] = ucfirst($row->name);
                $d = date('t');

                for ($i = 1; $i < 13; $i++) {
                    $sum = Transaction::where('package_id', $row->id)->whereYear('created_at', date('Y'))->whereMonth('created_at', $i)->sum('price');

                    $pack_data[ucfirst($row->name)]['sum'][] = (int)$sum;
                }
            }
            $params['pack_data'] = json_encode($pack_data);
            $params['pack_name'] = json_encode($pack_name);

            // Best Language
            $params['best_language'] = Language::orderBy('id', 'desc')->take(8)->get();
            $this->common->imageNameToUrl($params['best_language'], 'image', $this->folder_language);

            // Most Play Podcast
            $params['most_play_podcasts'] = Podcast::orderBy('total_play', 'desc')->latest()->take(6)->get();
            $this->common->imageNameToUrl($params['most_play_podcasts'], 'portrait_img', $this->folder_podcast);

            return view('admin.dashboard.dashboard', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function Page()
    {
        try {

            $currentURL = URL::current();

            $link_array = explode('/', $currentURL);
            $page = urldecode(end($link_array));

            $params['result'] = Page::where('title', $page)->first();

            $params['settings'] = Setting_Data();

            if (isset($params['result'])) {
                return view('page', $params);
            } else {
                return view('errors.404');
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
