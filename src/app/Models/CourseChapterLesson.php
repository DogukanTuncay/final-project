<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Translatable\HasTranslations;
use Illuminate\Support\Str;
use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class CourseChapterLesson extends Model 
{
    use HasTranslations, HasImage,HasFactory;

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
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
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
        'is_completed'
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
        if (!Auth::check()) {
            return false;
        }

        return $this->completions()->where('user_id', Auth::id())->exists();
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
}
