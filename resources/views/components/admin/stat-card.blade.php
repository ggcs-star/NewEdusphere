@props(['icon', 'color', 'count', 'label', 'change' => 0, 'direction' => 'flat'])

@php
$palette = [
    'violet' => ['bg' => 'from-violet-50 to-violet-100/60', 'badge' => 'bg-violet-500', 'blob' => 'bg-violet-300/30', 'ring' => 'ring-violet-100', 'btn' => 'bg-violet-100 text-violet-600 hover:bg-violet-200'],
    'amber' => ['bg' => 'from-amber-50 to-amber-100/60', 'badge' => 'bg-amber-500', 'blob' => 'bg-amber-300/30', 'ring' => 'ring-amber-100', 'btn' => 'bg-amber-100 text-amber-600 hover:bg-amber-200'],
    'emerald' => ['bg' => 'from-emerald-50 to-emerald-100/60', 'badge' => 'bg-emerald-500', 'blob' => 'bg-emerald-300/30', 'ring' => 'ring-emerald-100', 'btn' => 'bg-emerald-100 text-emerald-600 hover:bg-emerald-200'],
    'sky' => ['bg' => 'from-sky-50 to-sky-100/60', 'badge' => 'bg-sky-500', 'blob' => 'bg-sky-300/30', 'ring' => 'ring-sky-100', 'btn' => 'bg-sky-100 text-sky-600 hover:bg-sky-200'],
];
$p = $palette[$color] ?? $palette['violet'];
@endphp

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $p['bg'] }} ring-1 {{ $p['ring'] }} shadow-sm p-5">
    <div class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full {{ $p['blob'] }}"></div>

    <div class="relative h-11 w-11 rounded-xl {{ $p['badge'] }} text-white flex items-center justify-center shadow-sm">
        <i class="fa-solid {{ $icon }}"></i>
    </div>

    <p class="relative mt-4 text-3xl font-extrabold text-brand-950">{{ $count }}</p>
    <p class="relative text-sm text-slate-500 mt-0.5">{{ $label }}</p>

    <div class="relative mt-3 flex items-center justify-between">
        @if ($direction === 'up')
            <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600">
                <i class="fa-solid fa-arrow-trend-up"></i> +{{ $change }}% from last month
            </span>
        @elseif ($direction === 'down')
            <span class="inline-flex items-center gap-1 text-xs font-medium text-rose-600">
                <i class="fa-solid fa-arrow-trend-down"></i> {{ $change }}% from last month
            </span>
        @else
            <span class="inline-flex items-center gap-1 text-xs font-medium text-slate-400">
                <i class="fa-solid fa-minus"></i> No change
            </span>
        @endif

        <span class="h-8 w-8 rounded-full {{ $p['btn'] }} flex items-center justify-center transition">
            <i class="fa-solid fa-arrow-right text-xs"></i>
        </span>
    </div>
</div>
