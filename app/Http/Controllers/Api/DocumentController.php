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
        // Eğer route'tan user gelmişse (yani başkasının belgeleri isteniyorsa)
        if ($user) {
            // Eğer giriş yapan kişi farklıysa 403 döndür
            if ($user->id !== $request->user()->id) {
                return response()->json([
                    'errors' => [
                        'status' => 403,
                        'title' => 'Bu belgeleri görüntüleme izniniz yok.'
                    ]
                ], 403);
            }

            // Aynı kullanıcıysa belgeleri getir
            $documents = $user->documents()->latest()->get();

            return response()->json([
                'data' => [
                    'user' => $user->name,
                    'type' => 'documents',
                    'items' => $documents
                ]
            ], 200);
        }

        // Eğer route'ta user yoksa giriş yapan kişinin belgeleri
        $documents = $request->user()->documents()->latest()->get();

        return response()->json([
            'data' => [
                'user' => $request->user()->name,
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
