<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
use App\Http\Resources\DocumentResource;
use App\Models\Document;
use App\Models\User;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class DocumentController extends Controller
{
    use AuthorizesRequests;
    public function index(Request $request, ?User $user = null): JsonResponse
    {
        $this->authorize('viewAny', [Document::class, $user]);

        $target = $user ?? $request->user();

        $query = $target->documents()
            ->with(['media', 'user'])
            ->latest();


        if ($request->filled('search')) {
            $search = $request->input('search');
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $documents = $query->paginate(10);

        return response()->json([
            'user' => $target->name,
            'type' => 'documents',
            'meta' => [
                'total' => $documents->total(),
                'per_page' => $documents->perPage(),
                'current_page' => $documents->currentPage(),
                'last_page' => $documents->lastPage(),
                'filters' => [
                    'search' => $request->search ?? null,
                ],
            ],
            'links' => [
                'self' => $request->fullUrl(),
                'next' => $documents->nextPageUrl(),
                'prev' => $documents->previousPageUrl(),
            ],
            'items' => DocumentResource::collection($documents),
        ], 200);
    }

    public function show(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        return response()->json([
            'data' => new DocumentResource($document->load(['media', 'user'])),
        ], 200);
    }

    public function store(StoreDocumentRequest $request): JsonResponse
    {
        $document = $request->user()->documents()->create([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->hasFile('file')) {
            $document
                ->addMedia($request->file('file'))
                ->toMediaCollection('documents');
        }

        return response()->json([
            'message' => 'Belge başarıyla yüklendi.',
            'data' => new DocumentResource($document->load(['media', 'user'])),
        ], 201);
    }

    public function update(UpdateDocumentRequest $request, Document $document): JsonResponse
    {
        $this->authorize('update', $document);

        $document->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        if ($request->hasFile('file')) {
            $document->clearMediaCollection('documents');
            $document
                ->addMedia($request->file('file'))
                ->toMediaCollection('documents');
        }

        return response()->json([
            'message' => 'Belge başarıyla güncellendi.',
            'data' => new DocumentResource($document->load(['media', 'user'])),
        ]);
    }

    public function destroy(Request $request, Document $document): JsonResponse
    {
        $this->authorize('delete', $document);

        $document->clearMediaCollection('documents');
        $document->delete();

        return response()->json([
            'message' => 'Belge başarıyla silindi.'
        ], 200);
    }

}
