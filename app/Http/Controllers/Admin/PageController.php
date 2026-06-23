<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\Common;
use App\Models\General_Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Exception;

class PageController extends Controller
{
    private $folder_app = "images/app";
    public $common;
    public function __construct()
    {
        $this->common = new Common;
    }

    public function index(Request $request)
    {
        try {

            $params['data'] = [];
            $params['setting_data'] = Setting_Data();

            if ($request->ajax()) {

                $input_search = $request['input_search'];
                if (!empty($input_search)) {
                    $data = Page::where('title', 'LIKE', "%{$input_search}%")->latest()->get();
                } else {
                    $data = Page::latest()->get();
                }

                $this->common->imageNameToUrl($data, 'icon', $this->folder_app);

                return DataTables()::of($data)
                    ->addIndexColumn()
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

                        $delete = '<form onsubmit="return confirm(\'' . __('label.delete_page') . '\');" method="POST"  action="' . route('page.destroy', [$row->id]) . '">
                                <input type="hidden" name="_token" value="' . csrf_token() . '">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="edit-delete-btn"  title=' . __('label.delete') . ' ><i class="fa-solid fa-trash-can fa-xl"></i></button></form>';

                        $btn = '<div class="d-flex justify-content-center">';
                        $btn .= '<a href="' . route('page.edit', [$row->id]) . '" class="edit-delete-btn mr-4" title=' . __('label.edit') . '>';
                        $btn .= '<i class="fa-solid fa-pen-to-square fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= '<a href="' . route('admin.pages', $row->title) . '" class="edit-delete-btn mr-4" target="_blank" title="View">';
                        $btn .= '<i class="fa-regular fa-eye fa-xl"></i>';
                        $btn .= '</a>';
                        $btn .= $delete;
                        $btn .= '</a></div>';
                        return $btn;
                    })
                    ->rawColumns(['status', 'action'])
                    ->make(true);
            }
            return view('admin.page.index', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function create()
    {
        try {

            $params['settings'] = Setting_Data();
            return view('admin.page.add', $params);
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function store(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required|min:2',
                'description' => 'required',
                'icon' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(['status' => 400, 'errors' => $errs]);
            }

            $requestData = $request->all();
            if (isset($requestData['icon'])) {
                $files = $requestData['icon'];
                $requestData['icon'] = $this->common->saveImage($files, $this->folder_app, 'page_');
            }

            $page_data = Page::updateOrCreate(['id' => $requestData['id']], $requestData);
            if (isset($page_data->id)) {
                return response()->json(['status' => 200, 'success' => __('label.success_add_page')]);
            } else {
                return response()->json(['status' => 400, 'errors' => __('label.error_add_page')]);
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function edit($id)
    {
        try {

            $params['data'] = Page::where('id', $id)->first();

            if ($params['data'] != null) {

                $params['settings'] = Setting_Data();
                $this->common->imageNameToUrl(array($params['data']), 'icon', $this->folder_app);

                return view('admin.page.edit', $params);
            } else {
                return redirect()->back()->with('error', __('label.page_not_found'));
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function update(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'title' => 'required',
                'description' => 'required',
                'icon' => 'image|mimes:jpeg,png,jpg|max:2048',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $page = Page::where('id', $request->id)->first();

            if (isset($page->id)) {

                $page->title = $request->title;
                $page->description = $request->description;
                $page->status = 1;

                if (isset($request->icon)) {
                    $files = $request->icon;
                    $page->icon = $this->common->saveImage($files, $this->folder_app, "pages_");

                    $this->common->deleteImageToFolder($this->folder_app, basename($request->old_icon));
                }

                if ($page->save()) {
                    return response()->json(['status' => 200, 'success' => __('label.success_edit_page')]);
                } else {
                    return response()->json(['status' => 400, 'errors' => __('label.page_not_updated')]);
                }
            }
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
    public function save_setting(Request $request)
    {
        try {

            $validator = Validator::make($request->all(), [
                'background_color' => 'required',
                'title_color' => 'required',
            ]);
            if ($validator->fails()) {
                $errs = $validator->errors()->all();
                return response()->json(array('status' => 400, 'errors' => $errs));
            }

            $data = $request->all();
            $data["page_background_color"] = isset($data['background_color']) ? $data['background_color'] : '';
            $data["page_title_color"] = isset($data['title_color']) ? $data['title_color'] : '';

            foreach ($data as $key => $value) {
                $setting = General_Setting::where('key', $key)->first();
                if (isset($setting->id)) {
                    $setting->value = $value;
                    $setting->save();
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

            $data = Page::where('id', $id)->first();
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
    public function destroy($id)
    {
        try {

            $data = Page::where('id', $id)->first();

            if ($data) {
                $this->common->deleteImageToFolder($this->folder_app, $data['image']);
                $data->delete();
            }
            return redirect()->route('page.index')->with('success', __('label.success_delete_category'));
        } catch (Exception $e) {
            return response()->json(['status' => 400, 'errors' => $e->getMessage()]);
        }
    }
}
