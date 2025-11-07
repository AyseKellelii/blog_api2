<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class DocumentResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id'          => $this->id,
            'title'       => $this->title,
            'description' => $this->description,
            'created_at'  => $this->created_at?->format('Y-m-d H:i'),
            'updated_at'  => $this->updated_at?->format('Y-m-d H:i'),

            // Kullanıcı bilgisi (eager load edilmişse)
            'user' => $this->whenLoaded('user', fn () => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ]),

            // Medya dosyaları
            'media' => $this->whenLoaded('media', fn () =>
            $this->media->map(fn ($m) => [
                'id'  => $m->id,
                'name' => $m->file_name,
                'url'  => $m->getUrl(),
            ])
            ),
        ];
    }
}
