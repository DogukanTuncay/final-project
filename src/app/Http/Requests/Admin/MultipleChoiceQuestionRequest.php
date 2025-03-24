<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class MultipleChoiceQuestionRequest extends BaseRequest
{
    public function rules()
    {
        $rules = [
            'question' => 'required|array',
            'question.tr' => 'required|string',
            'question.en' => 'required|string',
            'feedback' => 'nullable|array',
            'feedback.tr' => 'nullable|string',
            'feedback.en' => 'nullable|string',
            'points' => 'nullable|integer|min:1',
            'is_multiple_answer' => 'boolean',
            'shuffle_options' => 'boolean',
            'is_active' => 'boolean',
            'options' => 'required|array|min:2',
            'options.*.text' => 'required|array',
            'options.*.text.tr' => 'required|string',
            'options.*.text.en' => 'required|string',
            'options.*.is_correct' => 'required|boolean',
            'options.*.order' => 'integer|min:0',
            'options.*.feedback' => 'nullable|array',
            'options.*.feedback.tr' => 'nullable|string',
            'options.*.feedback.en' => 'nullable|string',
        ];

        return $rules;
    }
}