<?php

use App\Http\Controllers\Api\CategoryController;
use App\Http\Controllers\Api\DocumentController;
use App\Http\Controllers\Api\PostController;
use App\Http\Controllers\Api\TagController;
use App\Http\Controllers\Api\UserPublicController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\AuthController;

Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::apiResources([
        'categories' => CategoryController::class,
        'posts'      => PostController::class,
        'tags'       => TagController::class,
        'documents'  => DocumentController::class,
    ]);
    Route::get('user/{user:name}/documents', [DocumentController::class, 'index']);
});

Route::prefix('user/{user:name}')->group(function () {
    Route::get('categories', [UserPublicController::class, 'categories']);
    Route::get('posts', [UserPublicController::class, 'posts']);
    Route::get('tags', [UserPublicController::class, 'tags']);
});
