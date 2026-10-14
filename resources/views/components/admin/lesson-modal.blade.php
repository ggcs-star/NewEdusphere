@props(['section' => null, 'lesson' => null, 'mode', 'routePrefix' => 'admin'])

@php
    $isEdit = $mode === 'edit';
    $showExpr = $isEdit ? "editLessonOpen === {$lesson->id}" : "addLessonOpen === {$section->id}";
    $closeExpr = $isEdit ? 'editLessonOpen = null' : 'addLessonOpen = null';
    $action = $isEdit ? route("{$routePrefix}.lessons.update", $lesson) : route("{$routePrefix}.lessons.store", $section);
    $model = $lesson; // may be null in add mode
@endphp

<div x-show="{{ $showExpr }}" x-cloak class="fixed inset-0 z-50 flex items-center justify-center p-4">
    <div class="absolute inset-0 bg-slate-900/60" @click="{{ $closeExpr }}"></div>
    <div class="relative w-full max-w-md max-h-[85vh] overflow-y-auto rounded-2xl bg-white shadow-xl"
        x-data="{
            lessonType: '{{ old('lesson_type', $model->lesson_type ?? 'video') }}',
            videoType: '{{ old('video_type', $model->video_type ?? 'youtube') }}',
            docMode: '{{ old('lesson_type') ? 'write' : (($model->attachment ?? null) ? 'upload' : 'write') }}',
        }">
        <div class="flex items-center justify-between border-b border-slate-100 px-6 py-4 sticky top-0 bg-white">
            <h3 class="text-base font-semibold text-brand-950">{{ $isEdit ? 'Edit Lesson' : 'Add Lesson' }}</h3>
            <button type="button" @click="{{ $closeExpr }}" class="text-slate-400 hover:text-slate-600">
                <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12" /></svg>
            </button>
        </div>

        <form action="{{ $action }}" method="POST" enctype="multipart/form-data" class="px-6 py-5 space-y-4">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Lesson Title <span class="text-rose-500">*</span></label>
                <input type="text" name="title" required value="{{ $model->title ?? '' }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Lesson Type</label>
                <x-admin.searchable-select
                    name="lesson_type"
                    :options="['video' => 'Video', 'document' => 'Document', 'quiz' => 'Quiz']"
                    bind-to="lessonType"
                />
            </div>

            {{-- Video --}}
            <div x-show="lessonType === 'video'" class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Source</label>
                    <x-admin.searchable-select
                        name="video_type"
                        :options="['youtube' => 'YouTube', 'upload' => 'Upload']"
                        bind-to="videoType"
                    />
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Duration</label>
                    <input type="text" name="duration" placeholder="12:30" value="{{ $model->duration ?? '' }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                </div>
            </div>

            <div x-show="lessonType === 'video' && videoType !== 'upload'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Video URL</label>
                <input type="text" name="video_url" placeholder="https://youtube.com/watch?v=..." value="{{ ($model->video_type ?? 'youtube') !== 'upload' ? old('video_url', $model->video_url ?? '') : old('video_url') }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">
                <p class="text-xs text-slate-400 mt-1">A watch link, share link or embed link all work.</p>
            </div>

            <div x-show="lessonType === 'video' && videoType === 'upload'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Upload Video <span class="text-slate-400">(MP4, WebM, OGG — max 50MB)</span></label>
                <input type="file" name="video_file" accept="video/mp4,video/webm,video/ogg"
                    class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                @if ($isEdit && $model->video_type === 'upload' && $model->video_url)
                    <p class="text-xs text-slate-400 mt-1">Current file: {{ basename($model->video_url) }}</p>
                @endif
            </div>

            {{-- Document --}}
            <div x-show="lessonType === 'document'">
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Content</label>
                <div class="inline-flex rounded-lg border border-slate-200 p-0.5 mb-3">
                    <button type="button" @click="docMode = 'write'"
                        :class="docMode === 'write' ? 'bg-brand-600 text-white' : 'text-slate-600'"
                        class="rounded-md px-3 py-1.5 text-xs font-medium transition">Write Content</button>
                    <button type="button" @click="docMode = 'upload'"
                        :class="docMode === 'upload' ? 'bg-brand-600 text-white' : 'text-slate-600'"
                        class="rounded-md px-3 py-1.5 text-xs font-medium transition">Upload File</button>
                </div>

                <div x-show="docMode === 'write'">
                    <textarea name="summary" rows="4" placeholder="Write the lesson content here..."
                        class="w-full rounded-lg border border-slate-300 px-3 py-2 text-sm outline-none focus:border-brand-500 focus:ring-1 focus:ring-brand-500">{{ $model->summary ?? '' }}</textarea>
                </div>

                <div x-show="docMode === 'upload'">
                    <input type="file" name="attachment_file" accept=".pdf,.doc,.docx,.ppt,.pptx,.xls,.xlsx,.txt,.zip"
                        class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                    <p class="text-xs text-slate-400 mt-1">PDF, Word, PowerPoint, Excel, text or zip — max 20MB.</p>
                    @if ($isEdit && $model->attachment)
                        <p class="text-xs text-slate-400 mt-1">Current file: {{ basename($model->attachment) }}</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-end gap-2 pt-2">
                <button type="button" @click="{{ $closeExpr }}" class="rounded-lg px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</button>
                <button type="submit" class="rounded-lg bg-accent-600 px-4 py-2 text-sm font-semibold text-white hover:bg-accent-700">{{ $isEdit ? 'Save Changes' : 'Add Lesson' }}</button>
            </div>
        </form>
    </div>
</div>
