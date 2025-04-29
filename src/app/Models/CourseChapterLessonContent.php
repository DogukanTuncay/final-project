<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Traits\LogsActivity;
use Spatie\Activitylog\LogOptions;

class CourseChapterLessonContent extends Model
{
    use HasFactory, SoftDeletes, LogsActivity;

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
     * Soft delete edilmeyen içerikleri filtrele
     */
    public function scopeWithoutTrashedContentable($query)
    {
        return $query->whereHas('contentable');
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

    /**
     * Configure the options for activity logging.
     */
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['course_chapter_lesson_id', 'contentable_id', 'contentable_type', 'order', 'is_active', 'meta_data'])
            ->logOnlyDirty()
            ->dontSubmitEmptyLogs();
    }

    /**
     * The "booted" method of the model.
     *
     * @return void
     */
    protected static function booted()
    {
        static::creating(function ($model) {
            // Eğer order değeri verilmemişse, bu ders için en yüksek sıraya sahip içeriğin üzerine ekle
            if (empty($model->order)) {
                $maxOrder = self::where('course_chapter_lesson_id', $model->course_chapter_lesson_id)
                    ->max('order');
                $model->order = $maxOrder ? $maxOrder + 1 : 0;
            }
        });
    }
}
