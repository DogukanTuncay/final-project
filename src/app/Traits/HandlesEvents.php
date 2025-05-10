<?php

namespace App\Traits;

use App\Services\Api\EventService;
use App\Http\Resources\EventResource;

trait HandlesEvents
{
    protected function addEvent(string $type, array $data, string $message, string $category = null): void
    {
        app(EventService::class)->addEvent($type, $data, $message, $category);
    }

    protected function getEvents(): array
    {
        return app(EventService::class)->getEvents();
    }

    protected function clearEvents(): void
    {
        app(EventService::class)->clearEvents();
    }



    /**
     * Özel bir event oluşturmak için kısayol metodu
     * 
     * @param string $type Event tipi
     * @param array $data Event verileri
     * @param string $message Kullanıcı dostu mesaj
     * @param string|null $category Kategori
     */
    protected function createEvent(string $type, array $data, string $message, string $category = null): void
    {
        $this->addEvent($type, $data, $message, $category);
    }
} 