@props(['items', 'heading', 'endpoint', 'showExpr', 'closeExpr'])

@php
    $listJson = $items->map(fn ($i) => ['id' => $i->id, 'title' => $i->title])->values()->toJson();
@endphp

<div
    x-show="{{ $showExpr }}"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    x-data="{
        list: {{ $listJson }},
        dragIndex: null,
        saving: false,
        drop(i) {
            if (this.dragIndex === null || this.dragIndex === i) return;
            const moved = this.list.splice(this.dragIndex, 1)[0];
            this.list.splice(i, 0, moved);
            this.dragIndex = null;
        },
        async save() {
            this.saving = true;
            try {
                const res = await fetch('{{ $endpoint }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    },
                    body: JSON.stringify({ order: this.list.map(i => i.id) }),
                });
                if (!res.ok) throw new Error('Request failed');
                window.location.reload();
            } catch (e) {
                this.saving = false;
                alert('Could not save the new order. Please try again.');
            }
        },
    }"
>
    <div class="absolute inset-0 bg-slate-900/60" @click="{{ $closeExpr }}"></div>

    <div class="relative w-full max-w-lg max-h-[85vh] flex flex-col rounded-2xl bg-white shadow-xl">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-brand-950">{{ $heading }}</h3>
            <button type="button" @click="{{ $closeExpr }}" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <div class="p-6 overflow-y-auto bg-slate-50 rounded-b-2xl">
            <div class="flex items-center justify-between mb-4">
                <p class="text-xs font-semibold uppercase tracking-wide text-slate-400">List</p>
                <button type="button" @click="save()" :disabled="saving"
                    class="rounded-full border border-brand-300 text-brand-700 text-sm font-semibold px-4 py-1.5 hover:bg-brand-50 disabled:opacity-50 transition">
                    <span x-show="!saving">Update Sorting</span>
                    <span x-show="saving">Saving...</span>
                </button>
            </div>

            <template x-if="list.length === 0">
                <p class="text-sm text-slate-400 text-center py-8">Nothing to sort yet.</p>
            </template>

            <div class="space-y-2">
                <template x-for="(item, index) in list" :key="item.id">
                    <div
                        draggable="true"
                        @dragstart="dragIndex = index"
                        @dragover.prevent
                        @drop.prevent="drop(index)"
                        class="flex items-center gap-3 rounded-xl border border-slate-200 bg-white px-4 py-3.5 shadow-sm cursor-move hover:border-brand-300 hover:shadow-md transition"
                    >
                        <i class="fa-solid fa-grip-vertical text-slate-300"></i>
                        <span class="text-sm font-medium text-slate-700" x-text="item.title"></span>
                    </div>
                </template>
            </div>
        </div>
    </div>
</div>
