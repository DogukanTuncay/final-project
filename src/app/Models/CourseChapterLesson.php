<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;
use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CourseChapterLesson extends Model 
{
    use HasTranslations, HasImage, HasFactory, SoftDeletes, LogsActivity;

    /**
     * Çevirilecek alanlar
     */
    public array $translatable = [
        'name',
        'description',
        'meta_title',
        'meta_description'
    ];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'course_chapter_id',
        'slug',
        'name',
        'description',
        'meta_title',
        'meta_description',
        'order',
        'is_active',    
        'thumbnail',
        'duration',
        'is_free',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_free' => 'boolean',
        'duration' => 'integer',
        'order' => 'integer',
        'name' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array'
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'thumbnail_url',
        'is_completed',
        'missing_prerequisites'
    ];

    /**
     * Model kaydedilmeden önce
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($courseChapterLesson) {
            if (empty($courseChapterLesson->slug)) {
                $courseChapterLesson->slug = Str::slug($courseChapterLesson->getTranslation('name', 'en'));
            }
        });
    }

    /**
     * Dersin bağlı olduğu bölüm
     */
    public function courseChapter(): BelongsTo
    {
        return $this->belongsTo(CourseChapter::class);
    }

    /**
     * Dersin tamamlanma durumları
     */
    public function completions(): HasMany
    {
        return $this->hasMany(LessonCompletion::class, 'lesson_id');
    }

    /**
     * Giriş yapmış kullanıcı için tamamlanma durumu
     */
    public function getIsCompletedAttribute(): bool
    {
        try {
            $user = JWTAuth::user();
            if (!$user) {
                return false;
            }

            return $this->completions()->where('user_id', $user->id)->exists();
        } catch (\Exception $e) {
            // Token bulunamadı veya geçersiz
            return false;
        }
    }

    /**
     * Sadece aktif dersleri filtreler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Bölüme göre filtreler
     */
    public function scopeByChapter($query, int $chapterId)
    {
        return $query->where('course_chapter_id', $chapterId);
    }

    /**
     * Sıralamaya göre getirir
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Thumbnail URL'ini al
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? Storage::url($this->thumbnail) : null;
    }

    /**
     * Dersin içerikleri
     */
    public function contents(): HasMany
    {
        return $this->hasMany(CourseChapterLessonContent::class)->orderBy('order');
    }

    /**
     * Dersin aktif içerikleri
     */
    public function activeContents(): HasMany
    {
        return $this->hasMany(CourseChapterLessonContent::class)
            ->where('is_active', true)
            ->orderBy('order');
    }

    /**
     * Bu dersin ön koşul dersleri
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseChapterLesson::class,
            'lesson_prerequisites',
            'lesson_id',
            'prerequisite_lesson_id'
        );
    }

    /**
     * Bu dersin ön koşulu olduğu dersler
     */
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseChapterLesson::class,
            'lesson_prerequisites',
            'prerequisite_lesson_id',
            'lesson_id'
        );
    }

    /**
     * Kullanıcının tamamlamadığı ön koşul derslerini döndürür
     * 
     * @return array
     */
    public function getMissingPrerequisitesAttribute(): array
    {
        // Kullanıcı giriş yapmamışsa boş dizi döndür
        try {
            $user = JWTAuth::user();
            if (!$user) {
                return [];
            }
            
            $userId = $user->id;
            
            // Ön koşul yoksa boş dizi döndür
            if (!$this->prerequisites()->exists()) {
                return [];
            }
            
            // Tüm ön koşulları al
            $prerequisites = $this->prerequisites()->get();
            
            // Kullanıcının tamamladığı ders ID'lerini al
            $completedLessonIds = LessonCompletion::where('user_id', $userId)
                ->whereIn('lesson_id', $prerequisites->pluck('course_chapter_lessons.id'))
                ->pluck('lesson_id')
                ->toArray();
            
            // Tamamlanmamış ön koşulları filtrele
            $missingPrerequisites = $prerequisites->filter(function($prerequisite) use ($completedLessonIds) {
                return !in_array($prerequisite->id, $completedLessonIds);
            });
            
            // İsim, id, slug bilgilerini içeren basit dizi formatında döndür
            return $missingPrerequisites->map(function($lesson) {
                return [
                    'id' => $lesson->id,
                    'name' => $lesson->name,
                    'slug' => $lesson->slug
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Token bulunamadı veya geçersiz
            return [];
        }
    }

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log all fillable attributes
            ->logOnlyDirty() // Only log changes
            ->useLogName('course_chapter_lesson')
            ->setDescriptionForEvent(fn(string $eventName) => "Lesson '{$this->name}' has been {$eventName}");
    }

}

