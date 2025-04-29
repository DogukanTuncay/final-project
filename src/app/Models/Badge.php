<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Translatable\HasTranslations;
use App\Traits\HasImage;

class Badge extends Model
{
    use HasTranslations, HasImage;

    protected $table = 'badges';
    
    public $translatable = ['name', 'description'];
    
    protected $fillable = [
        'name',
        'description',
        'image',
        'is_active',
        'conditions',
        'condition_logic'
    ];
    
    protected $casts = [
        'name' => 'array',
        'description' => 'array',
        'is_active' => 'boolean',
        'conditions' => 'array',
    ];
    
    /**
     * Otomatik eklenen özellikler
     */
    protected $appends = [
        'image_url'
    ];
    
    /**
     * Bu rozeti kazanan kullanıcılar
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'user_badges')
                    ->withPivot('earned_at')
                    ->withTimestamps();
    }
    
    /**
     * Belirli bir kullanıcının bu rozeti kazanıp kazanmadığını kontrol et
     */
    public function isEarnedByUser(User $user): bool
    {
        return $this->users()->where('user_id', $user->id)->exists();
    }
}
