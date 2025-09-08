<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\PasswordController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\RoleController;
use App\Http\Controllers\Admin\PermissionController;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

// Public Routes
// Route::get('/', function () {
//     return view('welcome');
// })->name('welcome');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Registration
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');

    // Route::post('/register', [AuthController::class, 'register'])->name('register');

    // Login
    Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('auth.login');
});

// Authenticated Routes
Route::middleware(['auth'])->group(function () {
    // Dashboard
    Route::get('/dashboard', function () {
        return view('auth.dashboard');
    })->name('dashboard');
    Route::get('/index', function () {
        return view('auth.index');
    })->name('index');

    // Profile Management
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::get('/posts/myposts', [PostController::class, 'myPosts'])
        ->name('posts.myposts');

    // Posts Resource
    Route::resource('posts', PostController::class);

    // Slug-based view route
    Route::get('posts/{post:slug}', [PostController::class, 'show'])
        ->name('posts.show');

    // Logout
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
});

Route::get('/testing', function (Request $request) {
    return response()->json([
        'message' => 'Testing route is working!',
        'user' => $request->user(),
    ]);
})->middleware('auth')->name('testing');

// Toggle
Route::patch('/posts/{post}/toggle-status', [PostController::class, 'toggleStatus'])
    ->name('posts.toggle-status')
    ->middleware('auth'); // Protect this route

Route::prefix('settings')->middleware(['auth', 'permission:view users'])->group(function () {
    
    Route::get('/', function () {
        return view('settings.index');
    })->name('settings');

    // Role Management
    Route::resource('roles', RoleController::class)->except(['show']);
    Route::resource('permissions', PermissionController::class)->except(['show']);

    // User Management
    Route::resource('users', UserController::class)->except(['show']);
    
    // Specific user actions
    Route::prefix('users/{user}')->group(function () {
        // Change password - PATCH /settings/users/1/password
        Route::patch('password', [PasswordController::class, 'update'])
            ->name('users.password.update'); 
        
        // Assign user to admin - PATCH /settings/users/1/assign-admin
        Route::patch('assign-admin', [UserController::class, 'assignToAdmin'])
            ->name('users.assign.admin');
        
        // Assign user to editor - PATCH /settings/users/1/assign-editor  
        Route::patch('assign-editor', [UserController::class, 'assignToEditor'])
            ->name('users.assign.editor');
    });
});