<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class ShortAnswerQuestionRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'question' => 'required|array',
            'question.tr' => 'required|string',
            'question.en' => 'required|string',
            'allowed_answers' => 'required|array',
            'allowed_answers.tr' => 'required|array|min:1',
            'allowed_answers.en' => 'required|array|min:1',
            'case_sensitive' => 'boolean',
            'max_attempts' => 'nullable|integer|min:1',
            'points' => 'required|integer|min:1',
            'feedback' => 'nullable|array',
            'feedback.tr' => 'required|array|min:1',
            'feedback.en' => 'required|array|min:1',
            'is_active' => 'boolean'
        ];
    }
    
    public function messages()
    {
        return [
            'question.required' => 'Soru metni zorunludur.',
            'question.tr.required' => 'Türkçe soru metni zorunludur.',
            'question.en.required' => 'İngilizce soru metni zorunludur.',
            'allowed_answers.required' => 'En az bir cevap zorunludur.',
            'allowed_answers.*.tr.required' => 'Türkçe cevap metni zorunludur.',
            'allowed_answers.*.en.required' => 'İngilizce cevap metni zorunludur.',
            'points.required' => 'Puan değeri zorunludur.',
            'points.integer' => 'Puan değeri tam sayı olmalıdır.',
            'points.min' => 'Puan değeri en az 1 olmalıdır.',
            'max_attempts.integer' => 'Maksimum deneme sayısı tam sayı olmalıdır.',
            'max_attempts.min' => 'Maksimum deneme sayısı en az 1 olmalıdır.',
        ];
    }
}