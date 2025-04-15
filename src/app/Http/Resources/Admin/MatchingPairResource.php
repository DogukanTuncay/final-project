<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class MatchingPairResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'matching_question_id' => $this->matching_question_id,
            'left_item' => $this->resource->getTranslations('left_item'),
            'right_item' => $this->resource->getTranslations('right_item'),
            'order' => $this->order,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ];
    }
} 