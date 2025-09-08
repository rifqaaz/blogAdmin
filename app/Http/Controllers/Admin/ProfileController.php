<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\RedirectResponse;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\Password;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function index()
    {
        $user = Auth::user(); 
        
        return view('profile.edit', compact('user'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email,' . $request->user()->id],
        ]);

        DB::transaction(function () use ($request) {
            $request->user()->fill($request->validate());
            
            $request->user()->save();
        });

        return Redirect::route('profile.edit')
            ->with('status', 'profile-updated')
            ->with('user', $request->user()->fresh());
    }
    
    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    { 
        dd;
        // Validate the password for account deletion
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        // Get the authenticated user
        $user = $request->user();

        // Start transaction for data integrity
        DB::transaction(function () use ($user) {
            $user->posts()->delete(); // Delete all posts associated with the user

            // Log user out
            Auth::logout();
            
            // Then delete user
            $user->delete();
        });

        // Full session cleanup
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        
        return Redirect::to('/')
            ->with('status', 'account-deleted')
            ->with('message', 'Your account has been successfully deleted.');
    }
}
