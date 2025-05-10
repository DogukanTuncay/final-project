<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Mission extends Model
{
    use HasTranslations;
    protected $table = 'missions';

    public $translatable = ['title', 'description', 'requirements'];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'title',
        'description',
        'type',
        'requirements',
        'xp_reward',
        'is_active',
        'completable_id',
        'completable_type',
        'required_amount',
        'trigger_event',
    ];

    /**
     * Görev tipleri
     */
    const TYPE_ONE_TIME = 'one_time';
    const TYPE_DAILY = 'daily';
    const TYPE_WEEKLY = 'weekly';
    const TYPE_MANUAL = 'manual';

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'requirements' => 'array',
        'is_active' => 'boolean',
        'xp_reward' => 'integer',
        'required_amount' => 'integer',
    ];

    /**
     * Bu görevin tamamlanmasını gerektiren ilişkili model (Course, Chapter vb.).
     */
    public function completable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Bu görevin kullanıcılar üzerindeki ilerlemeleri.
     */
    public function userProgresses(): HasMany
    {
        return $this->hasMany(UserMissionProgress::class);
    }

    /**
     * Bu görevin tamamlanma kayıtları.
     */
    public function completions(): HasMany
    {
        return $this->hasMany(UserMission::class);
    }

    /**
     * Bugün tamamlanmış kayıtlar.
     */
    public function todayCompletions()
    {
        return $this->completions()->whereDate('completed_date', today());
    }

    /**
     * Bu hafta tamamlanmış kayıtlar.
     */
    public function thisWeekCompletions()
    {
        return $this->completions()->whereDate('completed_date', '>=', now()->startOfWeek())
                                   ->whereDate('completed_date', '<=', now()->endOfWeek());
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Belirli bir kullanıcının bu görevdeki ilerlemesi.
     */
    public function getProgressForUser(User $user): ?UserMissionProgress
    {
        return $this->userProgresses()->where('user_id', $user->id)->first();
    }

    /**
     * Belirli bir kullanıcının bu görevi tamamlayıp tamamlamadığını kontrol et.
     */
    public function isCompletedByUser(User $user): bool
    {
        $progress = $this->getProgressForUser($user);
        return $progress ? $progress->isCompleted() : false;
    }
}
