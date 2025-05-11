<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Traits\HasImage;

class Story extends Model
{
    use HasTranslations, HasImage;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['title'];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'story_category_id',
        'title',
        'order_column',
        'is_active',
        'image',
        'images',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'title' => 'json',
        'is_active' => 'boolean',
        'images' => 'json',
    ];

    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'image_url',
        'images_url'
    ];

    /**
     * Story'nin ait olduğu kategoriyi döndürür.
     */
    public function storyCategory(): BelongsTo
    {
        return $this->belongsTo(StoryCategory::class);
    }
}
