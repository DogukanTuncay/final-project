<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class MatchingQuestionRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'question' => 'required|array',
            'question.tr' => 'required|string',
            'question.en' => 'required|string',
            'shuffle_items' => 'boolean',
            'points' => 'required|integer|min:1',
            'feedback' => 'nullable|array',
            'feedback.tr' => 'nullable|string',
            'feedback.en' => 'nullable|string',
            'is_active' => 'boolean',

            // Eşleştirme çiftleri için kurallar
            'pairs' => 'nullable|array',
            'pairs.*.left_item' => 'required_with:pairs|array',
            'pairs.*.left_item.tr' => 'required_with:pairs.*.left_item|string',
            'pairs.*.left_item.en' => 'required_with:pairs.*.left_item|string',
            'pairs.*.right_item' => 'required_with:pairs|array',
            'pairs.*.right_item.tr' => 'required_with:pairs.*.right_item|string',
            'pairs.*.right_item.en' => 'required_with:pairs.*.right_item|string',
            'pairs.*.order' => 'nullable|integer|min:0'
        ];
    }
}
