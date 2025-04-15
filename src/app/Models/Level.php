<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Level extends Model
{
    use HasTranslations, HasFactory;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = [
        'title',
        'description'
    ];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'level_number',
        'title',
        'description',
        'min_xp',
        'max_xp',
        'icon',
        'color_code',
        'is_active'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'level_number' => 'integer',
        'min_xp' => 'integer',
        'max_xp' => 'integer',
        'is_active' => 'boolean',
        'title' => 'array',
        'description' => 'array'
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'icon_url'
    ];

    /**
     * Bu seviyeye sahip kullanıcılar
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    /**
     * Seviye ikon URL'si
     */
    public function getIconUrlAttribute(): ?string
    {
        return $this->icon ? asset($this->icon) : null;
    }

    /**
     * Level progression range
     */
    public function getXpRangeAttribute(): int
    {
        return $this->max_xp - $this->min_xp;
    }

    /**
     * Aktif seviyeler için scope
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Seviye numarasına göre sırala
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('level_number');
    }

    /**
     * XP miktarına göre uygun seviyeyi bul
     */
    public static function findForXp($xpAmount)
    {
        return self::where('min_xp', '<=', $xpAmount)
            ->where('max_xp', '>', $xpAmount)
            ->active()
            ->first();
    }
} 