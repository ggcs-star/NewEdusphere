<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Course extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'category_id',
        'sub_category_id',
        'title',
        'slug',
        'short_description',
        'description',
        'outcomes',
        'requirements',
        'language',
        'level',
        'price',
        'discount_price',
        'is_free',
        'thumbnail',
        'preview_video_url',
        'preview_video_type',
        'is_top_course',
        'meta_keywords',
        'meta_description',
        'status',
        'rejection_reason',
    ];

    protected $casts = [
        'outcomes' => 'array',
        'requirements' => 'array',
        'is_free' => 'boolean',
        'is_top_course' => 'boolean',
        'price' => 'decimal:2',
        'discount_price' => 'decimal:2',
    ];

    public function instructor(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }

    public function subCategory(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'sub_category_id');
    }

    public function sections(): HasMany
    {
        return $this->hasMany(Section::class)->orderBy('order');
    }

    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function thumbnailUrl(): string
    {
        return $this->thumbnail
            ? asset('storage/'.$this->thumbnail)
            : asset('images/category-placeholder.png');
    }

    public function statusColor(): string
    {
        return match ($this->status) {
            'active' => 'emerald',
            'pending' => 'amber',
            'rejected' => 'rose',
            default => 'slate',
        };
    }

    public function hasDiscount(): bool
    {
        return ! $this->is_free && $this->discount_price !== null && $this->discount_price < $this->price;
    }

    public function effectivePrice(): float
    {
        if ($this->is_free) {
            return 0;
        }

        return $this->hasDiscount() ? (float) $this->discount_price : (float) $this->price;
    }

    public function discountPercent(): int
    {
        if (! $this->hasDiscount() || (float) $this->price <= 0) {
            return 0;
        }

        $percent = (($this->price - $this->discount_price) / $this->price) * 100;

        return (int) max(0, round($percent));
    }
}
