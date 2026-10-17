<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class Setting extends Model
{
    use HasFactory;

    protected $fillable = ['group', 'key', 'value'];

    /**
     * Fallback values for groups the public site reads directly (via the
     * frontend view composer) as well as the admin Settings form — kept
     * here, not duplicated in SettingController, so both always agree on
     * what an unset field defaults to.
     */
    public const DEFAULTS = [
        'system' => [
            'system_name' => 'EduSphere',
            'system_title' => 'EduSphere — Online Learning Platform',
            'website_keywords' => '',
            'website_description' => 'Explore courses, develop new skills and build your future with EduSphere.',
            'author' => '',
            'slogan' => 'Learn · Grow · Succeed',
            'system_email' => 'support@edusphere.in',
            'address' => 'Bhopal, Madhya Pradesh',
            'phone' => '+91 98765 43210',
            'youtube_api_key' => '',
            'vimeo_api_key' => '',
            'student_email_verification' => 'disable',
            'footer_link' => '',
        ],
        'frontend' => [
            'banner_title' => 'Build Your Future With EduSphere',
            'banner_sub_title' => 'Get access to high-quality online courses, expert instructors, and a supportive learning community. Learn new skills, advance your career, and achieve your goals.',
            'about_us' => "<p>EduSphere helps learners discover subjects, explore courses and build knowledge around their goals.</p><p>Whether you are starting something new or developing existing skills, your next learning opportunity starts here.</p>",
            'terms_and_condition' => '',
            'privacy_policy' => '',
        ],
        'theme' => [
            'theme_primary_color' => '#32105f',
            'theme_primary_dark' => '#250b49',
            'theme_primary_light' => '#4a2677',
            'theme_secondary_color' => '#f5bd32',
            'theme_secondary_dark' => '#d8a017',
        ],
    ];

    /**
     * Uploaded image settings (frontend group, but file-based — never part
     * of the plain-text DEFAULTS/update() loop since they need
     * FileUploadService, not a simple Setting::set($request->input(...))).
     */
    public const IMAGE_FIELDS = ['banner_image', 'light_logo', 'dark_logo', 'small_logo', 'favicon'];

    public static function get(string $key, $default = null)
    {
        return Cache::rememberForever("setting:{$key}", function () use ($key, $default) {
            return static::where('key', $key)->value('value') ?? $default;
        });
    }

    public static function set(string $key, $value, string $group = 'system'): void
    {
        static::updateOrCreate(['key' => $key], ['value' => $value, 'group' => $group]);
        Cache::forget("setting:{$key}");
    }

    public static function group(string $group): array
    {
        return static::where('group', $group)->pluck('value', 'key')->all();
    }

    /**
     * A group's stored values merged over its defaults, so every key is
     * always present even if it was never saved yet.
     */
    public static function withDefaults(string $group): array
    {
        return array_merge(self::DEFAULTS[$group] ?? [], static::group($group));
    }
}
