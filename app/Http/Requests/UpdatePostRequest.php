<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdatePostRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        // Slug parametresini al
        $slug = $this->route('slug');

        // Güncellenen postu bul (sadece user_id’ye ait olan)
        $post = Post::where('slug', $slug)
            ->where('user_id', auth()->id())
            ->first();

        return [
            'title' => [
                'required',
                'string',
                'max:255',
                // aynı kullanıcıya ait başka postlarda aynı başlık olmasın
                Rule::unique('posts', 'title')
                    ->where('user_id', auth()->id())
                    ->ignore($post?->id),
            ],
            'content' => ['required', 'string'],
            'category_id' => ['required', 'exists:categories,id'],
            'tags' => ['nullable', 'array'],
            'tags.*' => ['integer', 'exists:tags,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Başlık zorunludur.',
            'title.unique' => 'Bu başlık zaten mevcut.',
            'content.required' => 'İçerik zorunludur.',
            'category_id.required' => 'Kategori seçimi zorunludur.',
            'category_id.exists' => 'Seçilen kategori geçersizdir.',
            'tags.array' => 'Etiketler bir dizi olmalıdır.',
            'tags.*.exists' => 'Seçilen etiket geçersizdir.',
        ];
    }
}
