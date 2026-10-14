<?php

namespace App\Http\Controllers\Admin;

use App\Models\Language;
use App\Models\Setting;
use App\Services\FileUploadService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends BaseAdminController
{
    private const TABS = ['system', 'frontend', 'payment', 'smtp', 'instructor', 'languages'];

    private const IMAGE_FIELDS = ['banner_image', 'light_logo', 'dark_logo', 'small_logo', 'favicon'];

    private const DEFAULTS = [
        'system' => [
            'system_name' => 'EduSphere',
            'system_title' => 'EduSphere — Online Learning Platform',
            'website_keywords' => '',
            'website_description' => '',
            'author' => '',
            'slogan' => '',
            'system_email' => '',
            'address' => '',
            'phone' => '',
            'youtube_api_key' => '',
            'vimeo_api_key' => '',
            'purchase_code' => '',
            'student_email_verification' => 'disable',
            'footer_link' => '',
        ],
        'frontend' => [
            'banner_title' => '',
            'banner_sub_title' => '',
            'about_us' => '',
            'terms_and_condition' => '',
            'privacy_policy' => '',
        ],
        'payment' => [
            'system_currency' => 'USD',
            'currency_position' => 'left',
            'paypal_active' => '0',
            'paypal_mode' => 'sandbox',
            'paypal_currency' => 'USD',
            'paypal_sandbox_client_id' => '',
            'paypal_production_client_id' => '',
            'stripe_active' => '0',
            'stripe_testmode' => 'on',
            'stripe_currency' => 'USD',
            'stripe_test_secret_key' => '',
            'stripe_test_public_key' => '',
            'stripe_live_secret_key' => '',
            'stripe_live_public_key' => '',
        ],
        'smtp' => [
            'smtp_protocol' => 'smtp',
            'smtp_host' => '',
            'smtp_port' => '587',
            'smtp_username' => '',
            'smtp_password' => '',
            'smtp_encryption' => 'tls',
        ],
        'instructor' => [
            'allow_instructor' => '1',
            'instructor_revenue_percent' => '70',
        ],
    ];

    public function __construct(private readonly FileUploadService $uploads)
    {
    }

    public function index(string $tab = 'system'): View
    {
        if (! in_array($tab, self::TABS)) {
            abort(404);
        }

        if ($tab === 'languages') {
            return view('admin.settings.index', [
                'tab' => $tab,
                'tabs' => self::TABS,
                'values' => [],
                'languages' => Language::orderBy('is_default', 'desc')->orderBy('name')->get(),
            ]);
        }

        $values = array_merge(self::DEFAULTS[$tab], Setting::group($tab));

        if ($tab === 'frontend') {
            foreach (self::IMAGE_FIELDS as $field) {
                $values[$field] = Setting::get($field);
            }
        }

        $currencies = collect(config('currencies'));

        return view('admin.settings.index', [
            'tab' => $tab,
            'tabs' => self::TABS,
            'values' => $values,
            'languages' => collect(),
            'allCurrencies' => $currencies->map(fn ($c, $code) => "{$code} — {$c['name']}"),
            'paypalCurrencies' => $currencies->filter(fn ($c) => $c['paypal'])->map(fn ($c, $code) => "{$code} — {$c['name']}"),
            'stripeCurrencies' => $currencies->filter(fn ($c) => $c['stripe'])->map(fn ($c, $code) => "{$code} — {$c['name']}"),
        ]);
    }

    public function update(Request $request, string $tab): RedirectResponse
    {
        if (! in_array($tab, self::TABS) || $tab === 'languages') {
            abort(404);
        }

        $fields = array_keys(self::DEFAULTS[$tab]);

        return $this->tryAction(function () use ($fields, $request, $tab) {
            foreach ($fields as $field) {
                Setting::set($field, $request->input($field, '0'), $tab);
            }

            if ($tab === 'frontend') {
                foreach (self::IMAGE_FIELDS as $field) {
                    if ($request->hasFile($field)) {
                        $path = $this->uploads->replace(Setting::get($field), $request->file($field), 'branding');
                        Setting::set($field, $path, 'frontend');
                    }
                }
            }
        }, ucfirst($tab).' settings updated.', 'admin.settings.index', [$tab]);
    }
}
