<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class StoryCategoryResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        return array_merge($translated, [
            'id' => $this->id,
            'slug' => $this->slug,
            'image' => $this->image,
            'image_url' => $this->image_url,

            // Add other non-translatable attributes here
        ]);
    }
}