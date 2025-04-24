<?php

namespace App\\Models;

use Illuminate\\Database\\Eloquent\\Factories\\HasFactory;
use Illuminate\\Database\\Eloquent\\Model;
use Spatie\\Activitylog\\Traits\\LogsActivity;
use Spatie\\Activitylog\\LogOptions;

class MatchingQuestionPair extends Model
{
    use HasFactory, LogsActivity;

    public $timestamps = false;

    protected $fillable = [
        'matching_question_id',
        'left_item',
        'right_item',
        'order',
    ];

    public function question()
    {
        return $this->belongsTo(MatchingQuestion::class, 'matching_question_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logFillable()
            ->logOnlyDirty()
            ->useLogName('matching_pair')
            ->setDescriptionForEvent(fn(string $eventName) => "Matching Pair for Question ID {$this->matching_question_id} has been {$eventName}");
    }
} 