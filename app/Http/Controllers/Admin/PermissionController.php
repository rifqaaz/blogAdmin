<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    public function index()
    {
        return view('settings.roles.index');
    }

    public function store(Request $request)
    {
        // Validate the request
        $validated = $request->validate([
            'name' => 'required|string|unique:permissions,name'
        ]);

         // Create the permission
        $permission = Permission::create(['name' => $validated['name'], 'guard_name' => 'web']);

        return redirect()->back()->with('success', 'Permission created successfully.');
        
    }
}
