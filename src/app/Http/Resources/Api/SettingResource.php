<?php

namespace App\Http\Resources\Api;

use App\Http\Resources\BaseResource;

class SettingResource extends BaseResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        return array_merge($translated, [
            'id' => $this->id,
            'key' => $this->key,
            'type' => $this->type,
            'group' => $this->group,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
        
        
        
    }
    
   
}