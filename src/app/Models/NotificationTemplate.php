<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;

class NotificationTemplate extends Model
{
    use HasFactory, HasTranslations;

    /**
     * Çevirilecek alanlar
     */
    public $translatable = ['title', 'message'];

    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'name',
        'title',
        'message',
        'additional_data',
        'is_active',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'is_active' => 'boolean',
    ];
} 