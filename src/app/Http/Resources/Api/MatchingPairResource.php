<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class MatchingPairResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        return array_merge($translated, [
            'id' => $this->id,
            'order' => $this->order
        ]);
    }
} 