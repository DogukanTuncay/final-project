<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class ShortAnswerQuestionResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            // Add other non-translatable attributes here
        ]);
    }
}