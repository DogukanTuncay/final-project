<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class MatchingQuestion extends Model
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
        'shuffle_items',
        'points',
        'feedback',
        'created_by',
        'is_active'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'shuffle_items' => 'boolean',
        'points' => 'integer',
        'is_active' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Eşleştirme çiftleri
     */
    public function pairs(): HasMany
    {
        return $this->hasMany(MatchingPair::class, 'matching_question_id');
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
}
