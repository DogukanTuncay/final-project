<?php

namespace App\Events;

use App\Models\CourseChapter;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ChapterCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Olayı tetikleyen kullanıcı.
     */
    public User $user;

    /**
     * Tamamlanan bölüm.
     */
    public CourseChapter $chapter;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param CourseChapter $chapter
     */
    public function __construct(User $user, CourseChapter $chapter)
    {
        $this->user = $user;
        $this->chapter = $chapter;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return array<int, \Illuminate\Broadcasting\Channel>|\Illuminate\Broadcasting\Channel
     */
    // public function broadcastOn(): array
    // {
    //     return [
    //         new PrivateChannel('channel-name'),
    //     ];
    // }
} 