<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class FillInTheBlankRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        $rules = [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'text' => 'required|string',
            'answers' => 'required|array',
            'answers.*' => 'required|string|max:255',
            'is_active' => 'boolean',
            'case_sensitive' => 'boolean',
            'points' => 'nullable|integer|min:0',
            'feedback' => 'nullable|string',
        ];

        return $rules;
    }

    /**
     * Get custom messages for validator errors.
     *
     * @return array
     */
    public function messages()
    {
        return [
            'title.required' => 'Başlık alanı zorunludur.',
            'title.max' => 'Başlık en fazla 255 karakter olabilir.',
            'text.required' => 'Metin alanı zorunludur.',
            'answers.required' => 'En az bir cevap eklemelisiniz.',
            'answers.array' => 'Cevaplar liste formatında olmalıdır.',
            'answers.*.required' => 'Cevap boş olamaz.',
            'answers.*.max' => 'Cevap en fazla 255 karakter olabilir.',
            'points.integer' => 'Puan değeri tam sayı olmalıdır.',
            'points.min' => 'Puan değeri 0 veya daha büyük olmalıdır.',
        ];
    }
} 