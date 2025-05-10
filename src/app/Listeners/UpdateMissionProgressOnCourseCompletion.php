<?php

namespace App\Listeners;

use App\Events\CourseCompleted;
use App\Services\MissionProgressService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;

class UpdateMissionProgressOnCourseCompletion implements ShouldQueue
{
    use InteractsWithQueue;

    protected MissionProgressService $missionProgressService;

    public function __construct(MissionProgressService $missionProgressService)
    {
        $this->missionProgressService = $missionProgressService;
    }

    public function handle(CourseCompleted $event): void
    {
        Log::info('CourseCompleted event handled by UpdateMissionProgressOnCourseCompletion listener.', [
            'user_id' => $event->user->id,
            'course_id' => $event->course->id
        ]);

        $completedMissionIds = $this->missionProgressService->updateProgress(
            $event->user,
            'CourseCompleted',
            $event->course
        );
        if (!empty($completedMissionIds)) {
            Session::put('just_completed_missions', $completedMissionIds);
        }
    }
} 