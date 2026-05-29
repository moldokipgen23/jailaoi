<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Artist;
use App\Models\Common;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class ArtistController extends Controller
{
    private $folder = "artist";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {
            $params['data'] = [];
            if ($request->ajax()) {

                $input_search = $request['input_search'];
                if ($input_search != null && isset($input_search)) {
                    $data = Artist::with('user')->where('name', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Artist::with('user')->latest()->get();
                }

                $this->common->imageNameToUrl($data, 'image', $this->folder);

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('linked_user', function ($row) {
                        if ($row->user) {
                            $name = $row->user->full_name ?? '';
                            $email = $row->user->email ?? '';
                            return $name . '<br><small>' . $email . '</small>';
                        }
                        return '<span class="text-muted">Legacy Artist</span>';
                    })
                    ->addColumn('type_badge', function ($row) {
                        if ($row->user_id) {
                            return '<span class="badge badge-info">User Artist</span>';
                        }
                        return '<span class="badge badge-secondary">Legacy</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $delete = '<form onsubmit="return confirm(\'Are you sure !!! You want to Delete this Artist ?\');" method="POST"  action="' . route('artist.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn" style="outline: none;" title="Delete"><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-around" title="Edit">';
                        $btn .= '<a class="edit-delete-btn edit_artist" title="Edit" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-name="' . $row->name . '" data-image="' . $row->image . '" data-bio="' . $row->bio . '">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->rawColumns(['action', 'linked_user', 'type_badge'])
                    ->make(true);
            }
            return view('admin.artist.index', $params);
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|min:2',
                'bio' => 'required|min:2',
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
                return response()->json(array('status' => 200, 'success' => __('Label.data_add_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_added')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
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
                return response()->json(array('status' => 200, 'success' => __('Label.data_edit_successfully')));
            } else {
                return response()->json(array('status' => 400, 'errors' => __('Label.data_not_updated')));
            }
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
    public function destroy($id)
    {
        try {
            $data = Artist::where('id', $id)->first();

            if (isset($data)) {
                $this->common->deleteImageToFolder($this->folder, $data['image']);
                $data->delete();
            }
            return redirect()->route('artist.index')->with('success', __('Label.data_delete_successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
