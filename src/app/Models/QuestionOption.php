<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class QuestionOption extends Model
{
    use HasFactory, HasTranslations;
    
    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['text', 'feedback'];
    
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'question_id',
        'text',
        'is_correct',
        'order',
        'feedback'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_correct' => 'boolean',
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Bu seçeneğin ait olduğu çoktan seçmeli soru
     */
    public function question()
    {
        return $this->belongsTo(MultipleChoiceQuestion::class, 'question_id');
    }
}
