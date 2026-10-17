<x-layouts.admin title="Enrollment Details">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="flex items-center gap-4 min-w-0 flex-1">
                <a href="{{ route('admin.enrollments.index') }}" class="h-10 w-10 flex-shrink-0 rounded-full bg-white/80 text-brand-700 hover:bg-white flex items-center justify-center shadow-sm transition">
                    <i class="fa-solid fa-arrow-left text-sm"></i>
                </a>
                <div class="h-16 w-16 flex-shrink-0 rounded-2xl bg-brand-900 text-white shadow-lg flex items-center justify-center font-bold text-xl">
                    {{ strtoupper(substr($enrollment->user->name ?? '?', 0, 1)) }}
                </div>
                <div class="min-w-0">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-brand-950 tracking-tight truncate" title="{{ $enrollment->user->name ?? '—' }}">{{ $enrollment->user->name ?? '—' }}</h2>
                    <p class="mt-1 text-sm font-semibold text-accent-600 truncate">{{ $enrollment->user->email ?? '' }}</p>
                </div>
            </div>

            <div class="flex flex-shrink-0 items-center gap-2">
                <a href="{{ route('admin.enrollments.edit', $enrollment) }}"
                    class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-5 py-2.5 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                    <i class="fa-solid fa-pen text-xs"></i> Edit
                </a>
                <form action="{{ route('admin.enrollments.destroy', $enrollment) }}" method="POST" onsubmit="return confirm('Remove this enrollment?');">
                    @csrf @method('DELETE')
                    <button type="submit" class="inline-flex items-center gap-2 rounded-full bg-white/90 px-5 py-2.5 text-sm font-semibold text-rose-600 shadow-sm hover:bg-white transition">
                        <i class="fa-solid fa-trash text-xs"></i> Unenroll
                    </button>
                </form>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Summary --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-5 mb-8">
        <x-admin.info-card icon="fa-user-graduate" color="sky" label="Student">
            <a href="{{ route('admin.users.show', $enrollment->user) }}" class="hover:text-brand-700">{{ $enrollment->user->name ?? '—' }}</a>
        </x-admin.info-card>
        <x-admin.info-card icon="fa-chalkboard-user" color="amber" label="Instructor">
            {{ $enrollment->course->instructor->name ?? '—' }}
        </x-admin.info-card>
        <x-admin.info-card icon="fa-tag" color="rose" label="Price">
            @if (! $enrollment->course)
                —
            @elseif ($enrollment->course->is_free)
                Free
            @elseif ($enrollment->course->hasDiscount())
                <span class="text-base">{{ format_price($enrollment->course->discount_price) }}</span>
                <span class="text-sm font-medium text-slate-400 line-through ml-1">{{ format_price($enrollment->course->price) }}</span>
            @else
                {{ format_price($enrollment->course->price) }}
            @endif
        </x-admin.info-card>
    </div>

    {{-- Course details --}}
    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-6">
        <div class="flex items-center gap-2 mb-4">
            <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-book-open text-xs"></i>
            </span>
            <h3 class="text-lg font-bold text-brand-950">Course</h3>
            <span class="ml-auto text-xs text-slate-400">
                <i class="fa-regular fa-calendar-check mr-1"></i>Enrolled {{ $enrollment->created_at->format('D, d M Y') }}
            </span>
        </div>

        <div class="flex items-center gap-4">
            <div class="h-14 w-14 flex-shrink-0 rounded-xl bg-brand-500 flex items-center justify-center overflow-hidden">
                @if ($enrollment->course?->thumbnail)
                    <img src="{{ Storage::url($enrollment->course->thumbnail) }}" class="h-full w-full object-cover">
                @else
                    <i class="fa-solid fa-book-open text-white text-lg"></i>
                @endif
            </div>
            <div class="min-w-0">
                <a href="{{ route('admin.courses.show', $enrollment->course) }}" class="text-sm font-semibold text-brand-950 hover:text-brand-700 truncate block" title="{{ $enrollment->course->title ?? '—' }}">
                    {{ $enrollment->course->title ?? '—' }}
                </a>
                <div class="flex flex-wrap items-center gap-1.5 mt-1.5">
                    <x-admin.badge color="brand">{{ $enrollment->course->category->name ?? '—' }}</x-admin.badge>
                    @if ($enrollment->course?->subCategory)
                        <x-admin.badge color="accent">{{ $enrollment->course->subCategory->name }}</x-admin.badge>
                    @endif
                    <x-admin.badge :color="$enrollment->course?->status === 'active' ? 'emerald' : 'slate'">{{ ucfirst($enrollment->course->status ?? '—') }}</x-admin.badge>
                </div>
            </div>
        </div>
    </div>

    {{-- Quiz performance in this course --}}
    @if ($quizPerformance->isNotEmpty())
    <div class="mt-12 pt-8 border-t border-slate-200" x-data="{ open: null }">
        <div class="flex items-center gap-2 mb-1">
            <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-circle-question text-xs"></i>
            </span>
            <h3 class="text-lg font-bold text-brand-950">Quiz Performance</h3>
            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">{{ $quizPerformance->count() }}</span>
        </div>
        <p class="text-sm text-slate-500 mb-4 ml-9">How {{ $enrollment->user->name ?? 'this student' }} performed on each quiz in this course.</p>

        <div class="space-y-3">
            @foreach ($quizPerformance as $i => $qp)
                @php $attempt = $qp['attempt']; @endphp
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <button type="button" @if ($attempt) @click="open = open === {{ $i }} ? null : {{ $i }}" @endif
                        class="w-full flex items-center justify-between gap-4 px-5 py-4 text-left {{ $attempt ? 'hover:bg-brand-50/30 cursor-pointer' : 'cursor-default' }} transition">
                        <div class="min-w-0">
                            <p class="text-sm font-semibold text-brand-950 truncate">{{ $qp['lesson']->title }}</p>
                            @if ($attempt)
                                <p class="text-xs text-slate-400 mt-0.5">Submitted {{ $attempt->submitted_at?->format('D, d M Y · g:ia') }}</p>
                            @else
                                <p class="text-xs text-slate-400 mt-0.5">Not attempted yet</p>
                            @endif
                        </div>
                        <div class="flex items-center gap-3 flex-shrink-0">
                            @if ($attempt)
                                <x-admin.badge :color="$attempt->total_score > 0 && ($attempt->score / $attempt->total_score) >= 0.5 ? 'emerald' : 'rose'">
                                    {{ $attempt->score }} / {{ $attempt->total_score }} pts
                                </x-admin.badge>
                                <i class="fa-solid fa-chevron-down text-xs text-slate-400 transition-transform" :class="open === {{ $i }} ? 'rotate-180' : ''"></i>
                            @else
                                <x-admin.badge color="slate">Not attempted</x-admin.badge>
                            @endif
                        </div>
                    </button>

                    @if ($attempt)
                    <div x-show="open === {{ $i }}" x-cloak x-transition
                        class="border-t border-slate-100 divide-y divide-slate-100">
                        @foreach ($qp['review'] as $q)
                            <div class="px-5 py-4">
                                <p class="text-sm font-medium text-slate-800 mb-2">
                                    {{ $loop->iteration }}. {{ $q['title'] }}
                                    <span class="text-xs font-normal text-slate-400">({{ $q['type'] === 'multiple_choice' ? 'Multiple choice' : 'Single choice' }} · {{ $q['points'] }} {{ $q['points'] === 1 ? 'point' : 'points' }})</span>
                                </p>
                                <div class="flex flex-wrap gap-1.5">
                                    @foreach ($q['options'] as $idx => $opt)
                                        @php
                                            $isGiven = $q['type'] === 'multiple_choice'
                                                ? in_array($idx, $q['given_answer'] ?? [])
                                                : $q['given_answer'] === $idx;
                                            $isCorrectOpt = in_array($idx, $q['correct_answers']);
                                        @endphp
                                        <span class="inline-flex items-center gap-1.5 rounded-lg px-2.5 py-1 text-xs font-medium border
                                            {{ $isCorrectOpt ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : ($isGiven ? 'bg-rose-50 border-rose-200 text-rose-700' : 'bg-slate-50 border-slate-200 text-slate-500') }}">
                                            @if ($isCorrectOpt)
                                                <i class="fa-solid fa-check text-[10px]"></i>
                                            @elseif ($isGiven)
                                                <i class="fa-solid fa-xmark text-[10px]"></i>
                                            @endif
                                            {{ $opt }}
                                            @if ($isGiven)<span class="opacity-70">(their answer)</span>@endif
                                        </span>
                                    @endforeach
                                    @if ($q['type'] === 'single_choice' && $q['given_answer'] === null)
                                        <span class="inline-flex items-center rounded-lg px-2.5 py-1 text-xs font-medium border bg-slate-50 border-slate-200 text-slate-400">Not answered</span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Other courses this student is enrolled in --}}
    <div class="mt-12 pt-8 border-t border-slate-200">
        <div class="flex items-center gap-2 mb-1">
            <span class="h-7 w-7 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center flex-shrink-0">
                <i class="fa-solid fa-layer-group text-xs"></i>
            </span>
            <h3 class="text-lg font-bold text-brand-950">Additional Enrollments</h3>
            <span class="rounded-full bg-slate-100 px-2.5 py-0.5 text-xs font-semibold text-slate-500">{{ $otherEnrollments->count() }}</span>
        </div>
        <p class="text-sm text-slate-500 mb-4 ml-9">Other courses {{ $enrollment->user->name ?? 'this student' }} is currently enrolled in.</p>

        @if ($otherEnrollments->isEmpty())
            <x-admin.empty-state icon="fa-layer-group" title="No other enrollments" description="This student isn't enrolled in any other course yet." />
        @else
            <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-sm">
                        <thead>
                            <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                                <th class="px-5 py-3">Course</th>
                                <th class="px-5 py-3">Category</th>
                                <th class="px-5 py-3">Enrolled On</th>
                                <th class="px-5 py-3 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @foreach ($otherEnrollments as $other)
                                <tr class="hover:bg-brand-50/30 transition">
                                    <td class="px-5 py-3.5">
                                        <a href="{{ route('admin.courses.show', $other->course) }}" class="text-sm font-semibold text-brand-950 hover:text-brand-700 truncate max-w-xs block" title="{{ $other->course->title ?? '—' }}">
                                            {{ $other->course->title ?? '—' }}
                                        </a>
                                    </td>
                                    <td class="px-5 py-3.5">
                                        <x-admin.badge color="brand">{{ $other->course->category->name ?? '—' }}</x-admin.badge>
                                    </td>
                                    <td class="px-5 py-3.5 text-slate-500">{{ $other->created_at->format('D, d M Y') }}</td>
                                    <td class="px-5 py-3.5">
                                        <div class="flex items-center justify-end gap-1.5">
                                            <a href="{{ route('admin.enrollments.show', $other) }}" class="h-8 w-8 rounded-lg bg-brand-50 text-brand-700 hover:bg-brand-100 flex items-center justify-center" title="Details">
                                                <i class="fa-solid fa-eye text-xs"></i>
                                            </a>
                                            <a href="{{ route('admin.enrollments.edit', $other) }}" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center" title="Edit">
                                                <i class="fa-solid fa-pen text-xs"></i>
                                            </a>
                                            <form action="{{ route('admin.enrollments.destroy', $other) }}" method="POST" onsubmit="return confirm('Remove this enrollment?');">
                                                @csrf @method('DELETE')
                                                <button type="submit" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center" title="Delete">
                                                    <i class="fa-solid fa-trash text-xs"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        @endif
    </div>
</x-layouts.admin>
