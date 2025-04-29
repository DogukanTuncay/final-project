<?php

namespace App\Services;

use App\Models\User;
use App\Models\Mission;
use App\Models\UserMissionProgress;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log; // Loglama için
// Gerekirse MissionCompleted gibi bir event ekleyin
// use App\Events\MissionCompleted;

class MissionProgressService
{
    /**
     * Kullanıcının belirli bir olayla ilgili görevlerdeki ilerlemesini günceller.
     *
     * @param User $user Kullanıcı
     * @param string $eventType Tetikleyen olayın adı (örn: 'LessonCompleted')
     * @param Model|null $relatedModel Olayla doğrudan ilişkili model (örn: tamamlanan Lesson nesnesi)
     */
    public function updateProgress(User $user, string $eventType, Model $relatedModel = null): void
    {
        // Olay tipinin kısa adını al (sınıf adı olarak geldiğinde)
        $eventName = $eventType;
        if (strpos($eventType, '\\') !== false) {
            $eventName = class_basename($eventType);
        }

        Log::info("MissionProgressService: Updating progress for User ID: {$user->id}, Event: {$eventName}", [
            'relatedModel' => $relatedModel ? get_class($relatedModel) . '(' . $relatedModel->id . ')' : null
        ]);

        // 1. Olay türüyle tetiklenen genel görevleri bul (completable olmayanlar)
        $generalMissions = Mission::where('trigger_event', $eventName)
                                  ->whereNull('completable_type')
                                  ->where('is_active', true)
                                  ->get();

        // 2. Olay türü ve ilişkili modelle tetiklenen spesifik görevleri bul
        $specificMissions = collect(); // Boş koleksiyon
        if ($relatedModel) {
            $specificMissions = Mission::where('trigger_event', $eventName)
                                       ->where('completable_type', $relatedModel->getMorphClass()) // Modelin morph class adını al
                                       ->where('completable_id', $relatedModel->id)
                                       ->where('is_active', true)
                                       ->get();
        }

        // İki listeyi birleştir ve benzersiz görevleri al
        $relevantMissions = $generalMissions->merge($specificMissions)->unique('id');

        if ($relevantMissions->isEmpty()) {
            Log::info("MissionProgressService: No relevant missions found for User ID: {$user->id}, Event: {$eventName}");
            return; // İlgili görev yoksa çık
        }

        Log::info("MissionProgressService: Found relevant missions", [
            'mission_ids' => $relevantMissions->pluck('id')->all(),
            'mission_count' => $relevantMissions->count(),
            'mission_names' => $relevantMissions->pluck('title')->all()
        ]);

        // Her bir ilgili görev için ilerlemeyi güncelle
        foreach ($relevantMissions as $mission) {
            $this->processMissionForUser($user, $mission);
        }
    }

    /**
     * Belirli bir görev için kullanıcının ilerlemesini işler.
     *
     * @param User $user
     * @param Mission $mission
     */
    protected function processMissionForUser(User $user, Mission $mission): void
    {
        DB::transaction(function () use ($user, $mission) {
            // Kullanıcının bu görevdeki ilerlemesini al veya oluştur (kilitlemeli)
            $progress = UserMissionProgress::lockForUpdate()->firstOrCreate(
                [
                    'user_id' => $user->id,
                    'mission_id' => $mission->id,
                ],
                [
                    'current_amount' => 0, // Yeni oluşturuluyorsa başlangıç değeri
                ]
            );

            // Görev zaten tamamlanmışsa işlem yapma
            if ($progress->isCompleted()) {
                Log::info("MissionProgressService: Mission ID {$mission->id} already completed for User ID {$user->id}");
                return;
            }

            // İlerlemeyi artır
            $progress->current_amount += 1;

            Log::info("MissionProgressService: Incrementing progress for Mission ID {$mission->id}, User ID {$user->id}. New amount: {$progress->current_amount}");

            // Tamamlanma koşulunu kontrol et
            if ($progress->current_amount >= $mission->required_amount) {
                $progress->completed_at = now();
                Log::info("MissionProgressService: Mission ID {$mission->id} COMPLETED for User ID {$user->id}");
                
                // Görev tamamlama olayını tetikle - Bu kısmı ekledik
                if (class_exists('\App\Events\MissionCompleted')) {
                    event(new \App\Events\MissionCompleted($user, $mission, $progress));
                    Log::info("MissionCompleted event triggered for User ID: {$user->id}, Mission ID: {$mission->id}");
                }

                // İsteğe bağlı: Kullanıcıya XP ekle
                if ($mission->xp_reward > 0) {
                   $user->addExperiencePoints($mission->xp_reward); // User modelinde bu metod varsa
                    Log::info("MissionProgressService: Rewarding {$mission->xp_reward} XP for Mission ID {$mission->id} to User ID {$user->id}");
                }
            }

            $progress->save();

        }, 3); // Hata durumunda 3 kez yeniden dene
    }
} 