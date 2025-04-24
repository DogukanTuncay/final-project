<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class MissionsResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);

        return array_merge($translated, [
            'id' => $this->id,
            'type' => $this->type,
            'xp_reward' => $this->xp_reward,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
        ]);
    }
}
