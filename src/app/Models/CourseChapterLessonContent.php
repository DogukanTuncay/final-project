<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CourseChapterLessonContent extends Model
{
    use HasFactory;

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'course_chapter_lesson_id',
        'contentable_id',
        'contentable_type',
        'order',
        'is_active',
        'meta_data',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
        'meta_data' => 'array',
        'order' => 'integer',
    ];

    /**
     * Bu içeriğin bağlı olduğu ders
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseChapterLesson::class, 'course_chapter_lesson_id');
    }

    /**
     * İçeriğin polimorfik ilişkisi
     * Bu metod, farklı içerik türlerine (metin, video, quiz vb.) bağlanabilir
     */
    public function contentable(): MorphTo
    {
        return $this->morphTo();
    }

    /**
     * Sadece aktif içerikleri getir
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Belirli bir derse ait içerikleri getir
     */
    public function scopeByLesson($query, $lessonId)
    {
        return $query->where('course_chapter_lesson_id', $lessonId);
    }

    /**
     * İçerikleri sıralama numarasına göre sırala
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('order');
    }

    /**
     * Belirli bir içerik türüne ait içerikleri getir
     * 
     * @param \Illuminate\Database\Eloquent\Builder $query
     * @param string $type Sınıf adı (örn. App\Models\Contents\TextContent::class)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeOfType($query, string $type)
    {
        return $query->where('contentable_type', $type);
    }
}
