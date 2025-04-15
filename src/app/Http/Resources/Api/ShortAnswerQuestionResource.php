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
            'case_sensitive' => $this->case_sensitive,
            'max_attempts' => $this->max_attempts,
            'points' => $this->points,
            'is_active' => $this->is_active,
            // API'de allowed_answers göstermiyoruz, kullanıcılar cevapları göremesin
        ]);
    }
}