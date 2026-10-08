@php $isEdit = $course->exists; @endphp
<x-layouts.admin :title="$isEdit ? 'Edit Course' : 'Add Course'">
    <div x-data="{
        categoryId: '{{ old('category_id', $course->category_id) }}',
        isFree: {{ old('is_free', $course->is_free) ? 'true' : 'false' }},
        hasDiscount: {{ old('discount_flag', $course->discount_price !== null) ? 'true' : 'false' }},
        price: {{ old('price', $course->price ?? 0) }},
        discountPrice: {{ old('discount_price', $course->discount_price ?? 0) }},
        videoType: '{{ old('preview_video_type', $course->preview_video_type ?? 'youtube') }}',
        get discountPercent() {
            if (!this.price || !this.discountPrice) return 0;
            const pct = ((this.price - this.discountPrice) / this.price) * 100;
            return pct > 0 ? pct.toFixed(0) : 0;
        }
    }">
        <div class="flex items-center gap-3 mb-6">
            <a href="{{ route('admin.courses.index') }}" class="h-9 w-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-700">
                <i class="fa-solid fa-arrow-left text-xs"></i>
            </a>
            <div>
                <h2 class="text-2xl font-bold text-brand-950">{{ $isEdit ? 'Edit Course' : 'Add Course' }}</h2>
                <p class="text-sm text-slate-500 mt-0.5">{{ $isEdit ? $course->title : 'Fill in the details to create a new course.' }}</p>
            </div>
        </div>

        <form action="{{ $isEdit ? route('admin.courses.update', $course) : route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @if ($isEdit) @method('PUT') @endif

            {{-- Basic info --}}
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h3 class="font-semibold text-brand-950 mb-4">Basic Information</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Course Title <span class="text-rose-500">*</span></label>
                        <input type="text" name="title" required value="{{ old('title', $course->title) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Short Description</label>
                        <input type="text" name="short_description" maxlength="500" value="{{ old('short_description', $course->short_description) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>

                    <div class="sm:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Description</label>
                        <textarea name="description" class="tinymce-editor">{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Instructor <span class="text-rose-500">*</span></label>
                        <select name="user_id" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            @foreach ($instructors as $instructor)
                                <option value="{{ $instructor->id }}" @selected(old('user_id', $course->user_id) == $instructor->id)>{{ $instructor->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Language</label>
                        <input type="text" name="language" value="{{ old('language', $course->language ?? 'English') }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Category <span class="text-rose-500">*</span></label>
                        <select name="category_id" x-model="categoryId" required class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="">Select category</option>
                            @foreach ($categories as $cat)
                                <option value="{{ $cat->id }}" @selected(old('category_id', $course->category_id) == $cat->id)>{{ $cat->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Sub-category</label>
                        <select name="sub_category_id" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="">None</option>
                            @foreach ($categories as $cat)
                                @foreach ($cat->children as $child)
                                    <option value="{{ $child->id }}" x-show="categoryId == {{ $cat->id }}" @selected(old('sub_category_id', $course->sub_category_id) == $child->id)>{{ $child->name }}</option>
                                @endforeach
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Level</label>
                        <select name="level" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            @foreach (['beginner' => 'Beginner', 'intermediate' => 'Intermediate', 'advanced' => 'Advanced', 'all_levels' => 'All Levels'] as $value => $label)
                                <option value="{{ $value }}" @selected(old('level', $course->level) == $value)>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="sm:col-span-2">
                        <label class="inline-flex items-center gap-2">
                            <input type="hidden" name="is_top_course" value="0">
                            <input type="checkbox" name="is_top_course" value="1" @checked(old('is_top_course', $course->is_top_course))
                                class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                            <span class="text-sm text-slate-700">Feature this as a top course</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- Pricing --}}
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h3 class="font-semibold text-brand-950 mb-4">Pricing</h3>
                <label class="inline-flex items-center gap-2 mb-4">
                    <input type="hidden" name="is_free" value="0">
                    <input type="checkbox" name="is_free" value="1" x-model="isFree"
                        class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                    <span class="text-sm text-slate-700">This is a free course</span>
                </label>

                <div x-show="!isFree" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Price (Rs.)</label>
                        <input type="number" step="0.01" min="0" name="price" x-model.number="price"
                            class="w-full sm:w-64 rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>

                    <label class="inline-flex items-center gap-2">
                        <input type="hidden" name="discount_flag" value="0">
                        <input type="checkbox" name="discount_flag" value="1" x-model="hasDiscount"
                            class="rounded border-slate-300 text-brand-600 focus:ring-brand-500">
                        <span class="text-sm text-slate-700">Check if this course has a discount</span>
                    </label>

                    <div x-show="hasDiscount">
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Discounted Price (Rs.)</label>
                        <input type="number" step="0.01" min="0" name="discount_price" x-model.number="discountPrice"
                            class="w-full sm:w-64 rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                        <p class="text-xs text-slate-400 mt-1">This course has <span class="text-rose-500 font-semibold" x-text="discountPercent + '%'"></span> discount.</p>
                    </div>
                </div>
            </div>

            {{-- Media --}}
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h3 class="font-semibold text-brand-950 mb-4">Media</h3>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Thumbnail <span class="text-slate-400">(16:9 recommended)</span></label>
                        <input type="file" name="thumbnail" accept="image/*"
                            class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                        @if ($isEdit && $course->thumbnail)
                            <img src="{{ Storage::url($course->thumbnail) }}" class="mt-2 h-20 rounded-lg object-cover">
                        @endif
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Course Overview Provider</label>
                        <select name="preview_video_type" x-model="videoType" class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                            <option value="youtube">YouTube</option>
                            <option value="vimeo">Vimeo</option>
                            <option value="upload">Upload Video</option>
                        </select>
                    </div>
                </div>

                <div class="mt-4" x-show="videoType !== 'upload'">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Preview Video URL</label>
                    <input type="text" name="preview_video_url" placeholder="https://youtube.com/watch?v=..." value="{{ old('preview_video_url', $course->preview_video_type !== 'upload' ? $course->preview_video_url : '') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>

                <div class="mt-4" x-show="videoType === 'upload'">
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Upload Video <span class="text-slate-400">(MP4, WebM, OGG — max 50MB)</span></label>
                    <input type="file" name="preview_video_file" accept="video/mp4,video/webm,video/ogg"
                        class="w-full text-sm text-slate-600 file:mr-3 file:rounded-lg file:border-0 file:bg-brand-50 file:px-3 file:py-2 file:text-sm file:font-medium file:text-brand-700 hover:file:bg-brand-100">
                    @if ($isEdit && $course->preview_video_type === 'upload' && $course->preview_video_url)
                        <p class="text-xs text-slate-400 mt-1">Current file: {{ basename($course->preview_video_url) }}</p>
                    @endif
                </div>
            </div>

            {{-- Outcomes / Requirements --}}
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h3 class="font-semibold text-brand-950 mb-4">Outcomes &amp; Requirements</h3>
                <p class="text-xs text-slate-400 mb-3">One item per line.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">What students will learn</label>
                        <textarea name="outcomes_text" rows="5" placeholder="Build real-world projects&#10;Master the fundamentals"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ old('outcomes_text', implode("\n", $course->outcomes ?? [])) }}</textarea>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Requirements</label>
                        <textarea name="requirements_text" rows="5" placeholder="Basic computer skills&#10;No prior experience needed"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ old('requirements_text', implode("\n", $course->requirements ?? [])) }}</textarea>
                    </div>
                </div>
            </div>

            {{-- SEO --}}
            <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm">
                <h3 class="font-semibold text-brand-950 mb-4">SEO</h3>
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Meta Keywords <span class="text-slate-400">(comma separated)</span></label>
                        <input type="text" name="meta_keywords" placeholder="laravel, php, web development" value="{{ old('meta_keywords', $course->meta_keywords) }}"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1.5">Meta Description</label>
                        <textarea name="meta_description" rows="3" maxlength="500"
                            class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">{{ old('meta_description', $course->meta_description) }}</textarea>
                    </div>
                </div>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.courses.index') }}" class="rounded-lg px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</a>
                <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                    {{ $isEdit ? 'Save Changes' : 'Create Course' }}
                </button>
            </div>
        </form>
    </div>
</x-layouts.admin>
