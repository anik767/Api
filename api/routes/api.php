<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Models\User;

// Public routes (no authentication required)
Route::post('/login', [AuthController::class, 'login'])->name('login');


// Public post routes (read-only)
Route::get('/posts', [PostController::class, 'getAllPosts']);
Route::get('/posts/{id}', [PostController::class, 'getPostById']);

// Public category routes (read-only)
Route::get('/categories', [CategoryController::class, 'index']);
Route::get('/categories/{id}', [CategoryController::class, 'show']);

// Protected routes (requires authentication)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/users', function (Request $request) {
        return response()->json([
            'success' => true,
            'data' => User::all()
        ]);
    });
    Route::post('/logout', [AuthController::class, 'logout']);
    
    // Post management routes
    Route::post('/posts', [PostController::class, 'createPost']);
    Route::match(['put', 'patch'], '/posts/{id}', [PostController::class, 'update']);
    Route::delete('/posts/{id}', [PostController::class, 'destroy']);
    
    // Category management routes
    Route::post('/categories', [CategoryController::class, 'store']);
    Route::match(['put', 'patch'], '/categories/{id}', [CategoryController::class, 'update']);
    Route::delete('/categories/{id}', [CategoryController::class, 'destroy']);
    
    // Admin only routes
    Route::middleware('admin')->group(function () {
        Route::post('/register', [AuthController::class, 'register'])->name('admin.register');
    });
});

