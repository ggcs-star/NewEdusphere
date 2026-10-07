<x-layouts.admin :title="$pageTitle">
    <div x-data="{ rejectOpen: null }">

        {{-- Welcome banner --}}
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
            <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
                <div class="max-w-lg">
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">{{ $pageTitle }}</h2>
                    <p class="mt-1 text-sm font-semibold text-accent-600">
                        @if ($isPendingView)
                            Review and approve courses submitted by instructors.
                        @else
                            Manage every course on the platform.
                        @endif
                    </p>
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

                    <a href="{{ route('admin.courses.create') }}"
                        class="flex-shrink-0 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                        Add Course
                    </a>
                </div>
            </div>

            <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
            <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
        </div>

        {{-- Stats --}}
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
            <x-admin.stat-card
                icon="fa-book-open"
                color="violet"
                :count="$stats['active']['count']"
                label="Active Courses"
                :change="$stats['active']['change']"
                :direction="$stats['active']['direction']"
            />
            <x-admin.stat-card
                icon="fa-hourglass-half"
                color="amber"
                :count="$stats['pending']['count']"
                label="Pending Courses"
                :change="$stats['pending']['change']"
                :direction="$stats['pending']['direction']"
            />
            <x-admin.stat-card
                icon="fa-star"
                color="emerald"
                :count="$stats['free']['count']"
                label="Free Courses"
                :change="$stats['free']['change']"
                :direction="$stats['free']['direction']"
            />
            <x-admin.stat-card
                icon="fa-tag"
                color="sky"
                :count="$stats['paid']['count']"
                label="Paid Courses"
                :change="$stats['paid']['change']"
                :direction="$stats['paid']['direction']"
            />
        </div>

        {{-- Filters --}}
        <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
            <x-admin.search-input :value="$search" placeholder="Search courses..." />

            <select name="category_id" onchange="this.form.submit()"
                class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none">
                <option value="all">All Categories</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" @selected($selectedCategory == $cat->id)>{{ $cat->name }}</option>
                @endforeach
            </select>

            @unless ($isPendingView)
                <select name="status" onchange="this.form.submit()"
                    class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none">
                    <option value="all" @selected($selectedStatus == 'all')>All Status</option>
                    <option value="draft" @selected($selectedStatus == 'draft')>Draft</option>
                    <option value="pending" @selected($selectedStatus == 'pending')>Pending</option>
                    <option value="active" @selected($selectedStatus == 'active')>Active</option>
                    <option value="rejected" @selected($selectedStatus == 'rejected')>Rejected</option>
                </select>
            @endunless

            <select name="price" onchange="this.form.submit()"
                class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none">
                <option value="all" @selected($selectedPrice == 'all')>All Prices</option>
                <option value="free" @selected($selectedPrice == 'free')>Free</option>
                <option value="paid" @selected($selectedPrice == 'paid')>Paid</option>
            </select>

            <select name="instructor_id" onchange="this.form.submit()"
                class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none max-w-[180px]">
                <option value="all" @selected($selectedInstructor == 'all')>All Instructors</option>
                @foreach ($instructors as $instructor)
                    <option value="{{ $instructor->id }}" @selected($selectedInstructor == $instructor->id)>{{ $instructor->name }}</option>
                @endforeach
            </select>

            <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Filter</button>
        </form>

        {{-- Table --}}
        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                            <th class="px-5 py-3">Course</th>
                            <th class="px-5 py-3">Instructor</th>
                            <th class="px-5 py-3">Category</th>
                            <th class="px-5 py-3">Content</th>
                            <th class="px-5 py-3">Students</th>
                            <th class="px-5 py-3">Price</th>
                            <th class="px-5 py-3">Status</th>
                            <th class="px-5 py-3 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($courses as $course)
                            <tr class="hover:bg-brand-50/30 transition">
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center gap-3">
                                        <div class="h-11 w-11 flex-shrink-0 rounded-xl bg-gradient-to-br from-brand-700 to-brand-900 flex items-center justify-center overflow-hidden">
                                            @if ($course->thumbnail)
                                                <img src="{{ Storage::url($course->thumbnail) }}" class="h-full w-full object-cover">
                                            @else
                                                <i class="fa-solid fa-book-open text-white text-sm"></i>
                                            @endif
                                        </div>
                                        <div class="min-w-0">
                                            <p class="text-sm font-semibold text-brand-950 truncate max-w-xs" title="{{ $course->title }}">{{ $course->title }}</p>
                                            <p class="text-xs text-slate-400">{{ ucfirst(str_replace('_', ' ', $course->level)) }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $course->instructor->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-600">{{ $course->category->name ?? '—' }}</td>
                                <td class="px-5 py-3.5 text-slate-500 text-xs">
                                    <p>{{ $course->sections_count }} Section{{ $course->sections_count === 1 ? '' : 's' }}</p>
                                    <p>{{ $course->lessons_count }} Lesson{{ $course->lessons_count === 1 ? '' : 's' }}</p>
                                </td>
                                <td class="px-5 py-3.5">
                                    <span class="inline-flex items-center gap-1.5 text-slate-600">
                                        <i class="fa-solid fa-user-graduate text-slate-400 text-xs"></i>
                                        {{ $course->enrollments_count }}
                                    </span>
                                </td>
                                <td class="px-5 py-3.5 text-slate-600">
                                    @if ($course->is_free)
                                        <span class="text-emerald-600 font-semibold">Free</span>
                                    @elseif ($course->hasDiscount())
                                        <span class="text-slate-400 line-through text-xs">${{ number_format($course->price, 2) }}</span>
                                        <span class="text-rose-600 font-semibold ml-1">${{ number_format($course->discount_price, 2) }}</span>
                                        <span class="block text-[10px] text-rose-500">{{ $course->discountPercent() }}% off</span>
                                    @else
                                        ${{ number_format($course->price, 2) }}
                                    @endif
                                </td>
                                <td class="px-5 py-3.5">
                                    <form action="{{ route('admin.courses.update-status', $course) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <select name="status" onchange="this.form.submit()"
                                            class="rounded-full border-0 pl-3 pr-7 py-1.5 text-xs font-semibold outline-none focus:ring-2 focus:ring-brand-200 cursor-pointer
                                            @switch($course->status)
                                                @case('active') bg-emerald-50 text-emerald-700 @break
                                                @case('pending') bg-amber-50 text-amber-700 @break
                                                @case('rejected') bg-rose-50 text-rose-700 @break
                                                @default bg-slate-100 text-slate-600
                                            @endswitch">
                                            @foreach (['draft' => 'Draft', 'pending' => 'Pending', 'active' => 'Active', 'rejected' => 'Rejected'] as $value => $label)
                                                <option value="{{ $value }}" @selected($course->status === $value)>{{ $label }}</option>
                                            @endforeach
                                        </select>
                                    </form>
                                </td>
                                <td class="px-5 py-3.5">
                                    <div class="flex items-center justify-end gap-1.5">
                                        @if ($course->status === 'pending')
                                            <form action="{{ route('admin.courses.approve', $course) }}" method="POST">
                                                @csrf
                                                <button type="submit" class="h-8 w-8 rounded-lg bg-emerald-50 text-emerald-600 hover:bg-emerald-100 flex items-center justify-center" title="Approve">
                                                    <i class="fa-solid fa-check text-xs"></i>
                                                </button>
                                            </form>
                                            <button @click="rejectOpen = {{ $course->id }}" type="button" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center" title="Reject">
                                                <i class="fa-solid fa-xmark text-xs"></i>
                                            </button>
                                        @endif
                                        <a href="{{ route('admin.courses.builder', $course) }}" class="h-8 w-8 rounded-lg bg-brand-50 text-brand-700 hover:bg-brand-100 flex items-center justify-center" title="Manage Content">
                                            <i class="fa-solid fa-layer-group text-xs"></i>
                                        </a>
                                        <a href="{{ route('admin.courses.edit', $course) }}" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center" title="Edit">
                                            <i class="fa-solid fa-pen text-xs"></i>
                                        </a>
                                        <form action="{{ route('admin.courses.destroy', $course) }}" method="POST" onsubmit="return confirm('Delete this course? This also removes its sections, lessons and enrollments.');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center" title="Delete">
                                                <i class="fa-solid fa-trash text-xs"></i>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>

                            @if ($course->status === 'pending')
                                <div x-show="rejectOpen === {{ $course->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                    <div class="absolute inset-0 bg-slate-900/60" @click="rejectOpen = null"></div>
                                    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl p-6">
                                        <h3 class="text-base font-semibold text-brand-950 mb-1">Reject "{{ $course->title }}"</h3>
                                        <p class="text-sm text-slate-500 mb-4">Let the instructor know what needs fixing.</p>
                                        <form action="{{ route('admin.courses.reject', $course) }}" method="POST">
                                            @csrf
                                            <textarea name="rejection_reason" rows="3" required placeholder="Reason for rejection..."
                                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none"></textarea>
                                            <div class="flex justify-end gap-2 mt-4">
                                                <button type="button" @click="rejectOpen = null" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                                                <button type="submit" class="rounded-lg bg-rose-600 px-4 py-2 text-sm font-semibold text-white hover:bg-rose-700">Reject Course</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            @endif
                        @empty
                            <tr>
                                <td colspan="8" class="px-5 py-16 text-center text-slate-400">
                                    <i class="fa-solid fa-book-open text-2xl mb-2 block"></i>
                                    No courses found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <x-admin.pagination :paginator="$courses" />
    </div>
</x-layouts.admin>
