
<x-layouts.admin title="Certificate Templates">
    <div class="space-y-6">

        {{-- Page Header --}}
        <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h2 class="text-2xl font-bold text-brand-950">
                    Certificate Templates
                </h2>
                <p class="mt-1 text-sm text-slate-500">
                    Manage certificate designs and assign templates to courses.
                </p>
            </div>

            <a href="{{ route('admin.certificates.create') }}"
               class="inline-flex items-center justify-center gap-2 rounded-xl bg-brand-600 px-5 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-brand-700">
                <i class="fa-solid fa-plus text-xs"></i>
                Create Template
            </a>
        </div>

        {{-- Flash Messages --}}
        @if(session('status'))
            <div class="flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 p-4 text-sm text-emerald-800">
                <i class="fa-solid fa-circle-check mt-0.5"></i>
                <p>{{ session('status') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div class="flex items-start gap-3 rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <i class="fa-solid fa-circle-exclamation mt-0.5"></i>
                <p>{{ session('error') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="rounded-xl border border-rose-200 bg-rose-50 p-4 text-sm text-rose-700">
                <p class="font-semibold">Please fix the following errors:</p>
                <ul class="mt-2 list-inside list-disc space-y-1">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Search and Filters --}}
        <div class="rounded-2xl border border-slate-200 bg-white p-5 shadow-sm">
            <form method="GET"
                  action="{{ route('admin.certificates.index') }}"
                  class="grid grid-cols-1 items-end gap-4 md:grid-cols-12">

                <div class="md:col-span-6">
                    <label for="search" class="mb-2 block text-sm font-semibold text-slate-700">
                        Search Template
                    </label>

                    <div class="relative">
                        <i class="fa-solid fa-magnifying-glass absolute left-3.5 top-1/2 -translate-y-1/2 text-sm text-slate-400"></i>

                        <input type="text"
                               id="search"
                               name="search"
                               value="{{ request('search') }}"
                               placeholder="Search by template name..."
                               class="w-full rounded-xl border border-slate-200 py-2.5 pl-10 pr-4 text-sm text-slate-700 outline-none transition focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                    </div>
                </div>

                <div class="md:col-span-3">
                    <label for="status" class="mb-2 block text-sm font-semibold text-slate-700">
                        Status
                    </label>

                    <select id="status"
                            name="status"
                            class="w-full rounded-xl border border-slate-200 bg-white px-3 py-2.5 text-sm text-slate-700 outline-none focus:border-brand-500 focus:ring-2 focus:ring-brand-100">
                        <option value="">All Templates</option>
                        <option value="active" @selected(request('status') === 'active')>
                            Active
                        </option>
                        <option value="inactive" @selected(request('status') === 'inactive')>
                            Inactive
                        </option>
                    </select>
                </div>

                <div class="flex gap-2 md:col-span-3">
                    <button type="submit"
                            class="inline-flex flex-1 items-center justify-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-brand-700">
                        <i class="fa-solid fa-filter text-xs"></i>
                        Filter
                    </button>

                    <a href="{{ route('admin.certificates.index') }}"
                       class="inline-flex items-center justify-center rounded-xl border border-slate-200 px-4 py-2.5 text-sm font-semibold text-slate-600 transition hover:bg-slate-50">
                        Reset
                    </a>
                </div>
            </form>
        </div>

        {{-- Templates Table --}}
        <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">

            <div class="flex flex-col gap-2 border-b border-slate-100 px-5 py-4 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <h3 class="font-semibold text-brand-950">All Certificate Templates</h3>
                    <p class="mt-1 text-xs text-slate-500">
                        {{ $templates->total() }} template(s) found
                    </p>
                </div>

                <div class="flex h-10 w-10 items-center justify-center rounded-xl bg-brand-50 text-brand-600">
                    <i class="fa-solid fa-award text-lg"></i>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-slate-50">
                        <tr class="border-b border-slate-100">
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Template
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Assigned Courses
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Status
                            </th>
                            <th class="px-5 py-4 text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Order
                            </th>
                            <th class="px-5 py-4 text-right text-xs font-semibold uppercase tracking-wide text-slate-500">
                                Actions
                            </th>
                        </tr>
                    </thead>

                    <tbody class="divide-y divide-slate-100">
                        @forelse($templates as $template)
                            <tr class="transition hover:bg-slate-50/70">
                                <td class="px-5 py-4">
                                    <div class="flex min-w-[230px] items-center gap-3">
                                        @if($template->background_image)
                                            <img src="{{ \Illuminate\Support\Facades\Storage::url($template->background_image) }}"
                                                 alt="Certificate background"
                                                 class="h-16 w-24 rounded-lg border border-slate-200 object-cover">
                                        @else
                                            <div class="flex h-16 w-24 items-center justify-center rounded-lg border border-slate-200 bg-slate-50 text-slate-400">
                                                <i class="fa-solid fa-award text-2xl"></i>
                                            </div>
                                        @endif

                                        <div class="min-w-0">
                                            <p class="font-semibold text-slate-800">
                                                {{ $template->name ?? $template->title ?? 'Untitled Template' }}
                                            </p>
                                            <p class="mt-1 text-xs text-slate-400">
                                                Template #{{ $template->id }}
                                            </p>
                                        </div>
                                    </div>
                                </td>

                                <td class="px-5 py-4">
                                    <span class="inline-flex items-center gap-2 rounded-lg border border-slate-200 bg-slate-50 px-3 py-2 text-xs font-semibold text-slate-700">
                                        <i class="fa-solid fa-book-open text-brand-600"></i>
                                        {{ $template->courses_count }} courses
                                    </span>
                                </td>

                                <td class="px-5 py-4">
                                    @if($template->is_active)
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-emerald-50 px-3 py-1.5 text-xs font-semibold text-emerald-700">
                                            <span class="h-1.5 w-1.5 rounded-full bg-emerald-500"></span>
                                            Active
                                        </span>
                                    @else
                                        <span class="inline-flex items-center gap-1.5 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600">
                                            <span class="h-1.5 w-1.5 rounded-full bg-slate-400"></span>
                                            Inactive
                                        </span>
                                    @endif
                                </td>

                                <td class="px-5 py-4 text-sm text-slate-600">
                                    {{ $template->sort_order ?? 0 }}
                                </td>

                                <td class="px-5 py-4">
                                    <div class="flex items-center justify-end gap-2">
                                        <a href="{{ route('admin.certificates.show', $template) }}"
   class="text-sm font-semibold text-brand-600 hover:text-brand-800">
    View
</a>
                                        <a href="{{ route('admin.certificates.edit', $template) }}"
                                           class="inline-flex items-center gap-1.5 rounded-lg border border-slate-200 px-3 py-2 text-xs font-semibold text-slate-600 transition hover:border-brand-200 hover:bg-brand-50 hover:text-brand-700">
                                            <i class="fa-solid fa-pen text-[10px]"></i>
                                            Edit
                                        </a>

                                        <form method="POST"
                                              action="{{ route('admin.certificates.destroy', $template) }}"
                                              onsubmit="return confirm('Are you sure you want to delete this template?')">
                                            @csrf
                                            @method('DELETE')

                                            <button type="submit"
                                                    class="inline-flex items-center gap-1.5 rounded-lg border border-rose-100 px-3 py-2 text-xs font-semibold text-rose-600 transition hover:bg-rose-50">
                                                <i class="fa-solid fa-trash text-[10px]"></i>
                                                Delete
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-5 py-16 text-center">
                                    <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-brand-50 text-brand-600">
                                        <i class="fa-solid fa-award text-3xl"></i>
                                    </div>

                                    <h4 class="mt-4 font-semibold text-slate-800">
                                        No certificate templates found
                                    </h4>

                                    <p class="mt-1 text-sm text-slate-500">
                                        Create your first template to get started.
                                    </p>

                                    <a href="{{ route('admin.certificates.create') }}"
                                       class="mt-4 inline-flex items-center gap-2 rounded-xl bg-brand-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-brand-700">
                                        <i class="fa-solid fa-plus text-xs"></i>
                                        Create Template
                                    </a>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if($templates->hasPages())
                <div class="border-t border-slate-100 px-5 py-4">
                    {{ $templates->links() }}
                </div>
            @endif
        </div>

    </div>
</x-layouts.admin>
