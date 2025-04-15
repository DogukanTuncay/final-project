<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Translatable\HasTranslations;

class MatchingPair extends Model
{
    use HasFactory, HasTranslations;
    
    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['left_item', 'right_item'];
    
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'matching_question_id',
        'left_item',
        'right_item',
        'order'
    ];
    
    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'order' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    
    /**
     * Bu çiftin ait olduğu eşleştirme sorusu
     */
    public function question(): BelongsTo
    {
        return $this->belongsTo(MatchingQuestion::class, 'matching_question_id');
    }
} 