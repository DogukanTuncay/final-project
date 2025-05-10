<?php

namespace App\Listeners;

use App\Events\LessonCompleted;
use App\Services\MissionProgressService;
use Illuminate\Support\Facades\Log;

class UpdateMissionProgressOnLessonCompletion
{
    protected MissionProgressService $missionProgressService;

    /**
     * Create the event listener.
     */
    public function __construct(MissionProgressService $missionProgressService)
    {
        $this->missionProgressService = $missionProgressService;
    }

    /**
     * Handle the event.
     */
    public function handle(LessonCompleted $event): void
    {
        Log::info('LessonCompleted event handled by UpdateMissionProgressOnLessonCompletion listener.', [
            'user_id' => $event->user->id,
            'lesson_id' => $event->lesson->id
        ]);

        // Servis metodunu çağır ve tamamlanan görevleri al
        $completedMissionIds = $this->missionProgressService->updateProgress(
            $event->user,
            'LessonCompleted',
            $event->lesson
        );
    }
} 