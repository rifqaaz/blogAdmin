<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Validation\Rules\Password;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $users = User::with('roles')->get();
        $roles = Role::all();
        $allEditors = User::role('editor')->get(['id', 'name']);

        if (auth()->user()->hasRole('editor')) {
            $users = User::where('assigned_to_editor_id', auth()->id())->with('roles')->get();
        }
        
        return view('settings.users.index', compact('users', 'roles', 'allEditors'));
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
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'role' => 'required|in:editor,user',
            'assigned_to_editor_id' => 'nullable|exists:users,id',
        ]);

        // $admin = User::role('admin')->first();

        // // Create user
        // $user = User::create([
        //     'name' => $validated['name'],
        //     'email' => strtolower($validated['email']),
        //     'password' => Hash::make($validated['password']),
        //     'assigned_to_editor_id' => ($validated['role'] === 'user') ? $validated['assigned_to_editor_id'] : null,
        //     'assigned_to_admin_id' => ($validated['role'] === 'editor' && $admin) ? $admin->id : null
        // ]);
        
         // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
            'assigned_to_editor_id' => $validated['assigned_to_editor_id'],
            'assigned_to_admin_id' => $assignedToAdminId
        ]);
        // Log::error($user);

        // Assign role
        $user->assignRole($validated['role']);
        $admin = User::role('admin')->first();
        $assignedToAdminId = null;

        if ($user->hasRole('editor') && $admin) {
            $assignedToAdminId = $admin->id;
        }

        return back()->with('success', 'User created successfully');
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
        return view('settings.users.edit', compact('user', 'roles'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, User $user)
    {
        // dd($request->all()); 
        $this->authorize('edit users', $user);

        // Self Demotion Prevention
        if ($user->is(auth()->user())) {
            return redirect()->back()->withErrors(['You cannot change your own role.']);
        }

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'roles' => 'required|string',
            'assigned_to_editor_id' => 'nullable|exists:users,id',
        ]);

        // Update basic info
    $user->name = $validated['name'];
    
    // Update role
    $user->syncRoles([$validated['roles']]); // Use validated data

    // Handle editor assignment - ONLY if provided and user role is 'user'
    if ($validated['roles'] === 'user' && !empty($validated['assigned_to_editor_id'])) {
        $editor = User::findOrFail($validated['assigned_to_editor_id']);
        
        // Verify the assigned user is actually an editor
        if (!$editor->hasRole('editor')) {
            return redirect()->back()->withErrors(['The selected user must be an editor.']);
        }
        
        $user->assigned_to_editor_id = $editor->id;
    } else {
        // If role is not 'user' or no editor selected, clear the assignment
        $user->assigned_to_editor_id = null;
    }

    // Handle admin assignment for editors
    if ($validated['roles'] === 'editor') {
        $admin = User::role('admin')->first();
        $user->assigned_to_admin_id = $admin ? $admin->id : null;
    } else {
        $user->assigned_to_admin_id = null;
    }

    // Save changes
        $user->save();
        // dd($user);

        return redirect()->back()->with('success', 'User updated successfully.');
    }


    // Assign editor to admin
    public function assignToAdmin(Request $request, User $editor)
    {
        // Verify user is editor
        if (!$editor->hasRole('editor')) {
            return back()->with('error', 'Selected user must be an editor');
        }
        
        $validated = $request->validate([
            'admin_id' => 'required|exists:users,id'
        ]);
        
        $admin = User::findOrFail($validated['admin_id']);
        
        // Verify admin is actually an admin
        if (!$admin->hasRole('admin')) {
            return back()->with('error', 'Selected admin must have admin role');
        }
        
        $editor->assigned_to_admin_id = $admin->id;
        $editor->save();
        
        return back()->with('success', 'Editor assigned to admin successfully');
    }

    // Assign user to editor
    public function assignToEditor(Request $request, User $user)
    {
        // dd($request->all());
        $validated = $request->validate([
            'assigned_to_editor_id' => 'required|exists:users,id'
        ]);
        
        $editor = User::findOrFail($validated['assigned_to_editor_id']);-

        // dd($validated['assigned_to_editor_id']);
        
        $user->assigned_to_editor_id = $editor->id;
        $user->save();

        // dd($user);
        
        return back()->with('success', "User assigned to editor successfully");
}

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        DB::transaction(function () use ($user) {
            $user->posts()->delete();
            $user->delete();
        });

        return back()->with('success', 'User deleted successfully!');
    }
}
