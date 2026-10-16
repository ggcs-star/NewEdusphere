<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Storage;

class Lesson extends Model
{
    use HasFactory;

    protected $fillable = [
        'course_id', 'section_id', 'title', 'lesson_type', 'total_score',
        'video_type', 'video_url', 'duration', 'attachment', 'summary', 'order',
    ];

    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    public function section(): BelongsTo
    {
        return $this->belongsTo(Section::class);
    }

    public function questions(): HasMany
    {
        return $this->hasMany(Question::class)->orderBy('order');
    }

    /**
     * Recomputes and persists total_score from the current questions —
     * call this after any question is added, edited, or removed so the
     * stored total never drifts out of sync.
     */
    public function recalculateTotalScore(): void
    {
        $this->update(['total_score' => $this->questions()->sum('points')]);
    }

    /**
     * Playable embed URL for the lesson's video, or null if there's no
     * video_url or it doesn't match a recognizable YouTube/Vimeo link.
     * Uploaded videos store a relative storage path (not a full URL), so
     * those are resolved through Storage::url() to become playable.
     */
    public function embedUrl(): ?string
    {
        if (! $this->video_url) {
            return null;
        }

        return match ($this->video_type) {
            'youtube' => $this->youtubeEmbedUrl(),
            'vimeo' => $this->vimeoEmbedUrl(),
            'upload' => Storage::url($this->video_url),
            default => $this->video_url,
        };
    }

    private function youtubeEmbedUrl(): ?string
    {
        if (! preg_match('/(?:youtu\.be\/|youtube\.com\/(?:watch\?v=|embed\/|shorts\/))([a-zA-Z0-9_-]{11})/', $this->video_url, $matches)) {
            return null;
        }

        return "https://www.youtube.com/embed/{$matches[1]}";
    }

    private function vimeoEmbedUrl(): ?string
    {
        if (! preg_match('/vimeo\.com\/(\d+)/', $this->video_url, $matches)) {
            return null;
        }

        return "https://player.vimeo.com/video/{$matches[1]}";
    }
}
