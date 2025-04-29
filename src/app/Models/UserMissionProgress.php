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
    ];

    protected $casts = [
        'completed_at' => 'datetime',
        'current_amount' => 'integer',
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
} 