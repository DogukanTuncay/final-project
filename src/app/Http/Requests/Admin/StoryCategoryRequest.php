<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoryCategoryRequest extends BaseRequest
{
   

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        $rules = [
            'name' => ['required', 'array'], // Translatable alanlar array olmalı
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['sometimes', 'boolean'],
            'order' => ['sometimes', 'integer', 'min:0'],
            // Çevrilecek her dil için name kontrolü
            'name.*' => ['required', 'string', 'max:255'],
        ];

        // Slug validasyonu (isteğe bağlı, model otomatik oluşturuyor ama yine de kontrol edilebilir)
        // $categoryId = $this->route('story_category')?->id; // Veya route parametresinin adı ne ise
        // $rules['slug'] = ['sometimes', 'string', 'max:255', Rule::unique('story_categories', 'slug')->ignore($categoryId)];

        return $rules;
    }

 
    
}