<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    public function role_list()
    {
        $roles = Role::with('permissions')->get();
        return view('admin.roles.list', compact('roles'));
    }

    public function role_edit($id)
    {
        $role = Role::findOrFail($id);

        // Sirf wahi permissions dikhao jinka guard role ke guard se match karta hai
        $permissions = Permission::where('guard_name', $role->guard_name)->get();
        $rolePermissions = $role->permissions->pluck('id')->toArray();

        return view('admin.roles.edit', compact('role', 'permissions', 'rolePermissions'));
    }

    public function role_update(Request $request)
    {
        $request->validate([
            'id'            => 'required|exists:roles,id',
            'permissions'   => 'nullable|array',
            'permissions.*' => 'string',
        ]);

        $role  = Role::findOrFail($request->id);
        $input = $request->permissions ?? [];

        // Form se permission name aata hai (id bhi chalega).
        // Guard mismatch (GuardDoesNotMatch) se bachne ke liye role ke guard se filter.
        $permissions = Permission::where('guard_name', $role->guard_name)
            ->where(function ($q) use ($input) {
                $q->whereIn('name', $input)
                    ->orWhereIn('id', array_filter($input, 'is_numeric'));
            })
            ->get();

        $role->syncPermissions($permissions);

        return response()->json([
            'status'  => true,
            'message' => 'Role updated successfully',
        ]);
    }
}
