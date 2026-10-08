<x-layouts.admin title="Dashboard">
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm">
            <p class="text-sm text-slate-500">Categories</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">{{ $categoryCount }}</p>
            <a href="{{ route('admin.categories.index') }}" class="mt-3 inline-block text-sm font-medium text-brand-600 hover:text-brand-700">Manage &rarr;</a>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm opacity-60">
            <p class="text-sm text-slate-500">Active Courses</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">—</p>
            <span class="mt-3 inline-block text-sm font-medium text-slate-400">Coming soon</span>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm opacity-60">
            <p class="text-sm text-slate-500">Students</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">—</p>
            <span class="mt-3 inline-block text-sm font-medium text-slate-400">Coming soon</span>
        </div>
        <div class="rounded-2xl bg-white border border-slate-200 p-5 shadow-sm opacity-60">
            <p class="text-sm text-slate-500">Revenue</p>
            <p class="mt-2 text-3xl font-bold text-slate-900">—</p>
            <span class="mt-3 inline-block text-sm font-medium text-slate-400">Coming soon</span>
        </div>
    </div>
</x-layouts.admin>
