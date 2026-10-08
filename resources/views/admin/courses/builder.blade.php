<x-layouts.admin title="Course Builder">
    <div x-data="{
        addSectionOpen: false,
        editSectionOpen: null,
        sortSectionsOpen: false,
        addLessonOpen: null,
        editLessonOpen: null,
        sortLessonsOpen: null,
        questionsOpen: null,
        addQuestionOpen: false,
        optionRows: ['', ''],
        addOption() { this.optionRows.push('') },
        removeOption(i) { if (this.optionRows.length > 2) this.optionRows.splice(i, 1) },
    }">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.courses.index') }}" class="h-9 w-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-700">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div class="flex-1">
                <h2 class="text-2xl font-bold text-brand-950">{{ $course->title }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">Build out sections, lessons and quizzes for this course.</p>
            </div>
            <x-admin.badge :color="$course->statusColor()">{{ ucfirst($course->status) }}</x-admin.badge>
            @if ($course->sections->count() > 1)
                <button @click="sortSectionsOpen = true" type="button" class="inline-flex items-center gap-1.5 rounded-lg bg-white border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:text-brand-700">
                    <i class="fa-solid fa-arrow-down-up-across-line text-xs"></i> Sort Sections
                </button>
            @endif
            <a href="{{ route('admin.courses.edit', $course) }}" class="rounded-lg bg-white border border-slate-200 px-4 py-2 text-sm font-medium text-slate-600 hover:text-brand-700">Edit Details</a>
        </div>

        <div class="space-y-4">
            @forelse ($course->sections as $section)
                <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
                    <div class="flex items-center justify-between gap-3 bg-brand-50/60 px-5 py-3.5">
                        <div class="flex items-center gap-3">
                            <span class="h-7 w-7 rounded-full bg-brand-700 text-white text-xs font-bold flex items-center justify-center">{{ $loop->iteration }}</span>
                            <h3 class="font-semibold text-brand-950">{{ $section->title }}</h3>
                            <span class="text-xs text-slate-400">{{ $section->lessons->count() }} lessons</span>
                        </div>
                        <div class="flex items-center gap-1.5">
                            @if ($section->lessons->count() > 1)
                                <button @click="sortLessonsOpen = {{ $section->id }}" type="button" class="h-8 w-8 rounded-lg hover:bg-white text-slate-500 hover:text-brand-700 flex items-center justify-center" title="Sort Lessons">
                                    <i class="fa-solid fa-arrow-down-up-across-line text-xs"></i>
                                </button>
                            @endif
                            <button @click="editSectionOpen = {{ $section->id }}" type="button" class="h-8 w-8 rounded-lg hover:bg-white text-slate-500 hover:text-brand-700 flex items-center justify-center">
                                <i class="fa-solid fa-pen text-xs"></i>
                            </button>
                            <form action="{{ route('admin.sections.destroy', $section) }}" method="POST" onsubmit="return confirm('Delete this section and all its lessons?');">
                                @csrf @method('DELETE')
                                <button type="submit" class="h-8 w-8 rounded-lg hover:bg-white text-slate-500 hover:text-rose-600 flex items-center justify-center">
                                    <i class="fa-solid fa-trash text-xs"></i>
                                </button>
                            </form>
                        </div>
                    </div>

                    <ul class="divide-y divide-slate-100">
                        @foreach ($section->lessons as $lesson)
                            <li class="flex items-center justify-between gap-3 px-5 py-3 hover:bg-slate-50/60">
                                <div class="flex items-center gap-3 min-w-0">
                                    <span class="h-8 w-8 flex-shrink-0 rounded-lg bg-brand-50 text-brand-600 flex items-center justify-center">
                                        <i class="fa-solid {{ ['video' => 'fa-circle-play', 'document' => 'fa-file-lines', 'quiz' => 'fa-circle-question'][$lesson->lesson_type] }} text-xs"></i>
                                    </span>
                                    <div class="min-w-0">
                                        <p class="text-sm font-medium text-slate-800 truncate">{{ $lesson->title }}</p>
                                        <p class="text-xs text-slate-400">{{ ucfirst($lesson->lesson_type) }}{{ $lesson->duration ? ' · '.$lesson->duration : '' }}</p>
                                    </div>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0">
                                    @if ($lesson->lesson_type === 'quiz')
                                        <button @click="questionsOpen = {{ $lesson->id }}" type="button" class="rounded-lg bg-brand-50 text-brand-700 px-3 py-1.5 text-xs font-semibold hover:bg-brand-100">
                                            {{ $lesson->questions->count() }} Questions
                                        </button>
                                    @endif
                                    <button @click="editLessonOpen = {{ $lesson->id }}" type="button" class="h-7 w-7 rounded text-slate-400 hover:text-brand-700 flex items-center justify-center">
                                        <i class="fa-solid fa-pen text-[11px]"></i>
                                    </button>
                                    <form action="{{ route('admin.lessons.destroy', $lesson) }}" method="POST" onsubmit="return confirm('Delete this lesson?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-7 w-7 rounded text-slate-400 hover:text-rose-600 flex items-center justify-center">
                                            <i class="fa-solid fa-trash text-[11px]"></i>
                                        </button>
                                    </form>
                                </div>
                            </li>

                            {{-- Edit lesson modal --}}
                            <x-admin.lesson-modal :lesson="$lesson" mode="edit" />

                            {{-- Manage questions modal --}}
                            <div x-show="questionsOpen === {{ $lesson->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                                <div class="absolute inset-0 bg-slate-900/60" @click="questionsOpen = null; addQuestionOpen = false"></div>
                                <div class="relative w-full max-w-lg max-h-[85vh] overflow-y-auto rounded-2xl bg-white shadow-xl">
                                    <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white">
                                        <h3 class="text-base font-semibold text-brand-950">Quiz Questions — {{ $lesson->title }}</h3>
                                        <button type="button" @click="questionsOpen = null; addQuestionOpen = false" class="text-slate-400 hover:text-slate-600">
                                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
                                        </button>
                                    </div>

                                    <div class="p-6 space-y-3">
                                        @forelse ($lesson->questions as $question)
                                            <div class="rounded-xl border border-slate-200 p-4">
                                                <div class="flex items-start justify-between gap-2">
                                                    <p class="text-sm font-medium text-slate-800">{{ $loop->iteration }}. {{ $question->title }}</p>
                                                    <form action="{{ route('admin.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Delete this question?');">
                                                        @csrf @method('DELETE')
                                                        <button type="submit" class="text-slate-400 hover:text-rose-600"><i class="fa-solid fa-trash text-xs"></i></button>
                                                    </form>
                                                </div>
                                                <ul class="mt-2 space-y-1">
                                                    @foreach ($question->options as $i => $option)
                                                        <li class="text-xs flex items-center gap-2 {{ in_array($i, $question->correct_answers) ? 'text-emerald-700 font-medium' : 'text-slate-500' }}">
                                                            <i class="fa-solid {{ in_array($i, $question->correct_answers) ? 'fa-circle-check' : 'fa-circle' }} text-[10px]"></i>
                                                            {{ $option }}
                                                        </li>
                                                    @endforeach
                                                </ul>
                                            </div>
                                        @empty
                                            <p class="text-sm text-slate-400 text-center py-4">No questions yet.</p>
                                        @endforelse

                                        {{-- Add question form --}}
                                        <div x-show="!addQuestionOpen">
                                            <button @click="addQuestionOpen = true; optionRows = ['', '']" type="button"
                                                class="w-full rounded-xl border-2 border-dashed border-brand-200 py-3 text-sm font-medium text-brand-600 hover:bg-brand-50 transition">
                                                <i class="fa-solid fa-plus mr-1"></i> Add Question
                                            </button>
                                        </div>

                                        <form x-show="addQuestionOpen" action="{{ route('admin.questions.store', $lesson) }}" method="POST" class="rounded-xl border border-brand-200 bg-brand-50/40 p-4 space-y-3">
                                            @csrf
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Question</label>
                                                <input type="text" name="title" required class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Type</label>
                                                <select name="type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                                                    <option value="single_choice">Single choice</option>
                                                    <option value="multiple_choice">Multiple choice</option>
                                                </select>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-medium text-slate-700 mb-1">Options <span class="text-slate-400">(check the correct answer(s))</span></label>
                                                <div class="space-y-2">
                                                    <template x-for="(row, i) in optionRows" :key="i">
                                                        <div class="flex items-center gap-2">
                                                            <input type="checkbox" name="correct[]" :value="i" class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                                                            <input type="text" name="options[]" x-model="optionRows[i]" required placeholder="Option text"
                                                                class="flex-1 rounded-lg border border-slate-300 px-3 py-1.5 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                                                            <button type="button" @click="removeOption(i)" class="text-slate-400 hover:text-rose-600"><i class="fa-solid fa-xmark text-xs"></i></button>
                                                        </div>
                                                    </template>
                                                </div>
                                                <button type="button" @click="addOption()" class="mt-2 text-xs font-medium text-brand-600 hover:text-brand-700">+ Add option</button>
                                            </div>
                                            <div class="flex justify-end gap-2 pt-1">
                                                <button type="button" @click="addQuestionOpen = false" class="rounded-lg px-3 py-1.5 text-xs font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                                                <button type="submit" class="rounded-lg bg-accent-600 px-4 py-1.5 text-xs font-semibold text-white hover:bg-accent-700">Save Question</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        <li class="px-5 py-3">
                            <button @click="addLessonOpen = {{ $section->id }}" type="button" class="text-sm font-medium text-brand-600 hover:text-brand-700">
                                <i class="fa-solid fa-plus mr-1"></i> Add Lesson
                            </button>
                        </li>
                    </ul>
                </div>

                {{-- Edit section modal --}}
                <div x-show="editSectionOpen === {{ $section->id }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
                    <div class="absolute inset-0 bg-slate-900/60" @click="editSectionOpen = null"></div>
                    <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-xl p-6">
                        <h3 class="text-base font-semibold text-brand-950 mb-4">Edit Section</h3>
                        <form action="{{ route('admin.sections.update', $section) }}" method="POST">
                            @csrf @method('PUT')
                            <input type="text" name="title" required value="{{ $section->title }}"
                                class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                            <div class="flex justify-end gap-2 mt-4">
                                <button type="button" @click="editSectionOpen = null" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                                <button type="submit" class="rounded-lg bg-accent-600 px-4 py-2 text-sm font-semibold text-white hover:bg-accent-700">Save</button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- Add lesson modal --}}
                <x-admin.lesson-modal :section="$section" mode="add" />

                {{-- Sort lessons modal --}}
                <x-admin.sort-modal
                    :items="$section->lessons"
                    heading="Sort Lessons — {{ $section->title }}"
                    :endpoint="route('admin.lessons.reorder', $section)"
                    :show-expr="'sortLessonsOpen === '.$section->id"
                    close-expr="sortLessonsOpen = null"
                />
            @empty
                <x-admin.empty-state
                    icon="fa-layer-group"
                    title="No sections yet"
                    description="Start building this course by adding your first section."
                />
            @endforelse
        </div>

        <button @click="addSectionOpen = true" type="button"
            class="mt-5 w-full rounded-2xl border-2 border-dashed border-brand-200 py-4 text-sm font-semibold text-brand-600 hover:bg-brand-50 transition">
            <i class="fa-solid fa-plus mr-1"></i> Add Section
        </button>

        {{-- Add section modal --}}
        <div x-show="addSectionOpen" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
            <div class="absolute inset-0 bg-slate-900/60" @click="addSectionOpen = false"></div>
            <div class="relative w-full max-w-sm rounded-2xl bg-white shadow-xl p-6">
                <h3 class="text-base font-semibold text-brand-950 mb-4">Add Section</h3>
                <form action="{{ route('admin.sections.store', $course) }}" method="POST">
                    @csrf
                    <input type="text" name="title" required placeholder="e.g. Getting Started"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    <div class="flex justify-end gap-2 mt-4">
                        <button type="button" @click="addSectionOpen = false" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                        <button type="submit" class="rounded-lg bg-accent-600 px-4 py-2 text-sm font-semibold text-white hover:bg-accent-700">Add Section</button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Sort sections modal --}}
        <x-admin.sort-modal
            :items="$course->sections"
            heading="Sort Sections"
            :endpoint="route('admin.sections.reorder', $course)"
            show-expr="sortSectionsOpen"
            close-expr="sortSectionsOpen = false"
        />
    </div>
</x-layouts.admin>
