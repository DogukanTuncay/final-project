<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Badge;

class BadgeEarned
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public User $user;
    public Badge $badge;

    /**
     * Create a new event instance.
     */
    public function __construct(User $user, Badge $badge)
    {
        $this->user = $user;
        $this->badge = $badge;
    }
} 