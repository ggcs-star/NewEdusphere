@props(['name' => 'search', 'value' => '', 'placeholder' => 'Search...'])

<div {{ $attributes->merge(['class' => 'relative flex-1 min-w-[220px] max-w-sm']) }}>
    <svg class="pointer-events-none absolute left-3.5 top-1/2 -translate-y-1/2 h-4 w-4 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-4.35-4.35M11 19a8 8 0 100-16 8 8 0 000 16z" /></svg>
    <input type="text" name="{{ $name }}" value="{{ $value }}" placeholder="{{ $placeholder }}"
        class="w-full rounded-full bg-white border border-slate-200 pl-10 pr-4 py-2.5 text-sm focus:ring-2 focus:ring-brand-200 outline-none">
</div>
