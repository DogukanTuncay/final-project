<?php

namespace App\Services\Api;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

class EventService
{
    private array $events = [];

    /**
     * Bir eventi ekler
     * 
     * @param string $type Event tipi
     * @param array $data Event verileri
     * @param string $message Kullanıcı dostu mesaj
     * @param string|null $category Olay kategorisi (örn: 'experience', 'mission', 'badge')
     * @return void
     */
    public function addEvent(string $type, array $data, string $message, string $category = null): void
    {
        Log::info("EventService: Adding event", [
            'type' => $type,
            'category' => $category ?? $this->getCategoryFromType($type),
            'data' => $data,
            'message' => $message
        ]);
        
        $this->events[] = [
            'type' => $type,
            'category' => $category ?? $this->getCategoryFromType($type),
            'timestamp' => now(),
            'data' => $data,
            'message' => $message
        ];
    }

    /**
     * Olay tipinden kategori çıkarır
     * 
     * @param string $type
     * @return string
     */
    private function getCategoryFromType(string $type): string
    {
        $categoryMap = [
            'mission_completed' => 'mission',
            'lesson_completed' => 'lesson',
            'chapter_completed' => 'chapter',
            'course_completed' => 'course',
            'badge_earned' => 'badge',
            'level_up' => 'experience',
        ];

        return $categoryMap[$type] ?? 'general';
    }

    /**
     * Kayıtlı tüm eventleri döndürür
     * 
     * @return array
     */
    public function getEvents(): array
    {
        return $this->events;
    }

    /**
     * Geçici eventleri temizler
     * 
     * @return void
     */
    public function clearEvents(): void
    {
        $this->events = [];
    }
} 