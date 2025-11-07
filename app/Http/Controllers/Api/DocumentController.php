<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDocumentRequest;
use App\Http\Requests\UpdateDocumentRequest;
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

        // Belgeleri getir
        $target = $user ?? $request->user();
        $documents = $target->documents()->latest()->get();

        return response()->json([
            'data' => [
                'user' => $target->name,
                'type' => 'documents',
                'items' => $documents
            ]
        ], 200);
    }

    public function show(Document $document): JsonResponse
    {
        $this->authorize('view', $document);

        return response()->json([
            'data' => [
                'type' => 'documents',
                'id' => $document->id,
                'attributes' => $document->load('media')
            ]
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
            'data' => $document->load('media'),
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
            'data' => $document->load('media'),
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
