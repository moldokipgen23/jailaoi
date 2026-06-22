<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\ArtistRequest;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Exception;

class ArtistController extends Controller
{
    private $folder = "images/artist";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $params['data'] = [];
            $params['artist'] = Artist::orderBy('id', 'desc')->get();
            if ($request->ajax()) {

                $input_search = $request['input_search'];
                // JAILAOI: eager-load user + registration request for admin detail view
                if ($input_search != null && isset($input_search)) {
                    $data = Artist::with(['user', 'artistRequest'])->where('name', 'LIKE', "%{$input_search}%")->orderBy('id', 'desc')->get();
                } else {
                    $data = Artist::with(['user', 'artistRequest'])->orderBy('id', 'desc')->get();
                }

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('suspend_status', function ($row) {
                        if ($row->is_suspended) {
                            return '<span class="badge" style="background:#fef3c7;color:#92400e;padding:4px 8px;border-radius:4px;font-size:11px;">⚠ Suspended</span>';
                        }
                        return '<span class="badge" style="background:#d1fae5;color:#065f46;padding:4px 8px;border-radius:4px;font-size:11px;">Active</span>';
                    })
                    ->addColumn('status', function ($row) {
                        $status = $row->status == 1 ? "checked" : "";
                        return '<div class="switch">
                                    <input class="status-checkbox" id="checkbox' . $row->id . '" data-id="' . $row->id . '" type="checkbox" ' . $status . '>
                                    <label for="checkbox' . $row->id . '"></label>
                                      <span class="toggle-text"
                                        data-on="' . __('label.show') . '"
                                        data-off="' . __('label.hide') . '"></span>
                                    </div>';
                    })
                    ->addColumn('action', function ($row) {
                        // JAILAOI: include user email + registration fields for admin detail modal
                        $email       = $row->user?->email ?? '';
                        $fullName    = $row->user?->full_name ?? '';
                        $phone       = $row->user?->mobile_number ?? '';
                        $artistTypes = $row->artistRequest?->artist_types ?? '';
                        $regDate     = $row->artistRequest?->created_at?->format('Y-m-d') ?? ($row->created_at?->format('Y-m-d') ?? '');
                        $adminNote   = $row->artistRequest?->admin_note ?? '';

                        $delete = '<form onsubmit="return confirm(\'' . __('label.delete_artist') . '\');" method="POST"  action="' . route('artist.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn" title=' . __('label.delete') . ' ><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $isSuspended = $row->is_suspended ?? 0;
                        if ($isSuspended) {
                            $suspendBtn = '<button class="edit-delete-btn unsuspend-btn" title="Reinstate Artist" data-id="' . $row->id . '" style="color:#22c97a;"><i class="fa-solid fa-circle-check fa-xl"></i></button>';
                        } else {
                            $suspendBtn = '<button class="edit-delete-btn suspend-btn" title="Suspend Artist" data-id="' . $row->id . '" data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '" style="color:#f59e0b;"><i class="fa-solid fa-ban fa-xl"></i></button>';
                        }

                        $btn = '<div class="d-flex justify-content-center" style="gap:6px;">';
                        $btn .= '<a class="edit-delete-btn" title="View Detail" href="' . route('artist.detail', [$row->id]) . '" style="color:#3b82f6;">';
                        $btn .= '<i class="fa-solid fa-eye fa-xl"></i></a>';
                        $btn .= $suspendBtn;
                        $btn .= '<a class="edit-delete-btn edit_artist mr-4" title="' . __('label.edit') . '"'
                            . ' data-toggle="modal" href="#EditModel"'
                            . ' data-id="' . $row->id . '"'
                            . ' data-name="' . htmlspecialchars($row->name, ENT_QUOTES) . '"'
                            . ' data-image="' . $row->image . '"'
                            . ' data-bio="' . htmlspecialchars($row->bio ?? '', ENT_QUOTES) . '"'
                            . ' data-type="' . $row->type . '"'
                            . ' data-email="' . htmlspecialchars($email, ENT_QUOTES) . '"'
                            . ' data-fullname="' . htmlspecialchars($fullName, ENT_QUOTES) . '"'
                            . ' data-phone="' . htmlspecialchars($phone, ENT_QUOTES) . '"'
                            . ' data-artist-types="' . htmlspecialchars($artistTypes, ENT_QUOTES) . '"'
                            . ' data-reg-date="' . $regDate . '"'
                            . ' data-admin-note="' . htmlspecialchars($adminNote, ENT_QUOTES) . '"'
                            . '>';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</div>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'status', 'suspend_status'])
                    ->make(true);
            }
            return view('admin.artist.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'bio' => 'required|min:2',
                'type' => 'required',
                'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();
            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, 'artist_');
            }

            $artist_data = Artist::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($artist_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_artist')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.artist_not_added')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'bio' => 'required|min:2',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $requestData = $request->all();

            if (isset($requestData['image'])) {
                $files = $requestData['image'];
                $requestData['image'] = $this->common->saveImage($files, $this->folder, 'artist_');

                $this->common->deleteImageToFolder($this->folder, basename($requestData['old_image']));
            }
            unset($requestData['old_image']);

            $artist_data = Artist::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($artist_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_edit_artist')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.artist_not_updated')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function destroy($id)
    {
        try {

            $data = Artist::where('id', $id)->first();

            if (isset($data)) {
                // Reset the linked user's role so the app no longer shows the artist portal
                if (!empty($data->user_id)) {
                    \App\Models\User::where('id', $data->user_id)->update(['role' => 'user']);
                    // Remove the artist request so get_artist_request_status returns null, not 'approved'
                    ArtistRequest::where('user_id', $data->user_id)->delete();
                }
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                $data->delete();
            }
            return redirect()->route('artist.index')->with('success', __('label.success_delete_artist'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function suspend(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'reason' => 'required|min:5',
            ]);
            if ($validator->fails()) {
                return response()->json(['status' => 400, 'errors' => $validator->errors()->first()]);
            }

            $artist = Artist::find($id);
            if (!$artist) {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }

            $artist->update([
                'is_suspended'  => 1,
                'suspend_reason' => $request->reason,
                'suspended_at'  => now(),
            ]);

            return response()->json(['status' => 200, 'success' => 'Artist suspended successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function unsuspend($id)
    {
        try {
            $artist = Artist::find($id);
            if (!$artist) {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }

            $artist->update([
                'is_suspended'  => 0,
                'suspend_reason' => null,
                'suspended_at'  => null,
            ]);

            return response()->json(['status' => 200, 'success' => 'Artist reinstated successfully']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function ArtistSortableSave(Request $request)
    {
        try {

            $ids = $request['ids'];

            if (isset($ids) && $ids != null && $ids != "") {

                $id_array = explode(',', $ids);
                for ($i = 0; $i < count($id_array); $i++) {
                    Artist::where('id', $id_array[$i])->update(['sort_order' => $i + 1]);
                }
            }
            return response()->json(['status' => 200, 'success' => __('label.data_edit_successfully')]);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function show($id)
    {
        try {

            $data = Artist::where('id', $id)->first();
            if ($data) {
                $data->status = $data->status == 1 ? 0 : 1;
                $data->save();

                return response()->json(['status' => 200, 'success' => __('label.status_changed')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.data_not_found')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function analytics(Request $request)
    {
        try {
            $period   = $request->get('period', '30d');
            $currency = General_Setting::where('key', 'payout_currency')->value('value') ?? 'USD';
            $rate     = (float) (General_Setting::where('key', 'payout_rate_per_stream')->value('value') ?? 0);

            $since = match($period) {
                '7d'  => now()->subDays(7),
                '30d' => now()->subDays(30),
                '90d' => now()->subDays(90),
                '1yr' => now()->subYear(),
                default => null,
            };

            // Use tbl_artist_earnings as the source — it's populated from both add_play
            // and add_user_action, and has artist_id already resolved. amount=0 rows
            // are non-monetized artist plays (recorded for analytics only).
            $query = DB::table('tbl_artist_earnings as ae')
                ->join('tbl_artist as a', 'a.id', '=', 'ae.artist_id')
                ->select(
                    'a.id as artist_id',
                    'a.name as artist_name',
                    'a.image as artist_image',
                    DB::raw('COUNT(ae.id) as play_count')
                )
                ->groupBy('a.id', 'a.name', 'a.image')
                ->orderByDesc('play_count');

            if ($since) {
                $query->where('ae.created_at', '>=', $since);
            }

            $rows = $query->limit(100)->get();

            // Attach monetization status
            $artistIds = $rows->pluck('artist_id')->toArray();
            $monetizationMap = DB::table('tbl_monetization_applications')
                ->whereIn('artist_id', $artistIds)
                ->orderByDesc('id')
                ->get()
                ->keyBy('artist_id');

            $common = new Common;
            $artists = $rows->map(function ($row) use ($rate, $currency, $monetizationMap, $common) {
                $mon    = $monetizationMap[$row->artist_id] ?? null;
                $status = $mon ? $mon->status : 'none';
                return [
                    'id'           => $row->artist_id,
                    'name'         => $row->artist_name,
                    'image'        => $common->Get_Image('artist', $row->artist_image ?? ''),
                    'play_count'   => (int) $row->play_count,
                    'est_earnings' => round($row->play_count * $rate, 2),
                    'mon_status'   => $status,
                ];
            });

            // Summary stats for the period
            $totalPlays    = $artists->sum('play_count');
            $totalEstimate = round($artists->sum('est_earnings'), 2);
            $monetizedCount = $artists->where('mon_status', 'approved')->count();

            return view('admin.artist.analytics', compact(
                'artists', 'period', 'currency', 'rate',
                'totalPlays', 'totalEstimate', 'monetizedCount'
            ));
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }

    public function detail($id)
    {
        try {
            $artist = Artist::with(['artistRequest', 'user'])->findOrFail($id);

            $earnings = \App\Models\ArtistEarning::where('artist_id', $id)->get();
            $totalPlays = $earnings->count();
            $totalEarned = round((float) $earnings->sum('amount'), 2);

            $withdrawals = \App\Models\WithdrawalRequest::where('artist_id', $id)
                ->selectRaw('status, SUM(amount) as total')
                ->groupBy('status')
                ->pluck('total', 'status');

            $paidOut = round((float) ($withdrawals['paid'] ?? 0), 2);
            $pending = round((float) (($withdrawals['pending'] ?? 0) + ($withdrawals['approved'] ?? 0)), 2);
            $available = round(max(0, $totalEarned - $paidOut - $pending), 2);

            $songs = \App\Models\Song::where('artist_id', $id)->count();
            $podcasts = \App\Models\Podcast::where('artist_id', $id)->count();
            $music = \App\Models\Music::where('artist_id', $id)->count();
            $totalTracks = $songs + $podcasts + $music;

            $kyc = \App\Models\ArtistKyc::where('user_id', $artist->user_id)
                ->where('status', 'approved')
                ->latest()
                ->first();

            // JAILAOI: followers are in tbl_subscriber, not a User column
            $followers = \App\Models\Subscriber::where('to_user_id', $artist->user_id)
                ->where('status', 1)
                ->count();

            // JAILAOI: resolve image URLs in controller — don't pass $common to view
            $common = new Common;
            $artistImageUrl = $common->Get_Image('artist', $artist->image ?? '');
            $kycFrontUrl    = $kyc ? $common->Get_Image('kyc', $kyc->id_front_img) : null;
            $kycBackUrl     = $kyc ? $common->Get_Image('kyc', $kyc->id_back_img) : null;

            $monetization = \DB::table('tbl_monetization_applications')
                ->where('artist_id', $id)
                ->orderByDesc('id')
                ->first();

            return view('admin.artist.show', compact(
                'artist', 'totalPlays', 'totalEarned', 'paidOut', 'pending',
                'available', 'totalTracks', 'kyc', 'followers',
                'artistImageUrl', 'kycFrontUrl', 'kycBackUrl', 'monetization'
            ));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
