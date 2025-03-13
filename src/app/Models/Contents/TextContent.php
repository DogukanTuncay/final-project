<?php

namespace App\Models\Contents;

use App\Models\CourseChapterLessonContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class TextContent extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'content',
    ];

    public $translatable = [
        'content',
    ];

    protected $casts = [
        'content' => 'array',
    ];

    /**
     * Bu içerik modeli için ders içeriği ilişkisi
     */
    public function lessonContent(): MorphOne
    {
        return $this->morphOne(CourseChapterLessonContent::class, 'contentable');
    }
} 