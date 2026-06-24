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
        $permissionGroups = RoleMiddleware::getPermissionGroups();
        $rolePermissionsMap = $this->buildRolePermissionsMap($roles, $permissionGroups);
        return view('admin.admin.add', compact('roles', 'permissionGroups', 'rolePermissionsMap'));
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

        $permissions = $this->resolvePermissions($request->role, $request->input('permissions', []));

        Admin::create([
            'user_name' => $request->user_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'permissions' => $permissions,
            'status' => $request->has('status') ? 1 : 0,
        ]);

        return redirect()->route('admin.index')->with('success', __('label.admin_created_successfully'));
    }

    public function edit($id)
    {
        $admin = Admin::findOrFail($id);
        $roles = RoleMiddleware::getRoleLabels();
        $permissionGroups = RoleMiddleware::getPermissionGroups();
        $rolePermissionsMap = $this->buildRolePermissionsMap($roles, $permissionGroups);
        $savedPermissionLabels = $this->getSavedPermissionLabels($admin);
        return view('admin.admin.edit', compact('admin', 'roles', 'permissionGroups', 'rolePermissionsMap', 'savedPermissionLabels'));
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

        $permissions = $this->resolvePermissions($request->role, $request->input('permissions', []));

        $data = [
            'user_name' => $request->user_name,
            'email' => $request->email,
            'role' => $request->role,
            'permissions' => $permissions,
            'status' => $request->has('status') ? 1 : 0,
        ];

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $admin->update($data);

        return redirect()->route('admin.index')->with('success', __('label.admin_updated_successfully'));
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
        return redirect()->route('admin.index')->with('success', __('label.admin_deleted_successfully'));
    }

    public function changeStatus($id)
    {
        $admin = Admin::findOrFail($id);
        if ($admin->isSuperAdmin()) {
            return redirect()->back()->with('error', 'Cannot deactivate a Super Admin.');
        }
        $admin->status = $admin->status ? 0 : 1;
        $admin->save();
        return redirect()->route('admin.index')->with('success', __('label.status_updated_successfully'));
    }

    private function buildRolePermissionsMap(array $roles, array $permissionGroups): array
    {
        $map = [];
        foreach (array_keys($roles) as $role) {
            $map[$role] = [];
            foreach ($permissionGroups as $group) {
                foreach ($group as $item) {
                    $map[$role][] = [
                        'label' => $item['label'],
                        'checked' => RoleMiddleware::getRolePatternsForLabel($role, $item['label']),
                    ];
                }
            }
        }
        return $map;
    }

    private function getSavedPermissionLabels(Admin $admin): array
    {
        if (!$admin->permissions || !is_array($admin->permissions)) {
            return [];
        }
        $savedPatterns = $admin->permissions;
        $labels = [];
        $groups = RoleMiddleware::getPermissionGroups();
        foreach ($groups as $group) {
            foreach ($group as $item) {
                $hasAll = true;
                foreach ($item['routes'] as $route) {
                    if (!in_array($route, $savedPatterns)) {
                        $hasAll = false;
                        break;
                    }
                }
                if ($hasAll) {
                    $labels[] = $item['label'];
                }
            }
        }
        return $labels;
    }

    private function resolvePermissions(string $role, array $checkedLabels): ?array
    {
        $roleDefaults = RoleMiddleware::getDefaultPatterns($role);

        if ($role === 'super_admin') {
            return null;
        }

        $patterns = [];
        $groups = RoleMiddleware::getPermissionGroups();
        foreach ($groups as $group) {
            foreach ($group as $item) {
                if (in_array($item['label'], $checkedLabels)) {
                    foreach ($item['routes'] as $route) {
                        $patterns[] = $route;
                    }
                }
            }
        }

        if (empty($patterns)) {
            return null;
        }

        $patterns = array_values(array_unique($patterns));
        sort($patterns);

        $sortedDefaults = $roleDefaults;
        sort($sortedDefaults);

        if ($patterns === $sortedDefaults) {
            return null;
        }

        return $patterns;
    }
}
