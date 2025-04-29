<?php

namespace App\Listeners;

use App\Events\LessonCompleted; // Olayı import et
use App\Services\MissionProgressService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Log;

class UpdateMissionProgressOnLessonCompletion implements ShouldQueue // İsteğe bağlı: Kuyruğa alınabilir
{
    use InteractsWithQueue;

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

        // Servis metodunu çağır
        $this->missionProgressService->updateProgress(
            $event->user,
            'LessonCompleted', // Tam sınıf adı yerine sadece 'LessonCompleted' kullanıyoruz
            $event->lesson
        );
    }
} 