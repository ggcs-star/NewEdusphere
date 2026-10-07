@props(['section' => null, 'lesson' => null, 'mode'])

@php
    $isEdit = $mode === 'edit';
    $showExpr = $isEdit ? "editLessonOpen === {$lesson->id}" : "addLessonOpen === {$section->id}";
    $closeExpr = $isEdit ? 'editLessonOpen = null' : 'addLessonOpen = null';
    $action = $isEdit ? route('admin.lessons.update', $lesson) : route('admin.lessons.store', $section);
    $model = $lesson; // may be null in add mode
@endphp

<div x-show="{{ $showExpr }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60" @click="{{ $closeExpr }}"></div>
    <div class="relative w-full max-w-md rounded-2xl bg-white shadow-xl" x-data="{ lessonType: '{{ old('lesson_type', $model->lesson_type ?? 'video') }}' }">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4">
            <h3 class="text-base font-semibold text-brand-950">{{ $isEdit ? 'Edit Lesson' : 'Add Lesson' }}</h3>
            <button type="button" @click="{{ $closeExpr }}" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form action="{{ $action }}" method="POST" class="px-6 py-5 space-y-4">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Lesson Title <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required value="{{ $model->title ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Lesson Type</label>
                <select name="lesson_type" x-model="lessonType" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                    <option value="video">Video</option>
                    <option value="document">Document</option>
                    <option value="quiz">Quiz</option>
                </select>
            </div>

            <div x-show="lessonType === 'video'" class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Source</label>
                    <select name="video_type" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                        <option value="youtube" @selected(($model->video_type ?? '') == 'youtube')>YouTube</option>
                        <option value="vimeo" @selected(($model->video_type ?? '') == 'vimeo')>Vimeo</option>
                        <option value="upload" @selected(($model->video_type ?? '') == 'upload')>Upload</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Duration</label>
                    <input type="text" name="duration" placeholder="12:30" value="{{ $model->duration ?? '' }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div x-show="lessonType === 'video'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Video URL</label>
                <input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..." value="{{ $model->video_url ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>

            <div x-show="lessonType === 'document'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Content</label>
                <textarea name="summary" rows="4" class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ $model->summary ?? '' }}</textarea>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="{{ $closeExpr }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-accent-600 px-4 py-2 text-sm font-semibold text-white hover:bg-accent-700">{{ $isEdit ? 'Save Changes' : 'Add Lesson' }}</button>
            </div>
        </form>
    </div>
</div>
