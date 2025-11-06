<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class UserPublicController extends Controller
{
    public function categories(User $user): JsonResponse
    {
        $categories = $user->categories()->latest()->get();

        return response()->json([
            'data' => [
                'user' => $user->name,
                'type' => 'categories',
                'items' => $categories
            ]
        ], 200);
    }

    public function posts(User $user): JsonResponse
    {
        $posts = $user->posts()->with('category', 'tags')->latest()->get();

        return response()->json([
            'data' => [
                'user' => $user->name,
                'type' => 'posts',
                'items' => $posts
            ]
        ], 200);
    }

    public function tags(User $user): JsonResponse
    {
        $tags = $user->tags()->latest()->get();

        return response()->json([
            'data' => [
                'user' => $user->name,
                'type' => 'tags',
                'items' => $tags
            ]
        ], 200);
    }
}
