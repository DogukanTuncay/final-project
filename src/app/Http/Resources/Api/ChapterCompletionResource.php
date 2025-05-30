<?php

namespace App\Http\Resources\Api;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\BaseResource;

class ChapterCompletionResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'chapter_id' => $this->chapter_id,
            'completed_at' => $this->completed_at,
            'chapter' => $this->whenLoaded('chapter', function() {
                return [
                    'id' => $this->chapter->id,
                    'name' => $this->chapter->name,
                    'slug' => $this->chapter->slug
                ];
            })
        ];
    }
} 