<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreTagRequest;
use App\Http\Requests\UpdateTagRequest;
use App\Http\Resources\TagResource;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class TagController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request): JsonResponse
    {
        $query = $request->user()->tags()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }

        $tags = $query->paginate(10);

        return response()->json([
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
            'data' => TagResource::collection($tags),
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTagRequest $request): JsonResponse
    {
        $tag = $request->user()->tags()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'data' => new TagResource($tag),
            'message' => 'Etiket başarıyla oluşturuldu.',
        ], 201);
    }

    /**
     * Display the specified resource.
     */
    public function show(Request $request, Tag $tag): JsonResponse
    {
        $this->authorize('view', $tag);

        return response()->json([
            'data' => new TagResource($tag),
            'meta' => [
                'owner' => $tag->user->name,
            ],
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTagRequest $request, Tag $tag): JsonResponse
    {
        $this->authorize('update', $tag);

        $tag->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'data' => new TagResource($tag),
            'message' => 'Etiket başarıyla güncellendi.',
        ], 200);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Request $request, Tag $tag): JsonResponse
    {
        $this->authorize('delete', $tag);

        $tag->delete();

        return response()->json([
            'meta' => [
                'message' => 'Etiket başarıyla silindi.',
            ],
        ], 200);
    }
}
