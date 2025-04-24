<?php

namespace App\Models;

use App\Traits\HasImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class Course extends Model
{
    use HasImage, HasTranslations, HasFactory, SoftDeletes, LogsActivity;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = [
        'name',
        'short_description',
        'description',
        'objectives',
        'meta_title',
        'meta_description'
    ];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'slug',
        'image',
        'images',
        'is_active',
        'order',
        'category',
        'difficulty',
        'is_featured',
        'name',
        'short_description',
        'description',
        'objectives',
        'meta_title',
        'meta_description',
        'level_id',
        'category_id',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'images' => 'array',
        'objectives' => 'array',
        'name' => 'array',
        'short_description' => 'array',
        'description' => 'array',
        'meta_title' => 'array',
        'meta_description' => 'array'
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'image_url',           // Ana görselin tam URL'i
        'images_url',          // Ek görsellerin tam URL'leri
        'completion_status'    // Kullanıcının tamamlama durumu
    ];

    /**
     * Sabit kategori listesi
     */
    public const CATEGORIES = [
        'aqidah' => 'Akaid',      // İnanç esasları
        'worship' => 'İbadet',    // İbadetler
        'seerah' => 'Siyer',      // Hz. Muhammed'in hayatı
        'quran' => 'Kur\'an',     // Kur'an eğitimi
        'hadith' => 'Hadis',      // Hadis eğitimi
        'ethics' => 'Ahlak',      // İslami ahlak
        'fiqh' => 'Fıkıh'         // İslam hukuku
    ];

    /**
     * Sabit zorluk seviyeleri
     */
    public const DIFFICULTIES = [
        'basic' => 'Temel',
        'intermediate' => 'Orta',
        'advanced' => 'İleri'
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        parent::boot();

        static::creating(function (Course $course) {
            if (empty($course->slug)) {
                $source = $course->getTranslation('name', 'en', false) ?: (is_array($course->name) ? reset($course->name) : 'course');
                $course->slug = static::generateUniqueSlug($source);
            }
        });

        static::updating(function (Course $course) {
            if ($course->isDirty('name') && !$course->isDirty('slug')) {
                $source = $course->getTranslation('name', 'en', false) ?: (is_array($course->name) ? reset($course->name) : 'course');
                $course->slug = static::generateUniqueSlug($source, $course->id);
            }
        });
    }

    /**
     * Generate a unique slug.
     *
     * @param string $name
     * @param int|null $ignoreId
     * @return string
     */
    protected static function generateUniqueSlug(string $name, int $ignoreId = null): string
    {
        $baseSlug = Str::slug($name);
        if(empty($baseSlug)) $baseSlug = 'course';
        $slug = $baseSlug;
        $counter = 1;

        $query = static::where('slug', $slug);
        if ($ignoreId) {
            $query->where('id', '!=', $ignoreId);
        }

        while ($query->exists()) {
            $slug = $baseSlug . '-' . $counter;
            $counter++;
            $query = static::where('slug', $slug);
             if ($ignoreId) {
                $query->where('id', '!=', $ignoreId);
            }
        }

        return $slug;
    }

    /**
     * Kategori adını insan dostu formatta döndürür
     */
    public function getCategoryTextAttribute(): string
    {
        return self::CATEGORIES[$this->category] ?? $this->category;
    }

    /**
     * Zorluk seviyesini insan dostu formatta döndürür
     */
    public function getDifficultyTextAttribute(): string
    {
        return self::DIFFICULTIES[$this->difficulty] ?? $this->difficulty;
    }

    /*
      Kullanıcının kurs tamamlama durumunu döndürür

    public function getCompletionStatusAttribute(): array
    {
        if (!auth()->check()) {
            return [
                'completed' => false,
                'progress' => 0,
                'total_lessons' => $this->lessons()->count(),
                'completed_lessons' => 0
            ];
        }

        $totalLessons = $this->lessons()->count();
        $completedLessons = $this->lessons()
            ->whereHas('completions', function ($query) {
                $query->where('user_id', auth()->id());
            })->count();

        return [
            'completed' => $totalLessons > 0 && $totalLessons === $completedLessons,
            'progress' => $totalLessons > 0 ? ($completedLessons / $totalLessons) * 100 : 0,
            'total_lessons' => $totalLessons,
            'completed_lessons' => $completedLessons
        ];
    }
     */
    /*
     * Kursun dersleri ile ilişkisi

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
    }
     */
    /**
     * Kursun bölümleri ile ilişkisi
     */
    public function chapters(): HasMany
    {
        return $this->hasMany(CourseChapter::class)->orderBy('order');
    }

    /**
     * Sadece aktif kursları filtreler
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Sadece öne çıkan kursları filtreler
     */
    public function scopeFeatured($query)
    {
        return $query->where('is_featured', true);
    }

    /**
     * Kategoriye göre filtreler
     */
    public function scopeByCategory($query, string $category)
    {
        return $query->where('category', $category);
    }

    /**
     * Zorluk seviyesine göre filtreler
     */
    public function scopeByDifficulty($query, string $difficulty)
    {
        return $query->where('difficulty', $difficulty);
    }

    /**
     * Sıralamaya göre getirir
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    public function category()
    {
        return $this->belongsTo(CourseCategory::class, 'category_id');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        $name = is_array($this->name) ? ($this->name['en'] ?? reset($this->name)) : $this->name;
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('course')
            ->setDescriptionForEvent(fn(string $eventName) => "Course '{$name}' has been {$eventName}");
    }
}