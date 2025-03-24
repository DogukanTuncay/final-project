<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class MatchingQuestionRequest extends BaseRequest
{
    public function rules()
    {
        return [
            // API tarafında farklı doğrulama kuralları olabilir
        ];
    }
}