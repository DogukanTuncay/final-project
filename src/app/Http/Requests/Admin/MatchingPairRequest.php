<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class MatchingPairRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'left_item' => 'required|array',
            'left_item.tr' => 'required|string',
            'left_item.en' => 'required|string',
            'right_item' => 'required|array',
            'right_item.tr' => 'required|string',
            'right_item.en' => 'required|string',
            'order' => 'nullable|integer|min:0'
        ];
    }
    
    public function messages()
    {
        return [
            'left_item.required' => 'Sol taraf metni zorunludur.',
            'left_item.tr.required' => 'Türkçe sol taraf metni zorunludur.',
            'left_item.en.required' => 'İngilizce sol taraf metni zorunludur.',
            'right_item.required' => 'Sağ taraf metni zorunludur.',
            'right_item.tr.required' => 'Türkçe sağ taraf metni zorunludur.',
            'right_item.en.required' => 'İngilizce sağ taraf metni zorunludur.',
            'order.integer' => 'Sıra değeri tam sayı olmalıdır.',
            'order.min' => 'Sıra değeri 0 veya daha büyük olmalıdır.',
        ];
    }
} 