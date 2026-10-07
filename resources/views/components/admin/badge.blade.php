@props(['color' => 'slate'])

@php
$colors = [
    'emerald' => 'bg-emerald-50 text-emerald-700',
    'amber' => 'bg-amber-50 text-amber-700',
    'rose' => 'bg-rose-50 text-rose-700',
    'brand' => 'bg-brand-50 text-brand-700',
    'accent' => 'bg-accent-50 text-accent-700',
    'slate' => 'bg-slate-100 text-slate-600',
];
@endphp

<span {{ $attributes->merge(['class' => 'inline-flex items-center gap-1 rounded-full px-2.5 py-1 text-xs font-semibold '.($colors[$color] ?? $colors['slate'])]) }}>
    {{ $slot }}
</span>
