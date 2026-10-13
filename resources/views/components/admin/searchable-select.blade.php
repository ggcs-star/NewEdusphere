@props([
    'name',
    'options' => [],
    'optionGroups' => null,
    'groupBy' => null,
    'selected' => null,
    'placeholder' => 'Select...',
    'allLabel' => null,
    'pill' => false,
    'autosubmit' => false,
    'required' => false,
    'bindTo' => null,
    'id' => null,
    'buttonClass' => null,
])

@php
    $fieldId = $id ?? $name;
    $valueExpr = $bindTo ?: '__value';

    $optionList = [];
    if ($allLabel !== null) {
        $optionList[] = ['value' => 'all', 'label' => $allLabel, 'group' => null];
    }
    foreach ($options as $key => $label) {
        $optionList[] = [
            'value' => (string) $key,
            'label' => (string) $label,
            'group' => isset($optionGroups[$key]) ? (string) $optionGroups[$key] : null,
        ];
    }
@endphp

<div
    x-data="{
        open: false,
        query: '',
        @if (! $bindTo) {{ $valueExpr }}: @js((string) ($selected ?? '')), @endif
        options: @js($optionList),
        get visibleOptions() {
            @if ($groupBy)
            return this.options.filter(o => o.group === null || String(o.group) === String({{ $groupBy }}));
            @else
            return this.options;
            @endif
        },
        get filtered() {
            if (!this.query) return this.visibleOptions;
            const q = this.query.toLowerCase();
            return this.visibleOptions.filter(o => o.label.toLowerCase().includes(q));
        },
        get selectedLabel() {
            const found = this.options.find(o => String(o.value) === String(this.{{ $valueExpr }}));
            return found ? found.label : @js($placeholder);
        },
        select(opt) {
            this.{{ $valueExpr }} = opt.value;
            this.open = false;
            this.query = '';
            @if ($autosubmit)
            this.$nextTick(() => this.$el.closest('form').submit());
            @endif
        }
    }"
    @keydown.escape="open = false"
    {{ $attributes->merge(['class' => 'relative']) }}
>
    <input type="hidden" name="{{ $name }}" x-model="{{ $valueExpr }}" @if ($required) required @endif>

    <button type="button" id="{{ $fieldId }}"
        @click="open = !open; if (open) $nextTick(() => $refs.search_{{ $fieldId }}.focus())"
        class="{{ $pill ? 'rounded-full px-4 py-2.5' : 'w-full rounded-lg px-3 py-2.5' }} {{ $buttonClass ?? ($pill ? 'bg-white border border-slate-200 text-slate-600' : 'bg-white border border-slate-300 text-slate-700') }} text-sm text-left flex items-center justify-between gap-2 focus:ring-2 focus:ring-brand-200 outline-none transition cursor-pointer">
        <span class="truncate" x-text="selectedLabel"></span>
        <svg class="h-3.5 w-3.5 text-slate-400 flex-shrink-0 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 9l-7 7-7-7" /></svg>
    </button>

    <div x-show="open" x-cloak @click.outside="open = false" x-transition.origin.top
        class="absolute z-30 mt-1.5 w-full min-w-[14rem] rounded-xl bg-white border border-slate-200 shadow-lg overflow-hidden">
        <div class="p-2 border-b border-slate-100">
            <input type="text" x-ref="search_{{ $fieldId }}" x-model="query" placeholder="Type to search..."
                class="w-full rounded-lg border border-slate-200 px-3 py-1.5 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
        </div>
        <ul class="max-h-56 overflow-y-auto py-1">
            <template x-for="opt in filtered" :key="opt.value">
                <li @click="select(opt)"
                    class="px-3.5 py-2 text-sm cursor-pointer hover:bg-brand-50 flex items-center justify-between gap-2"
                    :class="String({{ $valueExpr }}) === String(opt.value) ? 'bg-brand-50 text-brand-700 font-semibold' : 'text-slate-700'">
                    <span x-text="opt.label" class="truncate"></span>
                    <i class="fa-solid fa-check text-xs text-brand-600 flex-shrink-0" x-show="String({{ $valueExpr }}) === String(opt.value)"></i>
                </li>
            </template>
            <li x-show="filtered.length === 0" class="px-3.5 py-2.5 text-sm text-slate-400">No matches found.</li>
        </ul>
    </div>
</div>
