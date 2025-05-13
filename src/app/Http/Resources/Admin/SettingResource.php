<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class SettingResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, [
            'id' => $this->id,
            'key' => $this->key,
            'type' => $this->type,
            'group' => $this->group,
            'value' => $this->value, // Raw value
            'is_translatable' => $this->is_translatable,
            'is_private' => $this->is_private,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
    }
}