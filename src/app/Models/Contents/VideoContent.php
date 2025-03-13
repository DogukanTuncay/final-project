<?php

namespace App\Models\Contents;

use App\Models\CourseChapterLessonContent;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class VideoContent extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = [
        'title',
        'description',
        'video_url',
        'provider', // youtube, vimeo, etc.
        'duration',
        'thumbnail',
    ];

    public $translatable = [
        'title',
        'description',
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'duration' => 'integer',
    ];

    protected $appends = [
        'thumbnail_url',
    ];

    /**
     * Bu içerik modeli için ders içeriği ilişkisi
     */
    public function lessonContent(): MorphOne
    {
        return $this->morphOne(CourseChapterLessonContent::class, 'contentable');
    }

    /**
     * Thumbnail URL'ini al
     */
    public function getThumbnailUrlAttribute(): ?string
    {
        return $this->thumbnail ? \Storage::url($this->thumbnail) : null;
    }
} 