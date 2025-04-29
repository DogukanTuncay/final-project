<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationLog extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'user_id',
        'notification_type',
        'title',
        'message',
        'sent_at',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'sent_at' => 'datetime',
    ];

    /**
     * Kullanıcıya ait ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Belirli bir kullanıcının son bildirimleri
     *
     * @param int $userId
     * @param int $limit
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public static function getRecentForUser(int $userId, int $limit = 10)
    {
        return self::where('user_id', $userId)
            ->orderBy('sent_at', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Belirli bir kullanıcının belirli bir türdeki son bildirimini alır
     *
     * @param int $userId
     * @param string $type
     * @return self|null
     */
    public static function getLastNotificationOfType(int $userId, string $type)
    {
        return self::where('user_id', $userId)
            ->where('notification_type', $type)
            ->orderBy('sent_at', 'desc')
            ->first();
    }
} 