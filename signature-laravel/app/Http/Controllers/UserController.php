<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {
        $query = User::with(['roles', 'creator']);
        
        // If user is not Admin or Super Admin, show only users they created
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            $query->where('created_by', auth()->id());
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('phone', 'like', '%' . $search . '%');
            });
        }
        
        $sort = $request->get('sort', 'date_desc');
        match ($sort) {
            'name_asc' => $query->orderBy('name', 'asc'),
            'name_desc' => $query->orderBy('name', 'desc'),
            'date_asc' => $query->orderBy('created_at', 'asc'),
            default => $query->orderBy('created_at', 'desc'),
        };
        
        $users = $query->paginate(10)->withQueryString();
        
        // Get roles that current user's role can assign
        $currentUserRole = auth()->user()->roles->first();
        $assignableRoleIds = [];
        
        if ($currentUserRole && $currentUserRole->assignable_roles) {
            $assignableRoleIds = json_decode($currentUserRole->assignable_roles, true) ?? [];
        }
        
        // If Admin/Super Admin or no restrictions, show all roles
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || empty($assignableRoleIds)) {
            $roles = Role::where('name', '!=', 'Super Admin')->orderBy('name')->get();
        } else {
            // Show only assignable roles
            $roles = Role::whereIn('id', $assignableRoleIds)->orderBy('name')->get();
        }
        
        return view('users.index', compact('users', 'roles'));
    }

    /**
     * Assign role to user
     */
    public function assignRole(Request $request, User $user)
    {
        $request->validate([
            'role' => 'required|exists:roles,name'
        ]);

        // Remove all existing roles and assign new one
        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Role assigned successfully.');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // Get roles that current user's role can assign
        $currentUserRole = auth()->user()->roles->first();
        $assignableRoleIds = [];
        
        if ($currentUserRole && $currentUserRole->assignable_roles) {
            $assignableRoleIds = json_decode($currentUserRole->assignable_roles, true) ?? [];
        }
        
        // Validate role assignment
        $roleValidation = 'required|exists:roles,name';
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !empty($assignableRoleIds)) {
            $allowedRoleNames = Role::whereIn('id', $assignableRoleIds)->pluck('name')->toArray();
            $roleValidation .= '|in:' . implode(',', $allowedRoleNames);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone',
            'password' => 'required|string|min:8|confirmed',
            'role' => $roleValidation
        ]);

        $user = User::create([
            'name' => $request->name,
            'phone' => $request->phone,
            'password' => bcrypt($request->password),
            'created_by' => auth()->id(),
        ]);

        $user->assignRole($request->role);

        return redirect()->route('users.index')
            ->with('success', 'Team member added successfully.');
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
    public function edit(User $user)
    {
        // Check if user can edit this user (must be Admin/Super Admin or the creator)
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && $user->created_by !== auth()->id()) {
            abort(403, 'You can only edit users you created.');
        }
        
        // Get roles that current user's role can assign
        $currentUserRole = auth()->user()->roles->first();
        $assignableRoleIds = [];
        
        if ($currentUserRole && $currentUserRole->assignable_roles) {
            $assignableRoleIds = json_decode($currentUserRole->assignable_roles, true) ?? [];
        }
        
        // If Admin/Super Admin or no restrictions, show all roles
        if (auth()->user()->hasAnyRole(['Admin', 'Super Admin']) || empty($assignableRoleIds)) {
            $roles = Role::where('name', '!=', 'Super Admin')->orderBy('name')->get();
        } else {
            // Show only assignable roles
            $roles = Role::whereIn('id', $assignableRoleIds)->orderBy('name')->get();
        }
        
        return view('users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // Check if user can update this user (must be Admin/Super Admin or the creator)
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && $user->created_by !== auth()->id()) {
            abort(403, 'You can only update users you created.');
        }
        
        // Get roles that current user's role can assign
        $currentUserRole = auth()->user()->roles->first();
        $assignableRoleIds = [];
        
        if ($currentUserRole && $currentUserRole->assignable_roles) {
            $assignableRoleIds = json_decode($currentUserRole->assignable_roles, true) ?? [];
        }
        
        // Validate role assignment
        $roleValidation = 'required|exists:roles,name';
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && !empty($assignableRoleIds)) {
            $allowedRoleNames = Role::whereIn('id', $assignableRoleIds)->pluck('name')->toArray();
            $roleValidation .= '|in:' . implode(',', $allowedRoleNames);
        }
        
        $request->validate([
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20|unique:users,phone,' . $user->id,
            'password' => 'nullable|string|min:8|confirmed',
            'role' => $roleValidation
        ]);

        $user->update([
            'name' => $request->name,
            'phone' => $request->phone,
        ]);

        if ($request->filled('password')) {
            $user->update([
                'password' => bcrypt($request->password),
            ]);
        }

        $user->syncRoles([$request->role]);

        return redirect()->route('users.index')
            ->with('success', 'Team member updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(User $user)
    {
        // Prevent deletion of Super Admin user
        if ($user->hasRole('Super Admin')) {
            return redirect()->route('users.index')
                ->with('error', 'Cannot delete Super Admin user.');
        }
        
        // Check if user can delete this user (must be Admin/Super Admin or the creator)
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin']) && $user->created_by !== auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You can only delete users you created.');
        }

        $user->delete();

        return redirect()->route('users.index')
            ->with('success', 'Team member deleted successfully.');
    }

    /**
     * Toggle user active/deactive status. Only Admin and Super Admin can do this.
     */
    public function toggleStatus(User $user)
    {
        if (!auth()->user()->hasAnyRole(['Admin', 'Super Admin'])) {
            abort(403, 'Only Admin or Super Admin can change user status.');
        }

        if ($user->id === auth()->id()) {
            return redirect()->route('users.index')
                ->with('error', 'You cannot deactivate your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->is_active ? 'activated' : 'deactivated';

        return redirect()->route('users.index')
            ->with('success', "User {$status} successfully.");
    }
}
