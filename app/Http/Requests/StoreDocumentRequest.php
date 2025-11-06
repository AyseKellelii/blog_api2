<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreDocumentRequest extends FormRequest
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
            'title' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
            'file' => ['required', 'file', 'mimes:pdf,doc,docx,txt', 'max:5120'], // Maksimum 5MB
        ];
    }

    public function messages(): array
    {
        return [
            'title.required' => 'Başlık zorunludur.',
            'file.required' => 'Dosya yüklemek zorunludur.',
            'file.mimes' => 'Yalnızca PDF, DOC, DOCX veya TXT formatındaki dosyalar kabul edilir.',
            'file.max' => 'Dosya boyutu en fazla 5MB olabilir.',
        ];
    }
}
