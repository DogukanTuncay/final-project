<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => uniqid('evt_'),  // Benzersiz bir event ID
            'type' => $this->type,
            'category' => $this->category ?? 'general',
            'timestamp' => $this->timestamp,
            'data' => $this->data,
            'message' => $this->message,
            // Özel durumlar için yardımcı alanlar
            'reason' => $this->data['event_reason'] ?? null,
            'source' => $this->data['event_source'] ?? null,
            'xp_reward' => $this->data['xp_reward'] ?? null,
        ];
    }
} 