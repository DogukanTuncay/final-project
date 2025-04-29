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
        'preferences' => 'json',
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
            'all' => true, // Tüm bildirimler
            'login_streak' => true, // Giriş streaki bildirimleri
            'course_completion' => true, // Kurs tamamlama bildirimleri
            'course_reminder' => true, // Kurs hatırlatma bildirimleri
            'custom' => true, // Özel bildirimler
            'broadcast' => true, // Toplu bildirimler
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
        $preferences = json_decode($this->preferences, true) ?: self::getDefaultPreferences();
        
        // Önce tüm bildirimlerin açık olup olmadığını kontrol et
        if (!($preferences['all'] ?? true)) {
            return false;
        }
        
        // Sonra spesifik bildirim türünü kontrol et
        return $preferences[$type] ?? true;
    }
} 