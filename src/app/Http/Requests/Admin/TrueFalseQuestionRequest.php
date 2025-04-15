<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class TrueFalseQuestionRequest extends BaseRequest
{
    /**
     * Validation kuralları
     *
     * @return array
     */
    public function rules(): array
    {
        $rules = [
            'question' => 'required|array',
            'question.tr' => 'required|string|min:2|max:1000',
            'question.en' => 'required|string|min:2|max:1000',
            'correct_answer' => 'required|boolean',
            'custom_text' => 'nullable|array',
            'custom_text.tr' => 'nullable|string|max:500',
            'custom_text.en' => 'nullable|string|max:500',
            'feedback' => 'nullable|array',
            'feedback.tr' => 'nullable|string|max:1000',
            'feedback.en' => 'nullable|string|max:1000',
            'points' => 'nullable|integer|min:0|max:100',
            'is_active' => 'nullable|boolean'
        ];

        // Güncelleme durumunda ID kontrolü yapma
        if ($this->isMethod('PUT') || $this->isMethod('PATCH')) {
            // Güncelleme kurallarını burada ekleyebilirsiniz
        }

        return $rules;
    }

    /**
     * Özelleştirilmiş doğrulama mesajları
     *
     * @return array
     */
    public function messages(): array
    {
        return [
            'question.required' => __('errors.true_false_question.question_required'),
            'question.tr.required' => __('errors.true_false_question.question_tr_required'),
            'question.en.required' => __('errors.true_false_question.question_en_required'),
            'correct_answer.required' => __('errors.true_false_question.correct_answer_required'),
            'correct_answer.boolean' => __('errors.true_false_question.correct_answer_boolean'),
            'points.integer' => __('errors.true_false_question.points_integer'),
            'points.min' => __('errors.true_false_question.points_min'),
            'points.max' => __('errors.true_false_question.points_max'),
        ];
    }
}