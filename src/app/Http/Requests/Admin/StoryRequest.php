<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class StoryRequest extends BaseRequest
{
    public function rules(): array
    {
        // Todo: Desteklenen diller config'den alınabilir.
        $supportedLocales = ['en', 'tr']; 
        $titleRules = [];
        foreach ($supportedLocales as $locale) {
            // Güncelleme işleminde bazen tüm diller gelmeyebilir, bu yüzden 'sometimes' ekleyebiliriz.
            $rulePrefix = ($this->isMethod('post') ? 'required' : 'sometimes');
            $titleRules["title.{$locale}"] = [$rulePrefix, 'string', 'max:255'];
        }
        
        return [
            'story_category_id' => ['required', 'integer', Rule::exists('story_categories', 'id')],
            'title' => ['required', 'array'],
            ...$titleRules, // Dinamik başlık kuralları
            'order_column' => ['nullable', 'integer', 'min:0'],
            'is_active' => ['nullable', 'boolean'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif'],
            'images' => ['sometimes', 'nullable', 'array'],
            'images.*' => ['image', 'mimes:jpeg,png,jpg,gif'],
        ];
    }
}