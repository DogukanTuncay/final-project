<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Spatie\Translatable\HasTranslations;

class MultipleChoiceQuestion extends Model
{
    use HasFactory, HasTranslations;
    
    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['question', 'feedback'];
    
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'question',
        'feedback',
        'points',
        'is_multiple_answer',
        'shuffle_options',
        'created_by',
        'is_active'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'points' => 'integer',
        'is_multiple_answer' => 'boolean',
        'shuffle_options' => 'boolean',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Çoktan seçmeli soru seçenekleri
     */
    public function options(): HasMany
    {
        return $this->hasMany(QuestionOption::class, 'question_id');
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
    public function lessonContent(): MorphMany
    {
        return $this->morphMany(CourseChapterLessonContent::class, 'contentable')->cascadeOnDelete();
    }
}
