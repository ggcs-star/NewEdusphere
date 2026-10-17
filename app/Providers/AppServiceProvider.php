<?php

namespace App\Providers;

use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;
use Laravel\Passport\Passport;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Schema::defaultStringLength(191);

        Passport::enablePasswordGrant();

        // Admin-configured site settings (Settings -> System / Frontend),
        // available as $siteSettings wherever the public site needs them —
        // this is what makes those admin edits actually show up live.
        View::composer(
            ['layouts.frontend', 'welcome', 'components.home.hero', 'components.home.about', 'frontend.legal'],
            function ($view) {
                // The site's social links live on the admin's own profile
                // (Settings has no separate "Social Links" section) — the
                // topbar icons on the public site use whatever's set there.
                $admin = User::where('role', 'admin')->first();
                $socialLinks = $admin?->socialLinks() ?? [];

                $view->with('siteSettings', array_merge(
                    Setting::withDefaults('system'),
                    Setting::withDefaults('frontend'),
                    Setting::withDefaults('theme'),
                    array_combine(Setting::IMAGE_FIELDS, array_map(
                        fn (string $field) => Setting::get($field, ''),
                        Setting::IMAGE_FIELDS,
                    )),
                    [
                        'facebook_link' => $socialLinks['facebook'] ?? '',
                        'twitter_link' => $socialLinks['twitter'] ?? '',
                        'linkedin_link' => $socialLinks['linkedin'] ?? '',
                    ],
                ));
            }
        );
    }
}