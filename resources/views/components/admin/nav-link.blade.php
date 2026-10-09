@props(['href', 'active' => false, 'disabled' => false, 'label' => null])

@if ($disabled)
    <span :title="collapsed ? '{{ $label }}' : ''"
        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm font-medium text-white/50 cursor-not-allowed"
        :class="collapsed ? 'justify-center px-0' : ''">
        {{ $slot }}
        <span x-show="!collapsed" x-cloak class="ml-auto text-[10px] uppercase tracking-wide text-white/40 px-1.5 py-0.5">Soon</span>
    </span>
@else
    <a href="{{ $href }}" :title="collapsed ? '{{ $label }}' : ''"
        class="flex items-center gap-3 rounded-xl px-3.5 py-2.5 text-sm transition
        {{ $active
            ? 'bg-accent-500 text-white font-semibold shadow-md shadow-accent-900/30'
            : 'text-white font-medium hover:bg-white/10' }}"
        :class="collapsed ? 'justify-center px-0' : ''">
        {{ $slot }}
    </a>
@endif
