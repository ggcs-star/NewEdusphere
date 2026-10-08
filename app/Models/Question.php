<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['lesson_id', 'title', 'type', 'options', 'correct_answers', 'order'];

    protected $casts = [
        'options' => 'array',
        'correct_answers' => 'array',
    ];

    public function lesson(): BelongsTo
    {
        return $this->belongsTo(Lesson::class);
    }
}
