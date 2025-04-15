<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
class User extends Authenticatable implements JWTSubject,MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable,HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'phone',
        'zip_code',
        'locale',
        'email',
        'password',
        'level_id',
        'experience_points',
    ];

    /**
     * Her zaman yüklenecek ilişkiler
     *
     * @var array
     */
    protected $with = ['level'];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Kullanıcının seviye bilgisi
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }


    public function missions()
    {
        return $this->belongsToMany(Mission::class)
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    public function dailyCompletedMissions($date = null)
    {
        $date = $date ?? now()->toDateString();

        return $this->missions()
            ->wherePivot('completed_at', $date)
            ->get();
    }

    /**
     * Kullanıcının mevcut seviyesi içindeki ilerleme yüzdesi
     */
    public function getLevelProgressAttribute()
    {
        // Eğer kullanıcının seviyesi yoksa 0 döndür
        if (!$this->level) {
            return 0;
        }

        // Seviye aralığı
        $levelRange = $this->level->max_xp - $this->level->min_xp;
        // Eğer seviye aralığı 0 veya daha az ise %100 döndür
        if ($levelRange <= 0) {
            return 100;
        }

        // Kullanıcının mevcut seviyedeki XP'si
        $userLevelXp = $this->experience_points - $this->level->min_xp;
        // Yüzde hesaplama
        $percentage = ($userLevelXp / $levelRange) * 100;

        // Yüzdeyi 0-100 arasında sınırla
        return min(100, max(0, $percentage));
    }

    /**
     * Kullanıcının deneyim puanı ekler ve seviye kontrolü yapar
     *
     * @param int $xpAmount
     * @param string|null $actionType
     * @param int|null $actionId
     * @return array
     */
    public function addExperiencePoints(int $xpAmount, string $actionType = null, int $actionId = null)
    {
        $oldLevel = $this->level;
        $oldXp = $this->experience_points;

        // Deneyim puanını güncelle
        $this->experience_points += $xpAmount;

        // Değişiklikleri kaydet
        $this->save();

        // Observer updated metodunda seviye kontrolü yapacak
        // Ancak burada sonuçları hesaplayıp döndürmemiz gerekiyor
        $newLevel = $this->level;
        $levelChanged = $oldLevel && $newLevel && $oldLevel->id != $newLevel->id;

        return [
            'experience_gained' => $xpAmount,
            'total_experience' => $this->experience_points,
            'level_changed' => $levelChanged,
            'new_level' => $levelChanged ? $newLevel : null,
            'old_level' => $levelChanged ? $oldLevel : null,
        ];
    }
}
