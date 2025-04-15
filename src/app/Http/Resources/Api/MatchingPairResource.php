<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class MatchingPairResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'left_item' => $this->resource->getTranslations('left_item'),
            'right_item' => $this->resource->getTranslations('right_item'),
            'order' => $this->order
        ];
    }
} 