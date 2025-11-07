<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'        => $this->id,
            'title'     => $this->title,
            'slug'      => $this->slug,
            'content'   => $this->content,
            'created_at'=> $this->created_at?->format('Y-m-d H:i'),

            // Kategori bilgisi (yalnızca ilişki yüklenmişse)
            'category'  => $this->whenLoaded('category', function () {
                return [
                    'id'   => $this->category->id,
                    'name' => $this->category->name,
                    'slug' => $this->category->slug,
                ];
            }),

            // Etiketler
            'tags' => TagResource::collection($this->whenLoaded('tags')),

            // Kullanıcı bilgisi
            'user' => $this->whenLoaded('user', function () {
                return [
                    'id'   => $this->user->id,
                    'name' => $this->user->name,
                ];
            }),
        ];
    }
}
