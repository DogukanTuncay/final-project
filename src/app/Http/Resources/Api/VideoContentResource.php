<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class VideoContentResource extends BaseResource
{
    /**
     * Video içerik verisini dönüştür
     *
     * @param \Illuminate\Http\Request $request
     * @return array
     */
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'video_url' => $this->video_url,
            'provider' => $this->provider,
            'embed_url' => $this->getEmbedUrl(),
            'duration' => $this->duration,
            'formatted_duration' => $this->formatted_duration,
            'is_active' => (bool) $this->is_active,
            'created_at' => $this->created_at ? $this->created_at->format('Y-m-d H:i:s') : null,
            'updated_at' => $this->updated_at ? $this->updated_at->format('Y-m-d H:i:s') : null,
        ]);
    }
}