<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;
use App\Http\Resources\Admin\UserResource;

class FillInTheBlankResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'answers' => $this->resource->getTranslations('answers'),
            'points' => $this->points,
            'feedback' => $this->resource->getTranslations('feedback'),
            'case_sensitive' => $this->case_sensitive,
            'created_by' => $this->created_by,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
            'creator' => $this->when($this->resource->relationLoaded('creator'), function () {
                return new UserResource($this->resource->creator);
            }),
        ]);
    }
} 