<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AiChatMessage extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'ai_chat_id',
        'user_id',
        'message',
        'is_from_ai',
        'is_active'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_from_ai' => 'boolean',
        'is_active' => 'boolean'
    ];

    /**
     * Bu mesajın ait olduğu sohbet
     */
    public function chat(): BelongsTo
    {
        return $this->belongsTo(AiChat::class, 'ai_chat_id');
    }

    /**
     * Bu mesajın sahibi olan kullanıcı
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Sadece aktif mesajları filtreler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Kullanıcı mesajlarını filtreler
     */
    public function scopeUserMessages($query)
    {
        return $query->where('is_from_ai', false);
    }

    /**
     * AI mesajlarını filtreler
     */
    public function scopeAiMessages($query)
    {
        return $query->where('is_from_ai', true);
    }
}
