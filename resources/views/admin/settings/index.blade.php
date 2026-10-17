@php
    $tabMeta = [
        'system' => ['label' => 'System', 'icon' => 'fa-gear'],
        'frontend' => ['label' => 'Frontend', 'icon' => 'fa-window-maximize'],
        'theme' => ['label' => 'Theme', 'icon' => 'fa-palette'],
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
                <form action="{{ route('admin.settings.update', $tab) }}" method="POST" enctype="multipart/form-data" class="space-y-4 {{ $tab === 'frontend' ? '' : 'max-w-xl' }}">
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
                            <textarea name="about_us" class="tinymce-editor">{{ $values['about_us'] }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Terms &amp; Conditions</label>
                            <textarea name="terms_and_condition" class="tinymce-editor">{{ $values['terms_and_condition'] }}</textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Privacy Policy</label>
                            <textarea name="privacy_policy" class="tinymce-editor">{{ $values['privacy_policy'] }}</textarea>
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
                                    <div x-data="{ removed: false, hasImage: {{ $values[$field] ? 'true' : 'false' }} }" class="relative">
                                        @if ($values[$field])
                                            <button type="button" x-show="!removed" @click="removed = true"
                                                class="absolute -top-2 -right-2 z-10 h-5 w-5 rounded-full bg-rose-600 text-white text-xs flex items-center justify-center hover:bg-rose-700 shadow"
                                                title="Remove {{ $label }}">
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                        @endif
                                        <div class="h-20 rounded-lg bg-slate-50 border border-slate-200 flex items-center justify-center overflow-hidden mb-2">
                                            @if ($values[$field])
                                                <img x-show="!removed" src="{{ Storage::url($values[$field]) }}" class="max-h-full max-w-full object-contain">
                                            @endif
                                            <i x-show="!hasImage || removed" class="fa-regular fa-image text-slate-300 text-xl"></i>
                                        </div>
                                        <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                                        <input type="hidden" name="remove_{{ $field }}" :value="removed ? '1' : '0'">
                                        <input type="file" name="{{ $field }}" accept="image/*" @change="removed = false" class="w-full text-xs text-slate-500 file:mr-2 file:rounded-lg file:border-0 file:bg-brand-50 file:px-2.5 file:py-1.5 file:text-xs file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                                        <p x-show="removed" x-cloak class="text-xs text-rose-600 mt-1">Will be removed when you save.</p>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if ($tab === 'theme')
                        <div class="rounded-xl border border-amber-200 bg-amber-50 px-4 py-3 text-xs text-amber-800 flex items-start gap-2">
                            <i class="fa-solid fa-circle-info mt-0.5"></i>
                            <span>These control the colors used across the public website (landing page, course pages, etc.) — not the admin panel. Defaults match the current site colors, so nothing changes until you save new ones.</span>
                        </div>

                        @foreach ([
                            'theme_primary_color' => ['Primary', 'Main brand color — buttons, links, header background accents.'],
                            'theme_primary_dark' => ['Primary (Dark)', 'Used for hovers and darker sections like the footer.'],
                            'theme_primary_light' => ['Primary (Light)', 'A lighter tint of the primary color, used sparingly for depth.'],
                            'theme_secondary_color' => ['Secondary', 'Accent color — call-to-action buttons, highlights.'],
                            'theme_secondary_dark' => ['Secondary (Dark)', 'Hover state for secondary/accent elements.'],
                        ] as $field => [$label, $help])
                            <div class="rounded-xl border border-slate-200 p-4 flex items-center gap-4" x-data="{ value: @js($values[$field]), valid: true }">
                                <input type="color" x-ref="picker" value="{{ $values[$field] }}"
                                    @input="value = $event.target.value; valid = true"
                                    class="h-12 w-16 flex-shrink-0 rounded-lg border border-slate-300 cursor-pointer">
                                <div class="min-w-0 flex-1">
                                    <p class="text-sm font-semibold text-brand-950">{{ $label }}</p>
                                    <p class="text-xs text-slate-400 mt-0.5">{{ $help }}</p>
                                </div>
                                <div class="flex-shrink-0 w-36">
                                    <input type="text" name="{{ $field }}" x-model="value" maxlength="7" placeholder="#000000"
                                        @input="valid = /^#[0-9a-fA-F]{6}$/.test(value); if (valid) $refs.picker.value = value"
                                        :class="valid ? 'border-slate-300 focus:border-brand-500 focus:ring-brand-500' : 'border-rose-400 focus:border-rose-500 focus:ring-rose-400'"
                                        class="w-full rounded-lg border px-3 py-2 text-xs font-mono uppercase focus:ring-1 outline-none">
                                    <p x-show="!valid" x-cloak class="text-xs text-rose-600 mt-1">Please enter a valid hex color code (e.g. #32105F).</p>
                                </div>
                            </div>
                        @endforeach
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

                        <div class="rounded-xl border border-slate-200 p-4" x-data="{ showSecret: false }">
                            <h4 class="text-sm font-semibold text-brand-950 mb-1">Razorpay</h4>
                            <p class="text-xs text-slate-400 mb-3">Used for all course checkouts. Find your keys in the Razorpay Dashboard under Settings &rarr; API Keys.</p>
                            <div class="grid grid-cols-1 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Key ID</label>
                                    <input type="text" name="razorpay_key" placeholder="rzp_live_xxxxxxxxxxxx" value="{{ $values['razorpay_key'] }}" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-slate-600 mb-1">Key Secret</label>
                                    <div class="relative">
                                        <input :type="showSecret ? 'text' : 'password'" name="razorpay_secret" placeholder="Key Secret" value="{{ $values['razorpay_secret'] }}" class="w-full rounded-lg border border-slate-300 pl-3 pr-10 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                        <button type="button" @click="showSecret = !showSecret" class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-brand-600" :aria-label="showSecret ? 'Hide secret' : 'Show secret'">
                                            <i class="fa-solid text-sm" :class="showSecret ? 'fa-eye-slash' : 'fa-eye'"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
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
