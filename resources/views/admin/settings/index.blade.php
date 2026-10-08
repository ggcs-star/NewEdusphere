@php
    $tabMeta = [
        'system' => ['label' => 'System', 'icon' => 'fa-gear'],
        'frontend' => ['label' => 'Frontend', 'icon' => 'fa-window-maximize'],
        'payment' => ['label' => 'Payment', 'icon' => 'fa-credit-card'],
        'smtp' => ['label' => 'SMTP', 'icon' => 'fa-envelope'],
        'instructor' => ['label' => 'Instructor', 'icon' => 'fa-chalkboard-user'],
    ];
@endphp
<x-layouts.admin title="Settings">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-brand-950">Settings</h2>
        <p class="text-sm text-slate-500 mt-1">Configure how the platform behaves.</p>
    </div>

    <div class="flex flex-col lg:flex-row gap-6">
        {{-- Tab nav --}}
        <div class="lg:w-56 flex-shrink-0">
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-2 flex lg:flex-col gap-1 overflow-x-auto">
                @foreach ($tabs as $t)
                    <a href="{{ route('admin.settings.index', $t) }}"
                        class="flex items-center gap-2.5 rounded-xl px-3.5 py-2.5 text-sm font-medium whitespace-nowrap transition
                        {{ $tab === $t ? 'bg-brand-900 text-white' : 'text-slate-600 hover:bg-brand-50' }}">
                        <i class="fa-solid {{ $tabMeta[$t]['icon'] }} text-xs w-4"></i>
                        {{ $tabMeta[$t]['label'] }}
                    </a>
                @endforeach
            </div>
        </div>

        {{-- Tab content --}}
        <div class="flex-1 rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
            <form action="{{ route('admin.settings.update', $tab) }}" method="POST" class="space-y-4 max-w-xl">
                @csrf

                @if ($tab === 'system')
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">System Name</label>
                        <input type="text" name="system_name" value="{{ $values['system_name'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">System Email</label>
                        <input type="email" name="system_email" value="{{ $values['system_email'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                        <input type="text" name="address" value="{{ $values['address'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                            <input type="text" name="phone" value="{{ $values['phone'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Currency</label>
                            <input type="text" name="system_currency" value="{{ $values['system_currency'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                    </div>
                @endif

                @if ($tab === 'frontend')
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Banner Title</label>
                        <input type="text" name="banner_title" value="{{ $values['banner_title'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Banner Subtitle</label>
                        <input type="text" name="banner_sub_title" value="{{ $values['banner_sub_title'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">About Us</label>
                        <textarea name="about_us" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ $values['about_us'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Terms &amp; Conditions</label>
                        <textarea name="terms_and_condition" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ $values['terms_and_condition'] }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Privacy Policy</label>
                        <textarea name="privacy_policy" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ $values['privacy_policy'] }}</textarea>
                    </div>
                @endif

                @if ($tab === 'payment')
                    <div class="rounded-xl border border-slate-200 p-4">
                        <label class="inline-flex items-center gap-2 mb-3">
                            <input type="hidden" name="paypal_active" value="0">
                            <input type="checkbox" name="paypal_active" value="1" @checked($values['paypal_active'] == '1') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-semibold text-brand-950">Enable PayPal</span>
                        </label>
                        <div class="grid grid-cols-1 gap-3">
                            <input type="text" name="paypal_client_id" placeholder="Client ID" value="{{ $values['paypal_client_id'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <input type="text" name="paypal_secret" placeholder="Secret" value="{{ $values['paypal_secret'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                    </div>
                    <div class="rounded-xl border border-slate-200 p-4">
                        <label class="inline-flex items-center gap-2 mb-3">
                            <input type="hidden" name="stripe_active" value="0">
                            <input type="checkbox" name="stripe_active" value="1" @checked($values['stripe_active'] == '1') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm font-semibold text-brand-950">Enable Stripe</span>
                        </label>
                        <div class="grid grid-cols-1 gap-3">
                            <input type="text" name="stripe_public_key" placeholder="Public Key" value="{{ $values['stripe_public_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <input type="text" name="stripe_secret_key" placeholder="Secret Key" value="{{ $values['stripe_secret_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                    </div>
                @endif

                @if ($tab === 'smtp')
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Host</label>
                            <input type="text" name="smtp_host" value="{{ $values['smtp_host'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Port</label>
                            <input type="text" name="smtp_port" value="{{ $values['smtp_port'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                        <input type="text" name="smtp_username" value="{{ $values['smtp_username'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                        <input type="password" name="smtp_password" value="{{ $values['smtp_password'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Encryption</label>
                        <select name="smtp_encryption" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="tls" @selected($values['smtp_encryption'] == 'tls')>TLS</option>
                            <option value="ssl" @selected($values['smtp_encryption'] == 'ssl')>SSL</option>
                            <option value="none" @selected($values['smtp_encryption'] == 'none')>None</option>
                        </select>
                    </div>
                @endif

                @if ($tab === 'instructor')
                    <label class="inline-flex items-center gap-2">
                        <input type="hidden" name="allow_instructor" value="0">
                        <input type="checkbox" name="allow_instructor" value="1" @checked($values['allow_instructor'] == '1') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-slate-700">Allow users to become instructors and sell courses</span>
                    </label>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Instructor Revenue Share (%)</label>
                        <input type="number" min="0" max="100" name="instructor_revenue_percent" value="{{ $values['instructor_revenue_percent'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        <p class="text-xs text-slate-400 mt-1">The remaining percentage goes to the platform on every sale.</p>
                    </div>
                @endif

                <div class="flex justify-end pt-2">
                    <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                        Save {{ $tabMeta[$tab]['label'] }} Settings
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-layouts.admin>
