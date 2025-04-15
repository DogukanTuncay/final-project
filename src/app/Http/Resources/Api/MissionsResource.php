<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class MissionsResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);

        return array_merge($translated, [
            'id' => $this->id,
                'title' => $this->getTranslations('title'),
                'description' => $this->getTranslations('description'),
                'type' => $this->type,
                'requirements' => $this->requirements,
                'xp_reward' => $this->xp_reward,
                'is_active' => $this->is_active,
                'created_at' => $this->created_at?->toDateTimeString(),
                'updated_at' => $this->updated_at?->toDateTimeString(),
        ]);
    }
}
