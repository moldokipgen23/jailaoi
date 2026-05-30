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

                $data = Artist::with('user');

                $input_search = $request['input_search'];
                if ($input_search != null && isset($input_search)) {
                    $data = $data->where('name', 'LIKE', "%{$input_search}%");
                }

                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('image', function ($row) {
                        return $this->common->getImage($this->folder, $row->image, 1);
                    })
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
                        $btn = '<div class="d-flex justify-content-around" title="Edit">';
                        $imageUrl = $this->common->getImage($this->folder, $row->image, 1);
                        $btn .= '<a class="edit-delete-btn edit_artist" title="Edit" data-toggle="modal" href="#EditModel" data-id="' . $row->id . '" data-name="' . $row->name . '" data-image="' . $imageUrl . '" data-bio="' . $row->bio . '">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= '</div>';
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
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $artist = new Artist();
            $artist->name = $request->name;
            $artist->bio = $request->bio;
            $artist->image = $this->common->saveImage($request->file('image'), $this->folder);
            $artist->save();

            return response()->json(array('status' => 200, 'success' => 'Artist created successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function update(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id' => 'required|numeric',
                'name' => 'required|min:2',
                'bio' => 'required|min:2',
                'image' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                return response()->json(array('status' => 400, 'errors' => $validator->errors()->all()));
            }

            $artist = Artist::find($request->id);
            if (!$artist) {
                return response()->json(array('status' => 400, 'errors' => 'Artist not found'));
            }

            $artist->name = $request->name;
            $artist->bio = $request->bio;
            if ($request->hasFile('image')) {
                $artist->image = $this->common->saveImage($request->file('image'), $this->folder);
            }
            $artist->save();

            return response()->json(array('status' => 200, 'success' => 'Artist updated successfully'));
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }

    public function destroy($id)
    {
        try {
            $artist = Artist::find($id);
            if (!$artist) {
                return response()->json(array('status' => 400, 'errors' => 'Artist not found'));
            }
            $artist->delete();
            return redirect()->back()->with('success', 'Artist deleted successfully');
        } catch (Exception $e) {
            return response()->json(array('status' => 400, 'errors' => $e->getMessage()));
        }
    }
}
