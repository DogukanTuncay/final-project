<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LessonCompletion extends Model
{
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'user_id',
        'lesson_id',
        'completed_at'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'completed_at' => 'datetime'
    ];

    /**
     * Tamamlanan ders ile ilişki
     */
    public function lesson(): BelongsTo
    {
        return $this->belongsTo(CourseChapterLesson::class, 'lesson_id');
    }

    /**
     * Dersi tamamlayan kullanıcı ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 