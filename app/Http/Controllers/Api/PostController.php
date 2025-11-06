<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePostRequest;
use App\Http\Requests\UpdatePostRequest;
use App\Http\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PostController extends Controller
{
    use AuthorizesRequests;

    public function index(Request $request)
    {
        // Tüm postları user, category ve tags ile birlikte çek
        $query = Post::with(['user:id,name', 'category:id,name', 'tags'])->latest();

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('content', 'like', "%{$search}%");
            });
        }

        if ($request->filled('tag')) {
            $tagName = $request->tag;
            $query->whereHas('tags', function ($q) use ($tagName) {
                $q->where('name', 'like', "%{$tagName}%");
            });
        }


        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('created_at', [
                $request->start_date . ' 00:00:00',
                $request->end_date . ' 23:59:59'
            ]);
        }

        $posts = $query->paginate(10);

        return response()->json([
            'meta' => [
                'total' => $posts->total(),
                'per_page' => $posts->perPage(),
                'current_page' => $posts->currentPage(),
                'last_page' => $posts->lastPage(),
                'filters' => [
                    'search' => $request->search ?? null,
                    'tag' => $request->tag ?? null,
                    'start_date' => $request->start_date ?? null,
                    'end_date' => $request->end_date ?? null,
                ],
            ],
            'links' => [
                'self' => $request->fullUrl(),
                'next' => $posts->nextPageUrl(),
                'prev' => $posts->previousPageUrl(),
            ],
            'data' => PostResource::collection($posts)
        ], 200);
    }

    public function store(StorePostRequest $request)
    {
        $category = $request->user()
            ->categories()
            ->where('id', $request->category_id)
            ->first();

        if (!$category) {
            return response()->json([
                'errors' => [
                    'status' => 404,
                    'title' => 'Bu kategori size ait değil veya bulunamadı.'
                ]
            ], 404);
        }

        // slug üretimi
        $baseSlug = Str::slug($request->title);
        $slug = $baseSlug;
        $count = 1;
        while (Post::where('slug', $slug)->exists()) {
            $slug = $baseSlug . '-' . $count++;
        }

        // gönderi oluştur
        $post = $request->user()->posts()->create([
            'category_id' => $category->id,
            'title' => $request->title,
            'slug' => $slug,
            'content' => $request->input('content'),
        ]);

        // etiketleri kullanıcıya göre eşleştir
        if ($request->has('tags')) {
            $tagIds = [];

            foreach ($request->tags as $tagIdOrName) {
                // Eğer sayıysa ID olarak al, yoksa isimden oluştur
                if (is_numeric($tagIdOrName)) {
                    $tag = \App\Models\Tag::find($tagIdOrName);
                } else {
                    $tag = \App\Models\Tag::firstOrCreate(
                        [
                            'name' => $tagIdOrName,
                            'user_id' => $request->user()->id
                        ],
                        [
                            'slug' => Str::slug($tagIdOrName),
                            'type' => 'default',
                            'order_column' => 1
                        ]
                    );
                }

                if ($tag) {
                    $tagIds[] = $tag->id;
                }
            }

            $post->tags()->sync($tagIds);
        }

        return response()->json([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => $post->load('category', 'tags')
            ],
            'message' => 'Gönderi başarıyla oluşturuldu.'
        ], 201);
    }

    public function show(Request $request, Post $post)
    {
        // Sadece kendi postunu tam detaylı görebilir
        $this->authorize('view', $post);

        return response()->json([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => $post->load('category', 'tags')
            ]
        ], 200);

    }

    public function update(UpdatePostRequest $request, Post $post): JsonResponse
    {
        $this->authorize('update', $post);

        // Slug yeniden üret (eğer başlık değiştiyse)
        $baseSlug = Str::slug($request->title);
        $newSlug = $baseSlug;
        $count = 1;

        while (
        Post::where('slug', $newSlug)
            ->where('id', '!=', $post->id)
            ->exists()
        ) {
            $newSlug = $baseSlug . '-' . $count++;
        }

        $post->update([
            'title' => $request->title,
            'slug' => $newSlug,
            'content' => $request->input('content'),
            'category_id' => $request->category_id,
        ]);

        // Etiketleri güncelle (sync)
        if ($request->has('tags')) {
            $tagIds = [];

            foreach ($request->tags as $tagIdOrName) {
                if (is_numeric($tagIdOrName)) {
                    $tag = Tag::find($tagIdOrName);
                } else {
                    $tag = Tag::firstOrCreate(
                        [
                            'name' => $tagIdOrName,
                            'user_id' => $request->user()->id
                        ],
                        [
                            'slug' => Str::slug($tagIdOrName),
                            'type' => 'default',
                            'order_column' => 1
                        ]
                    );
                }

                if ($tag) {
                    $tagIds[] = $tag->id;
                }
            }

            $post->tags()->sync($tagIds);
        }


        return response()->json([
            'data' => [
                'type' => 'posts',
                'id' => $post->id,
                'attributes' => $post->load('category', 'tags')
            ],
            'message' => 'Gönderi başarıyla güncellendi.'
        ], 200);
    }

    public function destroy(Request $request, Post $post)
    {
        $this->authorize('delete', $post);

        $post->delete();

        return response()->json([
            'message' => 'Gönderi başarıyla silindi.'
        ], 200);
    }
}
