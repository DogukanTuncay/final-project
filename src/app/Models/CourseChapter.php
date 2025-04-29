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
use App\Traits\HasImage;

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
        'images'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
        'images' => 'json',
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'image_url',
        'images_url'
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
