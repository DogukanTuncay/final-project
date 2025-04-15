<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Api\MatchingPairResource;

class MatchingQuestionResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'shuffle_items' => $this->shuffle_items,
            'points' => $this->points,
            'pairs' => $this->when($this->resource->relationLoaded('pairs'), function () {
                return MatchingPairResource::collection($this->resource->pairs);
            })
        ]);
    }
}