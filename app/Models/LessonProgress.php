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
    ];

    protected function casts(): array
    {
        return [
            'is_completed' => 'boolean',
            'last_watched_at' => 'datetime',
        ];
    }
}