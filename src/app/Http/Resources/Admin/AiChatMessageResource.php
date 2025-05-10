<?php

namespace App\Http\Resources\Admin;

use App\Http\Resources\BaseResource;

class AiChatMessageResource extends BaseResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'ai_chat_id' => $this->ai_chat_id,
            'user_id' => $this->user_id,
            'message' => $this->message,
            'is_from_ai' => $this->is_from_ai,
            'is_active' => $this->is_active,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => $this->when($this->relationLoaded('user'), function () {
                return [
                    'id' => $this->user->id,
                    'name' => $this->user->name,
                    'email' => $this->user->email,
                ];
            }),
            'chat' => $this->when($this->relationLoaded('chat'), function () {
                return [
                    'id' => $this->chat->id,
                    'title' => $this->chat->title,
                    'user_id' => $this->chat->user_id,
                ];
            }),
        ];
    }
}