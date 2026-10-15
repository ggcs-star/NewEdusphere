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
        'check'   => $ic('<polyline points="20 6 9 17 4 12"/>'),
        'play'    => $ic('<polygon points="6 4 20 12 6 20 6 4"/>', true),
        'clock'   => $ic('<circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/>'),
        'globe'   => $ic('<circle cx="12" cy="12" r="10"/><line x1="2" y1="12" x2="22" y2="12"/><path d="M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z"/>'),
        'user'    => $ic('<path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/>'),
        'level'   => $ic('<line x1="6" y1="20" x2="6" y2="14"/><line x1="12" y1="20" x2="12" y2="9"/><line x1="18" y1="20" x2="18" y2="4"/>'),
        'layers'  => $ic('<polygon points="12 2 2 7 12 12 22 7 12 2"/><polyline points="2 17 12 22 22 17"/><polyline points="2 12 12 17 22 12"/>'),
        'star'    => $ic('<polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/>', true),
        'cart'    => $ic('<circle cx="9" cy="21" r="1.4"/><circle cx="20" cy="21" r="1.4"/><path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>'),
        'bolt'    => $ic('<polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/>'),
        'chevron' => $ic('<polyline points="6 9 12 15 18 9"/>'),
        'close'   => $ic('<line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/>'),
        'device'  => $ic('<rect x="5" y="2" width="14" height="20" rx="2"/><line x1="12" y1="18" x2="12.01" y2="18"/>'),
        'infinity'=> $ic('<path d="M18.178 8c5.096 0 5.096 8 0 8-5.095 0-7.133-8-12.739-8-4.585 0-4.585 8 0 8 5.606 0 7.644-8 12.74-8z"/>'),
        'book'    => $ic('<path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/>'),
        'video'   => $ic('<polygon points="23 7 16 12 23 17 23 7"/><rect x="1" y="5" width="15" height="14" rx="2"/>'),
        'alert'   => $ic('<circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/>'),
    ];

    /* Safe attribute reader (never throws on missing columns) */
    $attr = function ($model, string $key, $default = null) {
        if (is_object($model) && method_exists($model, 'getAttributes')) {
            return array_key_exists($key, $model->getAttributes()) ? $model->getAttribute($key) : $default;
        }
        return data_get($model, $key, $default);
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
    $previewUrl      = trim((string) ($course->preview_video_url ?? ''));
    $previewEmbedUrl = '';
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
        } else {
            $previewEmbedUrl = $previewUrl; // direct MP4 / WebM / Ogg
        }
    }

    /* ---------- Curriculum ---------- */
    $sections     = collect($course->sections ?? []);
    $totalLessons = $sections->sum(fn ($s) => collect($s->lessons ?? [])->count());

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
    --cd-radius: 16px;
    --cd-shadow: 0 10px 30px rgba(50, 16, 95, .08);
    --cd-side: 380px;
    --cd-gap: 40px;
    --cd-pull: 220px;
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
.cd :focus-visible { outline: 3px solid rgba(245, 189, 50, .75); outline-offset: 2px; border-radius: 6px; }
.cd-container { width: 100%; max-width: 1240px; margin-inline: auto; padding-inline: 24px; }

/* ---------- Hero ---------- */
.cd-hero {
    position: relative; overflow: hidden; color: #fff;
    padding: 44px 0 96px;
    background:
        radial-gradient(700px 380px at 90% 0%, rgba(245, 189, 50, .16), transparent 60%),
        radial-gradient(600px 400px at 0% 100%, rgba(122, 79, 208, .4), transparent 60%),
        linear-gradient(130deg, var(--cd-dark) 0%, var(--cd-purple) 50%, var(--cd-purple-2) 100%);
}
.cd-hero::after { content: ""; position: absolute; width: 420px; height: 420px; right: -120px; top: -160px; border-radius: 50%; border: 1.5px solid rgba(255, 255, 255, .12); pointer-events: none; }
.cd-hero__copy { position: relative; z-index: 1; max-width: calc(100% - var(--cd-side) - var(--cd-gap)); min-width: 0; }

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

/* ---------- Alerts ---------- */
.cd-alert { display: flex; gap: 12px; align-items: flex-start; padding: 14px 18px; border-radius: 12px; margin-bottom: 20px; font-size: .94rem; line-height: 1.5; border: 1px solid; }
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

.cd-player { margin-bottom: 18px; background: var(--cd-dark); border-radius: var(--cd-radius); overflow: hidden; box-shadow: var(--cd-shadow); }
.cd-player[hidden] { display: none; }
.cd-player__bar { display: flex; align-items: center; justify-content: space-between; gap: 12px; padding: 14px 18px; color: #fff; }
.cd-player__bar h3 { font-size: 1rem; font-weight: 700; line-height: 1.4; overflow-wrap: anywhere; }
.cd-player__bar small { display: block; font-size: .72rem; letter-spacing: .08em; text-transform: uppercase; color: var(--cd-gold); font-weight: 800; margin-bottom: 2px; }
.cd-player__close { width: 36px; height: 36px; flex-shrink: 0; border-radius: 50%; border: 0; display: grid; place-items: center; cursor: pointer; background: rgba(255, 255, 255, .14); color: #fff; transition: background .2s; }
.cd-player__close:hover { background: rgba(255, 255, 255, .26); }
.cd-player__close svg { width: 18px; height: 18px; }
.cd-screen { position: relative; width: 100%; aspect-ratio: 16 / 9; background: #150826; display: flex; align-items: center; justify-content: center; overflow: hidden; color: #e6dcf3; text-align: center; padding: 0; }
.cd-screen iframe, .cd-screen video { position: absolute; inset: 0; width: 100%; height: 100%; border: 0; background: #000; display: block; }
.cd-screen img { width: 100%; height: 100%; object-fit: cover; }
.cd-screen__note { padding: 20px; font-size: .94rem; }

.cd-sections { display: grid; gap: 12px; }
.cd-sec { border: 1px solid var(--cd-line); border-radius: 12px; background: #fff; overflow: hidden; transition: border-color .2s, box-shadow .2s; }
.cd-sec[open] { border-color: #d9cbee; box-shadow: 0 6px 18px rgba(50, 16, 95, .06); }
.cd-sec__head { display: flex; align-items: center; gap: 12px; padding: 16px 18px; cursor: pointer; list-style: none; background: #f6f1fc; color: var(--cd-purple); font-weight: 700; line-height: 1.45; user-select: none; }
.cd-sec__head::-webkit-details-marker { display: none; }
.cd-sec__head:hover { background: #f0e8fa; }
.cd-sec__chev { width: 20px; height: 20px; transition: transform .25s ease; color: var(--cd-purple-2); }
.cd-sec[open] .cd-sec__chev { transform: rotate(180deg); }
.cd-sec__name { flex: 1; min-width: 0; overflow-wrap: anywhere; }
.cd-sec__count { flex-shrink: 0; font-size: .8rem; font-weight: 600; color: var(--cd-muted); white-space: nowrap; }

.cd-lesson { width: 100%; display: grid; grid-template-columns: auto minmax(0, 1fr) auto; align-items: center; gap: 12px; padding: 13px 18px; border: 0; border-top: 1px solid #f0eaf6; background: #fff; text-align: left; font-size: .94rem; line-height: 1.5; color: var(--cd-text); cursor: pointer; transition: background .2s, color .2s; }
.cd-lesson:hover { background: #fcfaff; color: var(--cd-purple); }
.cd-lesson.is-active { background: #fff8e1; color: var(--cd-purple); }
.cd-lesson__icon { width: 30px; height: 30px; border-radius: 50%; display: grid; place-items: center; background: var(--cd-purple); color: #fff; transition: background .2s, transform .2s; }
.cd-lesson__icon svg { width: 12px; height: 12px; margin-left: 2px; }
.cd-lesson:hover .cd-lesson__icon, .cd-lesson.is-active .cd-lesson__icon { background: var(--cd-gold-d); transform: scale(1.08); }
.cd-lesson__title { min-width: 0; overflow-wrap: anywhere; }
.cd-lesson__time { display: inline-flex; align-items: center; gap: 6px; font-size: .8rem; color: var(--cd-muted); white-space: nowrap; }
.cd-lesson__time svg { width: 13px; height: 13px; }
.cd-lesson--empty { cursor: default; display: block; color: var(--cd-muted); }
.cd-lesson--empty:hover { background: #fff; color: var(--cd-muted); }
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

/* ---------- Purchase card ---------- */
.cd-card { background: #fff; border: 1px solid var(--cd-line); border-radius: 20px; overflow: hidden; box-shadow: 0 24px 54px rgba(37, 11, 73, .2); }
.cd-card .cd-screen { border-radius: 0; }
.cd-card__ph { display: grid; place-items: center; gap: 10px; color: #cbb9f0; font-size: .9rem; padding: 20px; }
.cd-card__ph svg { width: 44px; height: 44px; opacity: .8; }
.cd-card__body { padding: 24px; }
.cd-price { display: flex; align-items: baseline; flex-wrap: wrap; gap: 6px 12px; }
.cd-price__now { font-size: 2.1rem; line-height: 1.1; font-weight: 800; color: var(--cd-purple); font-family: "Plus Jakarta Sans", sans-serif; }
.cd-price__now.is-free { color: var(--cd-green); }
.cd-price__old { font-size: 1.05rem; color: #a59cb4; text-decoration: line-through; }
.cd-price__off { display: inline-flex; padding: 3px 10px; border-radius: 999px; background: #ecfdf5; color: #047857; font-size: .76rem; font-weight: 800; }
.cd-actions { display: grid; gap: 12px; margin-top: 20px; }
.cd-actions form { margin: 0; }

.cd-btn { width: 100%; min-height: 52px; display: inline-flex; align-items: center; justify-content: center; gap: 10px; padding: 12px 18px; border-radius: 12px; border: 2px solid transparent; font-size: .98rem; font-weight: 800; line-height: 1.2; cursor: pointer; text-align: center; transition: transform .2s ease, background .2s ease, box-shadow .2s ease, border-color .2s ease, color .2s ease; }
.cd-btn svg { width: 19px; height: 19px; }
.cd-btn:hover { transform: translateY(-2px); }
.cd-btn[disabled] { opacity: .7; cursor: wait; transform: none; }
.cd-btn--gold { background: var(--cd-gold); color: var(--cd-dark); box-shadow: 0 10px 22px rgba(245, 189, 50, .35); }
.cd-btn--gold:hover { background: var(--cd-gold-d); }
.cd-btn--outline { background: #fff; color: var(--cd-purple); border-color: #cdbde6; }
.cd-btn--outline:hover { border-color: var(--cd-purple); background: #f8f5fd; }
.cd-btn--success { background: var(--cd-green); color: #fff; }
.cd-btn--success:hover { background: #047857; }

.cd-includes { margin-top: 24px; padding-top: 20px; border-top: 1px solid var(--cd-line); }
.cd-includes h4 { font-size: .95rem; font-weight: 800; color: var(--cd-purple); margin-bottom: 14px; }
.cd-includes ul { display: grid; gap: 12px; }
.cd-includes li { display: flex; align-items: center; gap: 12px; font-size: .92rem; color: var(--cd-text); line-height: 1.4; }
.cd-includes li svg { width: 18px; height: 18px; color: var(--cd-gold-d); }

/* ---------- Mobile buy bar ---------- */
.cd-bar { display: none; }

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
    .cd-lesson { padding: 12px 14px; gap: 10px; }
    .cd-inst { gap: 14px; }
    .cd-inst__avatar { width: 54px; height: 54px; font-size: 1.3rem; }
}
@media (max-width: 480px) {
    .cd-hero { padding-bottom: 60px; }
    .cd-lead { font-size: .98rem; line-height: 1.65; }
    .cd-lesson { grid-template-columns: auto minmax(0, 1fr); }
    .cd-lesson__time { grid-column: 2; margin-top: -4px; }
    .cd-sec__head { flex-wrap: wrap; }
    .cd-sec__count { width: 100%; padding-left: 32px; margin-top: -6px; }
    .cd-bar .cd-btn--outline { display: none; }
    .cd-bar .cd-btn { padding-inline: 22px; }
}
@media (prefers-reduced-motion: reduce) {
    .cd *, .cd *::before, .cd *::after { transition: none !important; animation: none !important; }
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
                    <section class="cd-panel" aria-labelledby="cd-learn">
                        <h2 class="cd-h2" id="cd-learn">What Will I Learn?</h2>
                        <ul class="cd-checks">
                            @foreach ($outcomes as $outcome)
                                <li><span class="tick">{!! $icons['check'] !!}</span><span>{{ $outcome }}</span></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="cd-panel" aria-labelledby="cd-curriculum">
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

                    <div class="cd-player" id="cdPlayerPanel" hidden>
                        <div class="cd-player__bar">
                            <div>
                                <small>Now playing</small>
                                <h3 id="cdPlayerTitle">Lesson</h3>
                            </div>
                            <button type="button" class="cd-player__close" id="cdPlayerClose" aria-label="Close player">{!! $icons['close'] !!}</button>
                        </div>
                        <div class="cd-screen" id="cdPlayer"></div>
                    </div>

                    @if ($sections->isEmpty())
                        <div class="cd-empty">Curriculum lessons will appear here once sections and lessons are added.</div>
                    @else
                        <div class="cd-sections">
                            @foreach ($sections as $section)
                                @php $lessons = collect($section->lessons ?? []); @endphp
                                <details class="cd-sec" @if ($loop->first) open @endif>
                                    <summary class="cd-sec__head">
                                        <span class="cd-sec__chev">{!! $icons['chevron'] !!}</span>
                                        <span class="cd-sec__name">{{ $section->title ?? $section->name ?? 'Course section' }}</span>
                                        <span class="cd-sec__count">{{ $lessons->count() }} {{ \Illuminate\Support\Str::plural('lesson', $lessons->count()) }}</span>
                                    </summary>

                                    @forelse ($lessons as $lesson)
                                        <button
                                            type="button"
                                            class="cd-lesson"
                                            data-title="{{ $lesson->title ?? 'Lesson' }}"
                                            data-url="{{ $lesson->video_url ?? '' }}"
                                            data-type="{{ strtolower((string) ($lesson->video_type ?? '')) }}"
                                        >
                                            <span class="cd-lesson__icon">{!! $icons['play'] !!}</span>
                                            <span class="cd-lesson__title">{{ $lesson->title ?? 'Lesson' }}</span>
                                            @if (!empty($lesson->duration))
                                                <span class="cd-lesson__time">{!! $icons['clock'] !!} {{ $lesson->duration }}</span>
                                            @endif
                                        </button>
                                    @empty
                                        <div class="cd-lesson cd-lesson--empty">No lessons have been added to this section yet.</div>
                                    @endforelse
                                </details>
                            @endforeach
                        </div>
                    @endif
                </section>

                @if (count($requirements))
                    <section class="cd-panel" aria-labelledby="cd-req">
                        <h2 class="cd-h2" id="cd-req">Requirements</h2>
                        <ul class="cd-checks cd-checks--single">
                            @foreach ($requirements as $requirement)
                                <li><span class="tick">{!! $icons['check'] !!}</span><span>{{ $requirement }}</span></li>
                            @endforeach
                        </ul>
                    </section>
                @endif

                <section class="cd-panel" aria-labelledby="cd-about">
                    <h2 class="cd-h2" id="cd-about">About This Course</h2>
                    <div class="cd-prose">{!! $course->description ?? 'Course description will be available soon.' !!}</div>
                </section>

                <section class="cd-panel" aria-labelledby="cd-instructor">
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
        </div>
    </div>

    {{-- Mobile / tablet sticky buy bar --}}
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
</main>

<script>
(function () {
    var root = document.querySelector('.cd');
    if (!root) return;

    /* ---------- Video helpers ---------- */
    function isYouTube(type, url) {
        return ['youtube', 'youtube_url', 'youtube_url_video'].indexOf((type || '').toLowerCase()) !== -1
            || /(?:youtube\.com|youtu\.be)/i.test(url || '');
    }

    function youtubeEmbed(url) {
        try {
            var parsed = new URL(url, window.location.origin);
            var id = '';
            if (parsed.hostname.indexOf('youtu.be') !== -1) {
                id = parsed.pathname.replace(/^\/+/, '').split('/')[0];
            } else if (parsed.hostname.indexOf('youtube.com') !== -1) {
                id = parsed.searchParams.get('v') || '';
                if (!id) {
                    var m = parsed.pathname.match(/\/(?:embed|shorts|live)\/([^/]+)/);
                    if (m) id = m[1];
                }
            }
            return id ? 'https://www.youtube-nocookie.com/embed/' + encodeURIComponent(id) + '?autoplay=1&rel=0' : '';
        } catch (e) { return ''; }
    }

    function note(container, text) {
        container.replaceChildren();
        var p = document.createElement('p');
        p.className = 'cd-screen__note';
        p.textContent = text;
        container.appendChild(p);
    }

    function renderPlayer(container, url, type) {
        container.replaceChildren();
        if (!url) { note(container, 'No video has been uploaded for this lesson yet.'); return; }

        if (isYouTube(type, url)) {
            var src = youtubeEmbed(url);
            if (!src) { note(container, 'This YouTube video URL is not valid.'); return; }
            var iframe = document.createElement('iframe');
            iframe.src = src;
            iframe.title = 'Course video player';
            iframe.allow = 'accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share';
            iframe.allowFullscreen = true;
            container.appendChild(iframe);
            return;
        }

        var video = document.createElement('video');
        video.src = url;
        video.controls = true;
        video.autoplay = true;
        video.playsInline = true;
        container.appendChild(video);
    }

    /* ---------- Lesson player ---------- */
    var panel  = document.getElementById('cdPlayerPanel');
    var title  = document.getElementById('cdPlayerTitle');
    var player = document.getElementById('cdPlayer');
    var close  = document.getElementById('cdPlayerClose');
    var active = null;

    root.querySelectorAll('.cd-lesson[data-url]').forEach(function (btn) {
        btn.addEventListener('click', function () {
            if (active) active.classList.remove('is-active');
            active = btn;
            btn.classList.add('is-active');

            panel.hidden = false;
            title.textContent = btn.dataset.title || 'Lesson';
            renderPlayer(player, btn.dataset.url || '', btn.dataset.type || '');
            panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
        });
    });

    if (close) {
        close.addEventListener('click', function () {
            player.replaceChildren();
            panel.hidden = true;
            if (active) { active.classList.remove('is-active'); active = null; }
        });
    }

    /* ---------- Expand / collapse all sections ---------- */
    var toggleAll = document.getElementById('cdToggleAll');
    if (toggleAll) {
        toggleAll.addEventListener('click', function () {
            var open = toggleAll.dataset.open !== 'true';
            root.querySelectorAll('.cd-sec').forEach(function (d) { d.open = open; });
            toggleAll.dataset.open = String(open);
            toggleAll.textContent = open ? 'Collapse all' : 'Expand all';
        });
    }

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