<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\HasImage;
use Tymon\JWTAuth\Facades\JWTAuth;

class CourseChapter extends Model
{
    use HasTranslations, HasFactory, SoftDeletes, LogsActivity, HasImage;

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
        'course_id',
        'slug',
        'order',
        'is_active',
        'name',
        'description',
        'meta_title',
        'meta_description',
        'image',
        'images',
        'difficulty'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
        'images' => 'json',
        'difficulty' => 'integer'
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'image_url',
        'images_url',
        'is_completed',
        'missing_prerequisites'
    ];

    /**
     * Model kaydedilmeden önce
     */
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($courseChapter) {
            if (empty($courseChapter->slug)) {
                $courseChapter->slug = Str::slug($courseChapter->getTranslation('name', 'en'));
            }
        });
    }

    /**
     * Bölümün bağlı olduğu kurs
     */
    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Bölümün dersleri
     */
    public function lessons()
    {
        return $this->hasMany(CourseChapterLesson::class)->orderBy('order');
    }

    /**
     * Bu bölümün tamamlanmasını gerektiren görevler (Missions).
     */
    public function missions(): MorphMany
    {
        return $this->morphMany(Mission::class, 'completable');
    }

    /**
     * Bu bölümün ön koşul bölümleri
     */
    public function prerequisites(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseChapter::class,
            'chapter_prerequisites',
            'chapter_id',
            'prerequisite_chapter_id'
        )->select('course_chapters.id', 'course_chapters.name', 'course_chapters.slug', 'course_chapters.order', 'course_chapters.image');
    }

    /**
     * Bu bölümün ön koşulu olduğu bölümler
     */
    public function prerequisiteFor(): BelongsToMany
    {
        return $this->belongsToMany(
            CourseChapter::class,
            'chapter_prerequisites',
            'prerequisite_chapter_id',
            'chapter_id'
        );
    }

    /**
     * Bölümün tamamlanma durumları
     */
    public function completions(): HasMany
    {
        return $this->hasMany(ChapterCompletion::class, 'chapter_id');
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
     * Kullanıcının tamamlamadığı ön koşul bölümlerini döndürür
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
            
            // Kullanıcının tamamladığı bölüm ID'lerini al
            $completedChapterIds = ChapterCompletion::where('user_id', $userId)
                ->whereIn('chapter_id', $prerequisites->pluck('id'))
                ->pluck('chapter_id')
                ->toArray();
            
            // Tamamlanmamış ön koşulları filtrele
            $missingPrerequisites = $prerequisites->filter(function($prerequisite) use ($completedChapterIds) {
                return !in_array($prerequisite->id, $completedChapterIds);
            });
            
            // İsim, id, slug bilgilerini içeren basit dizi formatında döndür
            return $missingPrerequisites->map(function($chapter) {
                return [
                    'id' => $chapter->id,
                    'name' => $chapter->name,
                    'slug' => $chapter->slug
                ];
            })->toArray();
        } catch (\Exception $e) {
            // Token bulunamadı veya geçersiz
            return [];
        }
    }

    /**
     * Chapter'ın tamamlanabilir olup olmadığını kontrol eder
     * 
     * @return bool
     */
    public function isCompletable(): bool
    {
        try {
            $user = JWTAuth::user();
            if (!$user) {
                return false;
            }

            // 1. Ön koşulların tamamlanma kontrolü
            if ($this->prerequisites()->exists()) {
                $prerequisites = $this->prerequisites()->get();
                $completedPrerequisites = ChapterCompletion::where('user_id', $user->id)
                    ->whereIn('chapter_id', $prerequisites->pluck('id'))
                    ->count();

                if ($completedPrerequisites < $prerequisites->count()) {
                    return false;
                }
            }

            // 2. Tüm derslerin tamamlanma kontrolü
            $lessons = $this->lessons()->get();
            if ($lessons->isEmpty()) {
                return false;
            }

            $completedLessons = LessonCompletion::where('user_id', $user->id)
                ->whereIn('lesson_id', $lessons->pluck('id'))
                ->count();

            return $completedLessons === $lessons->count();
        } catch (\Exception $e) {
            return false;
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
            ->useLogName('course_chapter')
            ->setDescriptionForEvent(fn(string $eventName) => "Course Chapter \"{$this->getTranslation('name', 'en', false)}\" (ID: {$this->id}) has been {$eventName}");
    }
}
