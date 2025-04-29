<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class OneSignalTemplateRequest extends BaseRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'title' => 'required|string|max:255',
            'message' => 'required|string|max:2000',
            'additional_data' => 'nullable|array',
            'additional_data.url' => 'nullable|string|url',
            'additional_data.type' => 'nullable|string|max:100',
            'additional_data.deep_link' => 'nullable|string|max:255',
            'is_active' => 'nullable|boolean',
        ];
    }

    /**
     * Get the error messages for the defined validation rules.
     *
     * @return array<string, string>
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Şablon adı zorunludur.',
            'name.max' => 'Şablon adı 255 karakterden uzun olamaz.',
            'title.required' => 'Başlık alanı zorunludur.',
            'title.max' => 'Başlık 255 karakterden uzun olamaz.',
            'message.required' => 'Mesaj alanı zorunludur.',
            'message.max' => 'Mesaj 2000 karakterden uzun olamaz.',
            'additional_data.url.url' => 'Geçerli bir URL giriniz.',
        ];
    }
} 