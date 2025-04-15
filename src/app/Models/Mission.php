<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\Translatable\HasTranslations;
class Mission extends Model
{
    use HasTranslations;
    protected $table = 'missions';

    public $translatable = ['title', 'description'];

    protected $fillable = [
        'title',
        'description',
        'type',
        'requirements',
        'xp_reward',
        'is_active'
    ];

    protected $casts = [
        'title' => 'array',
        'description' => 'array',
        'requirements' => 'array',
        'is_active' => 'boolean',
        'xp_reward' => 'integer'
    ];


    public function users()
    {
        return $this->belongsToMany(User::class)
                    ->withPivot('completed_at')
                    ->withTimestamps();
    }

    public function isCompletedBy(User $user, $date = null)
    {
        $date = $date ?? now()->toDateString();

        return $this->users()
            ->wherePivot('user_id', $user->id)
            ->wherePivot('completed_at', $date)
            ->exists();
    }

}
