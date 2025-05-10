<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class MissionsRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'title' => ['required', 'array'],
            'title.*' => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'array'],
            'description.*' => ['nullable', 'string'],
            'type' => ['required', 'in:one_time,daily,weekly,chain,manual'],
            'requirements' => ['sometimes', 'array'],
            'requirements.type' => ['sometimes', 'string'],
            'requirements.value' => ['sometimes'],
            'xp_reward' => ['required', 'integer', 'min:0'],
            'is_active' => ['boolean']
        ];
    }
}
