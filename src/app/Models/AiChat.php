<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class AiChat extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'user_id',
        'title',
        'is_active'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean'
    ];

    /**
     * Bu sohbete ait mesajlar
     */
    public function messages(): HasMany
    {
        return $this->hasMany(AiChatMessage::class);
    }

    /**
     * Bu sohbetin sahibi olan kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sadece aktif sohbetleri filtreler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Belirli bir kullanıcıya ait sohbetleri getirir
     */
    public function scopeForUser($query, $userId)
    {
        return $query->where('user_id', $userId);
    }
}
