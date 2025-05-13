<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class SettingRequest extends BaseRequest
{
    public function rules()
    {
        $rules = [
            'key' => 'required|string|max:255',
            'value' => 'nullable',
            'type' => 'required|string|in:text,boolean,number,json,image',
            'group' => 'required|string|max:255',
            'description' => 'nullable',
            'is_translatable' => 'boolean',
            'is_private' => 'boolean'
        ];

        return $rules;
    }
}