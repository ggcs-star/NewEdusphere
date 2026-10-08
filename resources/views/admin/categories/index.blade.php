<x-layouts.admin title="Categories">
    <div x-data="{ addOpen: false, editOpen: null }">

        {{-- Welcome banner --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
                <div class="max-w-lg">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">Categories</h2>
                    <p class="mt-1 text-sm font-semibold text-accent-600">Organize the courses catalog into categories and sub-categories.</p>
                </div>

                <div class="flex items-center gap-8">
                    {{-- Decorative cluster --}}
                    <div class="hidden lg:block relative w-20 h-20 flex-shrink-0">
                        <div class="h-20 w-20 rounded-3xl bg-brand-700 rotate-6 shadow-xl flex items-center justify-center">
                            <i class="fa-solid fa-graduation-cap text-3xl text-accent-400 -rotate-6"></i>
                        </div>
                        <div class="absolute -bottom-3 -left-4 h-10 w-14 rounded-xl bg-accent-500 shadow-lg flex items-center justify-center -rotate-12">
                            <i class="fa-solid fa-book text-base text-white"></i>
                        </div>
                    </div>

                    <button @click="addOpen = true" type="button"
                        class="flex-shrink-0 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Add Category
                    </button>
                </div>
            </div>

            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
            <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
            <x-admin.search-input :value="$search" placeholder="Search categories..." />

            <select name="status" onchange="this.form.submit()"
                class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none">
                <option value="all" @selected($selectedStatus == 'all')>All Status</option>
                <option value="active" @selected($selectedStatus == 'active')>Active</option>
                <option value="inactive" @selected($selectedStatus == 'inactive')>Inactive</option>
            </select>

            <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Filter</button>
        </form>

        {{-- Grid --}}
        @if ($categories->isEmpty())
            <x-admin.empty-state
                icon="fa-shapes"
                :title="$search || $selectedStatus !== 'all' ? 'No categories match your filters' : 'No categories yet'"
                :description="$search || $selectedStatus !== 'all' ? 'Try adjusting your search or filters.' : 'Get started by creating your first course category.'"
            />
        @else
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($categories as $category)
                    <div class="relative rounded-2xl bg-white border border-slate-200 shadow-sm hover:shadow-lg transition overflow-visible flex flex-col">
                        {{-- Header --}}
                        <div class="relative h-24 rounded-t-2xl bg-gradient-to-br from-brand-700 to-brand-900 overflow-hidden">
                            @if ($category->thumbnail)
                                <img src="{{ Storage::url($category->thumbnail) }}" alt="{{ $category->name }}" class="absolute inset-0 h-full w-full object-cover opacity-40">
                            @endif

                            {{-- Decorative blob --}}
                            <div class="pointer-events-none absolute -right-6 -top-10 h-28 w-28 rounded-full bg-white/10"></div>
                            <div class="pointer-events-none absolute right-6 top-4 h-10 w-10 rounded-full bg-white/10"></div>

                            <div class="absolute top-3 right-3 flex gap-1.5">
                                <button @click="editOpen = {{ $category->id }}" type="button"
                                    class="h-8 w-8 rounded-lg bg-white text-brand-700 hover:bg-brand-50 flex items-center justify-center shadow-sm transition">
                                    <i class="fa-solid fa-pen text-xs"></i>
                                </button>
                                <form action="{{ route('admin.categories.destroy', $category) }}" method="POST"
                                    onsubmit="return confirm('Delete this category and all its sub-categories?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="h-8 w-8 rounded-lg bg-white text-rose-600 hover:bg-rose-50 flex items-center justify-center shadow-sm transition">
                                        <i class="fa-solid fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </div>

                        {{-- Icon badge, outside the clipped header so the circle renders in full --}}
                        <div class="absolute top-[68px] left-5 h-14 w-14 rounded-full bg-accent-500 ring-4 ring-white shadow-md flex items-center justify-center">
                            <i class="{{ $category->icon ?: 'fa-solid fa-shapes' }} text-white text-lg"></i>
                        </div>

                        {{-- Body --}}
                        <div class="pt-9 pb-5 px-5 flex-1 flex flex-col">
                            <div class="flex items-center justify-between gap-2">
                                <h3 class="text-lg font-bold text-brand-950">{{ $category->name }}</h3>
                                <span class="h-8 w-8 flex-shrink-0 rounded-full bg-brand-50 text-brand-700 flex items-center justify-center">
                                    <i class="fa-solid fa-arrow-right text-xs"></i>
                                </span>
                            </div>
                            <span class="mt-2 inline-flex w-fit items-center gap-1.5 rounded-full bg-brand-50 px-2.5 py-1 text-xs font-medium text-brand-700">
                                <i class="fa-solid fa-folder text-[10px]"></i>
                                {{ $category->children->count() }} sub-categories
                            </span>

                            @if ($category->children->isNotEmpty())
                                <ul class="mt-4 space-y-1 border-t border-slate-100 pt-3">
                                    @foreach ($category->children as $child)
                                        <li class="group/sub flex items-center justify-between rounded-lg px-2 py-1.5 hover:bg-brand-50/60 text-sm">
                                            <span class="flex items-center gap-2 text-slate-600">
                                                <i class="{{ $child->icon ?: 'fa-solid fa-circle-small' }} text-brand-400 text-xs"></i>
                                                {{ $child->name }}
                                            </span>
                                            <span class="flex items-center gap-1 opacity-0 group-hover/sub:opacity-100 transition">
                                                <button @click="editOpen = {{ $child->id }}" type="button" class="h-6 w-6 rounded text-slate-400 hover:text-brand-700 flex items-center justify-center">
                                                    <i class="fa-solid fa-pen text-[10px]"></i>
                                                </button>
                                                <form action="{{ route('admin.categories.destroy', $child) }}" method="POST"
                                                    onsubmit="return confirm('Delete this sub-category?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="h-6 w-6 rounded text-slate-400 hover:text-rose-600 flex items-center justify-center">
                                                        <i class="fa-solid fa-trash text-[10px]"></i>
                                                    </button>
                                                </form>
                                            </span>
                                        </li>

                                        {{-- Edit modal for sub-category --}}
                                        <x-admin.category-modal :category="$child" :categories="$allCategories" open-var="editOpen" :id="$child->id" />
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    </div>

                    {{-- Edit modal for top-level category --}}
                    <x-admin.category-modal :category="$category" :categories="$allCategories" open-var="editOpen" :id="$category->id" />
                @endforeach
            </div>
        @endif

        <x-admin.pagination :paginator="$categories" />

        {{-- Add modal --}}
        <x-admin.category-modal :categories="$allCategories" open-var="addOpen" />
    </div>
</x-layouts.admin>
