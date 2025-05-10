<?php

namespace App\Http\Requests\Api;

use App\Http\Requests\BaseRequest;

class AiChatMessageRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'ai_chat_id' => 'required|exists:ai_chats,id',
            'message' => 'required|string|max:1000',
        ];
    }

    public function messages()
    {
        return [
            'ai_chat_id.required' => __('validation.required', ['attribute' => __('ai_chat_message.ai_chat_id')]),
            'ai_chat_id.exists' => __('validation.exists', ['attribute' => __('ai_chat_message.ai_chat_id')]),
            'message.required' => __('validation.required', ['attribute' => __('ai_chat_message.message')]),
            'message.string' => __('validation.string', ['attribute' => __('ai_chat_message.message')]),
            'message.max' => __('validation.max.string', ['attribute' => __('ai_chat_message.message'), 'max' => 1000]),
        ];
    }
}