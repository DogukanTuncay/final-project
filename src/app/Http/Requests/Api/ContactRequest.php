<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class ContactRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'message' => 'required|string',
        ];
    }
}