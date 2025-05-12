<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Contact extends Model
{
    /**
     * Toplu atama yapılabilecek alanlar
     */
    protected $fillable = [
        'name',
        'email',
        'message',
        'status',
    ];

    /**
     * Veri tipi dönüşümleri
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
}
