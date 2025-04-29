<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class MatchingPairResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        return array_merge($translated, [
            'id' => $this->id,
            'matching_question_id' => $this->matching_question_id,
            'order' => $this->order,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ]);
    }
} 