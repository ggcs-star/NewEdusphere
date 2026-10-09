@props(['icon', 'color' => 'violet', 'label'])

@php
$palette = [
    'violet' => ['bg' => 'from-violet-50 to-violet-100/60', 'badge' => 'bg-violet-500', 'blob' => 'bg-violet-300/30', 'ring' => 'ring-violet-100'],
    'amber' => ['bg' => 'from-amber-50 to-amber-100/60', 'badge' => 'bg-amber-500', 'blob' => 'bg-amber-300/30', 'ring' => 'ring-amber-100'],
    'emerald' => ['bg' => 'from-emerald-50 to-emerald-100/60', 'badge' => 'bg-emerald-500', 'blob' => 'bg-emerald-300/30', 'ring' => 'ring-emerald-100'],
    'sky' => ['bg' => 'from-sky-50 to-sky-100/60', 'badge' => 'bg-sky-500', 'blob' => 'bg-sky-300/30', 'ring' => 'ring-sky-100'],
    'rose' => ['bg' => 'from-rose-50 to-rose-100/60', 'badge' => 'bg-rose-500', 'blob' => 'bg-rose-300/30', 'ring' => 'ring-rose-100'],
];
$p = $palette[$color] ?? $palette['violet'];
@endphp

<div class="relative overflow-hidden rounded-2xl bg-gradient-to-br {{ $p['bg'] }} ring-1 {{ $p['ring'] }} shadow-sm p-5">
    <div class="pointer-events-none absolute -right-4 -top-4 h-20 w-20 rounded-full {{ $p['blob'] }}"></div>

    <div class="relative h-11 w-11 rounded-xl {{ $p['badge'] }} text-white flex items-center justify-center shadow-sm">
        <i class="fa-solid {{ $icon }}"></i>
    </div>

    <div class="relative mt-4 text-2xl font-extrabold text-brand-950 leading-tight">{{ $slot }}</div>
    <p class="relative text-sm text-slate-500 mt-0.5">{{ $label }}</p>
</div>
