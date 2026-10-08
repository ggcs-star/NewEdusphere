<?php

namespace App\Http\Controllers\Admin;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SettingController extends BaseAdminController
{
    private const TABS = ['system', 'frontend', 'payment', 'smtp', 'instructor'];

    private const DEFAULTS = [
        'system' => [
            'system_name' => 'EduSphere',
            'system_email' => '',
            'address' => '',
            'phone' => '',
            'system_currency' => 'Rs.',
        ],
        'frontend' => [
            'banner_title' => '',
            'banner_sub_title' => '',
            'about_us' => '',
            'terms_and_condition' => '',
            'privacy_policy' => '',
        ],
        'payment' => [
            'paypal_active' => '0',
            'paypal_client_id' => '',
            'paypal_secret' => '',
            'stripe_active' => '0',
            'stripe_public_key' => '',
            'stripe_secret_key' => '',
        ],
        'smtp' => [
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

    public function index(string $tab = 'system'): View
    {
        if (! in_array($tab, self::TABS)) {
            abort(404);
        }

        $values = array_merge(self::DEFAULTS[$tab], Setting::group($tab));

        return view('admin.settings.index', [
            'tab' => $tab,
            'tabs' => self::TABS,
            'values' => $values,
        ]);
    }

    public function update(Request $request, string $tab): RedirectResponse
    {
        if (! in_array($tab, self::TABS)) {
            abort(404);
        }

        $fields = array_keys(self::DEFAULTS[$tab]);

        return $this->tryAction(function () use ($fields, $request, $tab) {
            foreach ($fields as $field) {
                Setting::set($field, $request->input($field, '0'), $tab);
            }
        }, ucfirst($tab).' settings updated.', 'admin.settings.index', [$tab]);
    }
}
