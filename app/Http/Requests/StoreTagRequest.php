<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name',
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'Etiket adı zorunludur.',
            'name.unique' => 'Bu etiket zaten mevcut.',
        ];
    }
}
