<?php

namespace App\Http\Requests\Admin;

use App\Http\Requests\BaseRequest;

class AiChatMessageRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'ai_chat_id' => 'required|exists:ai_chats,id',
            'user_id' => 'required|exists:users,id',
            'message' => 'required|string|max:1000',
            'is_from_ai' => 'required|boolean',
            'is_active' => 'sometimes|boolean',
        ];
    }

    public function messages()
    {
        return [
            'ai_chat_id.required' => __('validation.required', ['attribute' => __('ai_chat_message.ai_chat_id')]),
            'ai_chat_id.exists' => __('validation.exists', ['attribute' => __('ai_chat_message.ai_chat_id')]),
            'user_id.required' => __('validation.required', ['attribute' => __('ai_chat_message.user_id')]),
            'user_id.exists' => __('validation.exists', ['attribute' => __('ai_chat_message.user_id')]),
            'message.required' => __('validation.required', ['attribute' => __('ai_chat_message.message')]),
            'message.string' => __('validation.string', ['attribute' => __('ai_chat_message.message')]),
            'message.max' => __('validation.max.string', ['attribute' => __('ai_chat_message.message'), 'max' => 1000]),
            'is_from_ai.required' => __('validation.required', ['attribute' => __('ai_chat_message.is_from_ai')]),
            'is_from_ai.boolean' => __('validation.boolean', ['attribute' => __('ai_chat_message.is_from_ai')]),
            'is_active.boolean' => __('validation.boolean', ['attribute' => __('ai_chat_message.is_active')]),
        ];
    }
}