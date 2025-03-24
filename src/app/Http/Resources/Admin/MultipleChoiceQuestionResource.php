<?php

namespace App\Http\Resources\Admin;

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
            'is_active' => $this->is_active,
            'created_by' => $this->created_by,
            'creator' => $this->whenLoaded('creator', function() {
                return [
                    'id' => $this->creator->id,
                    'name' => $this->creator->name,
                ];
            }),
            'options' => QuestionOptionResource::collection($this->whenLoaded('options')),
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ]);
    }
}