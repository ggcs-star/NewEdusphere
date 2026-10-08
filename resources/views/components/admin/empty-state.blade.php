@props(['icon' => 'fa-inbox', 'title' => 'No results', 'description' => null])

<div class="rounded-2xl border border-dashed border-slate-300 bg-white p-16 text-center">
    <div class="mx-auto h-12 w-12 rounded-full bg-brand-50 flex items-center justify-center text-brand-600">
        <i class="fa-solid {{ $icon }} text-xl"></i>
    </div>
    <h3 class="mt-4 text-sm font-semibold text-slate-900">{{ $title }}</h3>
    @if ($description)
        <p class="mt-1 text-sm text-slate-500">{{ $description }}</p>
    @endif
</div>
