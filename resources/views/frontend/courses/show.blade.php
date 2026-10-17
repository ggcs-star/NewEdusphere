@extends('layouts.frontend')

@section('title', $course->title ?? 'Course details')

@section('content')
@php
    $isEnrolled = (bool) ($isEnrolled ?? false);

    /* ---------- Helpers ---------- */
    $ic = function (string $paths, bool $fill = false): string {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="' . ($fill ? 'currentColor' : 'none')
            . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths . '</svg>';
    };

    $icons = [
        'check'    => $ic('<polyline points="20 6 9 17 4 12"/>'),
        'play'     => $ic('<polygon points="6 4 20 12 6 20 6 4"/>', true),
        'lock'     => $ic('<rect x="3" y="11" width="18" height="11" rx="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/>'),
        'clock'    => $ic('<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'),
        'globe'    => $ic('<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'),
        'user'     => $ic('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'),
        'level'    => $ic('<line x1="6" y1="20" x2="6" y2="14"/><line x1="12" y1="20" x2="12" y2="9"/><line x1="18" y1="20" x2="18" y2="4"/>'),
        'layers'   => $ic('<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>'),
        'star'     => $ic('<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>', true),
        'cart'     => $ic('<circle cx="9" cy="21" r="1.4"/><circle cx="20" cy="21" r="1.4"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>'),
        'bolt'     => $ic('<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'),
        'chevron'  => $ic('<polyline points="6 9 12 15 18 9"/>'),
        'close'    => $ic('<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>'),
        'device'   => $ic('<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'),
        'infinity' => $ic('<path d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.585 0-4.585 8 0 8 5.606 0 7.644-8 12.74-8z"/>'),
        'book'     => $ic('<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'),
        'video'    => $ic('<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>'),
        'alert'    => $ic('<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'),
        'file'     => $ic('<path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/><line x1="16" y1="13" x2="8" y2="13"/><line x1="16" y1="17" x2="8" y2="17"/>'),
        'quiz'     => $ic('<circle cx="12" cy="12" r="10"/><path d="M9.09 9a3 3 0 0 1 5.83 1c0 2-3 3-3 3"/><line x1="12" y1="17" x2="12.01" y2="17"/>'),
        'download' => $ic('<path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" y1="15" x2="12" y2="3"/>'),
        'external' => $ic('<path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6"/><polyline points="15 3 21 3 21 9"/><line x1="10" y1="14" x2="21" y2="3"/>'),
        'clip'     => $ic('<path d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/>'),
        'left'     => $ic('<polyline points="15 18 9 12 15 6"/>'),
        'right'    => $ic('<polyline points="9 18 15 12 9 6"/>'),
        'refresh'  => $ic('<polyline points="23 4 23 10 17 10"/><path d="M20.49 15a9 9 0 1 1-2.12-9.36L23 10"/>'),
        'trophy'   => $ic('<path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2z"/>'),
    ];

    /* Safe attribute reader (never throws on missing columns) */
    $attr = function ($model, string $key, $default = null) {
        if (is_object($model) && method_exists($model, 'getAttributes')) {
            return array_key_exists($key, $model->getAttributes()) ? $model->getAttribute($key) : $default;
        }
        return data_get($model, $key, $default);
    };

    /* First non-empty value among several possible keys */
    $pick = function ($model, array $keys, $default = null) use ($attr) {
        foreach ($keys as $key) {
            $value = $attr($model, $key, null);
            if ($value !== null && $value !== '') {
                return $value;
            }
        }
        return $default;
    };

    /* Stored path -> public URL; full URLs pass through */
    $fileUrl = function (?string $path): string {
        $path = trim((string) $path);
        if ($path === '') {
            return '';
        }
        if (preg_match('~^(https?:)?//~i', $path)) {
            return $path;
        }
        if (str_starts_with($path, '/')) {
            return url($path);
        }
        if (str_starts_with($path, 'storage/')) {
            return url('/' . $path);
        }
        $disk = config('filesystems.default');
        $disk = $disk === 'local' ? 'public' : $disk;
        try {
            return \Illuminate\Support\Facades\Storage::disk($disk)->url($path);
        } catch (\Throwable $e) {
            return url('storage/' . $path);
        }
    };

    /* Reads a relation (or JSON column) by trying several names */
    $readList = function ($model, array $names) use ($attr) {
        foreach ($names as $name) {
            if (is_object($model) && method_exists($model, $name)) {
                try {
                    $rel = $model->{$name};
                    if ($rel !== null) {
                        return $rel;
                    }
                } catch (\Throwable $e) {
                    // try the next name
                }
            }
        }
        foreach ($names as $name) {
            $value = $attr($model, $name, null);
            if ($value !== null) {
                if (is_string($value)) {
                    $decoded = json_decode($value, true);
                    $value = is_array($decoded) ? $decoded : null;
                }
                if ($value !== null) {
                    return $value;
                }
            }
        }
        return null;
    };

/* Builds quiz questions using the actual questions table columns.
   correct_answers stores zero-based option indexes, e.g. [0] or [0,1,2]. */
$buildQuiz = function ($lesson) use ($pick, $readList): array {
    $questions = $readList($lesson, ['questions', 'quizQuestions', 'quiz_questions']);

    if ($questions === null) {
        return [];
    }

    $out = [];

    foreach (collect($questions)->sortBy('order')->values() as $question) {
        $field = function (string $key, $default = null) use ($question, $pick) {
            if (is_array($question)) {
                return $question[$key] ?? $default;
            }

            return is_object($question)
                ? $pick($question, [$key], $default)
                : $default;
        };

        $text = trim(strip_tags((string) $field('title', '')));

        $rawOptions = $field('options', []);

        if (is_string($rawOptions)) {
            $rawOptions = json_decode($rawOptions, true) ?? [];
        }

        if (!is_array($rawOptions)) {
            continue;
        }

        $options = array_values(array_map(
            fn ($option) => trim(strip_tags((string) $option)),
            $rawOptions
        ));

        if ($text === '' || count($options) < 2) {
            continue;
        }

        $rawCorrect = $field('correct_answers', []);

        if (is_string($rawCorrect)) {
            $rawCorrect = json_decode($rawCorrect, true) ?? [];
        }

        if (!is_array($rawCorrect)) {
            $rawCorrect = [];
        }

        // Keep only valid zero-based indexes; do not guess 0-based vs 1-based.
        $correctIndexes = [];

        foreach ($rawCorrect as $index) {
            if (
                is_int($index) ||
                (is_string($index) && ctype_digit($index))
            ) {
                $index = (int) $index;

                if ($index >= 0 && $index < count($options)) {
                    $correctIndexes[] = $index;
                }
            }
        }

        $correctIndexes = array_values(array_unique($correctIndexes));
        sort($correctIndexes);

        $type = (string) $field('type', 'single_choice');

        // Single choice uses one index; multiple choice preserves all indexes.
        $answer = $type === 'multiple_choice'
            ? $correctIndexes
            : ($correctIndexes[0] ?? -1);

        $out[] = [
            'q' => $text,
            'opts' => $options,
            'a' => $answer,
            'type' => $type,
            'why' => trim(strip_tags((string) $field('explanation', ''))),
        ];
    }

    return $out;
};

    /* Raw question data for the debug comment (only printed when APP_DEBUG=true) */
    $quizRaw = function ($lesson) use ($readList): array {
        $qs   = $readList($lesson, ['questions', 'quizQuestions', 'quiz_questions']);
        $rows = [];

        foreach (collect($qs ?? [])->take(3) as $qn) {
            if (is_array($qn)) {
                $rows[] = $qn;
                continue;
            }
            if (!is_object($qn) || !method_exists($qn, 'getAttributes')) {
                continue;
            }
            $row  = $qn->getAttributes();
            $opts = $readList($qn, ['options', 'answers', 'choices']);
            $row['_options'] = collect($opts ?? [])->take(6)
                ->map(fn ($o) => is_object($o) && method_exists($o, 'getAttributes') ? $o->getAttributes() : $o)
                ->values()->all();
            $rows[] = $row;
        }

        return $rows;
    };

    /* ---------- Pricing ---------- */
    $price         = (float) ($course->effectivePrice() ?? $course->price ?? 0);
    $isFree        = (bool) ($course->is_free ?? false);
    $discountPrice = $course->discount_price;
    $hasDiscount   = !$isFree && $discountPrice !== null && (float) $discountPrice < (float) ($course->price ?? 0);
    $discountPct   = $hasDiscount ? (int) round((1 - ($price / max((float) $course->price, 1))) * 100) : 0;
    $image         = $course->thumbnail ?? null;

    /* ---------- Outcomes / requirements ---------- */
    $normalizeList = function ($value): array {
        if (is_string($value)) {
            $decoded = json_decode($value, true);
            $value   = is_array($decoded) ? $decoded : preg_split('/\r\n|\r|\n/', $value);
        }
        if (!is_array($value)) {
            return [];
        }
        $out = [];
        foreach ($value as $item) {
            if (is_string($item) || is_numeric($item)) {
                $item = trim((string) $item);
                if ($item !== '') {
                    $out[] = $item;
                }
            }
        }
        return $out;
    };

    $outcomes     = $normalizeList($course->outcomes ?? []);
    $requirements = $normalizeList($course->requirements ?? []);

    /* ---------- Preview video ---------- */
    $previewUrl       = trim((string) ($course->preview_video_url ?? ''));
    $previewEmbedUrl  = '';
    $previewIsYouTube = false;

    if ($previewUrl !== '' && filter_var($previewUrl, FILTER_VALIDATE_URL)) {
        $parts = parse_url($previewUrl);
        $host  = strtolower($parts['host'] ?? '');

        if (str_contains($host, 'youtube.com') || str_contains($host, 'youtu.be')) {
            $videoId = null;

            if (str_contains($host, 'youtu.be')) {
                $videoId = explode('/', trim($parts['path'] ?? '', '/'))[0] ?? null;
            } else {
                parse_str($parts['query'] ?? '', $queryParams);
                $videoId = $queryParams['v'] ?? null;

                if (!$videoId && !empty($parts['path']) && preg_match('~/(?:embed|shorts|live)/([^/?]+)~', $parts['path'], $m)) {
                    $videoId = $m[1];
                }
            }

            if ($videoId) {
                $previewIsYouTube = true;
                $previewEmbedUrl  = 'https://www.youtube-nocookie.com/embed/' . rawurlencode($videoId) . '?rel=0';
            }
        } elseif (str_contains($host, 'vimeo.com')) {
            if (preg_match('~vimeo\.com/(?:video/)?(\d+)~', $previewUrl, $m)) {
                $previewIsYouTube = true; // rendered as an iframe
                $previewEmbedUrl  = 'https://player.vimeo.com/video/' . $m[1];
            }
        } else {
            $previewEmbedUrl = $previewUrl; // direct MP4 / WebM / Ogg
        }
    } elseif ($previewUrl !== '') {
        $previewEmbedUrl = $fileUrl($previewUrl);
    }

    /* ---------- Curriculum (respects the admin "order" column) ---------- */
    $sections = collect($course->sections ?? [])
        ->sortBy(fn ($s) => (int) $attr($s, 'order', 0))
        ->values();

    $totalLessons = $sections->sum(fn ($s) => collect($s->lessons ?? [])->count());

    // Access flags are supplied by CourseController. The first two lessons
    // across the whole course are free previews, even across different sections.
$isCourseUnlocked = (bool) (
    $isCourseUnlocked
    ?? ($isFree || $isEnrolled)
);    $courseLessonIndex = 0;

    $kindLabels = ['video' => 'Video lesson', 'document' => 'Document', 'quiz' => 'Quiz'];

    /* ---------- Meta ---------- */
    $instructor     = $course->instructor ?? null;
    $instructorName = $instructor->name ?? 'EduSphere Team';
    $instructorBio  = trim(strip_tags((string) $attr($instructor, 'biography', '')));
    $instructorTitle = trim((string) $attr($instructor, 'short_title', ''));
    $instructorSocial = $instructor?->socialLinks() ?? [];
    $levelLabel     = ucfirst(str_replace('_', ' ', $course->level ?? 'All levels'));
    $language       = $course->language ?? 'English';
    $categoryName   = $course->category?->name ?? null;
    $rating         = (float) $attr($course, 'average_rating', $attr($course, 'rating', 0));
    $reviews        = (int) $attr($course, 'reviews_count', $attr($course, 'ratings_count', 0));
    $students       = (int) $attr($course, 'students_count', $attr($course, 'enrollments_count', 0));
    $updated        = $course->updated_at ? $course->updated_at->format('M Y') : null;

    /* ---------- Cart state ---------- */
    $cartIds = session()->get('cart_course_ids', []);
    $cartIds = is_array($cartIds) ? array_map('intval', $cartIds) : [];
    $inCart  = in_array((int) $course->id, $cartIds, true);

    $buyLabel = $isFree ? 'Enroll Now' : 'Buy Now';
@endphp

<main class="cd">

    {{-- ===================== Hero ===================== --}}
    <section class="cd-hero">
        <div class="cd-container">
            <div class="cd-hero__copy">
                <nav class="cd-crumbs" aria-label="Breadcrumb">
                    <a href="{{ route('home') }}">Home</a><span class="sep">/</span>
                    <a href="{{ url('/courses') }}">Courses</a><span class="sep">/</span>
                    <span class="current" aria-current="page">{{ $course->title }}</span>
                </nav>

                <div class="cd-badges">
                    <span class="cd-badge cd-badge--gold">{!! $icons['level'] !!} {{ $levelLabel }}</span>
                    @if ($categoryName)
                        <span class="cd-badge">{!! $icons['layers'] !!} {{ $categoryName }}</span>
                    @endif
                    @if (!empty($course->is_top_course))
                        <span class="cd-badge">{!! $icons['star'] !!} Featured</span>
                    @endif
                </div>

                <h1 class="cd-title">{{ $course->title }}</h1>

                <p class="cd-lead">
                    {{ $course->short_description ?? 'Explore this course and build practical skills.' }}
                </p>

                <ul class="cd-meta">
                    @if ($reviews > 0 || $rating > 0)
                        <li class="rate">{!! $icons['star'] !!} <strong>{{ number_format($rating, 1) }}</strong> ({{ number_format($reviews) }} {{ \Illuminate\Support\Str::plural('review', $reviews) }})</li>
                    @endif
                    @if ($students > 0)
                        <li>{!! $icons['user'] !!} {{ number_format($students) }} students</li>
                    @endif
                    <li>{!! $icons['user'] !!} Created by <strong>{{ $instructorName }}</strong></li>
                    <li>{!! $icons['globe'] !!} {{ $language }}</li>
                    @if ($updated)
                        <li>{!! $icons['clock'] !!} Updated {{ $updated }}</li>
                    @endif
                </ul>
            </div>
        </div>
    </section>

    {{-- ===================== Body ===================== --}}
    <div class="cd-container cd-body">

        @if (session('success'))
            <div class="cd-alert cd-alert--success" role="status">{!! $icons['check'] !!}<div>{{ session('success') }}</div></div>
        @endif
        @if (session('error'))
            <div class="cd-alert cd-alert--error" role="alert">{!! $icons['alert'] !!}<div>{{ session('error') }}</div></div>
        @endif
        @if ($errors->any())
            <div class="cd-alert cd-alert--error" role="alert">
                {!! $icons['alert'] !!}
                <ul>@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
            </div>
        @endif

        <div class="cd-grid">

            {{-- ---------- Main column ---------- --}}
            <div class="cd-main">

                @if (count($outcomes))
                    <section class="cd-panel cd-reveal" aria-labelledby="cd-learn">
                        <h2 class="cd-h2" id="cd-learn">What Will I Learn?</h2>
                        <ul class="cd-checks">
                            @foreach ($outcomes as $outcome)
                                <li><span class="tick">{!! $icons['check'] !!}</span><span>{{ $outcome }}</span></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="cd-panel cd-reveal" id="curriculum" aria-labelledby="cd-curriculum">
                    <div class="cd-cur__head">
                        <h2 class="cd-h2" id="cd-curriculum">Curriculum</h2>
                        <div class="cd-cur__stats">
                            <span>{{ $sections->count() }} {{ \Illuminate\Support\Str::plural('section', $sections->count()) }}</span>
                            <span>{{ $totalLessons }} {{ \Illuminate\Support\Str::plural('lesson', $totalLessons) }}</span>
                            @if ($sections->count() > 1)
                                <button type="button" class="cd-link-btn" id="cdToggleAll" data-open="false">Expand all</button>
                            @endif
                        </div>
                    </div>

                    @if ($sections->isEmpty())
                        <div class="cd-empty">Curriculum lessons will appear here once sections and lessons are added.</div>
                    @else
                        <div class="cd-sections">
                            @foreach ($sections as $section)
                                @php
                                    $lessons = collect($section->lessons ?? [])
                                        ->sortBy(fn ($l) => (int) $attr($l, 'order', 0))
                                        ->values();
                                @endphp
                                <details class="cd-sec">
                                    <summary class="cd-sec__head">
                                        <span class="cd-sec__chev">{!! $icons['chevron'] !!}</span>
                                        <span class="cd-sec__name">{{ $section->title ?? $section->name ?? 'Course section' }}</span>
                                        <span class="cd-sec__count">{{ $lessons->count() }} {{ \Illuminate\Support\Str::plural('lesson', $lessons->count()) }}</span>
                                    </summary>

                                    <div class="cd-sec__body">
                                        @forelse ($lessons as $lesson)
                                            @php
                                                $kind = strtolower((string) $attr($lesson, 'lesson_type', 'video'));
                                                $kind = in_array($kind, ['video', 'document', 'quiz'], true) ? $kind : 'video';

                                                $vType    = strtolower((string) $attr($lesson, 'video_type', ''));
                                                $rawVideo = trim((string) $attr($lesson, 'video_url', ''));
                                                $videoSrc = $vType === 'upload' ? $fileUrl($rawVideo) : $rawVideo;

                                                $attachment = $fileUrl((string) $attr($lesson, 'attachment', ''));
                                                $attName    = $attachment !== '' ? basename((string) parse_url($attachment, PHP_URL_PATH)) : '';
                                                $summary    = trim(strip_tags((string) $attr($lesson, 'summary', '')));
                                                $duration   = trim((string) $attr($lesson, 'duration', ''));
                                                $lTitle     = (string) $attr($lesson, 'title', 'Lesson');
                                                $kindIcon   = ['video' => 'play', 'document' => 'file', 'quiz' => 'quiz'][$kind];

                                                $isPreviewLesson = $courseLessonIndex < 2;
                                                $canAccessLesson = $isCourseUnlocked || $isPreviewLesson;
                                                $courseLessonIndex++;

                                                // Do not place locked lesson media/resources in HTML data attributes.
                                                $quizData  = ($canAccessLesson && $kind === 'quiz') ? $buildQuiz($lesson) : [];
                                                $quizCount = count($quizData);
                                            @endphp
                                            <button
                                                type="button"
                                                class="cd-lesson"
                                                data-id="{{ $attr($lesson, 'id', '') }}"
                                                data-kind="{{ $kind }}"
                                                data-title="{{ $lTitle }}"
                                                data-locked="{{ $canAccessLesson ? '0' : '1' }}"
                                                data-buy-url="{{ route('cart.buy-now', $course->id) }}"
                                                data-csrf="{{ csrf_token() }}"
                                                data-course-id="{{ (int) $course->id }}"
                                                data-progress-show="{{ route('courses.lessons.progress.show', [(int) $course->id, '__LESSON_ID__']) }}"
                                                data-progress-save="{{ route('courses.lessons.progress.save', [(int) $course->id, '__LESSON_ID__']) }}"
                                                data-vtype="{{ $canAccessLesson ? $vType : '' }}"
                                                data-url="{{ $canAccessLesson ? $videoSrc : '' }}"
                                                data-duration="{{ $duration }}"
                                                data-summary="{{ $canAccessLesson ? $summary : '' }}"
                                                data-attachment="{{ $canAccessLesson ? $attachment : '' }}"
                                                data-attname="{{ $canAccessLesson ? $attName : '' }}"
                                                @if ($kind === 'quiz' && $canAccessLesson) data-quiz="{{ json_encode($quizData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" data-qcount="{{ $quizCount }}" @endif
                                            >
                                                <span class="cd-lesson__icon">{!! $canAccessLesson ? $icons[$kindIcon] : $icons['lock'] ?? $icons['alert'] !!}</span>
                                                <span class="cd-lesson__text">
                                                    <span class="cd-lesson__title">{{ $lTitle }}</span>
                                                    <span class="cd-lesson__sub">
                                                        <span>{{ $kindLabels[$kind] }}</span>
                                                        @if ($isPreviewLesson && !$isCourseUnlocked)
                                                            <span class="cd-preview-badge">Free preview</span>
                                                        @elseif (!$canAccessLesson)
                                                            <span class="cd-locked-badge">Locked · Buy course to unlock</span>
                                                        @endif
                                                        @if ($kind === 'quiz' && $quizCount > 0)
                                                            <span>{{ $quizCount }} {{ \Illuminate\Support\Str::plural('question', $quizCount) }}</span>
                                                        @endif
                                                        @if ($canAccessLesson && $attachment !== '' && $kind !== 'document')
                                                            <span>{!! $icons['clip'] !!} Resource</span>
                                                        @endif
                                                    </span>
                                                </span>
                                                @if ($duration !== '')
                                                    <span class="cd-lesson__time">{!! $icons['clock'] !!} {{ $duration }}</span>
                                                @endif
                                            </button>
                                            @if (config('app.debug') && $kind === 'quiz')
                                                <!-- cd-quiz-debug lesson {{ $attr($lesson, 'id', '') }}: {!! str_replace('--', '- -', (string) json_encode($quizRaw($lesson), JSON_UNESCAPED_UNICODE | JSON_PARTIAL_OUTPUT_ON_ERROR)) !!} -->
                                            @endif
                                        @empty
                                            <div class="cd-lesson cd-lesson--empty">No lessons have been added to this section yet.</div>
                                        @endforelse
                                    </div>
                                </details>
                            @endforeach
                        </div>
                    @endif
                </section>

                @if (count($requirements))
                    <section class="cd-panel cd-reveal" aria-labelledby="cd-req">
                        <h2 class="cd-h2" id="cd-req">Requirements</h2>
                        <ul class="cd-checks cd-checks--single">
                            @foreach ($requirements as $requirement)
                                <li><span class="tick">{!! $icons['check'] !!}</span><span>{{ $requirement }}</span></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="cd-panel cd-reveal" aria-labelledby="cd-about">
                    <h2 class="cd-h2" id="cd-about">About This Course</h2>
                    <div class="cd-prose">{!! $course->description ?? 'Course description will be available soon.' !!}</div>
                </section>

                <section class="cd-panel cd-reveal" aria-labelledby="cd-instructor">
                    <h2 class="cd-h2" id="cd-instructor">Your Instructor</h2>
                    <div class="cd-inst">
                        @if ($instructor?->avatarUrl())
                            <img src="{{ $instructor->avatarUrl() }}" alt="{{ $instructorName }}" class="cd-inst__avatar" style="object-fit:cover">
                        @else
                            <div class="cd-inst__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($instructorName, 0, 1)) }}</div>
                        @endif
                        <div>
                            <div class="cd-inst__name">{{ $instructorName }}</div>
                            <div class="cd-inst__role">{{ $instructorTitle !== '' ? $instructorTitle : 'Course instructor' }}</div>
                            @if ($instructorBio !== '')
                                <p class="cd-inst__bio">{{ $instructorBio }}</p>
                            @endif
                            @if (!empty($instructorSocial))
                                <div style="display:flex;gap:10px;margin-top:10px">
                                    @foreach ($instructorSocial as $platform => $url)
                                        <a href="{{ $url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ ucfirst($platform) }}"
                                            style="width:32px;height:32px;border-radius:50%;background:#f3f0f9;display:grid;place-items:center;color:var(--cd-purple)">
                                            <i class="fa-brands fa-{{ $platform }}"></i>
                                        </a>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                    </div>
                </section>
            </div>

            {{-- ---------- Purchase card: hide after purchase ---------- --}}
            @unless ($isEnrolled)
            <aside class="cd-side" aria-label="Course purchase">
                <div class="cd-card" id="cdPurchase">
                    <div class="cd-screen">
                        @if ($previewIsYouTube)
                            <iframe
                                src="{{ $previewEmbedUrl }}"
                                title="{{ $course->title }} preview video"
                                loading="lazy"
                                allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share"
                                allowfullscreen
                            ></iframe>
                        @elseif ($previewEmbedUrl !== '')
                            <video controls preload="metadata" playsinline @if ($image) poster="{{ $course->thumbnailUrl() }}" @endif>
                                <source src="{{ $previewEmbedUrl }}">
                                Your browser does not support this video.
                            </video>
                        @elseif ($image)
                            <img src="{{ $course->thumbnailUrl() }}" alt="{{ $course->title }}">
                        @else
                            <div class="cd-card__ph">{!! $icons['video'] !!}<span>Preview coming soon</span></div>
                        @endif
                    </div>

                    <div class="cd-card__body">
                        <div class="cd-price">
                            @if ($isFree)
                                <span class="cd-price__now is-free">Free</span>
                            @else
                                <span class="cd-price__now">{{ format_price($price, 0) }}</span>
                                @if ($hasDiscount)
                                    <span class="cd-price__old">{{ format_price((float) $course->price, 0) }}</span>
                                    <span class="cd-price__off">{{ $discountPct }}% off</span>
                                @endif
                            @endif
                        </div>

                        <div class="cd-actions">
                            <form method="POST" action="{{ route('cart.buy-now', $course->id) }}" data-once>
                                @csrf
                                <button type="submit" class="cd-btn cd-btn--gold">{!! $icons['bolt'] !!} {{ $buyLabel }}</button>
                            </form>

                            @if ($inCart)
                                <a href="{{ route('cart.index') }}" class="cd-btn cd-btn--success">{!! $icons['check'] !!} In Cart · Go to Cart</a>
                            @else
                                <form method="POST" action="{{ route('cart.add', $course->id) }}" data-once>
                                    @csrf
                                    <button type="submit" class="cd-btn cd-btn--outline">{!! $icons['cart'] !!} Add To Cart</button>
                                </form>
                            @endif
                        </div>

                        <div class="cd-includes">
                            <h4>This course includes</h4>
                            <ul>
                                <li>{!! $icons['video'] !!} {{ $totalLessons }} {{ \Illuminate\Support\Str::plural('lesson', $totalLessons) }}</li>
                                <li>{!! $icons['book'] !!} Full course curriculum</li>
                                <li>{!! $icons['infinity'] !!} Self-paced learning</li>
                                <li>{!! $icons['device'] !!} Access on supported devices</li>
                            </ul>
                        </div>
                    </div>
                </div>
            </aside>
            @endunless
        </div>
    </div>

    {{-- Mobile / tablet sticky buy bar: hide after purchase --}}
    @unless ($isEnrolled)
    <div class="cd-bar" id="cdBar" aria-hidden="true">
        <div class="cd-bar__price">
            @if ($isFree)
                <strong>Free</strong>
            @else
                <strong>{{ format_price($price, 0) }}</strong>
                @if ($hasDiscount)<s>{{ format_price((float) $course->price, 0) }}</s>@endif
            @endif
        </div>

        @unless ($inCart)
            <form method="POST" action="{{ route('cart.add', $course->id) }}" data-once>
                @csrf
                <button type="submit" class="cd-btn cd-btn--outline" tabindex="-1">{!! $icons['cart'] !!} Add</button>
            </form>
        @endunless

        <form method="POST" action="{{ route('cart.buy-now', $course->id) }}" data-once>
            @csrf
            <button type="submit" class="cd-btn cd-btn--gold" tabindex="-1">{{ $buyLabel }}</button>
        </form>
    </div>
    @endunless

    {{-- ===================== Lesson popup ===================== --}}
    <div class="cd-modal" id="cdModal" hidden>
        <div class="cd-modal__backdrop" data-close></div>

        <div class="cd-dialog" role="dialog" aria-modal="true" aria-labelledby="cdModalTitle" tabindex="-1">
            <span class="cd-dialog__grip" aria-hidden="true"></span>

            <header class="cd-dialog__head">
                <div class="cd-dialog__titles">
                    <span class="cd-dialog__tag" id="cdModalTag">Lesson</span>
                    <h3 class="cd-dialog__title" id="cdModalTitle">Lesson</h3>
                </div>
                <button type="button" class="cd-dialog__close" data-close aria-label="Close lesson">{!! $icons['close'] !!}</button>
            </header>

            <div class="cd-dialog__scroll">
                <div class="cd-screen" id="cdModalScreen"></div>
                <div class="cd-dialog__info" id="cdModalInfo"></div>
                <div class="cd-progress-wrap" id="cdLessonProgress" hidden style="padding:0 22px 18px">
                    <div style="display:flex;justify-content:space-between;gap:12px;font-size:13px;margin-bottom:7px"><span>Lesson progress</span><strong id="cdProgressText">0%</strong></div>
                    <div style="height:6px;background:#e8eaf0;border-radius:99px;overflow:hidden"><div id="cdProgressFill" style="height:100%;width:0;background:#d6a84f;transition:width .2s"></div></div>
                    <small id="cdProgressTime" style="display:block;margin-top:6px;color:#667085"></small>
                </div>
            </div>

            <footer class="cd-dialog__foot">
                <button type="button" class="cd-nav" id="cdPrev">{!! $icons['left'] !!} <span>Previous</span></button>
                <span class="cd-dialog__count" id="cdCount"></span>
                <button type="button" class="cd-nav cd-nav--next" id="cdNext"><span>Next</span> {!! $icons['right'] !!}</button>
            </footer>
        </div>
    </div>
</main>

<script>
(function () {
    var root = document.querySelector('.cd');
    if (!root) return;
    root.classList.add('cd-js');
    document.documentElement.classList.add('cd-js');

    // Always start with every curriculum section collapsed on page load.
    // This also overrides any stale open state from cached/previous markup.
    root.querySelectorAll('.cd-sec').forEach(function (section) {
        section.removeAttribute('open');
        section.open = false;
    });

    var reduceMotion = window.matchMedia && window.matchMedia('(prefers-reduced-motion: reduce)').matches;

    var ICON = {
        file:     {!! json_encode($icons['file']) !!},
        quiz:     {!! json_encode($icons['quiz']) !!},
        clock:    {!! json_encode($icons['clock']) !!},
        clip:     {!! json_encode($icons['clip']) !!},
        download: {!! json_encode($icons['download']) !!},
        external: {!! json_encode($icons['external']) !!},
        play:     {!! json_encode($icons['play']) !!},
        alert:    {!! json_encode($icons['alert']) !!},
        check:    {!! json_encode($icons['check']) !!},
        close:    {!! json_encode($icons['close']) !!},
        refresh:  {!! json_encode($icons['refresh']) !!},
        trophy:   {!! json_encode($icons['trophy']) !!},
        right:    {!! json_encode($icons['right']) !!}
    };
    var KIND_LABEL = { video: 'Video lesson', document: 'Document', quiz: 'Quiz' };
    var QUIZ_KEY = 'cd_quiz_{{ (int) $course->id }}_';

    /* ---------- Scroll reveal ---------- */
    var reveals = root.querySelectorAll('.cd-reveal');
    if ('IntersectionObserver' in window && !reduceMotion) {
        var io = new IntersectionObserver(function (entries) {
            entries.forEach(function (en) {
                if (en.isIntersecting) { en.target.classList.add('is-in'); io.unobserve(en.target); }
            });
        }, { threshold: 0.08, rootMargin: '0px 0px -40px 0px' });
        reveals.forEach(function (el) { io.observe(el); });
    } else {
        reveals.forEach(function (el) { el.classList.add('is-in'); });
    }

    /* ---------- Animated accordion ---------- */
    root.querySelectorAll('.cd-sec').forEach(function (d) {
        var head = d.querySelector('.cd-sec__head');
        var body = d.querySelector('.cd-sec__body');
        var busy = false;
        if (!head || !body) return;

        head.addEventListener('click', function (e) {
            e.preventDefault();
            if (reduceMotion || !body.animate) { d.open = !d.open; return; }
            if (busy) return;
            busy = true;

            if (d.open) {
                var h = body.offsetHeight;
                d.classList.add('is-closing');
                body.animate([{ height: h + 'px', opacity: 1 }, { height: '0px', opacity: 0 }], { duration: 260, easing: 'ease' })
                    .onfinish = function () { d.open = false; d.classList.remove('is-closing'); busy = false; };
            } else {
                d.open = true;
                var h2 = body.offsetHeight;
                body.animate([{ height: '0px', opacity: 0 }, { height: h2 + 'px', opacity: 1 }], { duration: 300, easing: 'ease' })
                    .onfinish = function () { busy = false; };
            }
        });
    });

    var toggleAll = document.getElementById('cdToggleAll');
    if (toggleAll) {
        toggleAll.addEventListener('click', function () {
            var open = toggleAll.dataset.open !== 'true';
            root.querySelectorAll('.cd-sec').forEach(function (d) { d.open = open; });
            toggleAll.dataset.open = String(open);
            toggleAll.textContent = open ? 'Collapse all' : 'Expand all';
        });
    }

    /* ---------- Video helpers ---------- */
    function youtubeEmbed(url) {
        try {
            var p = new URL(url, window.location.origin), id = '';
            if (p.hostname.indexOf('youtu.be') !== -1) {
                id = p.pathname.replace(/^\/+/, '').split('/')[0];
            } else if (p.hostname.indexOf('youtube.com') !== -1) {
                id = p.searchParams.get('v') || '';
                if (!id) { var m = p.pathname.match(/\/(?:embed|shorts|live)\/([^/]+)/); if (m) id = m[1]; }
            }
            return id ? 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0&enablejsapi=1&origin=' + encodeURIComponent(window.location.origin) : '';
        } catch (e) { return ''; }
    }

    function vimeoEmbed(url) {
        var m = String(url).match(/vimeo\.com\/(?:video\/)?(\d+)(?:\/([a-z0-9]+))?/i);
        if (!m) return '';
        var src = 'https://player.vimeo.com/video/' + m[1] + '?autoplay=1';
        if (m[2]) src += '&h=' + m[2];
        return src;
    }

    function guessVideoType(type, url) {
        type = (type || '').toLowerCase();
        if (type === 'youtube' || type === 'vimeo' || type === 'upload') return type;
        if (/(?:youtube\.com|youtu\.be)/i.test(url)) return 'youtube';
        if (/vimeo\.com/i.test(url)) return 'vimeo';
        return 'upload';
    }

    /* ---------- Popup refs ---------- */
    var modal    = document.getElementById('cdModal');
    var dialog   = modal.querySelector('.cd-dialog');
    var screen   = document.getElementById('cdModalScreen');
    var info     = document.getElementById('cdModalInfo');
    var tag      = document.getElementById('cdModalTag');
    var title    = document.getElementById('cdModalTitle');
    var prevBtn  = document.getElementById('cdPrev');
    var nextBtn  = document.getElementById('cdNext');
    var count    = document.getElementById('cdCount');
    var scroller = modal.querySelector('.cd-dialog__scroll');

    var lessons = Array.prototype.slice.call(root.querySelectorAll('.cd-lesson[data-kind]'));
    var current = -1;
    var lastFocus = null;
    var closeTimer = null;

    function el(tagName, className, text) {
        var n = document.createElement(tagName);
        if (className) n.className = className;
        if (text != null) n.textContent = text;
        return n;
    }

    function setNote(text, withIcon) {
        screen.replaceChildren();
        screen.className = 'cd-screen';
        var p = el('p', 'cd-screen__note');
        if (withIcon) {
            var s = el('span');
            s.innerHTML = ICON.alert;
            s.firstChild.style.cssText = 'width:34px;height:34px;display:block;margin:0 auto 10px';
            p.appendChild(s);
        }
        p.appendChild(document.createTextNode(text));
        screen.appendChild(p);
    }

    // Persistent lesson progress (YouTube IFrame API + native video element).
    var activeProgress = null;       // {lessonId, showUrl, saveUrl, token, completed, disabled}
    var progressTimer = null;
    var youtubePlayer = null;
    var youtubePlayerReady = false;  // true only after the saved position was restored
    var activeVideo = null;          // {el, ready} for uploaded videos
    var progressToken = 0;           // changes on every lesson open/close; stale async work is ignored
    var youtubeApiState = 'idle';    // idle | loading
    var youtubeApiWaiters = [];
    var progressShow = document.getElementById('cdLessonProgress');
    var progressText = document.getElementById('cdProgressText');
    var progressFill = document.getElementById('cdProgressFill');
    var progressTime = document.getElementById('cdProgressTime');

    function formatTime(seconds) {
        seconds = Math.max(0, Math.floor(Number(seconds) || 0));
        var h = Math.floor(seconds / 3600), m = Math.floor((seconds % 3600) / 60), s = seconds % 60;
        return h ? h + ':' + String(m).padStart(2, '0') + ':' + String(s).padStart(2, '0') : m + ':' + String(s).padStart(2, '0');
    }
    function progressUrl(template, lessonId) { return String(template || '').replace('__LESSON_ID__', encodeURIComponent(lessonId)); }
    function progressHeaders() { return {'Content-Type':'application/json','Accept':'application/json','X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || (lessons[current] ? lessons[current].dataset.csrf : '')}; }
    function updateProgressUI(position, duration, completed) {
        if (!progressShow) return;
        progressShow.hidden = false;
        var pct = duration > 0 ? Math.min(100, Math.round(position / duration * 100)) : 0;
        if (completed) pct = 100;
        progressText.textContent = pct + '%' + (completed ? ' · Completed' : '');
        progressFill.style.width = pct + '%';
        progressTime.textContent = (duration > 0 ? formatTime(position) + ' / ' + formatTime(duration) : 'Progress will appear when the video starts.');
    }

    function isCurrentProgress(token) {
        return !!activeProgress && activeProgress.token === token;
    }

    // Controller returns {position, duration, completed}; also accept DB column names.
    function normalizeProgress(p) {
        p = p || {};
        var pos = p.position !== undefined ? p.position : (p.last_position_seconds !== undefined ? p.last_position_seconds : p.position_seconds);
        var dur = p.duration !== undefined ? p.duration : p.duration_seconds;
        var done = p.completed !== undefined ? p.completed : p.is_completed;
        return {
            position: Math.max(0, Math.floor(Number(pos) || 0)),
            duration: Math.max(0, Math.floor(Number(dur) || 0)),
            completed: !!done
        };
    }

    function fetchProgress() {
        if (!activeProgress || !activeProgress.showUrl) return Promise.resolve(null);
        var url = activeProgress.showUrl;
        return fetch(url, {headers: {'Accept': 'application/json'}, credentials: 'same-origin', cache: 'no-store'})
            .then(function (r) {
                if (!r.ok) throw new Error('HTTP ' + r.status);
                return r.json();
            })
            .then(function (d) { return normalizeProgress(d && d.progress ? d.progress : d); })
            .catch(function (error) {
                console.error('Could not fetch lesson progress:', url, error);
                return null;
            });
    }

    function saveProgress(position, duration, completed) {
        if (!activeProgress || !activeProgress.saveUrl || !activeProgress.lessonId) {
            console.error('Progress URL or lesson ID missing:', activeProgress);
            return;
        }
        if (activeProgress.disabled) return;

        position = Math.max(0, Math.floor(Number(position) || 0));
        duration = Math.max(0, Math.floor(Number(duration) || 0));

        // Unknown duration: never send (server would clamp the position to 0).
        if (duration <= 0) return;
        position = Math.min(position, duration);

        if (completed) activeProgress.completed = true;
        updateProgressUI(position, duration, activeProgress.completed);

        var url = activeProgress.saveUrl;

        fetch(url, {
            method: 'POST',
            headers: progressHeaders(),
            body: JSON.stringify({position: position, duration: duration, completed: !!completed}),
            credentials: 'same-origin',
            keepalive: true
        })
        .then(function (response) {
            return response.text().then(function (text) {
                var data;
                try { data = JSON.parse(text); } catch (e) { data = {message: text}; }

                if (!response.ok) {
                    console.error('Progress save failed:', response.status, data);
                    return;
                }
                console.debug('Progress saved:', data);
            });
        })
        .catch(function (error) {
            console.error('Progress request error:', url, error);
        });
    }

    // Save the current playback position (YouTube or uploaded video).
    function flushProgress() {
        if (!activeProgress) return;
        try {
            if (youtubePlayer && youtubePlayerReady && typeof youtubePlayer.getCurrentTime === 'function') {
                saveProgress(youtubePlayer.getCurrentTime(), youtubePlayer.getDuration(), false);
            } else if (activeVideo && activeVideo.ready && activeVideo.el.duration) {
                saveProgress(activeVideo.el.currentTime, activeVideo.el.duration, false);
            }
        } catch (e) {
            console.error('Could not save progress before leaving the lesson:', e);
        }
    }

    // Stop tracking the current lesson: optionally save, clear timer, destroy player.
    function stopProgress(shouldSave) {
        if (shouldSave) flushProgress();
        clearInterval(progressTimer);
        progressTimer = null;
        if (youtubePlayer) {
            try { youtubePlayer.destroy(); } catch (e) { console.error('Could not destroy YouTube player:', e); }
        }
        youtubePlayer = null;
        youtubePlayerReady = false;
        activeVideo = null;
        progressToken++;
        activeProgress = null;
    }

    function startProgress(data) {
        stopProgress(false);

        var btn = lessons[current];
        var token = progressToken;

        activeProgress = btn ? {
            lessonId: data.id,
            showUrl: progressUrl(btn.dataset.progressShow, data.id),
            saveUrl: progressUrl(btn.dataset.progressSave, data.id),
            token: token,
            completed: false,
            disabled: false
        } : null;

        if (progressShow) progressShow.hidden = !(activeProgress && data.id);
        if (progressText) progressText.textContent = '0%';
        if (progressFill) progressFill.style.width = '0%';
        if (progressTime) progressTime.textContent = 'Loading saved progress…';

        // The saved position is fetched exactly once per lesson-opening flow.
        return fetchProgress().then(function (saved) {
            if (!isCurrentProgress(token)) return null;   // lesson changed/closed while loading
            if (!saved) {
                activeProgress.disabled = true;           // never overwrite unknown saved progress
                if (progressTime) progressTime.textContent = 'Saved progress could not be loaded. Tracking is paused for this lesson.';
                return null;
            }
            activeProgress.completed = saved.completed;
            return saved;
        });
    }

    function loadYouTubeApi(callback) {
        if (window.YT && window.YT.Player) {
            callback();
            return;
        }

        youtubeApiWaiters.push(callback);
        if (youtubeApiState === 'loading') return;
        youtubeApiState = 'loading';

        var previousReady = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = function () {
            if (typeof previousReady === 'function') {
                try { previousReady(); } catch (e) { console.error('Previous YouTube API ready handler failed:', e); }
            }
            youtubeApiState = 'idle';
            youtubeApiWaiters.splice(0).forEach(function (fn) {
                try { fn(); } catch (e) { console.error('YouTube API callback failed:', e); }
            });
        };

        var script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        script.async = true;
        script.onerror = function () {
            youtubeApiState = 'idle';
            youtubeApiWaiters.length = 0;
            console.error('Failed to load the YouTube IFrame API.');
        };
        document.head.appendChild(script);
    }

    function startYouTubeProgress(iframe, saved, token) {
        loadYouTubeApi(function () {
            if (!isCurrentProgress(token) || !iframe || !iframe.isConnected) return;

            try {
                youtubePlayer = new YT.Player(iframe, {
                    events: {
                        onReady: function (event) {
                            if (!isCurrentProgress(token)) return;

                            var player = event.target;
                            var duration = Math.floor(player.getDuration() || 0) || saved.duration || 0;
                            var position = saved.position;

                            // Restore the saved timestamp (dynamic, from the DB record).
                            if (position > 0 && (duration <= 0 || position < duration - 2)) {
                                player.seekTo(position, true);
                            } else {
                                position = 0;
                            }

                            youtubePlayerReady = true;   // saving allowed only after restore
                            updateProgressUI(position, duration, saved.completed);

                            clearInterval(progressTimer);
                            progressTimer = setInterval(function () {
                                if (
                                    isCurrentProgress(token) && youtubePlayer && youtubePlayerReady &&
                                    youtubePlayer.getPlayerState() === YT.PlayerState.PLAYING
                                ) {
                                    saveProgress(youtubePlayer.getCurrentTime(), youtubePlayer.getDuration(), false);
                                }
                            }, 5000);
                        },

                        onStateChange: function (event) {
                            if (!isCurrentProgress(token) || !youtubePlayerReady) return;

                            if (event.data === YT.PlayerState.ENDED) {
                                var d = event.target.getDuration();
                                saveProgress(d, d, true);
                            } else if (event.data === YT.PlayerState.PAUSED) {
                                saveProgress(event.target.getCurrentTime(), event.target.getDuration(), false);
                            }
                        },

                        onError: function (event) {
                            console.error('YouTube player error code:', event.data);
                        }
                    }
                });
            } catch (error) {
                console.error('YouTube player initialization failed:', error);
            }
        });
    }

    function renderVideo(url, vtype) {
        screen.replaceChildren();
        screen.className = 'cd-screen';
        if (!url) { setNote('No video has been added for this lesson yet.', true); return; }
        var type = guessVideoType(vtype, url), src = '';
        if (type === 'youtube') {
            src = youtubeEmbed(url);

            if (!src) {
                setNote('This YouTube video URL is not valid.', true);
                return;
            }

            screen.appendChild(el('div', 'cd-loader'));

            var iframe = document.createElement('iframe');
            iframe.src = src;
            iframe.title = 'Lesson video player';
            iframe.allow =
                'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share';
            iframe.allowFullscreen = true;

            iframe.addEventListener('load', function () {
                var loader = screen.querySelector('.cd-loader');
                if (loader) loader.remove();
            });

            screen.appendChild(iframe);

            var ytPending = startProgress(window.currentLessonData || {});
            var ytToken = activeProgress ? activeProgress.token : 0;

            ytPending
                .then(function (saved) {
                    // null = stale lesson or fetch failed (already logged); video still plays.
                    if (!saved || !isCurrentProgress(ytToken)) return;
                    startYouTubeProgress(iframe, saved, ytToken);
                })
                .catch(function (error) {
                    console.error('Could not start YouTube progress tracking:', error);
                });

            return;
        }
        else if (type === 'vimeo') {
            src = vimeoEmbed(url);
            if (!src) { setNote('This Vimeo video URL is not valid.', true); return; }
            var vimeoFrame = document.createElement('iframe'); vimeoFrame.src = src; vimeoFrame.title = 'Lesson video player';
            vimeoFrame.allow = 'autoplay; fullscreen; picture-in-picture'; vimeoFrame.allowFullscreen = true;
            screen.appendChild(vimeoFrame);
            startProgress(window.currentLessonData || {});
            progressTime.textContent = 'Progress tracking is available for YouTube and uploaded videos.';
            return;
        }

        // Uploaded / local video.
        var video = document.createElement('video');
        video.src = url; video.controls = true; video.playsInline = true; video.preload = 'metadata';
        video.addEventListener('error', function(){ setNote('This video could not be loaded.', true); });
        screen.appendChild(video);

        var vState = {el: video, ready: false};
        var vPending = startProgress(window.currentLessonData || {});
        var vToken = activeProgress ? activeProgress.token : 0;
        activeVideo = vState;

        vPending.then(function (saved) {
            if (!saved || !isCurrentProgress(vToken) || !video.isConnected) return;

            function applyResume() {
                if (!isCurrentProgress(vToken)) return;
                var d = video.duration;
                var pos = saved.position;
                if (pos > 0 && isFinite(d) && pos < d - 2) video.currentTime = pos; else pos = 0;
                vState.ready = true;   // saving allowed only after restore
                updateProgressUI(pos, isFinite(d) ? d : 0, saved.completed);
                var p = video.play();
                if (p && p.catch) p.catch(function (err) { console.warn('Video autoplay was blocked:', err); });
            }

            // loadedmetadata may already have fired while progress was loading.
            if (video.readyState >= 1) applyResume();
            else video.addEventListener('loadedmetadata', applyResume, {once: true});
        }).catch(function (error) {
            console.error('Could not restore uploaded video progress:', error);
        });

        video.addEventListener('timeupdate', function(){ if (vState.ready && video.duration) updateProgressUI(video.currentTime, video.duration, activeProgress ? activeProgress.completed : false); });
        video.addEventListener('pause', function(){ if (vState.ready && video.duration && !video.ended) saveProgress(video.currentTime, video.duration, false); });
        video.addEventListener('ended', function(){ if (vState.ready) saveProgress(video.duration, video.duration, true); });
        progressTimer = setInterval(function(){ if (vState.ready && isCurrentProgress(vToken) && !video.paused && video.duration) saveProgress(video.currentTime, video.duration, false); }, 5000);
    }

function renderDocument(data) {
    screen.replaceChildren();
    screen.className = 'cd-screen cd-screen--document';

    if (!data.attachment) {
        setNote('No document has been attached to this lesson yet.', true);
        return;
    }

    var iframe = document.createElement('iframe');
    iframe.src = data.attachment;
    iframe.title = data.attname || data.title || 'Course document';
    iframe.setAttribute('loading', 'lazy');
    iframe.setAttribute('allowfullscreen', 'true');

    screen.appendChild(iframe);
}

    /* =====================================================
       Quiz
       ===================================================== */
    function readBest(id) {
        try { var v = parseInt(localStorage.getItem(QUIZ_KEY + id) || '', 10); return isNaN(v) ? null : v; } catch (e) { return null; }
    }
    function saveBest(id, pct) {
        try { var b = readBest(id); if (b === null || pct > b) localStorage.setItem(QUIZ_KEY + id, String(pct)); } catch (e) {}
    }
    function refreshBestChip(id) {
        var chip = info.querySelector('.cd-chip--best');
        if (!chip) return;
        var b = readBest(id);
        if (b === null) { chip.hidden = true; return; }
        chip.hidden = false;
        chip.textContent = 'Best score: ' + b + '%';
    }

    function renderQuiz(btn) {
        var questions = [];
        try { questions = JSON.parse(btn.dataset.quiz || '[]'); } catch (e) { questions = []; }
        if (!Array.isArray(questions)) questions = [];

        var lessonId = btn.dataset.id || '';

        screen.replaceChildren();
        screen.className = 'cd-screen cd-screen--quiz';

        if (!questions.length) {
            var empty = el('div', 'cd-quiz__empty');
            empty.innerHTML = ICON.quiz;
            empty.appendChild(el('h4', '', 'Questions coming soon'));
            empty.appendChild(el('p', '', 'This quiz does not have any questions yet. Please check back later.'));
            screen.appendChild(empty);
            return;
        }

        var N = questions.length;
        var st;

        function start() {
            st = { i: 0, score: 0, scored: 0, picked: [] };
            showQuestion();
        }

        function showQuestion() {
            var q = questions[st.i];
            var wrap = el('div', 'cd-quiz');
            screen.replaceChildren(wrap);

            var top = el('div', 'cd-quiz__top');
            top.appendChild(el('span', '', 'Question ' + (st.i + 1) + ' of ' + N));
            var sc = el('span', 'cd-quiz__score', 'Score: ' + st.score);
            top.appendChild(sc);
            wrap.appendChild(top);

            var bar = el('div', 'cd-quiz__bar');
            var fill = el('span');
            fill.style.width = (st.i / N * 100) + '%';
            bar.appendChild(fill);
            wrap.appendChild(bar);
            requestAnimationFrame(function () { requestAnimationFrame(function () { fill.style.width = ((st.i + 1) / N * 100) + '%'; }); });

            var qEl = el('h4', 'cd-quiz__q', (st.i + 1) + '. ' + q.q);
            qEl.tabIndex = -1;
            wrap.appendChild(qEl);

            var list = el('div', 'cd-quiz__opts');
            list.setAttribute('role', 'group');
            list.setAttribute('aria-label', 'Answer options');
            var fb = el('div');
            var foot = el('div', 'cd-quiz__foot');
            var optBtns = [];

            q.opts.forEach(function (text, idx) {
                var b = el('button', 'cd-opt');
                b.type = 'button';
                b.style.animationDelay = (idx * 60 + 80) + 'ms';
                var key = el('span', 'cd-opt__key', String.fromCharCode(65 + idx));
                var label = el('span', 'cd-opt__text', text);
                var mark = el('span', 'cd-opt__mark');
                mark.innerHTML = ICON.check;
                b.appendChild(key); b.appendChild(label); b.appendChild(mark);
                b.addEventListener('click', function () { choose(idx); });
                list.appendChild(b);
                optBtns.push(b);
            });
            wrap.appendChild(list);
            wrap.appendChild(fb);
            wrap.appendChild(foot);

            function choose(idx) {
                if (st.picked[st.i] !== undefined) return;
                st.picked[st.i] = idx;

                optBtns.forEach(function (b, i) {
                    b.disabled = true;
                    if (q.a >= 0) {
                        if (i === q.a) b.classList.add('is-correct');
                        else if (i === idx) { b.classList.add('is-wrong'); b.querySelector('.cd-opt__mark').innerHTML = ICON.close; }
                        else b.classList.add('is-dim');
                    } else {
                        if (i === idx) b.classList.add('is-picked'); else b.classList.add('is-dim');
                    }
                });

                var box;
                if (q.a >= 0) {
                    st.scored++;
                    if (idx === q.a) {
                        st.score++;
                        sc.textContent = 'Score: ' + st.score;
                        box = el('div', 'cd-quiz__fb cd-quiz__fb--ok');
                        box.appendChild(el('strong', '', 'Correct!'));
                    } else {
                        box = el('div', 'cd-quiz__fb cd-quiz__fb--no');
                        box.appendChild(el('strong', '', 'Not quite.'));
                        box.appendChild(document.createTextNode('Correct answer: ' + q.opts[q.a]));
                    }
                } else {
                    box = el('div', 'cd-quiz__fb cd-quiz__fb--neutral');
                    box.appendChild(el('strong', '', 'Answer recorded.'));
                }
                if (q.why) { var why = el('div', '', q.why); why.style.marginTop = '4px'; box.appendChild(why); }
                fb.replaceChildren(box);

                var last = st.i === N - 1;
                var go = el('button', 'cd-btn cd-btn--gold');
                go.type = 'button';
                go.innerHTML = '<span>' + (last ? 'See results' : 'Next question') + '</span>' + ICON.right;
                go.addEventListener('click', function () { if (last) showResult(); else { st.i++; showQuestion(); scroller.scrollTop = 0; } });
                foot.appendChild(go);
                go.focus({ preventScroll: true });
                go.scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
            }

            qEl.focus({ preventScroll: true });
        }

        function showResult() {
            var pct = st.scored ? Math.round(st.score / st.scored * 100) : 100;
            saveBest(lessonId, pct);
            refreshBestChip(lessonId);

            var wrap = el('div', 'cd-quiz');
            var res = el('div', 'cd-result');
            screen.replaceChildren(wrap);
            wrap.appendChild(res);

            var ring = el('div', 'cd-ring ' + (pct >= 70 ? 'is-good' : (pct < 40 ? 'is-low' : '')));
            ring.innerHTML = '<svg viewBox="0 0 120 120" aria-hidden="true"><circle class="track" cx="60" cy="60" r="52"></circle><circle class="bar" cx="60" cy="60" r="52"></circle></svg>';
            var num = el('div', 'cd-ring__num', '0%');
            ring.appendChild(num);
            res.appendChild(ring);

            var headline = pct >= 80 ? 'Excellent work!' : (pct >= 50 ? 'Good effort!' : 'Keep practising!');
            res.appendChild(el('h4', '', headline));
            res.appendChild(el('p', '', st.scored
                ? 'You answered ' + st.score + ' of ' + st.scored + ' questions correctly.'
                : 'You have completed this quiz.'));

            var actions = el('div', 'cd-result__actions');
            var retry = el('button', 'cd-btn cd-btn--outline cd-btn--sm'); retry.type = 'button';
            retry.innerHTML = ICON.refresh + ' <span>Retry quiz</span>';
            retry.addEventListener('click', function () { start(); scroller.scrollTop = 0; });
            var review = el('button', 'cd-btn cd-btn--outline cd-btn--sm'); review.type = 'button';
            review.innerHTML = ICON.quiz + ' <span>Review answers</span>';
            actions.appendChild(retry); actions.appendChild(review);

            if (current < lessons.length - 1) {
                var cont = el('button', 'cd-btn cd-btn--gold cd-btn--sm'); cont.type = 'button';
                cont.innerHTML = '<span>Next lesson</span>' + ICON.right;
                cont.addEventListener('click', function () { nextBtn.click(); });
                actions.appendChild(cont);
            }
            res.appendChild(actions);

            var list = el('div', 'cd-review'); list.hidden = true;
            questions.forEach(function (q, i) {
                var chosen = st.picked[i];
                var ok = q.a >= 0 && chosen === q.a;
                var item = el('div', 'cd-review__item ' + (q.a < 0 ? '' : (ok ? 'ok' : 'no')));
                item.style.animationDelay = (i * 40) + 'ms';
                item.appendChild(el('strong', '', (i + 1) + '. ' + q.q));
                item.appendChild(el('span', q.a < 0 ? '' : (ok ? 'good' : 'bad'), 'Your answer: ' + (chosen !== undefined ? q.opts[chosen] : 'Not answered')));
                if (q.a >= 0 && !ok) item.appendChild(el('span', 'good', 'Correct answer: ' + q.opts[q.a]));
                list.appendChild(item);
            });
            res.appendChild(list);
            review.addEventListener('click', function () {
                list.hidden = !list.hidden;
                if (!list.hidden) list.scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
            });

            // animate ring + number
            var bar = ring.querySelector('.bar');
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { bar.style.strokeDashoffset = String(327 * (1 - pct / 100)); });
            });
            if (reduceMotion) { num.textContent = pct + '%'; }
            else {
                var t0 = null, dur = 900;
                (function tick(ts) {
                    if (t0 === null) t0 = ts;
                    var p = Math.min((ts - t0) / dur, 1);
                    num.textContent = Math.round(pct * p) + '%';
                    if (p < 1) requestAnimationFrame(tick);
                })(performance.now());
            }
            scroller.scrollTop = 0;
        }

        start();
    }

    function renderInfo(kind, data) {
        info.replaceChildren();

        var chips = el('div', 'cd-chips');
        chips.appendChild(el('span', 'cd-chip', KIND_LABEL[kind] || 'Lesson'));
        if (kind === 'quiz' && data.qcount) {
            chips.appendChild(el('span', 'cd-chip', data.qcount + (data.qcount === 1 ? ' question' : ' questions')));
        }
        if (data.duration) {
            var d = el('span', 'cd-chip'); d.innerHTML = ICON.clock + ' ';
            d.appendChild(document.createTextNode(data.duration));
            chips.appendChild(d);
        }
        if (kind === 'quiz') {
            var best = el('span', 'cd-chip cd-chip--best'); best.hidden = true;
            chips.appendChild(best);
        }
        info.appendChild(chips);
        if (kind === 'quiz') refreshBestChip(data.id);

        if (data.summary) {
            var box = el('div', 'cd-dialog__summary');
            box.appendChild(el('h4', '', 'About this lesson'));
            box.appendChild(el('p', '', data.summary));
            info.appendChild(box);
        }

        if (data.attachment && kind !== 'document') {
            var a = el('a', 'cd-btn cd-btn--outline cd-btn--sm');
            a.href = data.attachment; a.target = '_blank'; a.rel = 'noopener';
            a.setAttribute('download', data.attname || '');
            a.innerHTML = ICON.download + ' ';
            a.appendChild(document.createTextNode('Download resource' + (data.attname ? ' (' + data.attname + ')' : '')));
            info.appendChild(a);
        }
    }

    function goToCourseCheckout(btn) {
        if (!btn || !btn.dataset.buyUrl) return;

        // Use the existing Buy Now POST route so the course is added to
        // the cart and the application's normal checkout flow is followed.
        var form = document.createElement('form');
        form.method = 'POST';
        form.action = btn.dataset.buyUrl;
        form.style.display = 'none';

        var csrf = document.createElement('input');
        csrf.type = 'hidden';
        csrf.name = '_token';
        csrf.value = btn.dataset.csrf || '';
        form.appendChild(csrf);

        document.body.appendChild(form);
        form.submit();
    }

    function render(index) {
        var btn = lessons[index];
        if (!btn) return;
        if (btn.dataset.locked === '1') {
            goToCourseCheckout(btn);
            return;
        }

        stopProgress(true); // save the lesson being left (uses its own lesson id) and destroy its player
        if (lessons[current]) lessons[current].classList.remove('is-active');
        current = index;
        btn.classList.add('is-active');

        var kind = btn.dataset.kind || 'video';
        var data = {
            id: btn.dataset.id || '',
            title: btn.dataset.title || 'Lesson',
            url: btn.dataset.url || '',
            vtype: btn.dataset.vtype || '',
            duration: btn.dataset.duration || '',
            summary: btn.dataset.summary || '',
            attachment: btn.dataset.attachment || '',
            attname: btn.dataset.attname || '',
            qcount: parseInt(btn.dataset.qcount || '0', 10) || 0
        };

        tag.textContent = KIND_LABEL[kind] || 'Lesson';
        title.textContent = data.title;
        count.textContent = 'Lesson ' + (index + 1) + ' of ' + lessons.length;
        prevBtn.disabled = index === 0;
        nextBtn.disabled = index === lessons.length - 1;

        window.currentLessonData = data;
     

if (kind === 'video') {
    renderVideo(data.url, data.vtype);
} else {
    clearInterval(progressTimer);
    activeProgress = null;

    if (progressShow) {
        progressShow.hidden = true;
    }

    if (kind === 'quiz') {
        renderQuiz(btn);
    } else {
        renderDocument(data);
    }
}


        renderInfo(kind, data);
        scroller.scrollTop = 0;
    }

    function lockScroll() {
        var gap = window.innerWidth - document.documentElement.clientWidth;
        if (gap > 0) document.body.style.paddingRight = gap + 'px';
        document.documentElement.classList.add('cd-lock');
    }
    function unlockScroll() {
        document.documentElement.classList.remove('cd-lock');
        document.body.style.paddingRight = '';
    }

    function openModal(index, trigger) {
        clearTimeout(closeTimer);
        lastFocus = trigger || document.activeElement;
        modal.hidden = false;
        lockScroll();
        render(index);
        requestAnimationFrame(function () {
            requestAnimationFrame(function () {
                modal.classList.add('is-open');
                dialog.focus({ preventScroll: true });
            });
        });
    }

    function closeModal() {
        stopProgress(true); // save position, clear timer, destroy player
        if (modal.hidden) return;
        modal.classList.remove('is-open');
        screen.replaceChildren();               // stops any playing video / audio
        if (lessons[current]) lessons[current].classList.remove('is-active');
        current = -1;

        closeTimer = setTimeout(function () {
            modal.hidden = true;
            unlockScroll();
            if (lastFocus && lastFocus.focus) lastFocus.focus({ preventScroll: true });
        }, reduceMotion ? 0 : 380);
    }

    lessons.forEach(function (btn, i) {
        btn.addEventListener('click', function () {
            if (btn.dataset.locked === '1') {
                goToCourseCheckout(btn);
                return;
            }
            openModal(i, btn);
        });
    });

    modal.addEventListener('click', function (e) {
        if (e.target.closest('[data-close]')) closeModal();
    });
    prevBtn.addEventListener('click', function () { if (current > 0) render(current - 1); });
    nextBtn.addEventListener('click', function () { if (current < lessons.length - 1) render(current + 1); });

    document.addEventListener('keydown', function (e) {
        if (modal.hidden) return;
        if (e.key === 'Escape') { e.preventDefault(); closeModal(); return; }
        if (e.key !== 'Tab') return;

        var f = dialog.querySelectorAll('a[href], button:not([disabled]), iframe, video[controls], [tabindex]:not([tabindex="-1"])');
        if (!f.length) { e.preventDefault(); return; }
        var first = f[0], last = f[f.length - 1];
        if (e.shiftKey && (document.activeElement === first || document.activeElement === dialog)) { e.preventDefault(); last.focus(); }
        else if (!e.shiftKey && document.activeElement === last) { e.preventDefault(); first.focus(); }
    });

    /* Swipe down on the sheet header to close (touch devices) */
    (function () {
        var head = modal.querySelector('.cd-dialog__head');
        var grip = modal.querySelector('.cd-dialog__grip');
        var startY = null;
        [head, grip].forEach(function (n) {
            n.addEventListener('touchstart', function (e) { startY = e.touches[0].clientY; }, { passive: true });
            n.addEventListener('touchend', function (e) {
                if (startY !== null && e.changedTouches[0].clientY - startY > 70) closeModal();
                startY = null;
            }, { passive: true });
        });
    })();

    /* ---------- Sticky buy bar (tablet / mobile) ---------- */
    var bar = document.getElementById('cdBar');
    var box = document.getElementById('cdPurchase');
    if (bar && box && 'IntersectionObserver' in window) {
        var boxVisible = true;
        var update = function () {
            var show = window.innerWidth < 1024 && !boxVisible;
            bar.classList.toggle('is-visible', show);
            bar.setAttribute('aria-hidden', show ? 'false' : 'true');
            bar.querySelectorAll('button').forEach(function (b) { b.tabIndex = show ? 0 : -1; });
        };
        new IntersectionObserver(function (entries) {
            boxVisible = entries[0].isIntersecting;
            update();
        }, { threshold: 0.05 }).observe(box);
        window.addEventListener('resize', update);
    }

    /* ---------- Prevent double submits ---------- */
    root.querySelectorAll('form[data-once]').forEach(function (form) {
        form.addEventListener('submit', function () {
            var btn = form.querySelector('button[type="submit"]');
            if (!btn) return;
            setTimeout(function () { btn.disabled = true; }, 0);
        });
    });
    window.addEventListener('pagehide', flushProgress);
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) root.querySelectorAll('form[data-once] button').forEach(function (b) { b.disabled = false; });
    });
})();
</script>
@endsection