<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class UserMissionProgress extends Model
{
    use HasFactory;

    protected $table = 'user_mission_progress';

    protected $fillable = [
        'user_id',
        'mission_id',
        'current_amount',
        'completed_at',
        'xp_reward',
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'current_amount' => 'integer',
        'xp_reward' => 'integer',
    ];

    /**
     * Bu ilerlemenin ait olduğu kullanıcı.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Bu ilerlemenin ait olduğu görev.
     */
    public function mission(): BelongsTo
    {
        return $this->belongsTo(Mission::class);
    }

    /**
     * Görevin tamamlanıp tamamlanmadığını kontrol et.
     */
    public function isCompleted(): bool
    {
        return $this->completed_at !== null;
    }

    /**
     * Görevin bugün tamamlandığını kontrol et
     */
    public function isCompletedToday(): bool
    {
        return $this->completed_at && $this->completed_at->isToday();
    }

    /**
     * Görevin bu hafta tamamlandığını kontrol et
     */
    public function isCompletedThisWeek(): bool
    {
        return $this->completed_at && $this->completed_at->isCurrentWeek();
    }

    /**
     * Görevin ilerleme durumunu sıfırla
     */
    public function resetProgress(): void
    {
        $this->current_amount = 0;
        $this->completed_at = null;
        $this->save();
    }
} 