<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreCategoryRequest;
use App\Http\Requests\UpdateCategoryRequest;
use App\Http\Resources\CategoryResource;
use App\Models\Category;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request): JsonResponse
    {

        $query = $request->user()->categories()->latest();

        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where('name', 'like', "%{$search}%");
        }


        $categories = $query->paginate(10);

        // JSON çıktısı
        return response()->json([
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
            'data' => CategoryResource::collection($categories),
        ]);
    }

    public function store(StoreCategoryRequest $request): JsonResponse
    {
        $category = $request->user()->categories()->create([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Kategori başarıyla oluşturuldu.',
        ], 201);
    }

    public function show(Request $request, Category $category): JsonResponse
    {
        $this->authorize('view', $category);

        return response()->json([
            'data' => new CategoryResource($category),
            'meta' => [
                'owner' => $category->user->name,
            ],
        ], 200);
    }

    public function update(UpdateCategoryRequest $request, Category $category): JsonResponse
    {
        $this->authorize('update', $category);

        $category->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
        ]);

        return response()->json([
            'data' => new CategoryResource($category),
            'message' => 'Kategori başarıyla güncellendi.',
        ], 200);
    }


    public function destroy(Request $request, Category $category): JsonResponse
    {
        $this->authorize('delete', $category);
        $category->delete();

        return response()->json([
            'message' => 'Kategori başarıyla silindi.',
        ], 200);
    }
}
