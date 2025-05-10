<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMission extends Model
{
    use HasFactory;

    protected $table = 'user_missions';

    protected $fillable = [
        'user_id',
        'mission_id',
        'xp_earned',
        'completed_date',
    ];

    protected $casts = [
        'completed_date' => 'date',
        'xp_earned' => 'integer',
    ];

    /**
     * Bu kaydın ait olduğu kullanıcı.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bu kaydın ait olduğu görev.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Belirtilen tarihte tamamlanıp tamamlanmadığını kontrol et
     */
    public function isCompletedOnDate($date): bool
    {
        return $this->completed_date->isSameDay($date);
    }

    /**
     * Bugün tamamlanıp tamamlanmadığını kontrol et
     */
    public function isCompletedToday(): bool
    {
        return $this->completed_date->isToday();
    }

    /**
     * Bu hafta tamamlanıp tamamlanmadığını kontrol et
     */
    public function isCompletedThisWeek(): bool
    {
        return $this->completed_date->isCurrentWeek();
    }
} 