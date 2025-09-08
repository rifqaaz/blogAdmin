<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\UserController;
use App\Http\Controllers\Api\ProfileController;

// This is where you will register your API routes

Route::get('/', function () {
    return response()->json(['status' => 'OK']);
});


// Profile Management
Route::resource('profile', ProfileController::class);

// Posts Resource
Route::get('posts', [PostController::class, 'index'])
    ->name('posts.index');
Route::post('create-post', [PostController::class, 'store'])
    ->name('posts.store');
Route::get('posts/{post:slug}', [PostController::class, 'show'])
    ->name('posts.show');

 // User Management
Route::resource('users', UserController::class);