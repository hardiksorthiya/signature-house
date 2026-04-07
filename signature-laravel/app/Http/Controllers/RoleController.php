<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {
        $permissions = \Spatie\Permission\Models\Permission::all();
        $rolesAll = Role::with('permissions')->orderBy('name')->get();

        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where('name', 'like', "%{$term}%");
        }

        $sort = $request->get('sort', 'name_asc');
        match ($sort) {
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };

        $roles = $query->paginate(10)->withQueryString();

        return view('roles.create', compact('permissions', 'roles', 'rolesAll'));
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'assignable_roles' => 'nullable|array',
            'assignable_roles.*' => 'exists:roles,id',
        ]);

        $role = Role::create([
            'name' => $request->name,
            'assignable_roles' => $request->has('assignable_roles') ? json_encode($request->assignable_roles) : null,
        ]);

        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = $request->permissions;
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();
            $role->givePermissionTo($permissions);
        }

        // Clear permission cache after creation
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.create')
            ->with('success', 'Role created successfully with permissions.');
    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Request $request, Role $role)
    {
        // Clear permission cache to ensure fresh data
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $permissions = \Spatie\Permission\Models\Permission::all();
        $rolesAll = Role::with('permissions')->orderBy('name')->get();

        $query = Role::with('permissions');

        if ($request->filled('search')) {
            $term = $request->search;
            $query->where('name', 'like', "%{$term}%");
        }

        $sort = $request->get('sort', 'name_asc');
        match ($sort) {
            'name_desc' => $query->orderBy('name', 'desc'),
            default => $query->orderBy('name', 'asc'),
        };

        $roles = $query->paginate(10)->withQueryString();

        $role->refresh();
        $role->load('permissions');

        return view('roles.edit', compact('role', 'permissions', 'roles', 'rolesAll'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $role->id,
            'permissions' => 'nullable|array',
            'permissions.*' => 'exists:permissions,id',
            'assignable_roles' => 'nullable|array',
            'assignable_roles.*' => 'exists:roles,id',
        ]);

        $role->update([
            'name' => $request->name,
            'assignable_roles' => $request->has('assignable_roles') ? json_encode($request->assignable_roles) : null,
        ]);

        // Sync permissions
        if ($request->has('permissions') && is_array($request->permissions)) {
            $permissionIds = $request->permissions;
            $permissions = \Spatie\Permission\Models\Permission::whereIn('id', $permissionIds)->get();
            $role->syncPermissions($permissions);
        } else {
            $role->syncPermissions([]);
        }

        // Clear permission cache after update
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        return redirect()->route('roles.edit', $role)
            ->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of Super Admin role
        if ($role->name === 'Super Admin') {
            return redirect()->route('roles.create')
                ->with('error', 'Cannot delete Super Admin role.');
        }

        $role->delete();

        return redirect()->route('roles.create')
            ->with('success', 'Role deleted successfully.');
    }
}
