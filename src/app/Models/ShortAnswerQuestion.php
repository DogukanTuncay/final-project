<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class ShortAnswerQuestion extends Model
{
    use HasFactory, HasTranslations;
    
    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['question', 'feedback', 'allowed_answers'];
    
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'question',
        'allowed_answers',
        'case_sensitive',
        'max_attempts',
        'points',
        'feedback',
        'created_by',
        'is_active'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'case_sensitive' => 'boolean',
        'max_attempts' => 'integer',
        'points' => 'integer',
        'is_active' => 'boolean',
        'allowed_answers' => 'array',
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
}
