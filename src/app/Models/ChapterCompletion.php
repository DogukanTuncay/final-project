<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ChapterCompletion extends Model
{
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'user_id',
        'chapter_id',
        'completed_at'
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'completed_at' => 'datetime'
    ];

    /**
     * Tamamlanan bölüm ile ilişki
     */
    public function chapter(): BelongsTo
    {
        return $this->belongsTo(CourseChapter::class, 'chapter_id');
    }

    /**
     * Bölümü tamamlayan kullanıcı ile ilişki
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
} 