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

            $correctIndexes = [];

            foreach ($rawCorrect as $index) {
                if (is_int($index) || (is_string($index) && ctype_digit($index))) {
                    $index = (int) $index;

                    if ($index >= 0 && $index < count($options)) {
                        $correctIndexes[] = $index;
                    }
                }
            }

            $correctIndexes = array_values(array_unique($correctIndexes));
            sort($correctIndexes);

            $type = (string) $field('type', 'single_choice');

            $answer = $type === 'multiple_choice'
                ? $correctIndexes
                : ($correctIndexes[0] ?? -1);

            $out[] = [
                'id'   => (int) $field('id', 0),
                'q'    => $text,
                'opts' => $options,
                'a'    => $answer,
                'type' => $type,
                'why'  => trim(strip_tags((string) $field('explanation', ''))),
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
    $isCourseUnlocked = (bool) ($isCourseUnlocked ?? ($isFree || $isEnrolled));
    $courseLessonIndex = 0;

    $kindLabels = ['video' => 'Video lesson', 'document' => 'Document', 'quiz' => 'Quiz'];

    /* ---------- Meta ---------- */
    $instructor     = $course->instructor ?? null;
    $instructorName = $instructor->name ?? 'EduSphere Team';
    $instructorBio  = trim(strip_tags((string) $attr($instructor, 'bio', '')));
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

<style>
/* =========================================================
   Course details page
   ========================================================= */
.cd{
    --cd-navy:#0f1b33; --cd-navy-2:#1a2b4f;
    --cd-gold:#d6a84f; --cd-gold-d:#b98a33;
    --cd-ink:#1b2333; --cd-muted:#667085; --cd-line:#e6e8ef; --cd-bg:#f5f6fa;
    --cd-ok:#12a150; --cd-ok-bg:#e8f7ef; --cd-no:#d92d20; --cd-no-bg:#fdecea;
    --cd-radius:16px;
    background:var(--cd-bg); color:var(--cd-ink); line-height:1.55;
    font-family:inherit; -webkit-font-smoothing:antialiased;
}
.cd *,.cd *::before,.cd *::after{box-sizing:border-box}
.cd svg{width:1.1em;height:1.1em;flex:none;display:inline-block;vertical-align:-.18em}
.cd a{color:inherit;text-decoration:none}
.cd button{font-family:inherit;cursor:pointer}
.cd-container{width:100%;max-width:1200px;margin:0 auto;padding:0 20px}
.cd-lock{overflow:hidden}

/* ---------- Hero ---------- */
.cd-hero{background:linear-gradient(135deg,var(--cd-navy) 0%,var(--cd-navy-2) 100%);color:#fff;padding:36px 0 60px;position:relative;overflow:hidden}
.cd-hero::after{content:"";position:absolute;right:-120px;top:-140px;width:420px;height:420px;border-radius:50%;background:radial-gradient(circle,rgba(214,168,79,.22),transparent 70%);pointer-events:none}
.cd-hero__copy{position:relative;z-index:1}
.cd-crumbs{display:flex;flex-wrap:wrap;gap:6px;font-size:13px;color:rgba(255,255,255,.7);margin-bottom:16px}
.cd-crumbs a:hover{color:var(--cd-gold)}
.cd-crumbs .sep{opacity:.5}
.cd-crumbs .current{color:#fff;max-width:100%;overflow:hidden;text-overflow:ellipsis;white-space:nowrap}
.cd-badges{display:flex;flex-wrap:wrap;gap:8px;margin-bottom:14px}
.cd-badge{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;font-size:12.5px;font-weight:600;background:rgba(255,255,255,.12);color:#fff}
.cd-badge--gold{background:var(--cd-gold);color:#241a05}
.cd-title{font-size:clamp(26px,4vw,40px);line-height:1.2;font-weight:800;margin:0 0 12px;letter-spacing:-.01em}
.cd-lead{font-size:clamp(15px,1.6vw,17px);color:rgba(255,255,255,.82);margin:0 0 18px;max-width:760px}
.cd-meta{list-style:none;display:flex;flex-wrap:wrap;gap:8px 20px;margin:0;padding:0;font-size:14px;color:rgba(255,255,255,.85)}
.cd-meta li{display:inline-flex;align-items:center;gap:7px}
.cd-meta .rate svg{color:var(--cd-gold)}

/* ---------- Body / grid ---------- */
.cd-body{padding-top:28px;padding-bottom:70px}
.cd-grid{display:grid;grid-template-columns:minmax(0,1fr);gap:24px;align-items:start}
.cd-main{display:grid;gap:22px;min-width:0}
.cd-side{order:-1;min-width:0}

@media (min-width:1024px){
    .cd-grid:not(.cd-grid--full){grid-template-columns:minmax(0,1fr) 380px;gap:32px}
    .cd-side{order:0;position:sticky;top:90px;margin-top:-210px;z-index:5}
    .cd-hero--wide .cd-hero__copy{max-width:calc(100% - 430px)}
}

/* ---------- Alerts ---------- */
.cd-alert{display:flex;gap:12px;align-items:flex-start;padding:14px 16px;border-radius:12px;margin-bottom:18px;font-size:14.5px;border:1px solid}
.cd-alert ul{margin:0;padding-left:18px}
.cd-alert svg{margin-top:3px;width:1.25em;height:1.25em}
.cd-alert--success{background:var(--cd-ok-bg);border-color:#b7e4c9;color:#0b6b36}
.cd-alert--error{background:var(--cd-no-bg);border-color:#f5c2bd;color:#a3231a}

/* ---------- Panels ---------- */
.cd-panel{background:#fff;border:1px solid var(--cd-line);border-radius:var(--cd-radius);padding:24px;box-shadow:0 1px 2px rgba(16,24,40,.04)}
.cd-h2{font-size:20px;font-weight:800;margin:0 0 16px;letter-spacing:-.01em}
.cd-js .cd-reveal{opacity:0;transform:translateY(18px);transition:opacity .55s ease,transform .55s ease}
.cd-js .cd-reveal.is-in{opacity:1;transform:none}

.cd-checks{list-style:none;margin:0;padding:0;display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:12px 24px}
.cd-checks--single{grid-template-columns:minmax(0,1fr)}
.cd-checks li{display:flex;gap:10px;align-items:flex-start;font-size:15px}
.cd-checks .tick{flex:none;width:22px;height:22px;border-radius:50%;background:var(--cd-ok-bg);color:var(--cd-ok);display:grid;place-items:center;margin-top:1px}
.cd-checks .tick svg{width:13px;height:13px;stroke-width:3}
@media (max-width:700px){.cd-checks{grid-template-columns:minmax(0,1fr)}}

/* ---------- Curriculum ---------- */
.cd-cur__head{display:flex;flex-wrap:wrap;align-items:center;justify-content:space-between;gap:10px;margin-bottom:16px}
.cd-cur__head .cd-h2{margin:0}
.cd-cur__stats{display:flex;flex-wrap:wrap;align-items:center;gap:6px 16px;font-size:13.5px;color:var(--cd-muted)}
.cd-link-btn{background:none;border:0;padding:0;color:var(--cd-gold-d);font-weight:700;font-size:13.5px}
.cd-link-btn:hover{text-decoration:underline}
.cd-empty{padding:22px;border:1px dashed var(--cd-line);border-radius:12px;color:var(--cd-muted);text-align:center;font-size:14.5px}
.cd-sections{display:grid;gap:10px}
.cd-sec{border:1px solid var(--cd-line);border-radius:12px;background:#fff;overflow:hidden}
.cd-sec[open]{border-color:#d9dcE6}
.cd-sec__head{list-style:none;display:flex;align-items:center;gap:12px;padding:14px 16px;cursor:pointer;background:#fafbfd;user-select:none}
.cd-sec__head::-webkit-details-marker{display:none}
.cd-sec__head:hover{background:#f3f5fa}
.cd-sec__chev{display:grid;place-items:center;transition:transform .25s ease;color:var(--cd-muted)}
.cd-sec[open]:not(.is-closing) .cd-sec__chev{transform:rotate(180deg)}
.cd-sec__name{flex:1;font-weight:700;font-size:15px;min-width:0}
.cd-sec__count{font-size:12.5px;color:var(--cd-muted);white-space:nowrap}
.cd-sec__body{overflow:hidden;border-top:1px solid var(--cd-line)}

.cd-lesson{display:flex;align-items:center;gap:12px;width:100%;text-align:left;background:#fff;border:0;border-bottom:1px solid var(--cd-line);padding:13px 16px;color:inherit;transition:background .15s}
.cd-sec__body > .cd-lesson:last-child{border-bottom:0}
.cd-lesson:hover{background:#fbf6ea}
.cd-lesson:focus-visible{outline:2px solid var(--cd-gold);outline-offset:-2px}
.cd-lesson.is-active{background:#fbf3de}
.cd-lesson__icon{flex:none;width:36px;height:36px;border-radius:10px;background:#fbf0d6;color:var(--cd-gold-d);display:grid;place-items:center}
.cd-lesson__icon svg{width:16px;height:16px}
.cd-lesson[data-locked="1"] .cd-lesson__icon{background:#eef0f5;color:#8a93a6}
.cd-lesson__text{flex:1;min-width:0;display:grid;gap:2px}
.cd-lesson__title{font-weight:600;font-size:14.5px;overflow:hidden;text-overflow:ellipsis}
.cd-lesson__sub{display:flex;flex-wrap:wrap;align-items:center;gap:4px 10px;font-size:12.5px;color:var(--cd-muted)}
.cd-lesson__sub span{display:inline-flex;align-items:center;gap:4px}
.cd-lesson__time{flex:none;display:inline-flex;align-items:center;gap:5px;font-size:12.5px;color:var(--cd-muted)}
.cd-preview-badge,.cd-locked-badge{padding:1px 8px;border-radius:99px;font-weight:700;font-size:11.5px}
.cd-preview-badge{background:var(--cd-ok-bg);color:#0b6b36}
.cd-locked-badge{background:#eef0f5;color:#667085}
.cd-lesson--empty{color:var(--cd-muted);cursor:default;font-size:14px}
.cd-lesson--empty:hover{background:#fff}

/* ---------- Prose / instructor ---------- */
.cd-prose{font-size:15.5px;overflow-wrap:anywhere}
.cd-prose img,.cd-prose iframe,.cd-prose video{max-width:100%;height:auto;border-radius:10px}
.cd-prose h1,.cd-prose h2,.cd-prose h3,.cd-prose h4{margin:1.2em 0 .5em;line-height:1.3}
.cd-prose p{margin:0 0 1em}
.cd-prose ul,.cd-prose ol{padding-left:22px;margin:0 0 1em}
.cd-inst{display:flex;gap:16px;align-items:flex-start}
.cd-inst__avatar{flex:none;width:64px;height:64px;border-radius:50%;background:linear-gradient(135deg,var(--cd-gold),#f0cf86);color:#241a05;font-weight:800;font-size:26px;display:grid;place-items:center}
.cd-inst__name{font-weight:800;font-size:17px}
.cd-inst__role{font-size:13.5px;color:var(--cd-muted)}
.cd-inst__bio{margin:10px 0 0;font-size:14.5px;color:#3b4457}

/* ---------- Purchase card ---------- */
.cd-card{background:#fff;border:1px solid var(--cd-line);border-radius:var(--cd-radius);overflow:hidden;box-shadow:0 18px 40px -18px rgba(16,24,40,.35)}
.cd-card__body{padding:20px}
.cd-card__ph{display:grid;place-items:center;gap:8px;color:rgba(255,255,255,.7);font-size:14px;text-align:center}
.cd-card__ph svg{width:34px;height:34px}
.cd-price{display:flex;align-items:baseline;flex-wrap:wrap;gap:6px 12px;margin-bottom:16px}
.cd-price__now{font-size:32px;font-weight:800;letter-spacing:-.02em}
.cd-price__now.is-free{color:var(--cd-ok)}
.cd-price__old{color:var(--cd-muted);text-decoration:line-through;font-size:16px}
.cd-price__off{background:var(--cd-no-bg);color:var(--cd-no);font-weight:700;font-size:12.5px;padding:3px 9px;border-radius:99px}
.cd-actions{display:grid;gap:10px}
.cd-actions form{margin:0}
.cd-includes{margin-top:20px;padding-top:18px;border-top:1px solid var(--cd-line)}
.cd-includes h4{margin:0 0 10px;font-size:15px}
.cd-includes ul{list-style:none;margin:0;padding:0;display:grid;gap:9px;font-size:14px;color:#3b4457}
.cd-includes li{display:flex;align-items:center;gap:10px}
.cd-includes li svg{color:var(--cd-gold-d)}

/* ---------- Buttons ---------- */
.cd-btn{display:inline-flex;align-items:center;justify-content:center;gap:8px;width:100%;padding:13px 18px;border-radius:12px;border:2px solid transparent;font-weight:700;font-size:15px;line-height:1.2;transition:transform .15s,box-shadow .15s,background .15s,border-color .15s,color .15s}
.cd-btn:active{transform:translateY(1px)}
.cd-btn:disabled{opacity:.6;cursor:not-allowed}
.cd-btn--gold{background:var(--cd-gold);color:#241a05;box-shadow:0 6px 16px -6px rgba(214,168,79,.8)}
.cd-btn--gold:hover:not(:disabled){background:#e0b560}
.cd-btn--outline{background:#fff;color:var(--cd-ink);border-color:var(--cd-line)}
.cd-btn--outline:hover:not(:disabled){border-color:var(--cd-gold);color:var(--cd-gold-d)}
.cd-btn--success{background:var(--cd-ok-bg);color:#0b6b36;border-color:#b7e4c9}
.cd-btn--sm{width:auto;padding:9px 14px;font-size:13.5px;border-radius:10px}
.cd-btn:focus-visible,.cd-nav:focus-visible,.cd-dialog__close:focus-visible,.cd-opt:focus-visible{outline:3px solid rgba(214,168,79,.55);outline-offset:2px}

/* ---------- Screen (video / document / quiz area) ---------- */
.cd-screen{position:relative;width:100%;aspect-ratio:16/9;background:#0b1224;display:flex;align-items:center;justify-content:center;overflow:hidden}
.cd-screen > iframe,.cd-screen > video,.cd-screen > img{position:absolute;inset:0;width:100%;height:100%;border:0;object-fit:cover;background:#000}
.cd-screen > video{object-fit:contain}
.cd-screen--document{aspect-ratio:auto;height:min(68vh,640px);background:#eef0f5}
.cd-screen--quiz{aspect-ratio:auto;min-height:340px;background:#fff;display:block;overflow:visible}
.cd-screen__note{color:rgba(255,255,255,.85);text-align:center;padding:24px;margin:0;font-size:14.5px}
.cd-loader{position:absolute;left:50%;top:50%;width:38px;height:38px;margin:-19px 0 0 -19px;border-radius:50%;border:3px solid rgba(255,255,255,.2);border-top-color:var(--cd-gold);animation:cdSpin .8s linear infinite}
@keyframes cdSpin{to{transform:rotate(360deg)}}

/* ---------- Sticky buy bar ---------- */
.cd-bar{position:fixed;left:0;right:0;bottom:0;z-index:900;display:flex;align-items:center;gap:10px;padding:10px 16px calc(10px + env(safe-area-inset-bottom));background:#fff;border-top:1px solid var(--cd-line);box-shadow:0 -8px 24px -12px rgba(16,24,40,.3);transform:translateY(110%);transition:transform .3s ease}
.cd-bar.is-visible{transform:none}
.cd-bar form{margin:0}
.cd-bar form:last-child{flex:1}
.cd-bar__price{display:grid;line-height:1.15;margin-right:auto}
.cd-bar__price strong{font-size:19px}
.cd-bar__price s{font-size:12.5px;color:var(--cd-muted)}
.cd-bar .cd-btn{padding:11px 16px}
@media (min-width:1024px){.cd-bar{display:none}}

/* ---------- Lesson popup ---------- */
.cd-modal{position:fixed;inset:0;z-index:1050;display:flex;align-items:center;justify-content:center;padding:20px}
.cd-modal[hidden]{display:none}
.cd-modal__backdrop{position:absolute;inset:0;background:rgba(9,14,28,.72);backdrop-filter:blur(4px);opacity:0;transition:opacity .35s ease}
.cd-modal.is-open .cd-modal__backdrop{opacity:1}
.cd-dialog{position:relative;width:min(980px,100%);max-height:calc(100vh - 40px);max-height:calc(100dvh - 40px);display:flex;flex-direction:column;background:#fff;border-radius:20px;overflow:hidden;box-shadow:0 40px 90px -20px rgba(0,0,0,.6);outline:0;opacity:0;transform:translateY(26px) scale(.98);transition:opacity .35s ease,transform .38s cubic-bezier(.2,.8,.2,1)}
.cd-modal.is-open .cd-dialog{opacity:1;transform:none}
.cd-dialog__grip{display:none;width:44px;height:5px;border-radius:99px;background:#d0d5dd;margin:10px auto 0;flex:none}
.cd-dialog__head{flex:none;display:flex;align-items:center;justify-content:space-between;gap:14px;padding:14px 20px;border-bottom:1px solid var(--cd-line)}
.cd-dialog__titles{min-width:0}
.cd-dialog__tag{display:inline-block;font-size:11.5px;font-weight:800;text-transform:uppercase;letter-spacing:.06em;color:var(--cd-gold-d)}
.cd-dialog__title{margin:2px 0 0;font-size:18px;font-weight:800;line-height:1.3;overflow:hidden;text-overflow:ellipsis;display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical}
.cd-dialog__close{flex:none;width:40px;height:40px;border-radius:50%;border:0;background:#f1f3f8;color:var(--cd-ink);display:grid;place-items:center;transition:background .15s,transform .15s}
.cd-dialog__close:hover{background:#e4e7ef;transform:rotate(90deg)}
.cd-dialog__scroll{flex:1;min-height:0;overflow-y:auto;overscroll-behavior:contain;-webkit-overflow-scrolling:touch}
.cd-dialog__info{padding:18px 22px 20px;display:grid;gap:14px}
.cd-dialog__info:empty{display:none}
.cd-chips{display:flex;flex-wrap:wrap;gap:8px}
.cd-chip{display:inline-flex;align-items:center;gap:6px;padding:5px 12px;border-radius:99px;background:#f1f3f8;color:#3b4457;font-size:12.5px;font-weight:600}
.cd-chip--best{background:#fbf0d6;color:#7a5712}
.cd-chip[hidden]{display:none}
.cd-dialog__summary{background:#fafbfd;border:1px solid var(--cd-line);border-radius:12px;padding:14px 16px}
.cd-dialog__summary h4{margin:0 0 6px;font-size:14.5px}
.cd-dialog__summary p{margin:0;font-size:14.5px;color:#3b4457;white-space:pre-line}
.cd-info-actions{display:flex;flex-wrap:wrap;gap:10px}

.cd-progress{padding:0 22px 18px}
.cd-progress[hidden]{display:none}
.cd-progress__row{display:flex;justify-content:space-between;gap:12px;font-size:13px;margin-bottom:7px}
.cd-progress__track{height:6px;background:#e8eaf0;border-radius:99px;overflow:hidden}
.cd-progress__fill{height:100%;width:0;background:var(--cd-gold);border-radius:99px;transition:width .25s ease}
.cd-progress__time{display:block;margin-top:6px;color:var(--cd-muted);font-size:12.5px}
.cd-cert-note{margin-top:12px;padding:12px 14px;border-radius:12px;background:var(--cd-ok-bg);color:#0b6b36;font-size:14px;font-weight:600}
.cd-cert-note a{margin-left:6px;color:inherit;text-decoration:underline}
.cd-cert-note--err{background:var(--cd-no-bg);color:#a3231a}
.cd-cert-note--info{background:#f1f3f8;color:#3b4457}
.cd-cert-note{display:flex;flex-wrap:wrap;align-items:center;gap:10px;justify-content:space-between}
.cd-cert-btn{display:inline-block;padding:9px 18px;border-radius:10px;background:var(--cd-gold);color:#1b1b1b;font-weight:700;text-decoration:none!important;margin-left:0!important}

.cd-dialog__foot{flex:none;display:flex;align-items:center;justify-content:space-between;gap:10px;padding:12px 20px calc(12px + env(safe-area-inset-bottom));border-top:1px solid var(--cd-line);background:#fff}
.cd-dialog__count{font-size:13px;color:var(--cd-muted);text-align:center}
.cd-nav{display:inline-flex;align-items:center;gap:6px;padding:10px 16px;border-radius:10px;border:2px solid var(--cd-line);background:#fff;font-weight:700;font-size:14px;color:var(--cd-ink);transition:border-color .15s,background .15s}
.cd-nav:hover:not(:disabled){border-color:var(--cd-gold)}
.cd-nav:disabled{opacity:.4;cursor:not-allowed}
.cd-nav--next{background:var(--cd-gold);border-color:var(--cd-gold);color:#241a05}
.cd-nav--next:hover:not(:disabled){background:#e0b560;border-color:#e0b560}

@media (max-width:640px){
    .cd-container{padding:0 16px}
    .cd-panel{padding:18px}
    .cd-modal{padding:0;align-items:flex-end}
    .cd-dialog{width:100%;max-height:94vh;max-height:94dvh;height:94vh;height:94dvh;border-radius:22px 22px 0 0;transform:translateY(100%);opacity:1}
    .cd-dialog__grip{display:block}
    .cd-dialog__head{padding:10px 16px 12px}
    .cd-dialog__title{font-size:16px}
    .cd-dialog__info{padding:16px}
    .cd-progress{padding:0 16px 16px}
    .cd-dialog__foot{padding:10px 14px calc(10px + env(safe-area-inset-bottom))}
    .cd-nav{padding:10px 12px}
    .cd-nav span{font-size:13px}
    .cd-screen--document{height:62vh}
    .cd-lesson__time{display:none}
}

/* ---------- Quiz ---------- */
.cd-quiz{padding:24px 22px 26px;max-width:760px;margin:0 auto;color:var(--cd-ink)}
.cd-quiz__top{display:flex;justify-content:space-between;font-size:13px;font-weight:700;color:var(--cd-muted);margin-bottom:8px}
.cd-quiz__score{color:var(--cd-gold-d)}
.cd-quiz__bar{height:6px;background:#e8eaf0;border-radius:99px;overflow:hidden;margin-bottom:20px}
.cd-quiz__bar span{display:block;height:100%;width:0;background:var(--cd-gold);border-radius:99px;transition:width .5s ease}
.cd-quiz__q{margin:0 0 8px;font-size:19px;line-height:1.4;font-weight:800;outline:0}
.cd-quiz__hint{margin:0 0 14px;font-size:13px;color:var(--cd-muted)}
.cd-quiz__opts{display:grid;gap:10px;margin-top:16px}
.cd-quiz__hint + .cd-quiz__opts{margin-top:0}
.cd-opt{display:flex;align-items:center;gap:12px;width:100%;text-align:left;padding:13px 14px;border-radius:12px;border:2px solid var(--cd-line);background:#fff;color:var(--cd-ink);font-size:15px;transition:border-color .15s,background .15s,transform .15s;animation:cdIn .35s both}
@keyframes cdIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:none}}
.cd-opt:hover:not(:disabled){border-color:var(--cd-gold);background:#fffaf0;transform:translateX(2px)}
.cd-opt:disabled{cursor:default}
.cd-opt__key{flex:none;width:30px;height:30px;border-radius:8px;background:#f1f3f8;display:grid;place-items:center;font-weight:800;font-size:13px}
.cd-opt__text{flex:1;min-width:0;overflow-wrap:anywhere}
.cd-opt__mark{flex:none;width:24px;height:24px;border-radius:50%;display:grid;place-items:center;opacity:0;color:#fff}
.cd-opt__mark svg{width:14px;height:14px;stroke-width:3}
.cd-opt.is-selected{border-color:var(--cd-gold);background:#fffaf0}
.cd-opt.is-selected .cd-opt__key{background:var(--cd-gold);color:#241a05}
.cd-opt.is-correct{border-color:var(--cd-ok);background:var(--cd-ok-bg)}
.cd-opt.is-correct .cd-opt__key{background:var(--cd-ok);color:#fff}
.cd-opt.is-correct .cd-opt__mark{opacity:1;background:var(--cd-ok)}
.cd-opt.is-wrong{border-color:var(--cd-no);background:var(--cd-no-bg)}
.cd-opt.is-wrong .cd-opt__key{background:var(--cd-no);color:#fff}
.cd-opt.is-wrong .cd-opt__mark{opacity:1;background:var(--cd-no)}
.cd-opt.is-picked{border-color:var(--cd-gold);background:#fffaf0}
.cd-opt.is-dim{opacity:.55}
.cd-quiz__fb{margin-top:16px;padding:13px 15px;border-radius:12px;font-size:14.5px;animation:cdIn .3s both}
.cd-quiz__fb strong{display:block;margin-bottom:2px}
.cd-quiz__fb--ok{background:var(--cd-ok-bg);color:#0b6b36}
.cd-quiz__fb--no{background:var(--cd-no-bg);color:#a3231a}
.cd-quiz__fb--neutral{background:#f1f3f8;color:#3b4457}
.cd-quiz__foot{display:flex;justify-content:flex-end;margin-top:16px}
.cd-quiz__foot .cd-btn{width:auto;min-width:170px}
.cd-quiz__empty{padding:48px 24px;text-align:center;color:var(--cd-muted)}
.cd-quiz__empty svg{width:42px;height:42px;color:var(--cd-gold-d)}
.cd-quiz__empty h4{margin:12px 0 4px;color:var(--cd-ink);font-size:17px}
.cd-quiz__empty p{margin:0}

.cd-result{text-align:center;padding:8px 0}
.cd-ring{position:relative;width:140px;height:140px;margin:0 auto 14px}
.cd-ring svg{width:100%;height:100%;transform:rotate(-90deg)}
.cd-ring .track{fill:none;stroke:#e8eaf0;stroke-width:10}
.cd-ring .bar{fill:none;stroke:var(--cd-gold);stroke-width:10;stroke-linecap:round;stroke-dasharray:327;stroke-dashoffset:327;transition:stroke-dashoffset 1s cubic-bezier(.2,.8,.2,1)}
.cd-ring.is-good .bar{stroke:var(--cd-ok)}
.cd-ring.is-low .bar{stroke:var(--cd-no)}
.cd-ring__num{position:absolute;inset:0;display:grid;place-items:center;font-size:30px;font-weight:800}
.cd-result h4{margin:0 0 4px;font-size:22px;font-weight:800}
.cd-result p{margin:0 0 18px;color:var(--cd-muted)}
.cd-result__actions{display:flex;flex-wrap:wrap;gap:10px;justify-content:center}
.cd-review{margin-top:22px;display:grid;gap:10px;text-align:left}
.cd-review[hidden]{display:none}
.cd-review__item{display:grid;gap:3px;padding:12px 14px;border-radius:12px;border:1px solid var(--cd-line);background:#fafbfd;font-size:14px;animation:cdIn .3s both}
.cd-review__item.ok{border-color:#b7e4c9;background:var(--cd-ok-bg)}
.cd-review__item.no{border-color:#f5c2bd;background:var(--cd-no-bg)}
.cd-review__item .good{color:#0b6b36}
.cd-review__item .bad{color:#a3231a}

@media (max-width:640px){
    .cd-quiz{padding:18px 14px 22px}
    .cd-quiz__q{font-size:17px}
    .cd-quiz__foot .cd-btn{width:100%}
}

@media (prefers-reduced-motion:reduce){
    .cd *,.cd *::before,.cd *::after{animation-duration:.01ms !important;transition-duration:.01ms !important}
}
</style>

<main class="cd">

    {{-- ===================== Hero ===================== --}}
    <section class="cd-hero {{ $isEnrolled ? '' : 'cd-hero--wide' }}">
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

        <div class="cd-grid {{ $isEnrolled ? 'cd-grid--full' : '' }}">

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
                                                @if ($kind === 'quiz' && $canAccessLesson) data-quiz="{{ json_encode($quizData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" data-qcount="{{ $quizCount }}" @if (\Illuminate\Support\Facades\Route::has('courses.quizzes.submit')) data-quiz-submit="{{ route('courses.quizzes.submit', [(int) $course->id, (int) $attr($lesson, 'id', 0)]) }}" @endif @endif
                                                @if (\Illuminate\Support\Facades\Route::has('user.certificates.index')) data-certificates-url="{{ route('user.certificates.index') }}" @endif
                                            >
                                                <span class="cd-lesson__icon">{!! $canAccessLesson ? $icons[$kindIcon] : $icons['lock'] !!}</span>
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
                        <div class="cd-inst__avatar" aria-hidden="true">{{ mb_strtoupper(mb_substr($instructorName, 0, 1)) }}</div>
                        <div>
                            <div class="cd-inst__name">{{ $instructorName }}</div>
                            <div class="cd-inst__role">Course instructor</div>
                            @if ($instructorBio !== '')
                                <p class="cd-inst__bio">{{ $instructorBio }}</p>
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
                                <span class="cd-price__now">₹{{ number_format($price, 0) }}</span>
                                @if ($hasDiscount)
                                    <span class="cd-price__old">₹{{ number_format((float) $course->price, 0) }}</span>
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
                <strong>₹{{ number_format($price, 0) }}</strong>
                @if ($hasDiscount)<s>₹{{ number_format((float) $course->price, 0) }}</s>@endif
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

                <div class="cd-progress" id="cdLessonProgress" hidden>
                    <div class="cd-progress__row"><span>Lesson progress</span><strong id="cdProgressText">0%</strong></div>
                    <div class="cd-progress__track"><div class="cd-progress__fill" id="cdProgressFill"></div></div>
                    <small class="cd-progress__time" id="cdProgressTime"></small>
                </div>

                <div class="cd-dialog__info" id="cdModalInfo"></div>
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
    var accessibleTotal = lessons.filter(function (b) { return b.dataset.locked !== '1'; }).length;
    var current = -1;
    var lastFocus = null;
    var closeTimer = null;

    function isLocked(i) { return !!lessons[i] && lessons[i].dataset.locked === '1'; }

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

    /* ---------- Persistent lesson progress ---------- */
    var activeProgress = null;       // {lessonId, showUrl, saveUrl, token, completed, disabled}
    var progressTimer = null;
    var youtubePlayer = null;
    var youtubePlayerReady = false;
    var vimeoPlayer = null;
    var activeVideo = null;
    var vimeoApiPromise = null;
    var progressToken = 0;
    var youtubeApiState = 'idle';
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
    function progressHeaders() {
        var meta = document.querySelector('meta[name="csrf-token"]');
        return {
            'Content-Type': 'application/json',
            'Accept': 'application/json',
            'X-CSRF-TOKEN': (meta && meta.content) || (lessons[current] ? lessons[current].dataset.csrf : '')
        };
    }
    function hideProgress() { if (progressShow) progressShow.hidden = true; }
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
                console.warn('Could not fetch lesson progress:', url, error);
                return null;
            });
    }



    /* Shows the certificate result: a "View Certificate" button once issued. */
    function showCertificateNotice(status, message, certUrl, fallbackUrl) {
        if (['issued', 'already_issued', 'incomplete', 'error'].indexOf(status) === -1) return;
        var host = progressShow && !progressShow.hidden ? progressShow : info;
        if (!host) return;
        var old = host.querySelector('.cd-cert-note');
        if (old) old.remove();
        var ok = status === 'issued' || status === 'already_issued';
        var note = document.createElement('div');
        note.className = 'cd-cert-note' + (status === 'error' ? ' cd-cert-note--err' : (status === 'incomplete' ? ' cd-cert-note--info' : ''));
        var text = document.createElement('span');
        text.textContent = status === 'issued'
            ? '\uD83C\uDFC6 Congratulations! Your certificate is ready.'
            : (status === 'already_issued' ? '\uD83C\uDFC6 Your certificate is ready.' : (message || ''));
        note.appendChild(text);
        var href = certUrl || (ok ? fallbackUrl : '');
        if (ok && href) {
            var a = document.createElement('a');
            a.href = href;
            a.className = 'cd-cert-btn';
            a.textContent = 'View Certificate';
            note.appendChild(a);
        }
        host.appendChild(note);
    }
    function certificatesUrl() {
        return (lessons[current] && lessons[current].dataset.certificatesUrl) || '';
    }

function saveProgress(position, duration, completed) {
    if (
        !activeProgress ||
        !activeProgress.saveUrl ||
        !activeProgress.lessonId ||
        activeProgress.disabled
    ) {
        return Promise.resolve(null);
    }

    position = Math.max(0, Math.floor(Number(position) || 0));
    duration = Math.max(0, Math.floor(Number(duration) || 0));

    if (duration <= 0) {
        return Promise.resolve(null);
    }

    position = Math.min(position, duration);

    // Capture these before the asynchronous request starts.
    var token = activeProgress.token;
    var url = activeProgress.saveUrl;

    return fetch(url, {
        method: 'POST',
        headers: progressHeaders(),
        body: JSON.stringify({
            position: position,
            duration: duration
        }),
        credentials: 'same-origin',
        keepalive: true
    })
    .then(function (response) {
        return response.json().catch(function () {
            return {};
        }).then(function (data) {
            if (!response.ok) {
                console.warn(
                    'Progress save failed:',
                    response.status,
                    data
                );
                return null;
            }

            // Do not update a different lesson after navigation.
            if (!isCurrentProgress(token)) {
                return data;
            }

            activeProgress.completed = !!data.completed;
            showCertificateNotice(data.certificate_status, data.certificate_message, data.certificate_url, certificatesUrl());

            // Documents have no playback, so no progress bar for them.
            if (!lessons[current] || lessons[current].dataset.kind !== 'document') {
                updateProgressUI(
                    Number(data.position) || position,
                    Number(data.duration) || duration,
                    !!data.completed
                );
            }

            return data;
        });
    })
    .catch(function (error) {
        console.warn('Progress request error:', url, error);
        return null;
    });
}



    function flushProgress() {
        if (!activeProgress) return;
        try {
            if (youtubePlayer && youtubePlayerReady && typeof youtubePlayer.getCurrentTime === 'function') {
                saveProgress(youtubePlayer.getCurrentTime(), youtubePlayer.getDuration(), false);
            } else if (activeVideo && activeVideo.ready && activeVideo.el.duration) {
                saveProgress(activeVideo.el.currentTime, activeVideo.el.duration, false);
            }
        } catch (e) {
            console.warn('Could not save progress before leaving the lesson:', e);
        }
    }

    function stopProgress(shouldSave) {
        if (shouldSave) flushProgress();
        clearInterval(progressTimer);
        progressTimer = null;
        if (youtubePlayer) {
            try { youtubePlayer.destroy(); } catch (e) { /* ignore */ }
        }
        if (vimeoPlayer) {
            try { vimeoPlayer.destroy(); } catch (e) { /* ignore */ }
        }
        youtubePlayer = null;
        youtubePlayerReady = false;
        vimeoPlayer = null;
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

        hideProgress();

        return fetchProgress().then(function (saved) {
            if (!isCurrentProgress(token)) return null;
            if (!saved) {
                // Guests / failed request: never overwrite unknown progress, hide the panel quietly.
                activeProgress.disabled = true;
                hideProgress();
                return null;
            }
            activeProgress.completed = saved.completed;
            return saved;
        });
    }

    function loadYouTubeApi(callback) {
        if (window.YT && window.YT.Player) { callback(); return; }

        youtubeApiWaiters.push(callback);
        if (youtubeApiState === 'loading') return;
        youtubeApiState = 'loading';

        var previousReady = window.onYouTubeIframeAPIReady;
        window.onYouTubeIframeAPIReady = function () {
            if (typeof previousReady === 'function') {
                try { previousReady(); } catch (e) { /* ignore */ }
            }
            youtubeApiState = 'idle';
            youtubeApiWaiters.splice(0).forEach(function (fn) {
                try { fn(); } catch (e) { console.warn('YouTube API callback failed:', e); }
            });
        };

        var script = document.createElement('script');
        script.src = 'https://www.youtube.com/iframe_api';
        script.async = true;
        script.onerror = function () {
            youtubeApiState = 'idle';
            youtubeApiWaiters.length = 0;
            console.warn('Failed to load the YouTube IFrame API.');
        };
        document.head.appendChild(script);
    }


function startYouTubeProgress(iframe, saved, token) {
    loadYouTubeApi(function () {
        if (!isCurrentProgress(token) || !iframe || !iframe.isConnected) {
            return;
        }

        try {
            youtubePlayer = new YT.Player(iframe, {
                events: {
                    onReady: function (event) {
                        if (!isCurrentProgress(token)) return;

                        var player = event.target;
                        var duration = Math.floor(player.getDuration() || 0)
                            || saved.duration
                            || 0;

                        var position = Number(saved.position) || 0;

                        // Track the furthest position actually watched.
                        var maxAllowedTime = 0;
                        var isRestoringPosition = false;
                        var lastKnownTime = 0;

                        // Restore saved progress.
                        if (
                            position > 0 &&
                            (duration <= 0 || position < duration - 2)
                        ) {
                            isRestoringPosition = true;
                            maxAllowedTime = position;
                            lastKnownTime = position;

                            player.seekTo(position, true);

                            setTimeout(function () {
                                isRestoringPosition = false;
                            }, 1000);
                        } else {
                            position = 0;
                        }

                        youtubePlayerReady = true;

                        updateProgressUI(
                            position,
                            duration,
                            saved.completed
                        );

                        // Remove any previous progress timer.
                        clearInterval(progressTimer);

                        // Monitor playback and prevent forward seeking.
                        progressTimer = setInterval(function () {
                            if (
                                !isCurrentProgress(token) ||
                                !youtubePlayer ||
                                !youtubePlayerReady
                            ) {
                                return;
                            }

                            var state = player.getPlayerState();
                            var currentTime = player.getCurrentTime();
                            var videoDuration = player.getDuration();

                            if (!videoDuration || !isFinite(currentTime)) {
                                return;
                            }

                            if (isRestoringPosition) {
                                lastKnownTime = currentTime;
                                return;
                            }

                            if (state === YT.PlayerState.PLAYING) {
                                // If the position jumps forward beyond
                                // normal playback, return to watched time.
                                if (currentTime > maxAllowedTime + 2) {
                                    player.seekTo(maxAllowedTime, true);
                                    lastKnownTime = maxAllowedTime;
                                    return;
                                }

                                // Update watched position only during playback.
                                if (currentTime > maxAllowedTime) {
                                    maxAllowedTime = currentTime;
                                }

                                lastKnownTime = currentTime;

                                saveProgress(
                                    maxAllowedTime,
                                    videoDuration,
                                    false
                                );

                                updateProgressUI(
                                    currentTime,
                                    videoDuration,
                                    saved.completed
                                );
                            } else {
                                // Detect a forward seek while paused too.
                                if (currentTime > maxAllowedTime + 2) {
                                    player.seekTo(maxAllowedTime, true);
                                    lastKnownTime = maxAllowedTime;
                                } else {
                                    lastKnownTime = currentTime;
                                }
                            }
                        }, 1000);
                    },

                    onStateChange: function (event) {
                        if (
                            !isCurrentProgress(token) ||
                            !youtubePlayerReady
                        ) {
                            return;
                        }

                        var player = event.target;
                        var duration = player.getDuration();
                        var currentTime = player.getCurrentTime();

                        if (event.data === YT.PlayerState.ENDED) {
                            // The video must reach its actual end.
                            saveProgress(duration, duration, true);
                        } else if (event.data === YT.PlayerState.PAUSED) {
                            saveProgress(
                                currentTime,
                                duration,
                                false
                            );
                        }
                    },

                    onError: function (event) {
                        console.warn(
                            'YouTube player error code:',
                            event.data
                        );
                    }
                }
            });
        } catch (error) {
            console.warn(
                'YouTube player initialization failed:',
                error
            );
        }
    });
}


function renderVideo(url, vtype) {
    screen.replaceChildren();
    screen.className = 'cd-screen';
    hideProgress();

    if (!url) {
        setNote('No video has been added for this lesson yet.', true);
        return;
    }

    var type = guessVideoType(vtype, url);
    var src = '';

    // ==========================================
    // YOUTUBE VIDEO
    // ==========================================
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
            'accelerometer; autoplay; clipboard-write; encrypted-media; ' +
            'gyroscope; picture-in-picture; fullscreen; web-share';
        iframe.allowFullscreen = true;

        iframe.addEventListener('load', function () {
            var loader = screen.querySelector('.cd-loader');
            if (loader) loader.remove();
        });

        screen.appendChild(iframe);

        var ytPending = startProgress(window.currentLessonData || {});
        var ytToken = activeProgress ? activeProgress.token : 0;

        ytPending.then(function (saved) {
            if (!isCurrentProgress(ytToken)) return;
            if (!saved) return;

            startYouTubeProgress(iframe, saved, ytToken);
        }).catch(function (error) {
            console.warn(
                'Could not start YouTube progress tracking:',
                error
            );
        });

        return;
    }

    // ==========================================
    // VIMEO VIDEO
    // ==========================================
    if (type === 'vimeo') {
        src = vimeoEmbed(url);

        if (!src) {
            setNote('This Vimeo video URL is not valid.', true);
            return;
        }

        var vimeoFrame = document.createElement('iframe');
        vimeoFrame.src = src;
        vimeoFrame.title = 'Lesson video player';
        vimeoFrame.allow =
            'autoplay; fullscreen; picture-in-picture';
        vimeoFrame.allowFullscreen = true;

        screen.appendChild(vimeoFrame);

        // Track Vimeo playback through the official Vimeo Player SDK.
        // The server still decides completion from plausible watched intervals.
        var vmPending = startProgress(window.currentLessonData || {});
        var vmToken = activeProgress ? activeProgress.token : 0;

        function loadVimeoApi() {
            if (window.Vimeo && window.Vimeo.Player) {
                return Promise.resolve();
            }
            if (vimeoApiPromise) return vimeoApiPromise;

            vimeoApiPromise = new Promise(function (resolve, reject) {
                var existing = document.querySelector('script[data-vimeo-player-api]');
                if (existing) {
                    existing.addEventListener('load', resolve, { once: true });
                    existing.addEventListener('error', reject, { once: true });
                    return;
                }
                var script = document.createElement('script');
                script.src = 'https://player.vimeo.com/api/player.js';
                script.async = true;
                script.dataset.vimeoPlayerApi = '1';
                script.onload = resolve;
                script.onerror = function () {
                    vimeoApiPromise = null;
                    reject(new Error('Failed to load Vimeo Player API'));
                };
                document.head.appendChild(script);
            });
            return vimeoApiPromise;
        }

        vmPending.then(function (saved) {
            if (!isCurrentProgress(vmToken) || !saved || !vimeoFrame.isConnected) return;

            return loadVimeoApi().then(function () {
                if (!isCurrentProgress(vmToken) || !vimeoFrame.isConnected || !window.Vimeo) return;

                var player = new window.Vimeo.Player(vimeoFrame);
                vimeoPlayer = player;
                var maxAllowedTime = Math.max(0, Number(saved.position) || 0);
                var restoring = false;
                var lastSaveAt = 0;

                return player.ready().then(function () {
                    if (!isCurrentProgress(vmToken)) return;
                    return Promise.all([player.getDuration(), player.getCurrentTime()]).then(function (values) {
                        var duration = Number(values[0]) || Number(saved.duration) || 0;
                        if (saved.position > 0 && duration > 0 && saved.position < duration - 2) {
                            restoring = true;
                            return player.setCurrentTime(saved.position).catch(function () {}).then(function () {
                                maxAllowedTime = Number(saved.position) || 0;
                                setTimeout(function () { restoring = false; }, 500);
                                updateProgressUI(maxAllowedTime, duration, saved.completed);
                            });
                        }
                        maxAllowedTime = 0;
                        updateProgressUI(0, duration, saved.completed);
                    });
                }).then(function () {
                    if (!isCurrentProgress(vmToken)) return;

                    player.on('seeking', function (event) {
                        if (!isCurrentProgress(vmToken) || restoring) return;
                        var target = Number(event && event.seconds) || 0;
                        if (target > maxAllowedTime + 2) {
                            restoring = true;
                            player.setCurrentTime(maxAllowedTime).catch(function () {}).finally(function () {
                                setTimeout(function () { restoring = false; }, 250);
                            });
                        }
                    });

                    player.on('timeupdate', function (event) {
                        if (!isCurrentProgress(vmToken) || restoring) return;
                        var position = Number(event && event.seconds) || 0;
                        var duration = Number(event && event.duration) || 0;
                        if (position > maxAllowedTime && position <= maxAllowedTime + 2) {
                            maxAllowedTime = position;
                        }
                        updateProgressUI(Math.min(position, maxAllowedTime), duration, activeProgress.completed);
                        var nowMs = Date.now();
                        if (nowMs - lastSaveAt >= 1000) {
                            lastSaveAt = nowMs;
                            player.getPaused().then(function (paused) {
                                if (!paused && isCurrentProgress(vmToken)) {
                                    saveProgress(Math.min(position, maxAllowedTime), duration, false);
                                }
                            }).catch(function () {});
                        }
                    });

                    player.on('pause', function (event) {
                        if (!isCurrentProgress(vmToken)) return;
                        saveProgress(Math.min(Number(event.seconds) || 0, maxAllowedTime), Number(event.duration) || 0, false);
                    });

                    player.on('ended', function (event) {
                        if (!isCurrentProgress(vmToken)) return;
                        var duration = Number(event.duration) || 0;
                        saveProgress(duration, duration, true);
                    });
                });
            });
        }).catch(function (error) {
            console.warn('Could not start Vimeo progress tracking:', error);
        });

        return;
    }

    // ==========================================
    // UPLOADED / LOCAL VIDEO
    // ==========================================
    var video = document.createElement('video');

    video.src = url;
    video.controls = true;
    video.playsInline = true;
    video.preload = 'metadata';

    // Highest position the learner has actually played to.
    var maxAllowedTime = 0;

    // Allow seeking temporarily while restoring saved progress.
    var isRestoringPosition = false;

    // Prevent the same video from being processed after navigation.
    var vState = {
        el: video,
        ready: false
    };

    video.addEventListener('error', function () {
        setNote('This video could not be loaded.', true);
    });

    screen.appendChild(video);

    var vPending = startProgress(window.currentLessonData || {});
    var vToken = activeProgress ? activeProgress.token : 0;

    activeVideo = vState;

    function tryPlay() {
        var p = video.play();

        if (p && p.catch) {
            p.catch(function () {
                // Browser may block autoplay.
            });
        }
    }

    // ------------------------------------------
    // Block forward seeking
    // ------------------------------------------
    video.addEventListener('seeking', function () {
        if (!vState.ready || isRestoringPosition) return;

        // Allow returning to an earlier position, but don't
        // allow jumping ahead beyond the watched position.
        if (video.currentTime > maxAllowedTime + 0.5) {
            video.currentTime = maxAllowedTime;
        }
    });

    // ------------------------------------------
    // Restore previously saved progress
    // ------------------------------------------
    vPending.then(function (saved) {
        if (!isCurrentProgress(vToken) || !video.isConnected) {
            return;
        }

        if (!saved) {
            vState.ready = true;
            tryPlay();
            return;
        }

        function applyResume() {
            if (!isCurrentProgress(vToken) || !video.isConnected) {
                return;
            }

            var duration = video.duration;
            var position = Number(saved.position) || 0;

            if (
                position > 0 &&
                isFinite(duration) &&
                position < duration - 2
            ) {
                // Permit a legitimate saved position to be restored.
                isRestoringPosition = true;
                video.currentTime = position;
                maxAllowedTime = position;

                // Seeking events are dispatched asynchronously.
                setTimeout(function () {
                    isRestoringPosition = false;
                }, 0);
            } else {
                position = 0;
                maxAllowedTime = 0;
            }

            vState.ready = true;

            updateProgressUI(
                position,
                isFinite(duration) ? duration : 0,
                saved.completed
            );

            tryPlay();
        }

        if (video.readyState >= 1) {
            applyResume();
        } else {
            video.addEventListener('loadedmetadata', applyResume, {
                once: true
            });
        }
    }).catch(function (error) {
        console.warn(
            'Could not restore uploaded video progress:',
            error
        );

        if (isCurrentProgress(vToken) && video.isConnected) {
            vState.ready = true;
            tryPlay();
        }
    });

    // ------------------------------------------
    // Update progress while the video plays
    // ------------------------------------------
    video.addEventListener('timeupdate', function () {
        if (
            !vState.ready ||
            !isCurrentProgress(vToken) ||
            !video.duration
        ) {
            return;
        }

        // Update the watched position only during normal playback.
        if (
            !video.seeking &&
            !video.paused &&
            video.currentTime > maxAllowedTime
        ) {
            maxAllowedTime = video.currentTime;
        }

        updateProgressUI(
            video.currentTime,
            video.duration,
            activeProgress ? activeProgress.completed : false
        );
    });

    // ------------------------------------------
    // Save progress when paused
    // ------------------------------------------
    video.addEventListener('pause', function () {
        if (
            vState.ready &&
            isCurrentProgress(vToken) &&
            video.duration &&
            !video.ended
        ) {
            saveProgress(
                Math.min(video.currentTime, maxAllowedTime),
                video.duration,
                false
            );
        }
    });

    // ------------------------------------------
    // Mark completed when the video ends
    // ------------------------------------------
    video.addEventListener('ended', function () {
        if (
            vState.ready &&
            isCurrentProgress(vToken)
        ) {
            saveProgress(
                video.duration,
                video.duration,
                true
            );
        }
    });

    // ------------------------------------------
    // Automatically save progress every 5 seconds
    // ------------------------------------------
    progressTimer = setInterval(function () {
        if (
            vState.ready &&
            isCurrentProgress(vToken) &&
            !video.paused &&
            video.duration
        ) {
            saveProgress(
                Math.min(video.currentTime, maxAllowedTime),
                video.duration,
                false
            );
        }
    }, 5000);
}


    function renderDocument(data) {
        screen.replaceChildren();
        screen.className = 'cd-screen cd-screen--document';

        if (!data.attachment) {
            setNote('No document has been attached to this lesson yet.', true);
            screen.firstChild.style.color = '#667085';
            return;
        }

        screen.appendChild(el('div', 'cd-loader'));

        var iframe = document.createElement('iframe');
        iframe.src = data.attachment;
        iframe.title = data.attname || data.title || 'Course document';
        iframe.setAttribute('allowfullscreen', 'true');
        iframe.addEventListener('load', function () {
            var loader = screen.querySelector('.cd-loader');
            if (loader) loader.remove();
        });
        screen.appendChild(iframe);

        // Opening a document counts as completing it; the server records it
        // and re-checks certificate eligibility.
        var docPending = startProgress(window.currentLessonData || {});
        var docToken = activeProgress ? activeProgress.token : 0;
        docPending.then(function (saved) {
            if (!saved || !isCurrentProgress(docToken)) return;
            saveProgress(1, 1, true);
        });
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

    function isMulti(q) { return q.type === 'multiple_choice' && Array.isArray(q.a); }
    function hasKey(q) { return isMulti(q) ? q.a.length > 0 : (typeof q.a === 'number' && q.a >= 0); }
    function keyList(q) { return !hasKey(q) ? [] : (isMulti(q) ? q.a : [q.a]); }
    function isCorrect(q, picked) {
        if (!hasKey(q) || picked === undefined) return false;
        if (isMulti(q)) {
            var p = (picked || []).slice().sort(function (a, b) { return a - b; });
            return p.length === q.a.length && p.every(function (v, i) { return v === q.a[i]; });
        }
        return picked === q.a;
    }
    function answerText(q, picked) {
        if (picked === undefined) return 'Not answered';
        if (Array.isArray(picked)) return picked.length ? picked.map(function (i) { return q.opts[i]; }).join(', ') : 'Not answered';
        return q.opts[picked];
    }
    function correctText(q) { return keyList(q).map(function (i) { return q.opts[i]; }).join(', '); }

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
        var serverSubmitted = false;

        /* Sends the learner's answers to the server (first completion only).
           The server scores them itself, creates the QuizAttempt and runs the
           certificate check. Local retries stay client-side. */
        function submitToServer(host) {
            var url = btn.dataset.quizSubmit;
            if (!url || serverSubmitted) return;
            serverSubmitted = true;

            var answers = {};
            questions.forEach(function (q, i) {
                var p = st.picked[i];
                if (q.id && p !== undefined) answers[String(q.id)] = p;
            });
            if (!Object.keys(answers).length) { serverSubmitted = false; return; }

            fetch(url, {
                method: 'POST',
                headers: progressHeaders(),
                credentials: 'same-origin',
                keepalive: true,
                body: JSON.stringify({ answers: answers })
            }).then(function (r) {
                return r.json().catch(function () { return {}; }).then(function (d) {
                    // 409 = already submitted earlier; still carries the certificate status.
                    if (!r.ok && r.status !== 409) {
                        console.warn('Quiz submit failed:', r.status, d);
                        serverSubmitted = false;
                        return;
                    }
                    var status = d.certificate_status;
                    showCertificateNotice(status, d.certificate_message, d.certificate_url, btn.dataset.certificatesUrl || '');
                });
            }).catch(function (e) {
                console.warn('Quiz submit error:', e);
                serverSubmitted = false;
            });
        }

        function start() {
            st = { i: 0, score: 0, scored: 0, picked: [] };
            showQuestion();
        }

        function showQuestion() {
            var q = questions[st.i];
            var multi = isMulti(q);
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

            if (multi) wrap.appendChild(el('p', 'cd-quiz__hint', 'Select all that apply, then submit your answer.'));

            var list = el('div', 'cd-quiz__opts');
            list.setAttribute('role', 'group');
            list.setAttribute('aria-label', 'Answer options');
            var fb = el('div');
            var foot = el('div', 'cd-quiz__foot');
            var optBtns = [];
            var sel = [];
            var submit = null;

            q.opts.forEach(function (text, idx) {
                var b = el('button', 'cd-opt');
                b.type = 'button';
                b.style.animationDelay = (idx * 60 + 80) + 'ms';
                var key = el('span', 'cd-opt__key', String.fromCharCode(65 + idx));
                var label = el('span', 'cd-opt__text', text);
                var mark = el('span', 'cd-opt__mark');
                mark.innerHTML = ICON.check;
                b.appendChild(key); b.appendChild(label); b.appendChild(mark);
                b.addEventListener('click', function () {
                    if (st.picked[st.i] !== undefined) return;
                    if (!multi) { finish(idx); return; }
                    var at = sel.indexOf(idx);
                    if (at === -1) sel.push(idx); else sel.splice(at, 1);
                    b.classList.toggle('is-selected', at === -1);
                    if (submit) submit.disabled = !sel.length;
                });
                list.appendChild(b);
                optBtns.push(b);
            });
            wrap.appendChild(list);
            wrap.appendChild(fb);
            wrap.appendChild(foot);

            if (multi) {
                submit = el('button', 'cd-btn cd-btn--gold');
                submit.type = 'button';
                submit.disabled = true;
                submit.innerHTML = '<span>Submit answer</span>' + ICON.check;
                submit.addEventListener('click', function () {
                    if (!sel.length) return;
                    finish(sel.slice().sort(function (a, b) { return a - b; }));
                });
                foot.appendChild(submit);
            }

            function finish(picked) {
                if (st.picked[st.i] !== undefined) return;
                st.picked[st.i] = picked;

                var pickedArr = Array.isArray(picked) ? picked : [picked];
                var keys = keyList(q);

                optBtns.forEach(function (b, i) {
                    b.disabled = true;
                    b.classList.remove('is-selected');
                    if (keys.length) {
                        if (keys.indexOf(i) !== -1) b.classList.add('is-correct');
                        else if (pickedArr.indexOf(i) !== -1) { b.classList.add('is-wrong'); b.querySelector('.cd-opt__mark').innerHTML = ICON.close; }
                        else b.classList.add('is-dim');
                    } else {
                        b.classList.add(pickedArr.indexOf(i) !== -1 ? 'is-picked' : 'is-dim');
                    }
                });

                if (submit) { submit.remove(); submit = null; }

                var box;
                if (keys.length) {
                    st.scored++;
                    if (isCorrect(q, picked)) {
                        st.score++;
                        sc.textContent = 'Score: ' + st.score;
                        box = el('div', 'cd-quiz__fb cd-quiz__fb--ok');
                        box.appendChild(el('strong', '', 'Correct!'));
                    } else {
                        box = el('div', 'cd-quiz__fb cd-quiz__fb--no');
                        box.appendChild(el('strong', '', 'Not quite.'));
                        box.appendChild(document.createTextNode('Correct answer: ' + correctText(q)));
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
                go.addEventListener('click', function () {
                    if (last) showResult();
                    else { st.i++; showQuestion(); scroller.scrollTop = 0; }
                });
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
            submitToServer();

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
                var keyed = hasKey(q);
                var ok = isCorrect(q, chosen);
                var item = el('div', 'cd-review__item ' + (!keyed ? '' : (ok ? 'ok' : 'no')));
                item.style.animationDelay = (i * 40) + 'ms';
                item.appendChild(el('strong', '', (i + 1) + '. ' + q.q));
                item.appendChild(el('span', !keyed ? '' : (ok ? 'good' : 'bad'), 'Your answer: ' + answerText(q, chosen)));
                if (keyed && !ok) item.appendChild(el('span', 'good', 'Correct answer: ' + correctText(q)));
                list.appendChild(item);
            });
            res.appendChild(list);
            review.addEventListener('click', function () {
                list.hidden = !list.hidden;
                if (!list.hidden) list.scrollIntoView({ block: 'nearest', behavior: reduceMotion ? 'auto' : 'smooth' });
            });

            var barEl = ring.querySelector('.bar');
            requestAnimationFrame(function () {
                requestAnimationFrame(function () { barEl.style.strokeDashoffset = String(327 * (1 - pct / 100)); });
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

        if (data.attachment) {
            var actions = el('div', 'cd-info-actions');

            if (kind === 'document') {
                var open = el('a', 'cd-btn cd-btn--outline cd-btn--sm');
                open.href = data.attachment; open.target = '_blank'; open.rel = 'noopener';
                open.innerHTML = ICON.external + ' ';
                open.appendChild(document.createTextNode('Open document'));
                actions.appendChild(open);
            }

            var a = el('a', 'cd-btn cd-btn--outline cd-btn--sm');
            a.href = data.attachment; a.target = '_blank'; a.rel = 'noopener';
            a.setAttribute('download', data.attname || '');
            a.innerHTML = ICON.download + ' ';
            a.appendChild(document.createTextNode((kind === 'document' ? 'Download' : 'Download resource') + (data.attname ? ' (' + data.attname + ')' : '')));
            actions.appendChild(a);

            info.appendChild(actions);
        }
    }

    function goToCourseCheckout(btn) {
        if (!btn || !btn.dataset.buyUrl) return;

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

    function updateNav(index) {
        var accessiblePos = 0;
        for (var i = 0; i <= index; i++) { if (!isLocked(i)) accessiblePos++; }

        count.textContent = 'Lesson ' + accessiblePos + ' of ' + accessibleTotal;
        prevBtn.disabled = index <= 0;

        var last = index >= lessons.length - 1;
        nextBtn.disabled = last;

        var label = nextBtn.querySelector('span');
        if (label) label.textContent = (!last && isLocked(index + 1)) ? 'Unlock course' : 'Next';
    }

    function render(index) {
        var btn = lessons[index];
        if (!btn) return;
        if (btn.dataset.locked === '1') {
            goToCourseCheckout(btn);
            return;
        }

        stopProgress(true);
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
        updateNav(index);

        window.currentLessonData = data;

        if (kind === 'video') {
            renderVideo(data.url, data.vtype);
        } else {
            hideProgress();
            if (kind === 'quiz') renderQuiz(btn);
            else renderDocument(data);
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
        stopProgress(true);
        if (modal.hidden) return;
        modal.classList.remove('is-open');
        screen.replaceChildren();
        hideProgress();
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

    prevBtn.addEventListener('click', function () {
        if (current > 0) render(current - 1);
    });
    nextBtn.addEventListener('click', function () {
        var n = current + 1;
        if (n >= lessons.length) return;
        if (isLocked(n)) { goToCourseCheckout(lessons[n]); return; }
        render(n);
    });

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