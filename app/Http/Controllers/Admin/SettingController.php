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
    private const TABS = ['system', 'frontend', 'theme', 'payment', 'smtp', 'instructor', 'languages'];

    private const DEFAULTS = [
        'system' => Setting::DEFAULTS['system'],
        'frontend' => Setting::DEFAULTS['frontend'],
        'theme' => Setting::DEFAULTS['theme'],
        'payment' => [
            'system_currency' => 'USD',
            'currency_position' => 'left',
            'razorpay_key' => '',
            'razorpay_secret' => '',
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
            foreach (Setting::IMAGE_FIELDS as $field) {
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
        ]);
    }

    public function update(Request $request, string $tab): RedirectResponse
    {
        if (! in_array($tab, self::TABS) || $tab === 'languages') {
            abort(404);
        }

        $fields = array_keys(self::DEFAULTS[$tab]);

        if ($tab === 'theme') {
            $request->validate(
                array_fill_keys($fields, ['required', 'regex:/^#[0-9a-fA-F]{6}$/']),
                array_fill_keys(
                    array_map(fn ($f) => "{$f}.regex", $fields),
                    'Please enter a valid hex color code (e.g. #32105F).'
                )
            );
        }

        return $this->tryAction(function () use ($fields, $request, $tab) {
            foreach ($fields as $field) {
                Setting::set($field, $request->input($field, '0'), $tab);
            }

            if ($tab === 'frontend') {
                foreach (Setting::IMAGE_FIELDS as $field) {
                    if ($request->hasFile($field)) {
                        $path = $this->uploads->replace(Setting::get($field), $request->file($field), 'branding');
                        Setting::set($field, $path, 'frontend');
                    } elseif ($request->boolean("remove_{$field}")) {
                        $this->uploads->delete(Setting::get($field));
                        Setting::set($field, '', 'frontend');
                    }
                }
            }
        }, ucfirst($tab).' settings updated.', 'admin.settings.index', [$tab]);
    }
}
