<?php

namespace App\Listeners;

use App\Events\ChapterCompleted;
use App\Services\MissionProgressService;
use Illuminate\Support\Facades\Log;

class UpdateMissionProgressOnChapterCompletion
{
    protected MissionProgressService $missionProgressService;

    public function __construct(MissionProgressService $missionProgressService)
    {
        $this->missionProgressService = $missionProgressService;
    }

    public function handle(ChapterCompleted $event): void
    {
        Log::info('ChapterCompleted event handled by UpdateMissionProgressOnChapterCompletion listener.', [
            'user_id' => $event->user->id,
            'chapter_id' => $event->chapter->id
        ]);

        $completedMissionIds = $this->missionProgressService->updateProgress(
            $event->user,
            'ChapterCompleted',
            $event->chapter
        );
    }
} 