@php
    $tabMeta = [
        'system' => ['label' => 'System', 'icon' => 'fa-gear'],
        'frontend' => ['label' => 'Frontend', 'icon' => 'fa-window-maximize'],
        'payment' => ['label' => 'Payment', 'icon' => 'fa-credit-card'],
        'smtp' => ['label' => 'SMTP', 'icon' => 'fa-envelope'],
        'instructor' => ['label' => 'Instructor', 'icon' => 'fa-chalkboard-user'],
        'languages' => ['label' => 'Languages', 'icon' => 'fa-language'],
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
            @if ($tab === 'languages')
                <div class="max-w-xl space-y-6">
                    <div>
                        <h3 class="font-semibold text-brand-950 mb-4">Languages</h3>
                        <div class="rounded-xl border border-slate-200 overflow-hidden">
                            <table class="w-full text-sm">
                                <thead>
                                    <tr class="bg-brand-50/60 text-left text-xs font-semibold uppercase tracking-wide text-brand-700">
                                        <th class="px-4 py-2.5">Name</th>
                                        <th class="px-4 py-2.5">Code</th>
                                        <th class="px-4 py-2.5 text-right">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-slate-100">
                                    @forelse ($languages as $language)
                                        <tr>
                                            <td class="px-4 py-2.5 font-medium text-brand-950">
                                                {{ $language->name }}
                                                @if ($language->is_default)
                                                    <x-admin.badge color="brand">Default</x-admin.badge>
                                                @endif
                                            </td>
                                            <td class="px-4 py-2.5 text-slate-500">{{ $language->code }}</td>
                                            <td class="px-4 py-2.5 text-right">
                                                @unless ($language->is_default)
                                                    <form action="{{ route('admin.languages.destroy', $language) }}" method="POST" onsubmit="return confirm('Remove this language?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="h-7 w-7 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 inline-flex items-center justify-center" title="Delete">
                                                            <i class="fa-solid fa-trash text-xs"></i>
                                                        </button>
                                                    </form>
                                                @endunless
                                            </td>
                                        </tr>
                                    @empty
                                        <tr>
                                            <td colspan="3" class="px-4 py-6 text-center text-slate-400">No languages yet.</td>
                                        </tr>
                                    @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>

                    <div>
                        <h3 class="font-semibold text-brand-950 mb-3">Add Language</h3>
                        <form action="{{ route('admin.languages.store') }}" method="POST" class="flex items-end gap-3">
                            @csrf
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Language Name</label>
                                <input type="text" name="name" required placeholder="e.g. Spanish" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            </div>
                            <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                                Add
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <form action="{{ route('admin.settings.update', $tab) }}" method="POST" enctype="multipart/form-data" class="space-y-4 max-w-xl">
                    @csrf

                    @if ($tab === 'system')
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Website Name</label>
                            <input type="text" name="system_name" value="{{ $values['system_name'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Website Title</label>
                            <input type="text" name="system_title" value="{{ $values['system_title'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Slogan</label>
                            <input type="text" name="slogan" value="{{ $values['slogan'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Website Keywords <span class="text-slate-400 font-normal">(comma separated)</span></label>
                            <input type="text" name="website_keywords" value="{{ $values['website_keywords'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Website Description</label>
                            <textarea name="website_description" rows="3" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ $values['website_description'] }}</textarea>
                        </div>
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Author</label>
                                <input type="text" name="author" value="{{ $values['author'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                                <input type="text" name="phone" value="{{ $values['phone'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">System Email</label>
                            <input type="email" name="system_email" value="{{ $values['system_email'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Address</label>
                            <textarea name="address" rows="2" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ $values['address'] }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">
                                YouTube API Key
                                <a href="https://developers.google.com/youtube/v3/getting-started" target="_blank" class="text-xs font-normal text-brand-600 hover:underline">(get one)</a>
                            </label>
                            <input type="text" name="youtube_api_key" value="{{ $values['youtube_api_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Vimeo API Key</label>
                            <input type="text" name="vimeo_api_key" value="{{ $values['vimeo_api_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Purchase Code</label>
                            <input type="text" name="purchase_code" value="{{ $values['purchase_code'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Student Email Verification</label>
                            <x-admin.searchable-select
                                name="student_email_verification"
                                :options="['enable' => 'Enable', 'disable' => 'Disable']"
                                :selected="$values['student_email_verification']"
                            />
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Footer Link</label>
                            <input type="text" name="footer_link" value="{{ $values['footer_link'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
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

                        <div class="pt-2 border-t border-slate-100">
                            <h3 class="font-semibold text-brand-950 mb-4">Branding Images</h3>
                            <div class="grid grid-cols-2 sm:grid-cols-3 gap-4">
                                @foreach ([
                                    'banner_image' => 'Banner Image',
                                    'light_logo' => 'Light Logo',
                                    'dark_logo' => 'Dark Logo',
                                    'small_logo' => 'Small Logo',
                                    'favicon' => 'Favicon',
                                ] as $field => $label)
                                    <div>
                                        <div class="h-20 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden mb-2">
                                            @if ($values[$field])
                                                <img src="{{ Storage::url($values[$field]) }}" class="max-h-full max-w-full object-contain">
                                            @else
                                                <i class="fa-regular fa-image text-slate-300 text-xl"></i>
                                            @endif
                                        </div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                                        <input type="file" name="{{ $field }}" accept="image/*" class="w-full text-xs text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-brand-50 file:px-2.5 file:py-1.5 file:text-xs file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($tab === 'payment')
                        <div class="rounded-xl border border-slate-200 p-4">
                            <h4 class="text-sm font-semibold text-brand-950 mb-3">System Currency</h4>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Currency Code</label>
                                    <x-admin.searchable-select
                                        name="system_currency"
                                        :options="$allCurrencies"
                                        :selected="$values['system_currency']"
                                        placeholder="Select a currency"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Currency Position</label>
                                    <x-admin.searchable-select
                                        name="currency_position"
                                        :options="['left' => 'Left ($10)', 'right' => 'Right (10$)', 'left-space' => 'Left with space ($ 10)', 'right-space' => 'Right with space (10 $)']"
                                        :selected="$values['currency_position']"
                                    />
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4">
                            <label class="inline-flex items-center gap-2 mb-3">
                                <input type="hidden" name="paypal_active" value="0">
                                <input type="checkbox" name="paypal_active" value="1" @checked($values['paypal_active'] == '1') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm font-semibold text-brand-950">Enable PayPal</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Mode</label>
                                    <x-admin.searchable-select
                                        name="paypal_mode"
                                        :options="['sandbox' => 'Sandbox', 'production' => 'Production']"
                                        :selected="$values['paypal_mode']"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">PayPal Currency</label>
                                    <x-admin.searchable-select
                                        name="paypal_currency"
                                        :options="$paypalCurrencies"
                                        :selected="$values['paypal_currency']"
                                        placeholder="Select a currency"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Client ID (Sandbox)</label>
                                    <input type="text" name="paypal_sandbox_client_id" value="{{ $values['paypal_sandbox_client_id'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Client ID (Production)</label>
                                    <input type="text" name="paypal_production_client_id" value="{{ $values['paypal_production_client_id'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                </div>
                            </div>
                        </div>

                        <div class="rounded-xl border border-slate-200 p-4">
                            <label class="inline-flex items-center gap-2 mb-3">
                                <input type="hidden" name="stripe_active" value="0">
                                <input type="checkbox" name="stripe_active" value="1" @checked($values['stripe_active'] == '1') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm font-semibold text-brand-950">Enable Stripe</span>
                            </label>
                            <div class="grid grid-cols-2 gap-3 mb-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Test Mode</label>
                                    <x-admin.searchable-select
                                        name="stripe_testmode"
                                        :options="['on' => 'On', 'off' => 'Off']"
                                        :selected="$values['stripe_testmode']"
                                    />
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Stripe Currency</label>
                                    <x-admin.searchable-select
                                        name="stripe_currency"
                                        :options="$stripeCurrencies"
                                        :selected="$values['stripe_currency']"
                                        placeholder="Select a currency"
                                    />
                                </div>
                            </div>
                            <div class="grid grid-cols-1 gap-3">
                                <input type="text" name="stripe_test_public_key" placeholder="Test Public Key" value="{{ $values['stripe_test_public_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                <input type="text" name="stripe_test_secret_key" placeholder="Test Secret Key" value="{{ $values['stripe_test_secret_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                <input type="text" name="stripe_live_public_key" placeholder="Live Public Key" value="{{ $values['stripe_live_public_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                <input type="text" name="stripe_live_secret_key" placeholder="Live Secret Key" value="{{ $values['stripe_live_secret_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            </div>
                        </div>

                        <div class="flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800">
                            <i class="fa-solid fa-triangle-exclamation mt-0.5"></i>
                            <span>Make sure "System Currency", "PayPal Currency" and "Stripe Currency" are the same.</span>
                        </div>
                    @endif

                    @if ($tab === 'smtp')
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Protocol</label>
                                <input type="text" name="smtp_protocol" value="{{ $values['smtp_protocol'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1.5">Port</label>
                                <input type="text" name="smtp_port" value="{{ $values['smtp_port'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Host</label>
                            <input type="text" name="smtp_host" value="{{ $values['smtp_host'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                            <input type="text" name="smtp_username" value="{{ $values['smtp_username'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        </div>
                        <div x-data="{ showPassword: false }">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                            <div class="relative">
                                <input :type="showPassword ? 'text' : 'password'" name="smtp_password" value="{{ $values['smtp_password'] }}" class="w-full rounded-lg border border-slate-300 pl-3 pr-10 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                <button type="button" @click="showPassword = !showPassword" class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-brand-600" :aria-label="showPassword ? 'Hide password' : 'Show password'">
                                    <i class="fa-solid text-sm" :class="showPassword ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Encryption</label>
                            <x-admin.searchable-select
                                name="smtp_encryption"
                                :options="['tls' => 'TLS', 'ssl' => 'SSL', 'none' => 'None']"
                                :selected="$values['smtp_encryption']"
                            />
                        </div>
                    @endif

                    @if ($tab === 'instructor')
                        <div x-data="{ instructorRevenue: {{ (int) $values['instructor_revenue_percent'] }} }">
                            <label class="inline-flex items-center gap-2">
                                <input type="hidden" name="allow_instructor" value="0">
                                <input type="checkbox" name="allow_instructor" value="1" @checked($values['allow_instructor'] == '1') class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                <span class="text-sm text-slate-700">Allow users to become instructors and sell courses</span>
                            </label>
                            <div class="grid grid-cols-2 gap-4 mt-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Instructor Revenue Share (%)</label>
                                    <input type="number" min="0" max="100" name="instructor_revenue_percent" x-model.number="instructorRevenue" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Platform Revenue Share (%)</label>
                                    <input type="number" disabled :value="Math.max(0, 100 - instructorRevenue)" class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-500">
                                </div>
                            </div>
                            <p class="text-xs text-slate-400 mt-1">The remaining percentage goes to the platform on every sale.</p>
                        </div>
                    @endif

                    <div class="flex justify-end pt-2">
                        <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                            Save {{ $tabMeta[$tab]['label'] }} Settings
                        </button>
                    </div>
                </form>
            @endif
        </div>
    </div>
</x-layouts.admin>
