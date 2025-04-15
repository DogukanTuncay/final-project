<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class MultipleChoiceQuestionResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'points' => $this->points,
            'is_multiple_answer' => $this->is_multiple_answer,
            'shuffle_options' => $this->shuffle_options,
            'options' => QuestionOptionResource::collection($this->options),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ]);
    }
}