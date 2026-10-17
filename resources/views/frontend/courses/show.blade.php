@extends('layouts.frontend')

@section('title', $course->title ?? 'Course details')

@section('content')
@php
    /* ---------- Helpers ---------- */
    $ic = function (string $paths, bool $fill = false): string {
        return '<svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="' . ($fill ? 'currentColor' : 'none')
            . '" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">' . $paths . '</svg>';
    };

    $icons = [
        'check'    => $ic('<polyline points="20 6 9 17 4 12"/>'),
        'play'     => $ic('<polygon points="6 4 20 12 6 20 6 4"/>', true),
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


<style>

/* =========================================================

   Course detail — scoped, collision-free (all classes: cd-*)

   ========================================================= */

.cd {

    --cd-purple: var(--purple, #32105f);

    --cd-purple-2: var(--purple-light, #4a2677);

    --cd-dark: var(--purple-dark, #250b49);

    --cd-gold: var(--gold, #f5bd32);

    --cd-gold-d: var(--gold-dark, #d8a017);

    --cd-ink: #211735;

    --cd-text: #4a3e5c;

    --cd-muted: #6f6883;

    --cd-line: #e9e3f0;

    --cd-bg: #f8f5fd;

    --cd-card: #ffffff;

    --cd-green: #059669;

    --cd-sky: #0284c7;

    --cd-amber: #d97706;

    --cd-radius: 16px;

    --cd-shadow: 0 10px 30px rgba(50, 16, 95, .08);

    --cd-side: 380px;

    --cd-gap: 40px;

    --cd-pull: 220px;

    --cd-ease: cubic-bezier(.22, 1, .36, 1);

    background: var(--cd-bg);

    color: var(--cd-ink);

    padding-bottom: 80px;

    overflow-x: clip;

}

.cd *, .cd *::before, .cd *::after { box-sizing: border-box; }

.cd h1, .cd h2, .cd h3, .cd h4, .cd p, .cd ul { margin: 0; padding: 0; }

.cd ul { list-style: none; }

.cd a { color: inherit; text-decoration: none; }

.cd button { font-family: inherit; }

.cd svg { display: inline-block; flex-shrink: 0; width: 1em; height: 1em; vertical-align: middle; }

.cd :focus-visible { outline: 3px solid rgba(245, 189, 50, .8); outline-offset: 2px; border-radius: 6px; }

.cd-container { width: 100%; max-width: 1240px; margin-inline: auto; padding-inline: 24px; }

html.cd-lock { overflow: hidden; }



@keyframes cdRise { from { opacity: 0; transform: translateY(18px); } to { opacity: 1; transform: none; } }

@keyframes cdFadeIn { from { opacity: 0; } to { opacity: 1; } }

@keyframes cdSpin { to { transform: rotate(360deg); } }

@keyframes cdShake { 0%, 100% { transform: translateX(0); } 20% { transform: translateX(-7px); } 40% { transform: translateX(6px); } 60% { transform: translateX(-4px); } 80% { transform: translateX(3px); } }

@keyframes cdPop { 0% { transform: scale(.96); } 50% { transform: scale(1.02); } 100% { transform: scale(1); } }



/* ---------- Hero ---------- */

.cd-hero {

    position: relative; overflow: hidden; color: #fff; padding: 44px 0 96px;

    background:

        radial-gradient(700px 380px at 90% 0%, rgba(245, 189, 50, .16), transparent 60%),

        radial-gradient(600px 400px at 0% 100%, rgba(122, 79, 208, .4), transparent 60%),

        linear-gradient(130deg, var(--cd-dark) 0%, var(--cd-purple) 50%, var(--cd-purple-2) 100%);

}

.cd-hero::after { content: ""; position: absolute; width: 420px; height: 420px; right: -120px; top: -160px; border-radius: 50%; border: 1.5px solid rgba(255, 255, 255, .12); pointer-events: none; }

.cd-hero__copy { position: relative; z-index: 1; max-width: calc(100% - var(--cd-side) - var(--cd-gap)); min-width: 0; }

.cd-hero__copy > * { animation: cdRise .7s var(--cd-ease) both; }

.cd-hero__copy > *:nth-child(2) { animation-delay: .06s; }

.cd-hero__copy > *:nth-child(3) { animation-delay: .12s; }

.cd-hero__copy > *:nth-child(4) { animation-delay: .18s; }

.cd-hero__copy > *:nth-child(5) { animation-delay: .24s; }



.cd-crumbs { display: flex; flex-wrap: wrap; align-items: center; gap: 6px 8px; margin-bottom: 20px; font-size: .84rem; color: rgba(255, 255, 255, .72); }

.cd-crumbs a { color: #fff; opacity: .9; }

.cd-crumbs a:hover { color: var(--cd-gold); opacity: 1; }

.cd-crumbs .sep { opacity: .5; }

.cd-crumbs .current { overflow: hidden; text-overflow: ellipsis; white-space: nowrap; max-width: 100%; }



.cd-badges { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 16px; }

.cd-badge { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; font-size: .72rem; font-weight: 800; letter-spacing: .04em; text-transform: uppercase; background: rgba(255, 255, 255, .14); border: 1px solid rgba(255, 255, 255, .22); color: #fff; }

.cd-badge svg { width: 13px; height: 13px; }

.cd-badge--gold { background: var(--cd-gold); border-color: var(--cd-gold); color: var(--cd-dark); }



.cd-title { font-size: clamp(1.75rem, 3.6vw, 2.75rem); line-height: 1.15; font-weight: 800; letter-spacing: -.02em; color: #fff; overflow-wrap: anywhere; }

.cd-lead { margin-top: 16px; max-width: 680px; font-size: clamp(1rem, 1.4vw, 1.1rem); line-height: 1.7; color: rgba(255, 255, 255, .88); }



.cd-meta { display: flex; flex-wrap: wrap; gap: 10px 22px; margin-top: 24px; font-size: .92rem; color: rgba(255, 255, 255, .9); }

.cd-meta li { display: inline-flex; align-items: center; gap: 8px; min-width: 0; }

.cd-meta svg { width: 17px; height: 17px; color: var(--cd-gold); }

.cd-meta .rate svg { fill: var(--cd-gold); stroke: var(--cd-gold); }

.cd-meta strong { font-weight: 700; }



/* ---------- Body grid ---------- */

.cd-body { padding-top: 40px; }

.cd-grid { display: grid; grid-template-columns: minmax(0, 1fr) var(--cd-side); gap: var(--cd-gap); align-items: start; }

.cd-main { min-width: 0; display: grid; gap: 24px; }

.cd-side { min-width: 0; position: sticky; top: 96px; margin-top: calc(var(--cd-pull) * -1); z-index: 5; align-self: start; }



.cd-js .cd-reveal { opacity: 0; transform: translateY(22px); transition: opacity .6s var(--cd-ease), transform .6s var(--cd-ease); }

.cd-js .cd-reveal.is-in { opacity: 1; transform: none; }



/* ---------- Alerts ---------- */

.cd-alert { display: flex; gap: 12px; align-items: flex-start; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: .94rem; line-height: 1.5; border: 1px solid; animation: cdRise .5s var(--cd-ease) both; }

.cd-alert svg { width: 20px; height: 20px; margin-top: 1px; }

.cd-alert--success { background: #ecfdf5; border-color: #a7f3d0; color: #065f46; }

.cd-alert--error { background: #fef2f2; border-color: #fecaca; color: #991b1b; }

.cd-alert ul { display: grid; gap: 4px; }



/* ---------- Panels ---------- */

.cd-panel { background: var(--cd-card); border: 1px solid var(--cd-line); border-radius: var(--cd-radius); padding: 28px; box-shadow: var(--cd-shadow); min-width: 0; }

.cd-h2 { display: flex; align-items: center; gap: 12px; margin-bottom: 20px; font-size: clamp(1.2rem, 2vw, 1.5rem); line-height: 1.3; font-weight: 800; letter-spacing: -.01em; color: var(--cd-purple); }

.cd-h2::before { content: ""; width: 5px; height: 1.1em; border-radius: 4px; background: var(--cd-gold); flex-shrink: 0; }



.cd-checks { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 14px 28px; }

.cd-checks li { display: flex; align-items: flex-start; gap: 12px; font-size: .95rem; line-height: 1.6; color: var(--cd-text); }

.cd-checks .tick { width: 22px; height: 22px; margin-top: 1px; border-radius: 50%; display: grid; place-items: center; flex-shrink: 0; background: #fdf0c9; color: var(--cd-purple); }

.cd-checks .tick svg { width: 12px; height: 12px; stroke-width: 3.2; }

.cd-checks span:last-child { min-width: 0; overflow-wrap: anywhere; }

.cd-checks--single { grid-template-columns: 1fr; }



/* ---------- Curriculum ---------- */

.cd-cur__head { display: flex; align-items: center; justify-content: space-between; flex-wrap: wrap; gap: 12px 20px; margin-bottom: 18px; }

.cd-cur__head .cd-h2 { margin: 0; }

.cd-cur__stats { display: flex; align-items: center; flex-wrap: wrap; gap: 6px 16px; font-size: .88rem; color: var(--cd-muted); }

.cd-link-btn { border: 0; background: none; padding: 0; color: var(--cd-purple-2); font-weight: 700; font-size: .88rem; cursor: pointer; }

.cd-link-btn:hover { color: var(--cd-gold-d); text-decoration: underline; }



.cd-sections { display: grid; gap: 12px; }

.cd-sec { border: 1px solid var(--cd-line); border-radius: 12px; background: #fff; overflow: hidden; transition: border-color .25s, box-shadow .25s; }

.cd-sec[open] { border-color: #d9cbee; box-shadow: 0 6px 18px rgba(50, 16, 95, .06); }

.cd-sec__head { display: flex; align-items: center; gap: 12px; padding: 16px 18px; cursor: pointer; list-style: none; background: #f6f1fc; color: var(--cd-purple); font-weight: 700; line-height: 1.45; user-select: none; transition: *background* .2s; }

.cd-sec__head::-webkit-details-marker { display: none; }

.cd-sec__head:hover { background: #f0e8fa; }

.cd-sec__chev { width: 20px; height: 20px; transition: transform .3s var(--cd-ease); color: var(--cd-purple-2); }

.cd-sec[open]:not(.is-closing) .cd-sec__chev { transform: rotate(180deg); }

.cd-sec__name { flex: 1; min-width: 0; overflow-wrap: anywhere; }

.cd-sec__count { flex-shrink: 0; font-size: .8rem; font-weight: 600; color: var(--cd-muted); white-space: nowrap; }

.cd-sec__body { overflow: hidden; }



.cd-lesson { position: relative; width: 100%; display: grid; grid-template-columns: auto minmax(0, 1fr) auto; align-items: center; gap: 14px; padding: 13px 18px; border: 0; border-top: 1px solid #f0eaf6; background: #fff; text-align: left; font-size: .94rem; line-height: 1.45; color: var(--cd-text); cursor: pointer; transition: *background* .2s, color .2s, padding-left .25s var(--cd-ease); }

.cd-lesson::before { content: ""; position: absolute; left: 0; top: 0; bottom: 0; width: 3px; background: var(--cd-gold); transform: scaleY(0); transition: transform .25s var(--cd-ease); }

.cd-lesson:hover { background: #fcfaff; color: var(--cd-purple); padding-left: 22px; }

.cd-lesson:hover::before, .cd-lesson.is-active::before { transform: scaleY(1); }

.cd-lesson.is-active { background: #fff8e1; color: var(--cd-purple); }

.cd-lesson__icon { width: 34px; height: 34px; border-radius: 50%; display: grid; place-items: center; background: var(--cd-purple); color: #fff; transition: transform .25s var(--cd-ease), box-shadow .25s; }

.cd-lesson__icon svg { width: 14px; height: 14px; }

.cd-lesson[data-kind="video"] .cd-lesson__icon svg { margin-left: 2px; }

.cd-lesson[data-kind="document"] .cd-lesson__icon { background: var(--cd-sky); }

.cd-lesson[data-kind="quiz"] .cd-lesson__icon { background: var(--cd-amber); }

.cd-lesson[data-kind="quiz"] .cd-lesson__icon svg { width: 16px; height: 16px; }

.cd-lesson:hover .cd-lesson__icon, .cd-lesson.is-active .cd-lesson__icon { transform: scale(1.1); box-shadow: 0 6px 14px rgba(50, 16, 95, .22); }

.cd-lesson__text { min-width: 0; }

.cd-lesson__title { display: block; font-weight: 600; overflow-wrap: anywhere; }

.cd-lesson__sub { display: flex; align-items: center; flex-wrap: wrap; gap: 4px 12px; margin-top: 2px; font-size: .76rem; font-weight: 500; color: var(--cd-muted); }

.cd-lesson__sub span { display: inline-flex; align-items: center; gap: 5px; }

.cd-lesson__sub svg { width: 12px; height: 12px; }

.cd-lesson__time { display: inline-flex; align-items: center; gap: 6px; font-size: .8rem; color: var(--cd-muted); white-space: nowrap; }

.cd-lesson__time svg { width: 13px; height: 13px; }

.cd-lesson--empty { cursor: default; display: block; color: var(--cd-muted); }

.cd-lesson--empty:hover { background: #fff; color: var(--cd-muted); padding-left: 18px; }

.cd-empty { padding: 32px 22px; text-align: center; border: 2px dashed #d9cbee; border-radius: 12px; background: #fcfaff; color: var(--cd-muted); }



/* ---------- About / prose ---------- */

.cd-prose { font-size: 1rem; line-height: 1.85; color: var(--cd-text); overflow-wrap: anywhere; max-width: none; }

.cd-prose > *:first-child { margin-top: 0; }

.cd-prose > *:last-child { margin-bottom: 0; }

.cd-prose p { margin: 0 0 16px; }

.cd-prose h1, .cd-prose h2, .cd-prose h3, .cd-prose h4 { margin: 26px 0 12px; color: var(--cd-purple); line-height: 1.35; font-weight: 800; }

.cd-prose h2 { font-size: 1.35rem; } .cd-prose h3 { font-size: 1.15rem; } .cd-prose h4 { font-size: 1rem; }

.cd-prose ul, .cd-prose ol { margin: 0 0 18px; padding-left: 24px; }

.cd-prose ul { list-style: disc; }

.cd-prose li { margin-bottom: 8px; }

.cd-prose a { color: var(--cd-purple-2); text-decoration: underline; }

.cd-prose img, .cd-prose video, .cd-prose iframe { max-width: 100%; height: auto; border-radius: 10px; }

.cd-prose table { display: block; max-width: 100%; overflow-x: auto; border-collapse: collapse; margin-bottom: 18px; }

.cd-prose th, .cd-prose td { padding: 10px 14px; border: 1px solid var(--cd-line); }

.cd-prose pre { max-width: 100%; overflow-x: auto; padding: 14px 16px; border-radius: 10px; background: #1e1433; color: #f3ecff; font-size: .9rem; }

.cd-prose blockquote { margin: 0 0 18px; padding: 8px 18px; border-left: 4px solid var(--cd-gold); background: #fdf9ec; border-radius: 0 10px 10px 0; }



/* ---------- Instructor ---------- */

.cd-inst { display: flex; align-items: flex-start; gap: 18px; }

.cd-inst__avatar { width: 64px; height: 64px; border-radius: 50%; flex-shrink: 0; display: grid; place-items: center; font-size: 1.6rem; font-weight: 800; color: var(--cd-dark); background: linear-gradient(135deg, var(--cd-gold), #fde68a); box-shadow: 0 8px 18px rgba(216, 160, 23, .3); }

.cd-inst__name { font-size: 1.1rem; font-weight: 800; color: var(--cd-purple); }

.cd-inst__role { font-size: .85rem; color: var(--cd-muted); margin-top: 2px; }

.cd-inst__bio { margin-top: 10px; font-size: .94rem; line-height: 1.75; color: var(--cd-text); }



/* ---------- Screen (preview + modal) ---------- */

.cd-screen { position: relative; width: 100%; aspect-ratio: 16 / 9; background: #150826; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #e6dcf3; text-align: center; padding: 0; }

.cd-screen iframe, .cd-screen video { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; background: #000; display: block; }

.cd-screen img { width: 100%; height: 100%; object-fit: cover; }

.cd-screen__note { padding: 20px; font-size: .94rem; }



/* ---------- Purchase card ---------- */

.cd-card { background: #fff; border: 1px solid var(--cd-line); border-radius: 20px; overflow: hidden; box-shadow: 0 24px 54px rgba(37, 11, 73, .2); }

.cd-card__ph { display: grid; place-items: center; gap: 10px; color: #cbb9f0; font-size: .9rem; padding: 20px; }

.cd-card__ph svg { width: 44px; height: 44px; opacity: .8; }

.cd-card__body { padding: 24px; }

.cd-price { display: flex; align-items: baseline; flex-wrap: wrap; gap: 6px 12px; }

.cd-price__now { font-size: 2.1rem; line-height: 1.1; font-weight: 800; color: var(--cd-purple); font-family: "Plus Jakarta Sans", sans-serif; }

.cd-price__now\.is-free { color: var(--cd-green); }

.cd-price__old { font-size: 1.05rem; color: #a59cb4; text-decoration: line-through; }

.cd-price__off { display: inline-flex; padding: 3px 10px; border-radius: 999px; background: #ecfdf5; color: #047857; font-size: .76rem; font-weight: 800; }

.cd-actions { display: grid; gap: 12px; margin-top: 20px; }

.cd-actions form { margin: 0; }



.cd-btn { width: 100%; min-height: 52px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 12px 18px; border-radius: 12px; border: 2px solid transparent; font-size: .98rem; font-weight: 800; line-height: 1.2; cursor: pointer; text-align: center; transition: transform .2s ease, *background* .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease; }

.cd-btn svg { width: 19px; height: 19px; }

.cd-btn:hover { transform: translateY(-2px); }

.cd-btn[disabled] { opacity: .7; cursor: wait; transform: none; }

.cd-btn--gold { background: var(--cd-gold); color: var(--cd-dark); box-shadow: 0 10px 22px rgba(245, 189, 50, .35); }

.cd-btn--gold:hover { background: var(--cd-gold-d); }

.cd-btn--outline { background: #fff; color: var(--cd-purple); border-color: #cdbde6; }

.cd-btn--outline:hover { border-color: var(--cd-purple); background: #f8f5fd; }

.cd-btn--success { background: var(--cd-green); color: #fff; }

.cd-btn--success:hover { background: #047857; }

.cd-btn--sm { width: auto; min-height: 44px; padding: 10px 18px; font-size: .9rem; }



.cd-includes { margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--cd-line); }

.cd-includes h4 { font-size: .95rem; font-weight: 800; color: var(--cd-purple); margin-bottom: 14px; }

.cd-includes ul { display: grid; gap: 12px; }

.cd-includes li { display: flex; align-items: center; gap: 12px; font-size: .92rem; color: var(--cd-text); line-height: 1.4; }

.cd-includes li svg { width: 18px; height: 18px; color: var(--cd-gold-d); }



/* ---------- Mobile buy bar ---------- */

.cd-bar { display: none; }



/* =========================================================

   Lesson popup (modal)

   ========================================================= */

.cd-modal { position: fixed; inset: 0; z-index: 1000; display: flex; align-items: center; justify-content: center; padding: 16px; }

.cd-modal[hidden] { display: none; }

.cd-modal__backdrop { position: absolute; inset: 0; background: rgba(21, 8, 38, .72); backdrop-filter: blur(6px); -webkit-backdrop-filter: blur(6px); opacity: 0; transition: opacity .3s ease; }

.cd-modal.is-open .cd-modal__backdrop { opacity: 1; }



.cd-dialog {

    position: relative;

    width: min(900px, 100%);

    max-height: calc(100vh - 80px);

    max-height: calc(100dvh - 80px);

    display: flex;

    flex-direction: column;

    background: #fff;

    border-radius: 22px;

    overflow: hidden;

    outline: none;

    box-shadow: 0 40px 100px rgba(10, 2, 25, .55);

    opacity: 0;

    transform: translateY(28px) scale(.96);

    transition: opacity .3s ease, transform .4s var(--cd-ease);

}

.cd-modal.is-open .cd-dialog { opacity: 1; transform: none; }

.cd-dialog__grip { display: none; }



.cd-dialog__head { display: flex; align-items: center; justify-content: space-between; gap: 14px; padding: 16px 20px; border-bottom: 1px solid var(--cd-line); background: #fff; }

.cd-dialog__titles { min-width: 0; }

.cd-dialog__tag { display: inline-flex; align-items: center; gap: 6px; margin-bottom: 4px; font-size: .7rem; font-weight: 800; letter-spacing: .08em; text-transform: uppercase; color: var(--cd-gold-d); }

.cd-dialog__title { font-size: clamp(1rem, 2vw, 1.2rem); font-weight: 800; line-height: 1.35; color: var(--cd-purple); overflow-wrap: anywhere; }

.cd-dialog__close { width: 40px; height: 40px; flex-shrink: 0; display: grid; place-items: center; border: 0; border-radius: 50%; background: #f3edfa; color: var(--cd-purple); cursor: pointer; transition: *background* .2s, transform .25s var(--cd-ease); }

.cd-dialog__close:hover { background: #e8dcf7; transform: rotate(90deg); }

.cd-dialog__close svg { width: 18px; height: 18px; }



.cd-dialog__scroll { flex: 1; min-height: 0; overflow-y: auto; overscroll-behavior: contain; -webkit-overflow-scrolling: touch; }

.cd-dialog .cd-screen { max-height: min(66vh, 640px); }

.cd-screen--card { aspect-ratio: auto; min-height: 260px; max-height: none !important; padding: 36px 20px; background: linear-gradient(135deg, #2a1348, #46247a); }

.cd-doc { display: grid; justify-items: center; gap: 12px; max-width: 460px; animation: cdFadeIn .4s ease both; }

.cd-doc__icon { width: 76px; height: 76px; border-radius: 22px; display: grid; place-items: center; color: #fff; background: rgba(255, 255, 255, .14); border: 1px solid rgba(255, 255, 255, .22); }

.cd-doc__icon svg { width: 36px; height: 36px; }

.cd-doc h4 { font-size: 1.1rem; font-weight: 800; color: #fff; }

.cd-doc p { font-size: .92rem; line-height: 1.6; color: rgba(255, 255, 255, .8); overflow-wrap: anywhere; }

.cd-doc__actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 6px; }

.cd-doc .cd-btn--outline { background: rgba(255, 255, 255, .1); color: #fff; border-color: rgba(255, 255, 255, .4); }

.cd-doc .cd-btn--outline:hover { background: rgba(255, 255, 255, .2); border-color: #fff; }

.cd-loader { width: 38px; height: 38px; border-radius: 50%; border: 3px solid rgba(255, 255, 255, .2); border-top-color: var(--cd-gold); animation: cdSpin .8s linear infinite; }



.cd-dialog__info { padding: 20px; display: grid; gap: 16px; }

.cd-dialog__info:empty { display: none; }

.cd-chips { display: flex; flex-wrap: wrap; gap: 8px; }

.cd-chip { display: inline-flex; align-items: center; gap: 6px; padding: 5px 12px; border-radius: 999px; background: #f3edfa; color: var(--cd-purple); font-size: .8rem; font-weight: 700; }

.cd-chip svg { width: 14px; height: 14px; }

.cd-chip--best { background: #fff4d6; color: #8a5a00; }

.cd-dialog__summary h4 { font-size: .95rem; font-weight: 800; color: var(--cd-purple); margin-bottom: 6px; }

.cd-dialog__summary p { font-size: .95rem; line-height: 1.75; color: var(--cd-text); white-space: pre-line; overflow-wrap: anywhere; }



.cd-dialog__foot { display: grid; grid-template-columns: 1fr auto 1fr; align-items: center; gap: 12px; padding: 14px 20px; border-top: 1px solid var(--cd-line); background: #fbf9fe; }

.cd-nav { display: inline-flex; align-items: center; justify-self: start; gap: 8px; max-width: 100%; padding: 10px 16px; border-radius: 10px; border: 1.5px solid #d9cbee; background: #fff; color: var(--cd-purple); font-size: .88rem; font-weight: 700; cursor: pointer; transition: *background* .2s, border-color .2s, transform .2s, opacity .2s; }

.cd-nav svg { width: 16px; height: 16px; }

.cd-nav:hover:not([disabled]) { background: #f3edfa; border-color: var(--cd-purple-2); transform: translateY(-1px); }

.cd-nav[disabled] { opacity: .4; cursor: not-allowed; }

.cd-nav--next { justify-self: end; }

.cd-dialog__count { font-size: .8rem; font-weight: 600; color: var(--cd-muted); text-align: center; white-space: nowrap; }

/* Lesson popup layout fix */

.cd-modal .cd-dialog {

    width: min(960px, calc(100vw - 32px));

    height: min(82vh, 760px);

    height: min(82dvh, 760px);

    max-height: calc(100dvh - 32px);

    display: flex;

    flex-direction: column;

    overflow: hidden;

    background: #fff;

    border-radius: 22px;

}



.cd-dialog__head,

.cd-dialog__foot {

    flex-shrink: 0;

}



.cd-dialog__scroll {

    flex: 1 1 auto;

    min-height: 0;

    overflow-y: auto;

    overflow-x: hidden;

}



.cd-dialog .cd-screen {

    position: relative;

    width: 100%;

    height: auto;

    max-height: none;

    aspect-ratio: 16 / 9;

    padding: 0;

    overflow: hidden;

    background: #080808;

}



.cd-dialog .cd-screen iframe,

.cd-dialog .cd-screen video {

    position: absolute;

    inset: 0;

    width: 100%;

    height: 100%;

    display: block;

    border: 0;

}



.cd-dialog .cd-dialog__foot {

    display: grid;

    grid-template-columns: 1fr auto 1fr;

    align-items: center;

    gap: 12px;

    padding: 12px 20px;

    background: #fcfaff;

    border-top: 1px solid #eee5f5;

}



.cd-dialog #cdPrev {

    justify-self: start;

}



.cd-dialog #cdNext {

    justify-self: end;

}



.cd-dialog__count {

    text-align: center;

}



@media (max-width: 640px) {

    .cd-modal .cd-dialog {

        width: 100%;

        height: min(85dvh, 760px);

        max-height: 94dvh;

        border-radius: 22px 22px 0 0;

    }



    .cd-dialog .cd-dialog__foot {

        grid-template-columns: 1fr 1fr;

        padding: 12px 16px;

    }



    .cd-dialog .cd-dialog__count {

        grid-column: 1 / -1;

        grid-row: 1;

    }

}

/* =========================================================

   Quiz (inside popup)

   ========================================================= */

.cd-screen--quiz { display: block; aspect-ratio: auto; max-height: none !important; overflow: visible; text-align: left; padding: 24px; color: var(--cd-ink); background: linear-gradient(180deg, #fbf9fe, #f4eefb); }

.cd-quiz { max-width: 760px; margin-inline: auto; }

.cd-quiz__top { display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 10px; font-size: .82rem; font-weight: 700; color: var(--cd-muted); }

.cd-quiz__score { display: inline-flex; align-items: center; gap: 6px; padding: 3px 10px; border-radius: 999px; background: #fff; border: 1px solid var(--cd-line); color: var(--cd-purple); }

.cd-quiz__bar { height: 8px; border-radius: 99px; background: #e6dcf3; overflow: hidden; margin-bottom: 22px; }

.cd-quiz__bar span { display: block; height: 100%; width: 0; border-radius: inherit; background: linear-gradient(90deg, var(--cd-gold), #fde68a); transition: width .55s var(--cd-ease); }

.cd-quiz__q { font-size: clamp(1.05rem, 2.2vw, 1.3rem); font-weight: 800; line-height: 1.45; color: var(--cd-purple); margin-bottom: 18px; outline: none; overflow-wrap: anywhere; animation: cdRise .45s var(--cd-ease) both; }

.cd-quiz__opts { display: grid; gap: 12px; }

.cd-opt {

    position: relative; display: flex; align-items: center; gap: 14px; width: 100%; min-height: 58px; padding: 12px 16px;

    border-radius: 14px; border: 2px solid #e3d9f1; background: #fff; text-align: left; font-size: .98rem; line-height: 1.45; color: var(--cd-text);

    cursor: pointer; animation: cdRise .45s var(--cd-ease) both;

    transition: border-color .2s, *background* .2s, transform .2s, box-shadow .2s, opacity .25s, color .2s;

}

.cd-opt__key { width: 34px; height: 34px; border-radius: 10px; display: grid; place-items: center; flex-shrink: 0; background: #f3edfa; color: var(--cd-purple); font-weight: 800; font-size: .85rem; transition: *background* .2s, color .2s; }

.cd-opt__text { flex: 1; min-width: 0; overflow-wrap: anywhere; }

.cd-opt__mark { width: 24px; height: 24px; flex-shrink: 0; display: none; place-items: center; border-radius: 50%; color: #fff; }

.cd-opt__mark svg { width: 14px; height: 14px; stroke-width: 3.2; }

.cd-opt:hover:not(:disabled) { border-color: var(--cd-purple-2); transform: translateY(-2px); box-shadow: 0 10px 22px rgba(50, 16, 95, .1); }

.cd-opt:hover:not(:disabled) .cd-opt__key { background: var(--cd-purple); color: #fff; }

.cd-opt:disabled { cursor: default; }

.cd-opt.is-picked { border-color: var(--cd-purple-2); background: #f3edfa; }

.cd-opt.is-correct { border-color: #10b981; background: #ecfdf5; color: #065f46; animation: cdPop .4s var(--cd-ease); }

.cd-opt.is-correct .cd-opt__key { background: #10b981; color: #fff; }

.cd-opt.is-correct .cd-opt__mark { display: grid; background: #10b981; }

.cd-opt.is-wrong { border-color: #ef4444; background: #fef2f2; color: #991b1b; animation: cdShake .4s ease; }

.cd-opt.is-wrong .cd-opt__key { background: #ef4444; color: #fff; }

.cd-opt.is-wrong .cd-opt__mark { display: grid; background: #ef4444; }

.cd-opt.is-dim { opacity: .55; }

.cd-quiz__fb { margin-top: 16px; padding: 12px 16px; border-radius: 12px; font-size: .92rem; line-height: 1.6; animation: cdRise .35s var(--cd-ease) both; }

.cd-quiz__fb strong { display: block; margin-bottom: 2px; }

.cd-quiz__fb--ok { background: #ecfdf5; color: #065f46; border: 1px solid #a7f3d0; }

.cd-quiz__fb--no { background: #fef2f2; color: #991b1b; border: 1px solid #fecaca; }

.cd-quiz__fb--neutral { background: #f3edfa; color: var(--cd-purple); border: 1px solid #e3d9f1; }

.cd-quiz__foot { display: flex; justify-content: flex-end; margin-top: 18px; }

.cd-quiz__foot .cd-btn { width: auto; min-width: 190px; animation: cdRise .35s var(--cd-ease) both; }

.cd-quiz__empty { display: grid; justify-items: center; gap: 10px; padding: 36px 16px; text-align: center; color: var(--cd-muted); }

.cd-quiz__empty svg { width: 44px; height: 44px; color: var(--cd-amber); }



.cd-result { display: grid; justify-items: center; gap: 12px; text-align: center; padding: 8px 0 4px; animation: cdRise .5s var(--cd-ease) both; }

.cd-ring { position: relative; width: 150px; height: 150px; }

.cd-ring svg { width: 150px; height: 150px; transform: rotate(-90deg); }

.cd-ring circle { fill: none; stroke-width: 11; stroke-linecap: round; }

.cd-ring .track { stroke: #e6dcf3; }

.cd-ring .bar { stroke: var(--cd-gold); stroke-dasharray: 327; stroke-dashoffset: 327; transition: stroke-dashoffset 1.1s var(--cd-ease); }

.cd-ring.is-good .bar { stroke: #10b981; }

.cd-ring.is-low .bar { stroke: #f97316; }

.cd-ring__num { position: absolute; inset: 0; display: grid; place-items: center; font-size: 2rem; font-weight: 800; color: var(--cd-purple); font-family: "Plus Jakarta Sans", sans-serif; }

.cd-result h4 { font-size: 1.35rem; font-weight: 800; color: var(--cd-purple); }

.cd-result p { color: var(--cd-text); font-size: .96rem; }

.cd-result__actions { display: flex; flex-wrap: wrap; justify-content: center; gap: 10px; margin-top: 6px; }

.cd-result__actions .cd-btn { width: auto; }

.cd-review { width: 100%; margin-top: 12px; display: grid; gap: 10px; text-align: left; }

.cd-review__item { padding: 14px 16px; border-radius: 12px; background: #fff; border: 1px solid var(--cd-line); border-left-width: 4px; font-size: .9rem; line-height: 1.55; animation: cdRise .4s var(--cd-ease) both; }

.cd-review__item.ok { border-left-color: #10b981; }

.cd-review__item.no { border-left-color: #ef4444; }

.cd-review__item strong { display: block; color: var(--cd-purple); margin-bottom: 4px; overflow-wrap: anywhere; }

.cd-review__item span { display: block; overflow-wrap: anywhere; }

.cd-review__item .good { color: #047857; }

.cd-review__item .bad { color: #b91c1c; }



/* =========================================================

   Responsive

   ========================================================= */

@media (min-width: 1500px) {

    .cd-container { max-width: 1360px; }

}

@media (max-width: 1180px) {

    .cd { --cd-side: 350px; --cd-gap: 32px; }

}

@media (max-width: 1023px) {

    .cd { padding-bottom: 104px; }

    .cd-hero { padding: 36px 0 72px; }

    .cd-hero__copy { max-width: none; }

    .cd-grid { grid-template-columns: minmax(0, 1fr); gap: 28px; }

    .cd-side { order: -1; position: static; margin-top: -44px; max-width: 640px; width: 100%; margin-inline: auto; }

    .cd-body { padding-top: 0; }

    .cd-card { box-shadow: 0 18px 44px rgba(37, 11, 73, .22); }



    .cd-bar {

        position: fixed; left: 0; right: 0; bottom: 0; z-index: 90;

        display: flex; align-items: center; gap: 12px; padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px));

        background: rgba(255, 255, 255, .98); border-top: 1px solid var(--cd-line);

        box-shadow: 0 -10px 30px rgba(37, 11, 73, .12);

        transform: translateY(110%); transition: transform .3s ease; visibility: hidden;

    }

    .cd-bar.is-visible { transform: none; visibility: visible; }

    .cd-bar__price { flex: 1; min-width: 0; line-height: 1.2; }

    .cd-bar__price strong { display: block; font-size: 1.25rem; font-weight: 800; color: var(--cd-purple); font-family: "Plus Jakarta Sans", sans-serif; }

    .cd-bar__price s { font-size: .8rem; color: #a59cb4; }

    .cd-bar form { margin: 0; }

    .cd-bar .cd-btn { min-height: 46px; width: auto; padding: 10px 18px; font-size: .9rem; }

}

@media (max-width: 767px) {

    .cd-container { padding-inline: 16px; }

    .cd-hero { padding: 28px 0 64px; }

    .cd-crumbs { margin-bottom: 16px; }

    .cd-meta { gap: 10px 16px; font-size: .86rem; margin-top: 20px; }

    .cd-panel { padding: 22px 18px; border-radius: 14px; }

    .cd-checks { grid-template-columns: minmax(0, 1fr); gap: 12px; }

    .cd-main { gap: 20px; }

    .cd-side { margin-top: -36px; }

    .cd-card { border-radius: 16px; }

    .cd-card__body { padding: 20px 18px 22px; }

    .cd-price__now { font-size: 1.85rem; }

    .cd-sec__head { padding: 14px; gap: 10px; }

    .cd-lesson { padding: 12px 14px; gap: 12px; }

    .cd-inst { gap: 14px; }

    .cd-inst__avatar { width: 54px; height: 54px; font-size: 1.3rem; }

}

/* Popup becomes a bottom sheet on phones */

@media (max-width: 640px) {

    .cd-modal { align-items: flex-end; padding: 0; }

    .cd-dialog {

        width: 100%; max-height: 94vh; max-height: 94dvh; border-radius: 22px 22px 0 0;

        transform: translateY(100%); opacity: 1; transition: transform .45s var(--cd-ease);

    }

    .cd-modal.is-open .cd-dialog { transform: none; }

    .cd-dialog__grip { display: block; width: 44px; height: 5px; margin: 10px auto 0; border-radius: 4px; background: #d9cbee; flex-shrink: 0; }

    .cd-dialog__head { padding: 12px 16px; }

    .cd-dialog__info { padding: 16px; }

    .cd-dialog__foot { grid-template-columns: 1fr 1fr; padding: 12px 16px calc(12px + env(safe-area-inset-bottom, 0px)); }

    .cd-dialog__count { grid-column: 1 / -1; grid-row: 1; }

    .cd-nav { justify-content: center; justify-self: stretch; }

    .cd-nav--next { justify-self: stretch; }

    .cd-screen--card { min-height: 220px; padding: 28px 16px; }

    .cd-screen--quiz { padding: 18px 16px 22px; }

    .cd-opt { padding: 11px 12px; gap: 12px; min-height: 56px; font-size: .95rem; }

    .cd-quiz__foot .cd-btn { width: 100%; }

    .cd-result__actions .cd-btn { flex: 1 1 100%; }

}

@media (max-width: 480px) {

    .cd-hero { padding-bottom: 60px; }

    .cd-lead { font-size: .98rem; line-height: 1.65; }

    .cd-lesson { grid-template-columns: auto minmax(0, 1fr); }

    .cd-lesson__time { grid-column: 2; margin-top: -6px; }

    .cd-sec__head { flex-wrap: wrap; }

    .cd-sec__count { width: 100%; padding-left: 32px; margin-top: -6px; }

    .cd-bar .cd-btn--outline { display: none; }

    .cd-bar .cd-btn { padding-inline: 22px; }

    .cd-doc__actions .cd-btn { width: 100%; }

    .cd-ring, .cd-ring svg { width: 128px; height: 128px; }

}

/* Landscape phones */

@media (max-height: 520px) and (orientation: landscape) {

    .cd-modal { padding: 0; align-items: stretch; }

    .cd-dialog { width: 100%; max-height: 100vh; max-height: 100dvh; border-radius: 0; transform: none; }

    .cd-dialog .cd-screen { max-height: 72vh; }

    .cd-dialog__head { padding: 8px 16px; }

    .cd-dialog__foot { padding: 8px 16px; }

}

@media (prefers-reduced-motion: reduce) {

    .cd *, .cd *::before, .cd *::after { transition: none !important; animation: none !important; }

}



/* =========================================================
   Final lesson modal fix: remove the extra outer scrollbar
   Keep the document/PDF viewer fitted inside the popup.
   ========================================================= */
.cd-modal .cd-dialog {
    height: min(82dvh, 760px);
    max-height: calc(100dvh - 32px);
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.cd-modal .cd-dialog__head,
.cd-modal .cd-dialog__foot {
    flex: 0 0 auto;
}

.cd-modal .cd-dialog__scroll {
    flex: 1 1 auto;
    min-height: 0;
    overflow: hidden;
    display: flex;
    flex-direction: column;
}

.cd-modal .cd-dialog__scroll > .cd-screen {
    flex: 1 1 auto;
    min-height: 0;
    height: auto;
    max-height: none;
}

.cd-modal .cd-dialog__scroll > .cd-screen--document {
    position: relative;
    width: 100%;
    min-height: 0;
    max-height: none !important;
    aspect-ratio: auto;
    padding: 0;
    overflow: hidden;
    background: #fff;
}

.cd-modal .cd-dialog__scroll > .cd-screen--document iframe {
    position: absolute;
    inset: 0;
    display: block;
    width: 100%;
    height: 100%;
    min-height: 0;
    border: 0;
    background: #fff;
}

.cd-modal .cd-dialog__info {
    flex: 0 0 auto;
}

@media (max-width: 640px) {
    .cd-modal .cd-dialog {
        height: 85dvh;
        max-height: 94dvh;
    }
}

@media (max-height: 520px) and (orientation: landscape) {
    .cd-modal .cd-dialog {
        height: 100dvh;
        max-height: 100dvh;
    }
}

</style>


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
                                <details class="cd-sec" @if ($loop->first) open @endif>
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

                                                $quizData  = $kind === 'quiz' ? $buildQuiz($lesson) : [];
                                                $quizCount = count($quizData);
                                            @endphp
                                            <button
                                                type="button"
                                                class="cd-lesson"
                                                data-id="{{ $attr($lesson, 'id', '') }}"
                                                data-kind="{{ $kind }}"
                                                data-title="{{ $lTitle }}"
                                                data-vtype="{{ $vType }}"
                                                data-url="{{ $videoSrc }}"
                                                data-duration="{{ $duration }}"
                                                data-summary="{{ $summary }}"
                                                data-attachment="{{ $attachment }}"
                                                data-attname="{{ $attName }}"
                                                @if ($kind === 'quiz') data-quiz="{{ json_encode($quizData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) }}" data-qcount="{{ $quizCount }}" @endif
                                            >
                                                <span class="cd-lesson__icon">{!! $icons[$kindIcon] !!}</span>
                                                <span class="cd-lesson__text">
                                                    <span class="cd-lesson__title">{{ $lTitle }}</span>
                                                    <span class="cd-lesson__sub">
                                                        <span>{{ $kindLabels[$kind] }}</span>
                                                        @if ($kind === 'quiz' && $quizCount > 0)
                                                            <span>{{ $quizCount }} {{ \Illuminate\Support\Str::plural('question', $quizCount) }}</span>
                                                        @endif
                                                        @if ($attachment !== '' && $kind !== 'document')
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

            {{-- ---------- Purchase card ---------- --}}
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
        </div>
    </div>

    {{-- Mobile / tablet sticky buy bar --}}
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
            return id ? 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0' : '';
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

    function renderVideo(url, vtype) {
        screen.replaceChildren();
        screen.className = 'cd-screen';

        if (!url) { setNote('No video has been added for this lesson yet.', true); return; }

        var type = guessVideoType(vtype, url);
        var src = '';

        if (type === 'youtube') {
            src = youtubeEmbed(url);
            if (!src) { setNote('This YouTube video URL is not valid.', true); return; }
        } else if (type === 'vimeo') {
            src = vimeoEmbed(url);
            if (!src) { setNote('This Vimeo video URL is not valid.', true); return; }
        }

        if (src) {
            screen.appendChild(el('div', 'cd-loader'));
            var iframe = document.createElement('iframe');
            iframe.src = src;
            iframe.title = 'Lesson video player';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; fullscreen; web-share';
            iframe.allowFullscreen = true;
            iframe.addEventListener('load', function () {
                var l = screen.querySelector('.cd-loader'); if (l) l.remove();
            });
            screen.appendChild(iframe);
            return;
        }

        var video = document.createElement('video');
        video.src = url;
        video.controls = true;
        video.autoplay = true;
        video.playsInline = true;
        video.preload = 'metadata';
        video.addEventListener('error', function () { setNote('This video could not be loaded.', true); });
        screen.appendChild(video);
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

    function render(index) {
        var btn = lessons[index];
        if (!btn) return;

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

        if (kind === 'video') renderVideo(data.url, data.vtype);
        else if (kind === 'quiz') renderQuiz(btn);
        else renderDocument(data);

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
        btn.addEventListener('click', function () { openModal(i, btn); });
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
    window.addEventListener('pageshow', function (e) {
        if (e.persisted) root.querySelectorAll('form[data-once] button').forEach(function (b) { b.disabled = false; });
    });
})();
</script>
@endsection