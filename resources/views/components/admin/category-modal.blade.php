@props(['category' => null, 'categories', 'openVar', 'id' => null])

@php
    $isEdit = (bool) $category;
    $showExpr = $isEdit ? "editOpen === {$id}" : "{$openVar}";
    $closeExpr = $isEdit ? 'editOpen = null' : "{$openVar} = false";
    $heading = $isEdit ? 'Edit Category' : 'Add New Category';
    $parentOptions = $categories->reject(fn ($c) => $isEdit && $c->id === $category->id);
@endphp

<div
    x-show="{{ $showExpr }}"
    x-cloak
    class="fixed inset-0 z-50 flex items-center justify-center p-4"
    style="display: none;"
>
    {{-- Backdrop --}}
    <div class="absolute inset-0 bg-slate-900/60" @click="{{ $closeExpr }}"></div>

    {{-- Panel --}}
    <div
        x-show="{{ $showExpr }}"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0 scale-95"
        x-transition:enter-end="opacity-100 scale-100"
        class="relative w-full max-w-md rounded-2xl bg-white shadow-xl"
        x-data="{ parentId: '{{ $category->parent_id ?? '0' }}' }"
    >
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-slate-900">{{ $heading }}</h3>
            <button type="button" @click="{{ $closeExpr }}" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form
            action="{{ $isEdit ? route('admin.categories.update', $category) : route('admin.categories.store') }}"
            method="POST"
            enctype="multipart/form-data"
            class="px-6 py-5 space-y-4"
        >
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Category Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ $category->name ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Parent Category</label>
                <select name="parent_id" x-model="parentId"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    <option value="0">None (top-level category)</option>
                    @foreach ($parentOptions as $option)
                        <option value="{{ $option->id }}" @selected(($category->parent_id ?? null) == $option->id)>{{ $option->name }}</option>
                    @endforeach
                </select>
            </div>

            <div x-show="parentId == 0 || parentId === ''">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Icon <span class="text-slate-400">(Font Awesome class)</span></label>
                <input type="text" name="icon" placeholder="fa-solid fa-code" value="{{ $category->icon ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div x-show="parentId == 0 || parentId === ''">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Thumbnail <span class="text-slate-400">(400×255px)</span></label>
                <input type="file" name="thumbnail" accept="image/*"
                    class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="{{ $closeExpr }}"
                    class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">
                    Cancel
                </button>
                <button type="submit"
                    class="rounded-lg bg-accent-600 px-4 py-2 text-sm font-semibold text-white hover:bg-accent-700">
                    {{ $isEdit ? 'Save Changes' : 'Create Category' }}
                </button>
            </div>
        </form>
    </div>
</div>
