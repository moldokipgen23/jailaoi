<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Album;
use App\Models\Common;
use App\Models\User;
use App\Models\Content;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class AlbumController extends Controller
{
    private $folder = "content";
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
                $data = Album::with('user');
                $input_search = $request['input_search'];
                if ($input_search) {
                    $data = $data->where('name', 'LIKE', "%{$input_search}%");
                }
                return DataTables()::of($data)
                    ->addIndexColumn()
                    ->addColumn('cover_image', function ($row) {
                        return $this->common->getImage($this->folder, $row->cover_image, $row->cover_image_storage_type ?? 1);
                    })
                    ->addColumn('artist_name', function ($row) {
                        return $row->user ? $row->user->channel_name : '—';
                    })
                    ->addColumn('song_count', function ($row) {
                        return Content::where('album_id', $row->id)->where('content_type', 2)->count();
                    })
                    ->addColumn('status_badge', function ($row) {
                        return $row->status ? '<span class="badge badge-success">Show</span>' : '<span class="badge badge-danger">Hide</span>';
                    })
                    ->addColumn('action', function ($row) {
                        $btn = '<div class="d-flex justify-content-around">';
                        $btn .= '<a class="edit-delete-btn" title="Edit" href="' . route('admin.album.edit', $row->id) . '">';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i></a> ';
                        $btn .= '<a class="edit-delete-btn" title="Delete" onclick="delete_album(' . $row->id . ')">';
                        $btn .= '<i class="fa-solid fa-trash-can fa-xl danger-color"></i></a></div>';
                        return $btn;
                    })
                    ->rawColumns(['cover_image', 'status_badge', 'action'])
                    ->make(true);
            }
            return view('admin.album.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function edit($id)
    {
        try {
            $album = Album::with('user')->findOrFail($id);
            $album->cover_image = $this->common->getImage($this->folder, $album->cover_image, $album->cover_image_storage_type ?? 1);
            $params['data'] = $album;
            return view('admin.album.edit', $params);
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        try {
            $album = Album::findOrFail($request['id']);
            $album->name = $request['name'] ?? $album->name;
            $album->description = $request['description'] ?? $album->description;
            if ($request->has('status')) {
                $album->status = $request['status'];
            }
            if ($request->hasFile('cover_image')) {
                $album->cover_image = $this->common->saveImage($request->file('cover_image'), $this->folder, 'album_', 1);
                $album->cover_image_storage_type = 1;
            }
            $album->save();
            return response()->json(['status' => 200, 'success' => 'Album Updated Successfully.']);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }

    public function show($id)
    {
        try {
            $album = Album::findOrFail($id);
            Content::where('album_id', $album->id)->update(['album_id' => null]);
            $album->delete();
            return redirect()->route('admin.album.index')->with('success', 'Album Deleted Successfully.');
        } catch (Exception $e) {
            return redirect()->back()->with('error', $e->getMessage());
        }
    }
}
