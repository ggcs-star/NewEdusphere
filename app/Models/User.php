<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
        'phone',
        'biography',
        'avatar',
        'short_title',
        'facebook_link',
        'twitter_link',
        'linkedin_link',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_instructor' => 'boolean',
        'status' => 'boolean',
    ];

    /**
     * `role` is the single source of truth for admin/instructor/user — a
     * row can never be more than one of the three. `is_instructor` is kept
     * in sync automatically here purely so the many existing call sites
     * that still read it as a flag keep working; it's no longer settable
     * independently (removed from $fillable above).
     */
    protected static function booted(): void
    {
        static::saving(function (User $user) {
            $user->is_instructor = $user->role === 'instructor';
        });
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isInstructor(): bool
    {
        return $this->role === 'instructor';
    }

    public function isStudent(): bool
    {
        return $this->role === 'user';
    }

    public function avatarUrl(): ?string
    {
        return $this->avatar ? Storage::url($this->avatar) : null;
    }

    /**
     * Only the social links that are actually set, keyed by icon name —
     * so views can just loop over whatever exists instead of repeating
     * an @if for every platform.
     */
    public function socialLinks(): array
    {
        $normalize = fn (?string $url) => $url && ! preg_match('#^https?://#i', $url)
            ? "https://{$url}"
            : $url;

        return array_filter([
            'facebook' => $normalize($this->facebook_link),
            'twitter' => $normalize($this->twitter_link),
            'linkedin' => $normalize($this->linkedin_link),
        ]);
    }

    public function courses(): HasMany
    {
        return $this->hasMany(Course::class);
    }

    public function enrollments(): HasMany
    {
        return $this->hasMany(Enrollment::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}