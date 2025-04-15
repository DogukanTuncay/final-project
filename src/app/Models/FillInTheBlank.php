<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FillInTheBlank extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['question', 'answers', 'feedback'];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'title',
        'slug',
        'description',
        'text',
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

    
    protected static function boot()
    {
        parent::boot();
        
        // Yeni kayıt oluşturulurken slug otomatik oluşturulur
        static::creating(function ($fillInTheBlank) {
            $fillInTheBlank->slug = Str::slug($fillInTheBlank->getTranslation('question', 'en'));
        });
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