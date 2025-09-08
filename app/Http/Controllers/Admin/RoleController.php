<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $roles = Role::with('permissions')->get(); // Eager load permissions
        $allPermissions = Permission::all();
        return view('settings.roles.index', compact('roles', 'allPermissions'));
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
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name',
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        // Create the role
        $role = Role::create(['name' => $validated['name'], 'guard_name' => 'web']);

        // Sync the permissions (assign them to the role)
        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Role created successfully.');
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
        $roles = Role::all();
        return view('settings.roles.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Role $role)
    {
        // dd($request->all());
        $validated = $request->validate([
            'name' => 'required|string|unique:roles,name,' . $role->id,
            'permissions' => 'sometimes|array',
            'permissions.*' => 'exists:permissions,name'
        ]);

        $role->update(['name' => $validated['name']]);

        if (isset($validated['permissions'])) {
            $role->syncPermissions($validated['permissions']);
        }

        return redirect()->route('roles.index')->with('success', 'Role updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Role $role)
    {
        // Prevent deletion of admin role or other protected roles
        $protectedRoles = ['admin', 'user', 'editor'];
        
        if (in_array($role->name, $protectedRoles)) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete protected role: ' . $role->name);
        }
        
        // Check if role has users assigned (optional)
        if ($role->users()->count() > 0) {
            return redirect()->route('roles.index')
                ->with('error', 'Cannot delete role that has users assigned. Please reassign users first.');
        }
        
        try {
            DB::transaction(function () use ($role) {
                $role->permissions()->detach();
                $role->delete();
            });
            
            return redirect()->route('roles.index')
                ->with('success', 'Role deleted successfully.');
                
        } catch (\Exception $e) {
            return redirect()->route('roles.index')
            ->with('error', 'Error deleting role: ' . $e->getMessage());   
        }
    }
}
