<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePostRequest extends FormRequest
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
        return [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'tags' => 'array',
            'tags.*' => 'exists:tags,id',
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Başlık alanı zorunludur.',
            'title.string' => 'Başlık metin formatında olmalıdır.',
            'title.max' => 'Başlık en fazla 255 karakter olabilir.',
            'content.required' => 'İçerik alanı zorunludur.',
            'content.string' => 'İçerik metin formatında olmalıdır.',
            'category_id.required' => 'Kategori alanı zorunludur.',
            'category_id.exists' => 'Seçilen kategori geçersizdir.',
            'tags.array' => 'Etiketler bir dizi olmalıdır.',
            'tags.*.exists' => 'Seçilen etiket geçersizdir.',
        ];
    }
}
