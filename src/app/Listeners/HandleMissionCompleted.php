<?php

namespace App\Listeners;

use App\Events\MissionCompleted;
use App\Models\UserMissionProgress;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Interfaces\Services\Api\NotificationServiceInterface;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class HandleMissionCompleted implements ShouldQueue
{
    use InteractsWithQueue, SerializesModels;

    /**
     * Kuyruktaki işlemin zaman aşımı (saniye)
     */
    public $timeout = 60;

    /**
     * Kuyruktaki işlemin önceliği
     */
    public $priority = 90;  // Yüksek öncelik (düşük değer)

    /**
     * İşlemin commit sonrası çalışması
     */
    public $afterCommit = true;

    /**
     * @var NotificationServiceInterface
     */
    protected $notificationService;

    /**
     * HandleMissionCompleted constructor.
     * 
     * @param NotificationServiceInterface $notificationService
     */
    public function __construct(
        NotificationServiceInterface $notificationService
    ) {
        $this->notificationService = $notificationService;
    }

    /**
     * Handle the event.
     *
     * @param  MissionCompleted  $event
     * @return void
     */
    public function handle(MissionCompleted $event)
    {
        $user = $event->user;
        $mission = $event->mission;
        $progress = $event->progress;

        Log::info("HandleMissionCompleted: İşlem başladı. User ID: {$user->id}, Mission ID: {$mission->id}", [
            'xp_reward' => $event->xpReward,
            'completed_at' => $event->completedAt->toDateTimeString(),
            'progress_id' => $progress ? $progress->id : null,
            'progress_amount' => $progress ? $progress->current_amount : null,
            'event_class' => get_class($event)
        ]);
        
        // Görev ilerlemesini tekrar kontrol et
        if ($progress && !$progress->completed_at) {
            Log::warning("HandleMissionCompleted: Mission progress does not have completed_at set. Setting it now.", [
                'progress_id' => $progress->id,
                'user_id' => $user->id,
                'mission_id' => $mission->id
            ]);
            
            // Progress'i güncelle
            try {
                DB::transaction(function() use ($progress, $mission, $event) {
                    $progress->completed_at = $event->completedAt;
                    $progress->xp_reward = $mission->xp_reward;
                    $progress->save();
                    
                    Log::info("HandleMissionCompleted: Mission progress updated with completed_at and xp_reward");
                });
            } catch (\Exception $e) {
                Log::error("HandleMissionCompleted: Failed to update progress: " . $e->getMessage());
            }
        }

        
        // Burada rozetler, başarılar vb. ile ilgili ek mantık ekleyebilirsiniz
        Log::info("HandleMissionCompleted: İşlem tamamlandı. User ID: {$user->id}, Mission ID: {$mission->id}");
    }
} 