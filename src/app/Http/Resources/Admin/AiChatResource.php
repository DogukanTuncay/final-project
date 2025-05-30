<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class AiChatResource extends BaseResource
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