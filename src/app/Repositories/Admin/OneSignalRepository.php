<?php

namespace App\Repositories\Admin;

use App\Interfaces\Repositories\Admin\OneSignalRepositoryInterface;
use App\Models\NotificationTemplate;
use App\Models\UserNotificationLog;
use OneSignal;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class OneSignalRepository implements OneSignalRepositoryInterface
{
    /**
     * Tüm bildirimleri getirir
     *
     * @param array $filters
     * @param int $perPage
     * @return mixed
     */
    public function getAll(array $filters = [], int $perPage = 15)
    {
        $query = UserNotificationLog::query();

        // Filtreleme işlemleri
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        if (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        }

        if (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        if (isset($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('title', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('message', 'like', '%' . $filters['search'] . '%');
            });
        }

        return $query->orderBy('created_at', 'desc')->get();
    }

    /**
     * ID'ye göre bildirim detayını getirir
     *
     * @param int $id
     * @return mixed
     */
    public function getById(int $id)
    {
        return UserNotificationLog::findOrFail($id);
    }

    /**
     * Tek bir kullanıcıya bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendToUser(array $data): bool
    {
        $user = User::find($data['user_id']);

        if(!$user){
            return false;
        }
        try {
            $response = OneSignal::sendNotificationCustom([
                'contents' => ['en' => $data['message']],
                'headings' => ['en' => $data['title']],
                'include_player_ids' => [$user->onesignal_player_id],
                // 'data' => json_encode($data['additional_data'] ?? []),
            ]);
            

            // Bildirim kaydı oluştur
            if (isset($data['user_id'])) {
                UserNotificationLog::create([
                    'user_id' => $data['user_id'],
                    'notification_type' => 'admin_custom',
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'data' => $data['additional_data'] ?? [],
                    'notification_id' => $response->id ?? null,
                    'sent_at' => now(),
                ]);
            }

            return true;
        } catch (\Exception $e) {
            Log::error('OneSignal kullanıcıya bildirim gönderimi başarısız: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Belirli kullanıcı gruplarına bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendToSegment(array $data): bool
    {
        try {
            $response = OneSignal::sendNotificationToSegment(
                $data['message'],
                $data['segment'],
                null,
                [
                    'headings' => ['en' => $data['title']],
                    'data' => $data['additional_data'] ?? []
                ]
            );

            // Log bildirim bilgilerini veritabanına
           
                UserNotificationLog::create([
                    'notification_type' => 'admin_segment_for_'.$data['segment'],
                    'title' => $data['title'],
                    'message' => $data['message'],
                    'data' => json_encode($data['additional_data'] ?? []),
                    'notification_id' => $response['id'] ?? null,
                    'sent_at' => now(),
                ]);
            

            return true;
        } catch (\Exception $e) {
            Log::error('OneSignal segment bildirim gönderimi başarısız: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Tüm kullanıcılara bildirim gönderir
     *
     * @param array $data
     * @return bool
     */
    public function sendToAll(array $data): bool
    {
        try {
            $response = OneSignal::sendNotificationCustom([
                'contents' => ['en' => $data['message']],
                'headings' => ['en' => $data['title']],
                'included_segments' => ['All'],
                'data' => null,
            ]);

            // Genel bir bildirim kaydı oluştur (user_id olmadan)
            UserNotificationLog::create([
                'notification_type' => 'admin_custom_to_all',
                'title' => $data['title'],
                'message' => $data['message'],
                'data' => json_encode($data['additional_data'] ?? []),
                'notification_id' => $response->id ?? null,
                'sent_at' => now(),
            ]);

            return true;
        } catch (\Exception $e) {
            Log::error('OneSignal toplu bildirim gönderimi başarısız: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bildirim şablonu oluşturur
     *
     * @param array $data
     * @return mixed
     */
    public function createTemplate(array $data)
    {
        return NotificationTemplate::create([
            'name' => $data['name'],
            'title' => $data['title'],
            'message' => $data['message'],
            'additional_data' => json_encode($data['additional_data'] ?? []),
            'is_active' => $data['is_active'] ?? true
        ]);
    }

    /**
     * Bildirim şablonunu günceller
     *
     * @param int $id
     * @param array $data
     * @return mixed
     */
    public function updateTemplate(int $id, array $data)
    {
        $template = NotificationTemplate::findOrFail($id);
        
        $template->update([
            'name' => $data['name'],
            'title' => $data['title'],
            'message' => $data['message'],
            'additional_data' => json_encode($data['additional_data'] ?? []),
            'is_active' => $data['is_active'] ?? $template->is_active
        ]);

        return $template;
    }

    /**
     * Bildirim şablonunu siler
     *
     * @param int $id
     * @return bool
     */
    public function deleteTemplate(int $id): bool
    {
        return NotificationTemplate::findOrFail($id)->delete();
    }

    /**
     * Bildirim iptal eder
     *
     * @param string $notificationId
     * @return bool
     */
    public function cancelNotification(string $notificationId): bool
    {
        try {
            OneSignal::cancelNotification($notificationId);
            
            // İlgili log kaydını güncelle
            UserNotificationLog::where('notification_id', $notificationId)
                ->update(['status' => 'cancelled']);
            
            return true;
        } catch (\Exception $e) {
            Log::error('OneSignal bildirim iptali başarısız: ' . $e->getMessage());
            return false;
        }
    }

    /**
     * Bildirim istatistiklerini getirir
     *
     * @param array $filters
     * @return mixed
     */
    public function getNotificationStatistics(array $filters = [])
    {
        $query = UserNotificationLog::query();

        // Zaman filtresi
        if (isset($filters['date_from']) && isset($filters['date_to'])) {
            $query->whereBetween('created_at', [$filters['date_from'], $filters['date_to']]);
        } elseif (isset($filters['date_from'])) {
            $query->whereDate('created_at', '>=', $filters['date_from']);
        } elseif (isset($filters['date_to'])) {
            $query->whereDate('created_at', '<=', $filters['date_to']);
        }

        // Tip filtresi
        if (isset($filters['type'])) {
            $query->where('type', $filters['type']);
        }

        // İstatistikleri hesapla
        $totalCount = $query->count();
        $byTypeCount = $query->selectRaw('type, count(*) as count')
            ->groupBy('type')
            ->get()
            ->pluck('count', 'type')
            ->toArray();

        // Son 7 günün günlük dağılımı
        $last7DaysStats = $query->selectRaw('DATE(created_at) as date, count(*) as count')
            ->whereDate('created_at', '>=', now()->subDays(7))
            ->groupBy('date')
            ->get()
            ->pluck('count', 'date')
            ->toArray();

        return [
            'total_count' => $totalCount,
            'by_type' => $byTypeCount,
            'last_7_days' => $last7DaysStats
        ];
    }
} 