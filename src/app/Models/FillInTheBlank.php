<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class FillInTheBlank extends Model
{
    use HasFactory, HasTranslations;

    protected $fillable = ['question', 'answers'];

    // Hangi alanların çok dilli olduğunu belirtmek için $translatable kullanılır
    public $translatable = ['question', 'answers'];

    protected $casts = [
        'answers' => 'array', // answers alanını array olarak cast etmek
    ];

    /**
     * Bu içerik modeli için ders içeriği ilişkisi
     * Bu metod, içerik modelini bir dersle ilişkilendirmek için kullanılır
     */
    public function lessonContent(): MorphOne
    {
        return $this->morphOne(CourseChapterLessonContent::class, 'contentable');
    }
}