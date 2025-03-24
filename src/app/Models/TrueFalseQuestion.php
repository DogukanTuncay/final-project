<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Spatie\Translatable\HasTranslations;

class TrueFalseQuestion extends Model
{
    use HasFactory, HasTranslations;
    
    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['true_text', 'false_text', 'true_feedback', 'false_feedback'];
    
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'correct_answer',
        'true_text',
        'false_text',
        'true_feedback',
        'false_feedback'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'correct_answer' => 'boolean',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Ana soru içeriği ile ilişki
     */
    public function questionContent(): MorphOne
    {
        return $this->morphOne(QuestionContent::class, 'contentable');
    }
}
