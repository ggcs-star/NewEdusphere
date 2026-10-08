<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Admin' }} · EduSphere</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-page text-slate-800 antialiased">
    <div
        class="flex min-h-screen"
        x-data="{ collapsed: localStorage.getItem('sidebar_collapsed') === '1' }"
        x-init="$watch('collapsed', value => localStorage.setItem('sidebar_collapsed', value ? '1' : '0'))"
    >

        {{-- Sidebar --}}
        <aside
            class="hidden lg:flex lg:flex-col lg:fixed lg:inset-y-0 lg:left-0 bg-brand-950 text-white transition-all duration-200"
            :class="collapsed ? 'lg:w-20' : 'lg:w-64'"
        >
            <div class="flex items-center h-16 border-b border-white/10" :class="collapsed ? 'justify-center px-0' : 'gap-2.5 px-6'">
                <div class="flex h-9 w-9 flex-shrink-0 items-center justify-center rounded-full bg-accent-500 text-white">
                    <i class="fa-solid fa-graduation-cap text-base"></i>
                </div>
                <span x-show="!collapsed" x-transition.opacity x-cloak class="font-extrabold text-lg tracking-tight whitespace-nowrap overflow-hidden">
                    <span class="text-white">Edu</span><span class="text-accent-500">Sphere</span>
                </span>
            </div>

            <nav class="relative flex-1 overflow-y-auto overflow-x-hidden admin-scroll px-3 py-6 space-y-6">
                <div>
                    <p x-show="!collapsed" x-cloak class="px-3 text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2">Overview</p>
                    <x-admin.nav-link :href="route('admin.dashboard')" :active="request()->routeIs('admin.dashboard')" label="Dashboard">
                        <x-admin.icon name="dashboard" />
                        <span x-show="!collapsed" x-cloak>Dashboard</span>
                    </x-admin.nav-link>
                </div>

                <div>
                    <p x-show="!collapsed" x-cloak class="px-3 text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2">Catalog</p>
                    <div class="space-y-1">
                        <x-admin.nav-link :href="route('admin.categories.index')" :active="request()->routeIs('admin.categories.*')" label="Categories">
                            <x-admin.icon name="categories" />
                            <span x-show="!collapsed" x-cloak>Categories</span>
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*') && !request()->routeIs('admin.courses.pending')" label="Courses">
                            <x-admin.icon name="courses" />
                            <span x-show="!collapsed" x-cloak>Courses</span>
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.courses.pending')" :active="request()->routeIs('admin.courses.pending')" label="Pending Courses">
                            <x-admin.icon name="pending" />
                            <span x-show="!collapsed" x-cloak>Pending Courses</span>
                        </x-admin.nav-link>
                    </div>
                </div>

                <div>
                    <p x-show="!collapsed" x-cloak class="px-3 text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2">People</p>
                    <div class="space-y-1">
                        <x-admin.nav-link :href="route('admin.users.index')" :active="request()->routeIs('admin.users.*')" label="Users">
                            <x-admin.icon name="users" />
                            <span x-show="!collapsed" x-cloak>Users</span>
                        </x-admin.nav-link>
                        <x-admin.nav-link :href="route('admin.enrollments.index')" :active="request()->routeIs('admin.enrollments.*')" label="Enrollment History">
                            <x-admin.icon name="enrollment" />
                            <span x-show="!collapsed" x-cloak>Enrollment History</span>
                        </x-admin.nav-link>
                    </div>
                </div>

                <div>
                    <p x-show="!collapsed" x-cloak class="px-3 text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2">Finance</p>
                    <div class="space-y-1">
                        <x-admin.nav-link :href="route('admin.revenue.index')" :active="request()->routeIs('admin.revenue.*')" label="Revenue">
                            <x-admin.icon name="revenue" />
                            <span x-show="!collapsed" x-cloak>Revenue</span>
                        </x-admin.nav-link>
                    </div>
                </div>

                <div>
                    <p x-show="!collapsed" x-cloak class="px-3 text-xs font-semibold uppercase tracking-wider text-brand-400 mb-2">System</p>
                    <div class="space-y-1">
                        <x-admin.nav-link :href="route('admin.settings.index')" :active="request()->routeIs('admin.settings.*')" label="Settings">
                            <x-admin.icon name="settings" />
                            <span x-show="!collapsed" x-cloak>Settings</span>
                        </x-admin.nav-link>
                    </div>
                </div>
            </nav>

            <div class="relative border-t border-white/10 p-4">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" title="Sign out"
                        class="w-full flex items-center rounded-lg px-3 py-2 text-sm font-medium text-white hover:bg-white/10 transition"
                        :class="collapsed ? 'justify-center' : 'gap-2'">
                        <x-admin.icon name="logout" />
                        <span x-show="!collapsed" x-cloak>Sign out</span>
                    </button>
                </form>
            </div>
        </aside>

        {{-- Main --}}
        <div class="flex-1 flex flex-col min-h-screen transition-all duration-200" :class="collapsed ? 'lg:pl-20' : 'lg:pl-64'">
            <header class="h-16 bg-white border-b border-slate-200 flex items-center gap-4 px-4 lg:px-8 sticky top-0 z-20">
                <button type="button" @click="collapsed = !collapsed"
                    class="h-10 w-10 flex items-center justify-center rounded-full hover:bg-brand-50 text-brand-700 transition">
                    <i class="fa-solid fa-bars"></i>
                </button>

                <div class="flex items-center gap-4 ml-auto">
                    <button type="button" class="relative h-10 w-10 flex items-center justify-center rounded-full hover:bg-brand-50 text-brand-700 transition">
                        <i class="fa-regular fa-bell text-lg"></i>
                        <span class="absolute top-2 right-2.5 h-2 w-2 rounded-full bg-accent-500 ring-2 ring-white"></span>
                    </button>

                    <div class="flex items-center gap-2.5 pl-4 border-l border-slate-200">
                        <div class="h-9 w-9 rounded-full bg-brand-900 text-white flex items-center justify-center font-semibold text-sm">
                            {{ strtoupper(substr(current_admin_user()->name ?? 'A', 0, 1)) }}
                        </div>
                        <span class="hidden sm:inline text-sm font-semibold text-slate-700">{{ current_admin_user()->name ?? 'Admin' }}</span>
                        <svg class="h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
                    </div>
                </div>
            </header>

            <main class="flex-1 p-4 lg:p-8">
                @if (session('flash_success'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-800">
                        <svg class="h-5 w-5 flex-shrink-0 text-emerald-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M5 13l4 4L19 7" /></svg>
                        <span>{{ session('flash_success') }}</span>
                    </div>
                @endif
                @if (session('flash_error'))
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <svg class="h-5 w-5 flex-shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <span>{{ session('flash_error') }}</span>
                    </div>
                @endif
                @if ($errors->any())
                    <div class="mb-6 flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm text-rose-800">
                        <svg class="h-5 w-5 flex-shrink-0 text-rose-500" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                        <ul class="space-y-0.5">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{ $slot }}
            </main>
        </div>
    </div>
</body>
</html>
