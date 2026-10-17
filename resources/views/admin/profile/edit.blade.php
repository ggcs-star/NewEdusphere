@php
    $inInstructorPortal = request()->routeIs('instructor.*');
    $updateRoute = $inInstructorPortal ? route('instructor.profile.update') : route('admin.profile.update');
@endphp
<x-layouts.admin title="My Profile">
    <div class="mb-6">
        <h2 class="text-2xl font-bold text-brand-950">My Profile</h2>
        <p class="text-sm text-slate-500 mt-1">Manage your account details and password.</p>
    </div>

    <div class="max-w-2xl rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <form action="{{ $updateRoute }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div>
                <h3 class="font-semibold text-brand-950 mb-4">Basic Info</h3>
                <div class="space-y-4">
                    <div class="flex items-center gap-4">
                        @if ($user->avatarUrl())
                            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" class="h-16 w-16 rounded-full object-cover flex-shrink-0">
                        @else
                            <div class="h-16 w-16 rounded-full bg-brand-900 text-white flex items-center justify-center text-xl font-semibold flex-shrink-0">
                                {{ strtoupper(substr($user->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Profile Photo</label>
                            <input type="file" name="avatar" accept="image/*"
                                class="text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name</label>
                        <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                        <input type="email" value="{{ $user->email }}" disabled
                            class="w-full rounded-lg border border-slate-200 bg-slate-50 px-3 py-2.5 text-sm text-slate-500">
                        <p class="text-xs text-slate-400 mt-1">Email changes should be handled through your account verification flow.</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                        <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Biography</label>
                        <textarea name="biography" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ old('biography', $user->biography) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">A Short Title About Yourself</label>
                        <input type="text" name="short_title" placeholder="e.g. Senior Web Developer" value="{{ old('short_title', $user->short_title) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h3 class="font-semibold text-brand-950 mb-4">Social Links</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Facebook Link</label>
                        <input type="text" name="facebook_link" placeholder="facebook.com/yourprofile" value="{{ old('facebook_link', $user->facebook_link) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Twitter Link</label>
                        <input type="text" name="twitter_link" placeholder="x.com/yourprofile" value="{{ old('twitter_link', $user->twitter_link) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">LinkedIn Link</label>
                        <input type="text" name="linkedin_link" placeholder="linkedin.com/in/yourprofile" value="{{ old('linkedin_link', $user->linkedin_link) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                </div>
            </div>

            <div class="pt-6 border-t border-slate-100">
                <h3 class="font-semibold text-brand-950 mb-1">Change Password</h3>
                <p class="text-xs text-slate-400 mb-4">Leave these blank to keep your current password.</p>
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">Current Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="current_password" autocomplete="current-password"
                                    class="w-full rounded-lg border border-slate-300 pl-3 pr-10 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                <button type="button" @click="show = !show" class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-brand-600" :aria-label="show ? 'Hide password' : 'Show password'">
                                    <i class="fa-solid text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                        <div x-data="{ show: false }">
                            <label class="block text-sm font-medium text-slate-700 mb-1.5">New Password</label>
                            <div class="relative">
                                <input :type="show ? 'text' : 'password'" name="new_password" autocomplete="new-password"
                                    class="w-full rounded-lg border border-slate-300 pl-3 pr-10 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                                <button type="button" @click="show = !show" class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-brand-600" :aria-label="show ? 'Hide password' : 'Show password'">
                                    <i class="fa-solid text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                    <div x-data="{ show: false }">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm New Password</label>
                        <div class="relative">
                            <input :type="show ? 'text' : 'password'" name="new_password_confirmation" autocomplete="new-password"
                                class="w-full rounded-lg border border-slate-300 pl-3 pr-10 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <button type="button" @click="show = !show" class="absolute right-0 top-0 h-full w-10 flex items-center justify-center text-slate-400 hover:text-brand-600" :aria-label="show ? 'Hide password' : 'Show password'">
                                <i class="fa-solid text-sm" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex justify-end pt-2">
                <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
