<?php

namespace App\Http\Resources\Auth;

use App\Http\Resources\BaseResource;

class CourseResource extends BaseResource
{
    public function toArray($request)
    {
        $translated = $this->getTranslated($this->resource);
        
        return array_merge($translated, parent::toArray($request));
    }
}