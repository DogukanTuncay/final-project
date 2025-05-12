<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserNotificationSetting extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'user_id',
        'preferences',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'preferences' => 'array',
    ];

    /**
     * Kullanıcıya ait ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Varsayılan bildirim tercihlerini döndürür
     *
     * @return array
     */
    public static function getDefaultPreferences(): array
    {
        return [
            'all' => true,
            'login_streak' => true,
            'course_reminder' => true,
            'custom' => true,
            'broadcast' => true
        ];
    }

    /**
     * Kullanıcının belirli bir tür bildirim alıp alamayacağını kontrol eder
     *
     * @param string $type
     * @return bool
     */
    public function canReceiveNotificationType(string $type): bool
    {
        $preferences = is_array($this->preferences) ? $this->preferences : self::getDefaultPreferences();
        
        // Önce tüm bildirimlerin açık olup olmadığını kontrol et
        if (isset($preferences['all']) && $preferences['all'] === false) {
            return false;
        }
        
        // Belirli bildirim türünü kontrol et
        if (isset($preferences[$type])) {
            return (bool)$preferences[$type];
        }
        
        return true; // Varsayılan olarak etkinleştirilmiş
    }
} 