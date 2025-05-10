<?php

namespace App\Events;

use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use App\Models\User;
use App\Models\Mission;
use App\Models\UserMissionProgress;

class MissionCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;
    public $mission;
    public $progress;
    public $xpReward;
    public $completedAt;

    /**
     * MissionCompleted constructor.
     *
     * @param User $user
     * @param Mission $mission
     * @param UserMissionProgress $progress
     * @param int $xpReward
     * @param \Carbon\Carbon|null $completedAt
     */
    public function __construct(User $user, Mission $mission, UserMissionProgress $progress, int $xpReward, $completedAt = null)
    {
        $this->user = $user;
        $this->mission = $mission;
        $this->progress = $progress;
        $this->xpReward = $xpReward;
        $this->completedAt = $completedAt ?? now();

    }

    /**
     * Event verilerini hazırla
     *
     * @return array
     */
    public function toArray()
    {
        return [
            'event_type' => 'mission_completed',
            'mission_id' => $this->mission->id,
            'mission_title' => $this->mission->getTranslation('title', app()->getLocale()),
            'mission_type' => $this->mission->type,
            'user_id' => $this->user->id,
            'user_name' => $this->user->name,
            'xp_reward' => $this->xpReward,
            'completed_at' => $this->completedAt->toDateTimeString(),
            'current_amount' => $this->progress ? $this->progress->current_amount : null,
            'required_amount' => $this->mission->required_amount ?? 1,
        ];
    }
} 