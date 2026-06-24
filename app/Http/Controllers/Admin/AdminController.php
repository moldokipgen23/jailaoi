<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Middleware\RoleMiddleware;
use App\Models\Admin;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AdminController extends Controller
{
    public function index()
    {
        $admins = Admin::all();
        return view('admin.admin.index', compact('admins'));
    }

    public function create()
    {
        $roles = RoleMiddleware::getRoleLabels();
        return view('admin.admin.add', compact('roles'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:255|unique:tbl_admin,user_name',
            'email' => 'required|email|max:255|unique:tbl_admin,email',
            'password' => 'required|min:6',
            'role' => 'required|in:super_admin,staff,finance,support',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->role === 'super_admin') {
            $existingSuper = Admin::where('role', 'super_admin')->count();
            if ($existingSuper > 0 && !auth('admin')->user()?->isSuperAdmin()) {
                return redirect()->back()->with('error', 'Only a Super Admin can create another Super Admin.');
            }
        }

        Admin::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.admin.index')->with('success', __('label.admin_created_successfully'));
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        $roles = RoleMiddleware::getRoleLabels();
        return view('admin.admin.edit', compact('admin', 'roles'));
    }

    public function update(Request $request, $id)
    {
        $admin = Admin::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'user_name' => 'required|max:255|unique:tbl_admin,user_name,' . $id,
            'email' => 'required|email|max:255|unique:tbl_admin,email,' . $id,
            'password' => 'nullable|min:6',
            'role' => 'required|in:super_admin,staff,finance,support',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        if ($request->role === 'super_admin' && $admin->role !== 'super_admin') {
            $existingSuper = Admin::where('role', 'super_admin')->count();
            if ($existingSuper > 0 && !auth('admin')->user()?->isSuperAdmin()) {
                return redirect()->back()->with('error', 'Only a Super Admin can promote to Super Admin.');
            }
        }

        $data = [
            'user_name' => $request->user_name,
            'email' => $request->email,
            'role' => $request->role,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.admin.index')->with('success', __('label.admin_updated_successfully'));
    }

    public function destroy($id)
    {
        if ($id == auth('admin')->id()) {
            return redirect()->back()->with('error', 'You cannot delete yourself.');
        }

        $admin = Admin::findOrFail($id);
        if ($admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot delete a Super Admin.');
        }

        $admin->delete();
        return redirect()->route('admin.admin.index')->with('success', __('label.admin_deleted_successfully'));
    }

    public function changeStatus($id)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot deactivate a Super Admin.');
        }
        $admin->status = $admin->status ? 0 : 1;
        $admin->save();
        return redirect()->route('admin.admin.index')->with('success', __('label.status_updated_successfully'));
    }
}
