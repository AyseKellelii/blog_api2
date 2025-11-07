<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\CategoryResource;
use App\Http\Resources\PostResource;
use App\Http\Resources\TagResource;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class UserPublicController extends Controller
{
    public function categories(Request $request, User $user): JsonResponse
    {
        $query = $user->categories()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $categories = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'user' => $user->name,
            'type' => 'categories',
            'meta' => [
                'total' => $categories->total(),
                'per_page' => $categories->perPage(),
                'current_page' => $categories->currentPage(),
                'last_page' => $categories->lastPage(),
                'filters' => [
                    'search' => $request->search ?? null,
                ],
            ],
            'links' => [
                'self' => $request->fullUrl(),
                'next' => $categories->nextPageUrl(),
                'prev' => $categories->previousPageUrl(),
            ],
            'items' => CategoryResource::collection($categories)
        ]);
    }

    public function posts(Request $request, User $user): JsonResponse
    {
        $query = $user->posts()
            ->with(['category:id,name,slug', 'tags:id,name,slug'])
            ->latest();

        // Arama filtresi (başlık veya içerik)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        // Tarih aralığı filtreleme (isteğe bağlı)
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $posts = $query->paginate(10);

        return response()->json([
            'user' => $user->name,
            'type' => 'posts',
            'meta' => [
                'total' => $posts->total(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'filters' => [
                    'search' => $request->search ?? null,
                    'start_date' => $request->start_date ?? null,
                    'end_date' => $request->end_date ?? null,
                ],
            ],
            'links' => [
                'self' => $request->fullUrl(),
                'next' => $posts->nextPageUrl(),
                'prev' => $posts->previousPageUrl(),
            ],
            'items' => PostResource::collection($posts)
        ]);
    }

    public function tags(Request $request, User $user): JsonResponse
    {
        $query = $user->tags()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->paginate($request->input('per_page', 10));

        return response()->json([
            'user' => $user->name,
            'type' => 'tags',
            'meta' => [
                'total' => $tags->total(),
                'per_page' => $tags->perPage(),
                'current_page' => $tags->currentPage(),
                'last_page' => $tags->lastPage(),
                'filters' => [
                    'search' => $request->search ?? null,
                ],
            ],
            'links' => [
                'self' => $request->fullUrl(),
                'next' => $tags->nextPageUrl(),
                'prev' => $tags->previousPageUrl(),
            ],
            'items' => TagResource::collection($tags)
        ]);
    }
}
