<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\Admin\MatchingPairResource;

class MatchingQuestionResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'shuffle_items' => $this->shuffle_items,
            'points' => $this->points,
            'created_by' => $this->created_by,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'pairs' => MatchingPairResource::collection($this->pairs),
            'creator' => $this->when($this->resource->relationLoaded('creator'), function () {
                return new UserResource($this->resource->creator);
            }),
        ]);
    }
}