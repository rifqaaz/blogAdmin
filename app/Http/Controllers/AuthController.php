<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Routing\Controller;
use Session;

class AuthController extends Controller
{

    /**
     * Handle the registration request.
     */
    public function register(Request $request)
    {
        // Validate input
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8'],
        ]);

        // Create user
        $user = User::create([
            'name' => $validated['name'],
            'email' => strtolower($validated['email']),
            'password' => Hash::make($validated['password']),
        ]);

        // Assign default role
        $user->assignRole('user');

        // Trigger registration event
        event(new Register($user));

        // Auto-login and redirect
        auth()->login($user);

        return redirect()->route('dashboard')
            ->with('success', 'Registration successful!');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        // Add remember me check
        // $remember = $request->has('remember');

        // if (Auth::attempt($credentials, $remember)) {
        //     $request->session()->regenerate();
        //     return redirect()->intended(route('dashboard'))->with('success', 'Logged in successfully!');
        // }
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended(route('dashboard'))->with('success', 'Logged in successfully!');
        }

        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->onlyInput('email');
    }

    /**
     * Handle logout request.
     */
    public function logout(Request $request)
    {
        // $sessionId = $request->session()->getId();

        // Auth::logout();
        // $request->session()->invalidate();
        // $request->session()->regenerateToken();

        // DB::table('sessions')->where('id', $sessionId)->delete();
        $request->session()->flush();
        Auth::logout();
        
        return redirect()->route('login')->with('success', 'You have logged out!');
    }
}