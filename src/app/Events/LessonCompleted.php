<?php

namespace App\Events;

use App\Models\CourseChapterLesson;
use App\Models\User;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class LessonCompleted
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    /**
     * Olayı tetikleyen kullanıcı.
     */
    public User $user;

    /**
     * Tamamlanan ders.
     */
    public CourseChapterLesson $lesson;

    /**
     * Create a new event instance.
     *
     * @param User $user
     * @param CourseChapterLesson $lesson
     */
    public function __construct(User $user, CourseChapterLesson $lesson)
    {
        $this->user = $user;
        $this->lesson = $lesson;
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