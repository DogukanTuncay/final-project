<?php

namespace App\Http\Resources\Admin;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\Admin\UserResource;
use App\Http\Resources\BaseResource;
class FillInTheBlankResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'answers' => $this->answers,
            'is_active' => $this->is_active,
            'case_sensitive' => $this->case_sensitive,
            'points' => $this->points,
            'created_at' => $this->created_at ? $this->created_at->toIso8601String() : null,
            'updated_at' => $this->updated_at ? $this->updated_at->toIso8601String() : null,
            'creator' => $this->when($this->resource->relationLoaded('creator'), function () {
                return new UserResource($this->resource->creator);
            }),
        ]);
    }
} 