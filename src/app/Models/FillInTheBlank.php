<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;
use Illuminate\Support\Str;

class FillInTheBlank extends Model
{
    use HasFactory, HasTranslations, SoftDeletes, LogsActivity;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['question', 'feedback'];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'question',
        'answers',
        'is_active',
        'points',
        'feedback',
        'created_by',
        'case_sensitive'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'answers' => 'array',
        'points' => 'integer',
        'is_active' => 'boolean',
        'case_sensitive' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * The "booted" method of the model.
     */
    protected static function booted(): void
    {
        parent::boot();
       
    }


    /**
     * Soruyu oluşturan kullanıcı
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Bu içerik modeli için ders içeriği ilişkisi
     */
    public function lessonContent(): MorphOne
    {
        return $this->morphOne(CourseChapterLessonContent::class, 'contentable');
    }

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log all fillable attributes
            ->logOnlyDirty() // Only log changes
            ->useLogName('fill_in_the_blank')
            ->setDescriptionForEvent(fn(string $eventName) => "FillInTheBlank Question '{$this->title}' has been {$eventName}");
    }
}