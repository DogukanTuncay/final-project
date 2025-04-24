<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class TrueFalseQuestion extends Model
{
    use HasFactory, HasTranslations, SoftDeletes, LogsActivity;
    
    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['question', 'feedback', 'custom_text'];
    
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'question',
        'correct_answer',
        'custom_text',
        'feedback',
        'points',
        'created_by',
        'is_active'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'correct_answer' => 'boolean',
        'points' => 'integer',
        'is_active' => 'boolean',
        'question' => 'array',
        'custom_text' => 'array',
        'feedback' => 'array',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
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
     * Sadece aktif soruları getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Belirli bir kullanıcının sorularını getir
     */
    public function scopeByUser($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable() // Log all fillable attributes
            ->logOnlyDirty() // Only log changes
            ->useLogName('true_false')
            ->setDescriptionForEvent(fn(string $eventName) => "TrueFalse Question '{$this->question}' has been {$eventName}");
    }
}
