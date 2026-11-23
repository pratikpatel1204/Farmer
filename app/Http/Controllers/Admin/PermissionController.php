<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class PermissionController extends Controller
{
    /**
     * Keep this in sync with the groups used on the Edit Role page.
     */
    protected function permissionGroups(): array
    {
        return [
            'Dashboard',
            'Master Setting',
            'Member Management',
            'Network Management',
            'Notifications Management',
            'Policy & Terms',
            'Product Management',
            'Profile Management',
            'Inquiries Management',
            'Surveys Management',
            'Roles & Permissions',
        ];
    }

    /**
     * Allowed guards (config/auth.php ke guards ke naam).
     */
    protected function guards(): array
    {
        return ['admin', 'web'];
    }

    public function permissions_list()
    {
        $permissions = Permission::orderBy('group_name')->orderBy('id')->get();
        return view('admin.permissions.list', compact('permissions'));
    }

    public function permissions_create()
    {
        $permissionGroups = $this->permissionGroups();
        $guards = $this->guards();
        return view('admin.permissions.create', compact('permissionGroups', 'guards'));
    }

    public function permissions_store(Request $request)
    {
        $request->validate([
            'name'       => [
                'required',
                'string',
                'max:255',
                // Same name alag guard me allowed hai, same guard me nahi
                Rule::unique('permissions', 'name')
                    ->where('guard_name', $request->input('guard_name', 'admin')),
            ],
            'group_name' => 'required|string|max:255',
            'guard_name' => ['nullable', Rule::in($this->guards())],
        ]);

        Permission::create([
            'name'       => trim($request->name),
            'group_name' => $request->group_name,
            'guard_name' => $request->input('guard_name', 'admin'),
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Permission created successfully',
        ]);
    }

    public function permissions_edit($id)
    {
        $permission = Permission::findOrFail($id);
        $permissionGroups = $this->permissionGroups();
        return view('admin.permissions.edit', compact('permission', 'permissionGroups'));
    }

    public function permissions_update(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:permissions,id',
        ]);

        $permission = Permission::findOrFail($request->id);

        $request->validate([
            'name'       => [
                'required',
                'string',
                'max:255',
                Rule::unique('permissions', 'name')
                    ->where('guard_name', $permission->guard_name)
                    ->ignore($permission->id),
            ],
            'group_name' => 'required|string|max:255',
        ]);

        // guard_name update nahi kar rahe: badalne se roles ke saath mismatch ho jata hai
        $permission->update([
            'name'       => trim($request->name),
            'group_name' => $request->group_name,
        ]);

        return response()->json([
            'status'  => true,
            'message' => 'Permission updated successfully',
        ]);
    }
}
