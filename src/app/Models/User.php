<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Carbon\Carbon;
use App\Traits\HasImage;

class User extends Authenticatable implements JWTSubject, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable, HasRoles, LogsActivity, HasImage;

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
        'xp',
        'onesignal_api_key',
        'profile_image',
        'onesignal_player_id',
    ];

    /**
     * Her zaman yüklenecek ilişkiler
     *
     * @var array
     */
    protected $with = ['level'];

    /**
     * Otomatik hesaplanan özellikler
     * 
     * @var array
     */
    protected $appends = [
        'level_progress',
        'current_streak',
        'longest_streak',
        'profile_image_url',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        static::creating(function (User $user) {
            if (empty($user->username)) {
                $user->username = static::generateUniqueUsername($user->email ?? $user->name);
            }
        });
    }

    /**
     * Generate a unique username based on email or name.
     *
     * @param string $source
     * @return string
     */
    protected static function generateUniqueUsername(string $source): string
    {
        if (filter_var($source, FILTER_VALIDATE_EMAIL)) {
            // Use email prefix
            $baseUsername = Str::slug(explode('@', $source)[0], '_');
        } else {
            // Use name
            $baseUsername = Str::slug($source, '_');
        }

        // Handle cases where slug might be empty (e.g., only symbols in name/email prefix)
        if (empty($baseUsername)) {
            $baseUsername = 'user';
        }

        $username = $baseUsername;
        $counter = 1;

        // Check if username exists and append number if needed
        while (static::where('username', $username)->exists()) {
            $username = $baseUsername . '_' . $counter;
            $counter++;
        }

        return $username;
    }

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
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
        'onesignal_player_id' => 'string',
    ];

    /**
     * Kullanıcının seviye bilgisi
     */
    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Kullanıcının görevlerdeki ilerlemesi.
     */
    public function missionProgresses(): HasMany
    {
        return $this->hasMany(UserMissionProgress::class);
    }

    /**
     * Kullanıcının giriş kayıtları
     */
    public function logins(): HasMany
    {
        return $this->hasMany(UserLogin::class);
    }

    /**
     * Kullanıcının ilişkili olduğu tüm görevler (ilerleme üzerinden).
     */
    public function missions(): BelongsToMany
    {
        return $this->belongsToMany(Mission::class, 'user_mission_progress')
                    ->using(UserMissionProgress::class) // İsterseniz özel pivot modeli kullanabilirsiniz
                    ->withPivot('current_amount', 'completed_at')
                    ->withTimestamps();
    }

    /**
     * Kullanıcının kazandığı rozetler
     */
    public function badges(): BelongsToMany
    {
        return $this->belongsToMany(Badge::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }
    
    /**
     * Kullanıcının kazandığı rozetleri getirir
     */
    public function earnedBadges()
    {
        return $this->badges()->wherePivotNotNull('earned_at');
    }

    /**
     * Belirli bir tarihte tamamlanan görevler (Eski fonksiyona benzer ama yeni yapı ile).
     */
    public function completedMissionsOn(string $date)
    {
        return $this->missions()
            ->wherePivotNotNull('completed_at')
            ->whereDate('user_mission_progress.completed_at', $date) // Pivot tablo adını belirtmek önemli
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
    public function addExperiencePoints(int $xpAmount): bool
    {
        if ($xpAmount <= 0) {
            return false; // Eklenecek XP pozitif olmalı
        }

        // Deneyim puanını (xp sütununu) güncelle
        // increment kullanımı atomik işlem sağlar ve daha verimlidir.
        // return $this->increment('xp', $xpAmount);

        // Veya save() kullanarak:
        $this->experience_points += $xpAmount;
        return $this->save();

        // Önceki seviye kontrolü mantığı kaldırıldı.
        // Seviye atlama mantığı gerekiyorsa ayrı bir yerde ele alınmalıdır.
    }

    /**
     * Kullanıcının mevcut streak'ini hesaplar
     * Streak, kullanıcının kesintisiz olarak kaç gün uygulamaya giriş yaptığını gösterir
     * 
     * @return int
     */
    public function getCurrentStreakAttribute(): int
    {
        $loginDates = $this->logins()
            ->orderBy('login_date', 'desc')
            ->pluck('login_date')
            ->map(function($date) { 
                return Carbon::parse($date)->startOfDay(); 
            })
            ->toArray();
        
        if (empty($loginDates)) {
            return 0;
        }
        
        $today = Carbon::today();
        $yesterday = Carbon::yesterday();
        
        // Son giriş bugün veya dün değilse, streak kırılmıştır
        if ($loginDates[0]->ne($today) && $loginDates[0]->ne($yesterday)) {
            return 0;
        }
        
        $streak = 1; // En azından bir gün giriş yapmış
        
        for ($i = 0; $i < count($loginDates) - 1; $i++) {
            $diff = $loginDates[$i]->diffInDays($loginDates[$i + 1]);
            
            // Ardışık günlerse streak'i arttır
            if ($diff === 1) {
                $streak++;
            } else {
                break; // Ardışık olmayan bir gün bulduğumuzda durakla
            }
        }
        
        return $streak;
    }
    
    /**
     * Kullanıcının en uzun streak'ini hesaplar
     * 
     * @return int
     */
    public function getLongestStreakAttribute(): int
    {
        $loginDates = $this->logins()
            ->orderBy('login_date')
            ->pluck('login_date')
            ->map(function($date) { 
                return Carbon::parse($date)->startOfDay(); 
            })
            ->toArray();
        
        if (empty($loginDates)) {
            return 0;
        }
        
        $longestStreak = 1;
        $currentStreak = 1;
        
        for ($i = 0; $i < count($loginDates) - 1; $i++) {
            $diff = $loginDates[$i]->diffInDays($loginDates[$i + 1]);
            
            if ($diff === 1) {
                $currentStreak++;
                $longestStreak = max($longestStreak, $currentStreak);
            } else if ($diff > 1) {
                $currentStreak = 1;
            }
        }
        
        return $longestStreak;
    }

    /**
     * Bir kullanıcının giriş kayıtlarını bugün için kaydeder
     * 
     * @return UserLogin
     */
    public function recordLoginToday()
    {
        return UserLogin::recordLoginToday($this->id);
    }

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logAll() // Log all attributes
            ->logOnlyDirty() // Only log changes
            ->useLogName('user')
            ->setDescriptionForEvent(fn(string $eventName) => "User {$this->name} ({$this->email}) has been {$eventName}");
    }

    /**
     * Kullanıcının bildirim ayarları
     */
    public function notificationSettings()
    {
        return $this->hasOne(UserNotificationSetting::class);
    }

    /**
     * Kullanıcının bildirim logları
     */
    public function notificationLogs()
    {
        return $this->hasMany(UserNotificationLog::class);
    }
}
