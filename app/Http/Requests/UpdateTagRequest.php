<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTagRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:tags,name,' . $this->route('tag')->id,
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
