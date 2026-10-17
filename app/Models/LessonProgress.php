<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LessonProgress extends Model
{
    protected $table = 'lesson_progress';

    protected $fillable = [
        'user_id',
        'course_id',
        'lesson_id',
        'last_position_seconds',
        'duration_seconds',
        'is_completed',
        'last_watched_at',
        'watched_ranges',
        'last_reported_position_seconds',
    ];

    protected $casts = [
        'user_id' => 'integer',
        'course_id' => 'integer',
        'lesson_id' => 'integer',
        'last_position_seconds' => 'integer',
        'duration_seconds' => 'integer',
        'last_reported_position_seconds' => 'integer',
        'is_completed' => 'boolean',
        'last_watched_at' => 'datetime',
        'watched_ranges' => 'array',
    ];
}
