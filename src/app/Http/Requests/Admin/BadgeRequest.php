<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class BadgeRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => ['required', 'array'],
            'name.*' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'image' => ['sometimes', 'nullable', 'image', 'mimes:jpeg,png,jpg,gif', 'max:2048'],
            'is_active' => ['boolean'],
            'conditions' => ['required', 'array'],
            'condition_logic' => ['required', 'in:all,any']
        ];
    }
}