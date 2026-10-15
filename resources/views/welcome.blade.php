@extends('layouts.frontend')

@section('title', 'Home')

@section('content')
@php
    $icons = [
        'arrow'  => '<svg class="ico-arrow" viewBox="0 0 24 24" aria-hidden="true"><path d="M5 12h14"/><path d="m12 5 7 7-7 7"/></svg>',
        'star'   => '<svg class="ico-star" viewBox="0 0 24 24" aria-hidden="true"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>',
        'check'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="20 6 9 17 4 12"/></svg>',
        'code'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>',
        'award'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="8" r="6"/><path d="M15.477 12.89 17 22l-5-3-5 3 1.523-9.11"/></svg>',
        'chart'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><line x1="18" y1="20" x2="18" y2="10"/><line x1="12" y1="20" x2="12" y2="4"/><line x1="6" y1="20" x2="6" y2="14"/></svg>',
        'sliders'=> '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 21v-7"/><path d="M4 10V3"/><path d="M12 21v-9"/><path d="M12 8V3"/><path d="M20 21v-5"/><path d="M20 12V3"/><path d="M1 14h6"/><path d="M9 8h6"/><path d="M17 16h6"/></svg>',
        'cap'    => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M22 10 12 5 2 10l10 5 10-5z"/><path d="M6 12v5c3 3 9 3 12 0v-5"/></svg>',
        'bulb'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 18h6"/><path d="M10 22h4"/><path d="M12 2a7 7 0 0 0-4 12.7c.6.5 1 1.3 1 2.3h6c0-1 .4-1.8 1-2.3A7 7 0 0 0 12 2z"/></svg>',
        'target' => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><circle cx="12" cy="12" r="6"/><circle cx="12" cy="12" r="2"/></svg>',
        'book'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 3h6a4 4 0 0 1 4 4v14a3 3 0 0 0-3-3H2z"/><path d="M22 3h-6a4 4 0 0 0-4 4v14a3 3 0 0 1 3-3h7z"/></svg>',
        'clock'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="12" cy="12" r="10"/><polyline points="12 6 12 12 16 14"/></svg>',
        'rocket' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4.5 16.5c-1.5 1.26-2 5-2 5s3.74-.5 5-2c.71-.84.7-2.13-.09-2.91a2.18 2.18 0 0 0-2.91-.09z"/><path d="m12 15-3-3a22 22 0 0 1 2-3.95A12.88 12.88 0 0 1 22 2c0 2.72-.78 7.5-6 11a22.35 22.35 0 0 1-4 2z"/><path d="M9 12H4s.55-3.03 2-4c1.62-1.08 5 0 5 0"/><path d="M12 15v5s3.03-.55 4-2c1.08-1.62 0-5 0-5"/></svg>',
        'user'   => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>',
        'level'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><line x1="6" y1="20" x2="6" y2="14"/><line x1="12" y1="20" x2="12" y2="9"/><line x1="18" y1="20" x2="18" y2="4"/></svg>',
        'shield' => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/><polyline points="9 12 11 14 15 10"/></svg>',
        'group'  => '<svg viewBox="0 0 24 24" aria-hidden="true"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>',
    ];
    $fallbackImages = [
        'https://images.unsplash.com/photo-1455390582262-044cdead277a?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1523240795612-9a054b0db644?auto=format&fit=crop&w=900&q=80',
        'https://images.unsplash.com/photo-1521737711867-e3b97375f902?auto=format&fit=crop&w=900&q=80',
    ];
@endphp

<style>
/* =========================================================
   EduSphere Home – scoped fixes (icons + responsive)
   ========================================================= */
.edu-home { overflow-x: hidden; }
.edu-home *, .edu-home *::before, .edu-home *::after { box-sizing: border-box; }
.edu-home img { max-width: 100%; display: block; }
.edu-home .container { width: 100%; max-width: 1200px; margin-inline: auto; padding-inline: 20px; }
.edu-home .hero .container { max-width: 1400px; }

/* ---------- SVG helpers ---------- */
.edu-home svg { flex-shrink: 0; }
.edu-home .ico-arrow {
    width: 1.1em; height: 1.1em; margin-left: 8px;
    fill: none; stroke: currentColor; stroke-width: 2.2;
    stroke-linecap: round; stroke-linejoin: round;
    transition: transform .25s ease;
}
.edu-home a:hover > .ico-arrow, .edu-home .btn:hover .ico-arrow { transform: translateX(4px); }
.edu-home .ico-star {
    width: 13px; height: 13px; margin-right: 5px;
    fill: currentColor; stroke: currentColor; stroke-width: 1;
    stroke-linejoin: round; vertical-align: -1px;
}

/* ---------- Buttons ---------- */
.edu-home .btn {
    display: inline-flex; align-items: center; justify-content: center;
    white-space: nowrap; text-align: center;
}

/* ---------- Section headings ---------- */
.edu-home .section { padding: 80px 0; }
.edu-home .section-heading { text-align: center; max-width: 720px; margin: 0 auto 48px; }
.edu-home .section-line { width: 70px; height: 4px; border-radius: 4px; margin: 14px auto 0; }
.edu-home .section-description { margin-top: 16px; }

/* ---------- Hero ---------- */
.edu-home .hero { position: relative; overflow: hidden; padding: 76px 0 66px; background: var(--purple); }
.edu-home .hero-grid {
    display: grid; grid-template-columns: 1.1fr .9fr;
    gap: 40px; align-items: center; position: relative; z-index: 2;
}
.edu-home .hero h1 { font-size: clamp(1.4rem, 2.6vw, 2.5rem); line-height: 1.18; white-space: nowrap; }
@media (max-width: 900px) { .edu-home .hero h1 { white-space: normal; } }
.edu-home .hero-description { max-width: 520px; margin-top: 18px; }
.edu-home .hero-buttons { display: flex; flex-wrap: wrap; gap: 14px; margin-top: 30px; }

.edu-home .hero-feature-row {
    display: flex; flex-wrap: wrap; gap: 26px;
    margin-top: 34px; padding-top: 24px; width: 100%;
    border-top: 1px solid rgba(255,255,255,.15);
}
.edu-home .hero-feature-row .feature-item {
    display: flex; align-items: center; gap: 10px;
    font-size: 13px; font-weight: 700; color: #ece3f7;
}
.edu-home .hero-feature-row .feature-icon {
    width: 32px; height: 32px; border-radius: 50%; flex-shrink: 0;
    display: flex; align-items: center; justify-content: center;
    background: rgba(245,189,50,.16); color: var(--gold);
}
.edu-home .hero-feature-row .feature-icon svg {
    width: 16px; height: 16px; fill: none; stroke: currentColor;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round;
}

/* ---------- Hero visual ---------- */
.edu-home .hero-image {
    position: relative; min-height: 400px;
    display: flex; align-items: center; justify-content: center;
    background: none; clip-path: none; overflow: visible;
}
.edu-home .hero-image img { clip-path: none; }
.edu-home .hero-visual-card {
    position: relative; z-index: 1;
    width: 100%; max-width: 650px; margin: 0 auto;
    background: #fff; border-radius: 24px; padding: 20px;
    box-shadow: 0 30px 60px rgba(10,4,26,.35);
}
.edu-home .hero-image .hero-visual {
    position: relative; inset: auto; z-index: 1;
    width: 100%; height: auto; margin: 0 auto;
    object-fit: contain; object-position: center;
    border-radius: 14px; display: block;
}

/* ---------- Course carousel ---------- */
.edu-home .course-carousel { position: relative; }
.edu-home .course-viewport { overflow: hidden; }
.edu-home .course-track {
    display: flex; flex-wrap: nowrap; gap: 26px;
    transition: transform .5s ease;
}
.edu-home .course-track .course-card {
    flex: 0 0 calc((100% - 52px) / 3);
    min-width: 0;
}
.edu-home .carousel-arrow {
    position: absolute; top: 42%; transform: translateY(-50%);
    width: 46px; height: 46px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    background: #fff; border: 1px solid #e8ecf3; color: var(--purple);
    box-shadow: 0 10px 24px rgba(15,35,70,.14); cursor: pointer; z-index: 5;
    transition: background .2s ease, color .2s ease, box-shadow .2s ease;
}
.edu-home .carousel-arrow:hover { background: var(--purple); color: #fff; box-shadow: 0 14px 30px rgba(15,35,70,.22); }
.edu-home .carousel-arrow svg { width: 18px; height: 18px; fill: none; stroke: currentColor; stroke-width: 2.4; stroke-linecap: round; stroke-linejoin: round; }
.edu-home .carousel-arrow.prev { left: -23px; }
.edu-home .carousel-arrow.next { right: -23px; }

/* ---------- Course cards ---------- */
.edu-home .course-grid {
    display: grid; gap: 26px;
    grid-template-columns: repeat(auto-fill, minmax(290px, 1fr));
}
.edu-home .course-card {
    display: flex; flex-direction: column; height: 100%;
    background: var(--light-bg); border: 1px solid #e8ecf3; border-radius: 18px;
    overflow: hidden; box-shadow: 0 8px 26px rgba(15,35,70,.07);
    transition: transform .3s ease, box-shadow .3s ease;
}
.edu-home .course-card:hover { transform: translateY(-6px); box-shadow: 0 18px 40px rgba(15,35,70,.14); }
.edu-home .course-image { position: relative; aspect-ratio: 16 / 10; overflow: hidden; background: #eef1f6; }
.edu-home .course-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .5s ease; }
.edu-home .course-card:hover .course-image img { transform: scale(1.06); }
.edu-home .course-label {
    position: absolute; top: 14px; left: 14px; z-index: 2;
    display: inline-flex; align-items: center;
    padding: 6px 12px; border-radius: 999px;
    font-size: 12px; font-weight: 700; letter-spacing: .3px;
    background: var(--gold); color: var(--purple-dark); box-shadow: 0 4px 12px rgba(0,0,0,.18);
}
.edu-home .course-content { display: flex; flex-direction: column; flex: 1; padding: 20px; }
.edu-home .course-category { font-size: 12px; font-weight: 800; text-transform: uppercase; letter-spacing: 1px; }
.edu-home .course-title {
    margin: 8px 0; font-size: 1.15rem; line-height: 1.35;
    display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;
}
.edu-home .course-description { font-size: .93rem; line-height: 1.6; opacity: .8; margin-bottom: 14px; }
.edu-home .course-meta {
    display: flex; flex-wrap: wrap; gap: 8px 16px;
    font-size: .85rem; padding-top: 14px; margin-top: auto;
    border-top: 1px dashed #dfe5ef;
}
.edu-home .course-meta span { display: inline-flex; align-items: center; gap: 6px; }
.edu-home .course-meta svg {
    width: 15px; height: 15px; fill: none; stroke: currentColor;
    stroke-width: 2; stroke-linecap: round; stroke-linejoin: round; opacity: .7;
}
.edu-home .course-actions {
    display: flex; align-items: center; justify-content: space-between;
    gap: 12px; margin-top: 16px;
}
.edu-home .course-price { font-size: 1.3rem; font-weight: 800; display: flex; align-items: baseline; gap: 8px; flex-wrap: wrap; }
.edu-home .course-old-price { font-size: .85rem; font-weight: 500; text-decoration: line-through; opacity: .5; }
.edu-home .course-actions .btn { padding: 10px 18px; font-size: .85rem; }
.edu-home .empty-state { grid-column: 1 / -1; text-align: center; padding: 50px 20px; border: 2px dashed #d7deea; border-radius: 18px; }
.edu-home .featured-cta { text-align: center; margin-top: 38px; }

/* ---------- Learning goals ---------- */
.edu-home .learning-goals-section {
    padding: 10px 0 90px;
    background: #fff;
}
.edu-home .featured-section { padding-bottom: 30px; }
.edu-home .goals-heading { text-align: center; max-width: 720px; margin: 0 auto 50px; }
.edu-home .goals-kicker { display: inline-block; font-size: 12px; font-weight: 800; letter-spacing: 2px; color: var(--gold-dark); margin-bottom: 12px; }
.edu-home .goals-heading h2 { font-size: clamp(1.7rem, 3.6vw, 2.6rem); line-height: 1.2; color: var(--purple); margin: 0 0 14px; }
.edu-home .goals-heading p { color: var(--muted); line-height: 1.7; margin: 16px 0 0; }
.edu-home .goals-layout { display: grid; grid-template-columns: 1.05fr .95fr; gap: 34px; align-items: stretch; }
.edu-home .goals-features { display: grid; grid-template-columns: repeat(2, 1fr); gap: 16px; align-content: start; }
.edu-home .goal-feature {
    background: var(--light-bg); border: 1px solid var(--border); border-radius: 16px;
    padding: 22px 24px; transition: all .3s ease;
    display: flex; flex-direction: column; min-height: 300px;
}
.edu-home .goal-feature:hover, .edu-home .goal-feature.active {
    border-color: var(--gold); box-shadow: 0 14px 34px rgba(35,15,84,.1); transform: translateY(-2px);
}
.edu-home .goal-feature-title { display: flex; align-items: center; gap: 14px; margin-bottom: 10px; }
.edu-home .goal-feature-title h3 { margin: 0; font-size: 1.1rem; color: var(--purple); display: flex; align-items: center; flex-wrap: wrap; gap: 8px; }
.edu-home .goal-icon {
    width: 46px; height: 46px; border-radius: 13px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    color: #fff; background: linear-gradient(135deg, var(--purple), var(--purple-light));
    box-shadow: 0 8px 18px rgba(35,15,84,.25);
}
.edu-home .goal-feature:nth-child(2) .goal-icon { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); box-shadow: 0 8px 18px rgba(245,189,50,.28); }
.edu-home .goal-feature:nth-child(3) .goal-icon { background: linear-gradient(135deg, var(--gold), var(--gold-dark)); box-shadow: 0 8px 18px rgba(245,189,50,.28); }
.edu-home .goal-feature:nth-child(4) .goal-icon { background: linear-gradient(135deg, var(--purple-light), var(--purple)); box-shadow: 0 8px 18px rgba(35,15,84,.28); }
.edu-home .goal-icon svg, .edu-home .panel-icon svg, .edu-home .panel-bottom-icon svg, .edu-home .progress-emoji svg {
    width: 24px; height: 24px; fill: none; stroke: currentColor;
    stroke-width: 1.9; stroke-linecap: round; stroke-linejoin: round;
}
.edu-home .goal-feature p { margin: 0 0 12px; color: var(--muted); line-height: 1.65; font-size: .95rem; }
.edu-home .goal-feature > a { display: inline-flex; align-items: center; font-weight: 700; font-size: .9rem; color: var(--purple); text-decoration: none; margin-top: auto; }
.edu-home .goal-feature > a:hover { color: var(--gold-dark); }
.edu-home .plan-label {
    font-size: 10.5px; font-weight: 700; padding: 3px 9px; border-radius: 999px;
    background: #FFF4E5; color: var(--gold-dark); letter-spacing: .3px; white-space: nowrap;
}

.edu-home .learning-panel {
    background: linear-gradient(145deg, var(--purple) 0%, var(--purple-dark) 100%);
    color: #fff; border-radius: 22px; padding: 28px;
    box-shadow: 0 24px 50px rgba(35,15,84,.3);
    border: 1px solid rgba(245,189,50,.2);
    position: sticky; top: 100px;
    overflow: hidden; isolation: isolate;
}
.edu-home .learning-panel::before {
    content: ""; position: absolute; z-index: -1;
    top: -60px; right: -60px; width: 220px; height: 220px;
    border-radius: 50%; background: rgba(245,189,50,.16); filter: blur(10px);
    pointer-events: none;
}
.edu-home .learning-panel > * { position: relative; z-index: 1; }
.edu-home .panel-top { display: flex; justify-content: space-between; align-items: flex-start; gap: 16px; }
.edu-home .panel-label, .edu-home .progress-label { font-size: 11px; font-weight: 800; letter-spacing: 1.6px; color: var(--gold); }
.edu-home .panel-top h3 { margin: 8px 0 6px; font-size: 1.5rem; color: #fff; }
.edu-home .panel-top p { margin: 0; color: rgba(255,255,255,.75); }
.edu-home .panel-icon {
    width: 56px; height: 56px; border-radius: 16px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.12); color: var(--gold);
}
.edu-home .panel-icon svg { width: 28px; height: 28px; }
.edu-home .progress-card {
    margin-top: 22px; padding: 22px; border-radius: 16px;
    background: linear-gradient(135deg, #f3e8f7 0%, #f7e9d7 100%);
    border: 1px solid rgba(245,189,50,.4);
    box-shadow: 0 10px 24px rgba(35,15,84,.18);
}
.edu-home .progress-card .progress-label { color: var(--purple); }
.edu-home .progress-card-heading { display: flex; justify-content: space-between; gap: 14px; align-items: flex-start; }
.edu-home .progress-card h4 { margin: 8px 0 0; font-size: 1.1rem; color: var(--purple-dark); line-height: 1.4; }
.edu-home .progress-emoji {
    width: 44px; height: 44px; border-radius: 12px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    background: linear-gradient(135deg, var(--gold), var(--gold-dark)); color: #fff;
}
.edu-home .progress-track { height: 9px; background: rgba(50,16,95,.12); border-radius: 99px; margin: 18px 0; overflow: hidden; }
.edu-home .progress-track span {
    display: block; height: 100%; width: 38%; border-radius: 99px;
    background: linear-gradient(90deg, var(--gold-dark), var(--gold));
    animation: eduProgress 2.4s ease-in-out infinite alternate;
}
@keyframes eduProgress { from { width: 30%; } to { width: 62%; } }
.edu-home .progress-note { margin: 0 0 18px; color: var(--muted); line-height: 1.6; font-size: .93rem; }
.edu-home .goals-button {
    display: inline-flex; align-items: center; justify-content: center;
    padding: 12px 22px; border-radius: 10px; font-weight: 800; font-size: .9rem;
    background: var(--gold); color: var(--purple-dark); text-decoration: none; transition: all .25s ease;
}
.edu-home .goals-button:hover { background: var(--gold-dark); transform: translateY(-2px); }
.edu-home .panel-bottom { display: flex; gap: 14px; align-items: flex-start; margin-top: 20px; }
.edu-home .panel-bottom-icon {
    width: 42px; height: 42px; border-radius: 12px; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    background: rgba(255,255,255,.12); color: var(--gold);
}
.edu-home .panel-bottom strong { display: block; color: #fff; margin-bottom: 4px; }
.edu-home .panel-bottom p { margin: 0; color: rgba(255,255,255,.72); font-size: .9rem; line-height: 1.55; }

/* ---------- Category + Benefit cards ---------- */
.edu-home #categories.categories-section {
    position: relative;
    overflow: hidden;
    background: linear-gradient(135deg, var(--purple-dark) 0%, var(--purple) 100%);
}
.edu-home #categories .section-kicker { color: var(--gold); }
.edu-home #categories .section-title { color: #fff; }
.edu-home #categories .section-description { color: rgba(255,255,255,.75); }
.edu-home #categories .dot-grid {
    position: absolute;
    width: 110px; height: 110px;
    background-image: radial-gradient(rgba(255,255,255,.35) 1.5px, transparent 1.5px);
    background-size: 14px 14px;
    opacity: .9;
    pointer-events: none;
}
.edu-home #categories .dot-grid.top-left { top: 28px; left: 4%; }
.edu-home #categories .dot-grid.bottom-right { bottom: 28px; right: 4%; }
.edu-home #categories .bg-blob {
    position: absolute;
    border-radius: 50%;
    background: rgba(245,189,50,.16);
    filter: blur(6px);
    pointer-events: none;
}
.edu-home #categories .bg-blob.one { width: 150px; height: 150px; bottom: -60px; left: -40px; }
.edu-home #categories .bg-blob.two { width: 90px; height: 90px; top: 20%; right: 2%; }
.edu-home #categories .category-card { box-shadow: 0 14px 34px rgba(10,4,26,.35); }
.edu-home #categories .categories-cta { position: relative; z-index: 1; text-align: center; margin-top: 36px; }

.edu-home .category-grid {
    position: relative; z-index: 1;
    display: grid; gap: 22px;
    grid-template-columns: repeat(auto-fit, minmax(230px, 1fr));
}
.edu-home .category-card {
    display: flex; flex-direction: column; align-items: center; text-align: center;
    padding: 30px 22px; background: #fff; border: 1px solid #e8ecf3;
    border-radius: 18px; text-decoration: none; color: inherit;
    box-shadow: 0 8px 24px rgba(35,15,84,.08);
    transition: transform .3s ease, box-shadow .3s ease, border-color .3s ease;
}
.edu-home .category-card:hover { transform: translateY(-6px); box-shadow: 0 18px 38px rgba(35,15,84,.16); border-color: var(--gold); }
.edu-home .category-card h3 { margin: 0 0 8px; font-size: 1.1rem; color: var(--purple); }
.edu-home .category-card p { margin: 0; font-size: .92rem; line-height: 1.6; opacity: .75; }
.edu-home .category-icon {
    width: 86px; height: 86px; margin: 0 auto 18px; border-radius: 50%; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    font-size: 2.1rem; line-height: 1; color: var(--purple);
    background: var(--light-bg);
    box-shadow: inset 0 0 0 1px var(--border);
    transition: transform .3s ease, box-shadow .3s ease;
}
.edu-home .category-icon i::before {
    background: linear-gradient(135deg, var(--purple) 0%, var(--gold) 100%);
    -webkit-background-clip: text;
    background-clip: text;
    -webkit-text-fill-color: transparent;
    color: transparent;
}
.edu-home .category-icon img { width: 100%; height: 100%; object-fit: contain; box-sizing: border-box; padding: 10px; }
.edu-home .category-icon svg {
    width: 32px; height: 32px; fill: none; stroke: currentColor;
    stroke-width: 1.8; stroke-linecap: round; stroke-linejoin: round;
}
.edu-home .category-card:hover .category-icon { transform: translateY(-4px) scale(1.06); box-shadow: inset 0 0 0 1px var(--gold); }
.edu-home #benefits.categories-section { position: relative; overflow: hidden; background: var(--light-bg); }
.edu-home #benefits .section-title { color: var(--purple); }
.edu-home #benefits .dot-grid {
    position: absolute;
    width: 110px; height: 110px;
    background-image: radial-gradient(var(--border) 1.5px, transparent 1.5px);
    background-size: 14px 14px;
    opacity: .9;
    pointer-events: none;
}
.edu-home #benefits .dot-grid.top-left { top: 28px; left: 4%; }
.edu-home #benefits .dot-grid.bottom-right { bottom: 28px; right: 4%; }
.edu-home #benefits .bg-blob {
    position: absolute;
    border-radius: 50%;
    background: var(--border);
    opacity: .5;
    filter: blur(2px);
    pointer-events: none;
}
.edu-home #benefits .bg-blob.one { width: 150px; height: 150px; bottom: -60px; left: -40px; }
.edu-home #benefits .bg-blob.two { width: 90px; height: 90px; top: 20%; right: 2%; }
.edu-home #benefits .category-grid { position: relative; z-index: 1; }
.edu-home #benefits .icon-goal    { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
.edu-home #benefits .icon-explore { background: linear-gradient(135deg, #0ea5e9, #06b6d4); }
.edu-home #benefits .icon-pace    { background: linear-gradient(135deg, #f59e0b, #f97316); }
.edu-home #benefits .icon-grow    { background: linear-gradient(135deg, #10b981, #22c55e); }
.edu-home .categories-empty { grid-column: 1 / -1; text-align: center; padding: 40px 20px; }
.edu-home #categories .categories-empty { color: #fff; }
.edu-home #categories .categories-empty p { color: rgba(255,255,255,.75); }

/* ---------- About ---------- */
.edu-home .about-grid { display: grid; grid-template-columns: 1fr 1fr; gap: 56px; align-items: center; }
.edu-home .about-image img { width: 100%; aspect-ratio: 4 / 3.3; object-fit: cover; border-radius: 22px; box-shadow: 0 22px 50px rgba(15,35,70,.18); }
.edu-home .about-content h2 { font-size: clamp(1.8rem, 3.8vw, 2.7rem); line-height: 1.2; margin: 10px 0 16px; }
.edu-home .about-content p { line-height: 1.75; margin-bottom: 14px; }
.edu-home .about-points { display: grid; grid-template-columns: repeat(2, 1fr); gap: 12px 18px; margin: 22px 0 28px; }
.edu-home .about-point { display: flex; align-items: center; gap: 10px; font-weight: 600; }
.edu-home .about-point .check {
    width: 26px; height: 26px; border-radius: 50%; flex-shrink: 0;
    display: inline-flex; align-items: center; justify-content: center;
    background: #e7f8ee; color: #16a34a;
}
.edu-home .about-point .check svg {
    width: 14px; height: 14px; fill: none; stroke: currentColor;
    stroke-width: 3; stroke-linecap: round; stroke-linejoin: round;
}

/* ---------- CTA ---------- */
.edu-home .cta-section { padding: 30px 0 80px; }
.edu-home .cta-box {
    display: flex; align-items: center; justify-content: space-between; gap: 28px;
    padding: 44px 48px; border-radius: 24px;
    background: linear-gradient(135deg, #f3e8f7 0%, #f7e9d7 100%);
    border: 1px solid rgba(245,189,50,.4);
    box-shadow: 0 24px 54px rgba(35,15,84,.18);
}
.edu-home .cta-copy h2 { margin: 0 0 8px; font-size: clamp(1.5rem, 3vw, 2.2rem); color: var(--purple-dark); line-height: 1.25; }
.edu-home .cta-copy p { margin: 0; color: var(--muted); }

/* =========================================================
   Responsive
   ========================================================= */
@media (max-width: 1024px) {
    .edu-home .section { padding: 64px 0; }
    .edu-home .learning-goals-section { padding: 30px 0 64px; }
    .edu-home .hero { padding: 60px 0; }
    .edu-home .hero-grid, .edu-home .about-grid { gap: 36px; }
    .edu-home .goals-layout { grid-template-columns: 1fr; }
    .edu-home .learning-panel { position: static; }
    .edu-home .course-track .course-card { flex-basis: calc((100% - 26px) / 2); }
}
@media (max-width: 820px) {
    .edu-home .hero-grid, .edu-home .about-grid { grid-template-columns: 1fr; }
    .edu-home .hero-content { text-align: center; align-items: center; }
    .edu-home .hero-description { margin-inline: auto; }
    .edu-home .hero-buttons { justify-content: center; }
    .edu-home .hero-feature-row { justify-content: center; }
    .edu-home .hero-image { order: -1; max-width: 460px; min-height: 0; margin: 0 auto 20px; }
    .edu-home .about-image { max-width: 560px; margin-inline: auto; }
    .edu-home .cta-box { flex-direction: column; text-align: center; padding: 38px 28px; }
}
@media (max-width: 600px) {
    .edu-home .container { padding-inline: 16px; }
    .edu-home .section { padding: 52px 0; }
    .edu-home .learning-goals-section { padding: 24px 0 52px; }
    .edu-home .hero { padding: 44px 0; }
    .edu-home .hero-image { max-width: 320px; }
    .edu-home .section-heading, .edu-home .goals-heading { margin-bottom: 32px; }
    .edu-home .course-grid { grid-template-columns: 1fr; }
    .edu-home .course-track .course-card { flex-basis: 100%; }
    .edu-home .carousel-arrow { display: none; }
    .edu-home .category-grid { grid-template-columns: repeat(2, 1fr); gap: 14px; }
    .edu-home .category-card { padding: 22px 14px; }
    .edu-home .category-icon { width: 58px; height: 58px; border-radius: 15px; }
    .edu-home .category-icon svg { width: 28px; height: 28px; }
    .edu-home .about-points { grid-template-columns: 1fr; }
    .edu-home .hero-buttons .btn, .edu-home .about-content .btn, .edu-home .cta-box .btn { width: 100%; }
    .edu-home .learning-panel { padding: 22px 18px; }
    .edu-home .goals-features { grid-template-columns: 1fr; }
    .edu-home .goal-feature { padding: 18px; }
    .edu-home .goal-icon { width: 42px; height: 42px; }
    .edu-home .cta-box { padding: 32px 20px; border-radius: 18px; }
}
@media (max-width: 380px) {
    .edu-home .category-grid { grid-template-columns: 1fr; }
    .edu-home .course-actions { flex-direction: column; align-items: stretch; }
    .edu-home .course-actions .btn { width: 100%; }
}
</style>

<main class="edu-home">

    {{-- Hero --}}
    <section class="hero">
        <div class="hero-decoration"></div>

        <div class="container hero-grid">
            <div class="hero-content">
                <div class="hero-kicker">Learn Anytime, Anywhere</div>

                <h1>Build Your Future With <span>EduSphere</span></h1>

                <p class="hero-description">
                    Get access to high-quality online courses, expert instructors, and a
                    supportive learning community. Learn new skills, advance your career,
                    and achieve your goals.
                </p>

                <div class="hero-buttons">
                    <a href="#courses" class="btn btn-gold">
                        Explore Courses {!! $icons['arrow'] !!}
                    </a>
                    <a href="#" class="btn btn-outline-light">
                        <svg viewBox="0 0 24 24" aria-hidden="true" style="width:1em;height:1em;margin-right:8px;fill:none;stroke:currentColor;stroke-width:2;stroke-linecap:round;stroke-linejoin:round;vertical-align:-2px;"><circle cx="12" cy="12" r="10"/><polygon points="10 8 16 12 10 16 10 8"/></svg>
                        Watch Video
                    </a>
                </div>

                <div class="hero-feature-row">
                    <div class="feature-item"><span class="feature-icon">{!! $icons['book'] !!}</span> Expert Instructors</div>
                    <div class="feature-item"><span class="feature-icon">{!! $icons['shield'] !!}</span> Lifetime Access</div>
                    <div class="feature-item"><span class="feature-icon">{!! $icons['group'] !!}</span> Learn at Your Pace</div>
                </div>
            </div>

            <div class="hero-image">
              <div class="hero-visual-card">
                <img
                    src="{{ asset('images/background.png') }}"
                    alt="Student learning online with EduSphere — video lectures, quizzes and certificates"
                    class="hero-visual"
                    fetchpriority="high"
                >
              </div>
            </div>
        </div>
    </section>

    {{-- Featured Courses --}}
    <section class="section featured-section" id="courses">
        <div class="container">
            <div class="section-heading">
                <div class="section-kicker">Find a Course</div>
                <h2 class="section-title">Featured Courses</h2>
                <div class="section-line"></div>
                <p class="section-description">
                    Explore courses, discover new interests and develop skills
                    that help you reach your goals.
                </p>
            </div>

            <div class="course-carousel" data-course-carousel>
                <div class="course-viewport">
                    <div class="course-track" data-course-track>
                @forelse ($featuredCourses as $course)
                    <article class="course-card">
                        <div class="course-image">
                            @if ($course->thumbnail)
                                <img
                                    src="{{ $course->thumbnailUrl() }}"
                                    alt="{{ $course->title }}"
                                    loading="lazy"
                                >
                            @else
                                <img
                                    src="{{ $fallbackImages[$loop->index % 3] }}"
                                    alt="{{ $course->title }}"
                                    loading="lazy"
                                >
                            @endif

                            @if ($course->is_top_course)
                                <span class="course-label">{!! $icons['star'] !!} Featured</span>
                            @elseif ($course->is_free)
                                <span class="course-label">Free Course</span>
                            @endif
                        </div>

                        <div class="course-content">
                            <div class="course-category">
                                {{ $course->category?->name ?? 'General Course' }}
                            </div>

                            <h3 class="course-title">{{ $course->title }}</h3>

                            <p class="course-description">
                                {{ \Illuminate\Support\Str::limit(
                                    $course->short_description ?: ($course->description ?: 'Explore this course and develop useful skills through structured learning.'),
                                    125
                                ) }}
                            </p>

                            <div class="course-meta">
                                <span>{!! $icons['user'] !!} {{ $course->instructor?->name ?? 'EduSphere Team' }}</span>
                                <span>{!! $icons['level'] !!} {{ ucfirst($course->level ?? 'All Levels') }}</span>
                            </div>

                            <div class="course-actions">
                                <div class="course-price">
                                    @if ($course->is_free)
                                        Free
                                    @else
                                        <span>₹{{ number_format($course->effectivePrice(), 0) }}</span>

                                        @if ($course->hasDiscount())
                                            <span class="course-old-price">
                                                ₹{{ number_format((float) $course->price, 0) }}
                                            </span>
                                        @endif
                                    @endif
                                </div>

                                <a href="{{ route('register') }}" class="btn btn-gold">
                                    MORE {!! $icons['arrow'] !!}
                                </a>
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="empty-state">
                        <h3>Courses Coming Soon</h3>
                        <p>Active courses will appear here once published by the administrator.</p>
                    </div>
                @endforelse
                    </div>
                </div>

                @if ($featuredCourses->count() > 3)
                    <button type="button" class="carousel-arrow prev" data-carousel-prev aria-label="Previous courses">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="15 18 9 12 15 6" /></svg>
                    </button>
                    <button type="button" class="carousel-arrow next" data-carousel-next aria-label="Next courses">
                        <svg viewBox="0 0 24 24" aria-hidden="true"><polyline points="9 18 15 12 9 6" /></svg>
                    </button>
                @endif
            </div>

            @if ($featuredCourses->count() > 0)
                <div class="featured-cta">
                    <a href="{{ route('courses.index') }}" class="btn btn-gold">EXPLORE COURSES {!! $icons['arrow'] !!}</a>
                </div>
            @endif
        </div>
    </section>

    {{-- Learning goals --}}
    <section class="learning-goals-section" id="learning-goals">
        <div class="container">
            <div class="goals-heading">
                <span class="goals-kicker">LEARN · PRACTICE · ACHIEVE</span>
                <h2>Learning focused on your goals</h2>
                <div class="section-line"></div>
                <p>
                    Build practical skills, prepare for certifications and
                    keep moving forward on your learning journey.
                </p>
            </div>

            <div class="goals-layout">
                <div class="goals-features">
                    <article class="goal-feature active">
                        <div class="goal-feature-title">
                            <span class="goal-icon">{!! $icons['code'] !!}</span>
                            <h3>Hands-on training</h3>
                        </div>
                        <p>
                            Upskill effectively with practical coding exercises,
                            practice tests, quizzes and real-world learning activities.
                        </p>
                        <a href="#courses">Explore courses {!! $icons['arrow'] !!}</a>
                    </article>

                    <article class="goal-feature">
                        <div class="goal-feature-title">
                            <span class="goal-icon">{!! $icons['award'] !!}</span>
                            <h3>Certification prep</h3>
                        </div>
                        <p>
                            Prepare for industry-recognized certifications by
                            practising concepts and solving real-world challenges.
                        </p>
                        <a href="#courses">Explore courses {!! $icons['arrow'] !!}</a>
                    </article>

                    <article class="goal-feature">
                        <div class="goal-feature-title">
                            <span class="goal-icon">{!! $icons['chart'] !!}</span>
                            <h3>Insights and analytics <span class="plan-label">Coming soon</span></h3>
                        </div>
                        <p>
                            Track your learning progress, identify improvement areas
                            and stay focused on your goals.
                        </p>
                        <a href="{{ route('register') }}">Get started {!! $icons['arrow'] !!}</a>
                    </article>

                    <article class="goal-feature">
                        <div class="goal-feature-title">
                            <span class="goal-icon">{!! $icons['sliders'] !!}</span>
                            <h3>Customizable content <span class="plan-label">Coming soon</span></h3>
                        </div>
                        <p>
                            Discover flexible learning opportunities and build
                            a learning journey around your interests.
                        </p>
                        <a href="#categories">Explore categories {!! $icons['arrow'] !!}</a>
                    </article>
                </div>

                <div class="learning-panel">
                    <div class="panel-top">
                        <div>
                            <span class="panel-label">YOUR LEARNING SPACE</span>
                            <h3>Keep moving forward</h3>
                            <p>Small steps today. Stronger skills tomorrow.</p>
                        </div>
                        <div class="panel-icon">{!! $icons['cap'] !!}</div>
                    </div>

                    <div class="progress-card">
                        <div class="progress-card-heading">
                            <div>
                                <span class="progress-label">YOUR LEARNING JOURNEY</span>
                                <h4>Ready for your next achievement?</h4>
                            </div>
                            <span class="progress-emoji">{!! $icons['rocket'] !!}</span>
                        </div>

                        <div class="progress-track"><span></span></div>

                        <p class="progress-note">
                            Create an account and start exploring courses
                            that match your goals.
                        </p>

                        <a href="{{ route('register') }}" class="goals-button">
                            Start learning {!! $icons['arrow'] !!}
                        </a>
                    </div>

                    <div class="panel-bottom">
                        <div class="panel-bottom-icon">{!! $icons['bulb'] !!}</div>
                        <div>
                            <strong>Keep learning, keep growing</strong>
                            <p>
                                Explore new subjects and build your skills
                                at your own pace.
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- Categories --}}
    <section class="section categories-section" id="categories">
        <span class="dot-grid top-left" aria-hidden="true"></span>
        <span class="dot-grid bottom-right" aria-hidden="true"></span>
        <span class="bg-blob one" aria-hidden="true"></span>
        <span class="bg-blob two" aria-hidden="true"></span>

        <div class="container">
            <div class="section-heading">
                <div class="section-kicker">Discover Your Interests</div>
                <h2 class="section-title">Explore Categories</h2>
                <div class="section-line"></div>
                <p class="section-description">
                    Find subjects that interest you and discover your next
                    learning opportunity.
                </p>
            </div>

            <div class="category-grid">
                @forelse ($categories->sortByDesc(fn ($c) => (int) (bool) $c->thumbnail)->take(4) as $category)
                    <a href="#courses" class="category-card">
                        <div class="category-icon">
                            @if ($category->thumbnail)
                                <img
                                    src="{{ $category->thumbnailUrl() }}"
                                    alt="{{ $category->name }}"
                                    loading="lazy"
                                >
                            @elseif ($category->icon && \Illuminate\Support\Str::contains($category->icon, ['fa-', 'bi-']))
                                <i class="{{ $category->icon }}" aria-hidden="true"></i>
                            @elseif ($category->icon)
                                <span>{{ $category->icon }}</span>
                            @else
                                {!! $icons['book'] !!}
                            @endif
                        </div>

                        <h3>{{ $category->name }}</h3>
                        <p>{{ $category->children_count }} subcategories</p>
                    </a>
                @empty
                    <div class="categories-empty">
                        <h3>No Categories Yet</h3>
                        <p>Categories will appear when added by the administrator.</p>
                    </div>
                @endforelse
            </div>

            @if ($categories->count() > 4)
                <div class="categories-cta">
                    <a href="{{ route('categories.index') }}" class="btn btn-outline-light">
                        Show All Categories {!! $icons['arrow'] !!}
                    </a>
                </div>
            @endif
        </div>
    </section>

    {{-- About --}}
    <section class="section" id="about">
        <div class="container about-grid">
            <div class="about-image">
                <img
                    src="https://images.unsplash.com/photo-1523580846011-d3a5bc25702b?auto=format&fit=crop&w=1100&q=85"
                    alt="Students learning together"
                    loading="lazy"
                >
            </div>

            <div class="about-content">
                <div class="section-kicker">ABOUT EDUSPHERE</div>
                <h2>Learn Today.<br>Grow Every Day.</h2>

                <p>
                    EduSphere helps learners discover subjects, explore courses
                    and build knowledge around their goals.
                </p>

                <p>
                    Whether you are starting something new or developing
                    existing skills, your next learning opportunity starts here.
                </p>

                <div class="about-points">
                    <div class="about-point"><span class="check">{!! $icons['check'] !!}</span> Flexible learning</div>
                    <div class="about-point"><span class="check">{!! $icons['check'] !!}</span> Multiple subjects</div>
                    <div class="about-point"><span class="check">{!! $icons['check'] !!}</span> Clear course details</div>
                    <div class="about-point"><span class="check">{!! $icons['check'] !!}</span> Learn at your pace</div>
                </div>

                <a href="{{ route('register') }}" class="btn btn-gold">
                    START LEARNING {!! $icons['arrow'] !!}
                </a>
            </div>
        </div>
    </section>

    {{-- Benefits --}}
    <section class="section categories-section" id="benefits">
        <span class="dot-grid top-left" aria-hidden="true"></span>
        <span class="dot-grid bottom-right" aria-hidden="true"></span>
        <span class="bg-blob one" aria-hidden="true"></span>
        <span class="bg-blob two" aria-hidden="true"></span>

        <div class="container">
            <div class="section-heading">
                <div class="section-kicker">THE EDUSPHERE EXPERIENCE</div>
                <h2 class="section-title">Why Choose EduSphere?</h2>
                <div class="section-line"></div>
            </div>

            <div class="category-grid">
                <article class="category-card">
                    <div class="category-icon icon-goal" aria-hidden="true">{!! $icons['target'] !!}</div>
                    <h3>Goal-Oriented Learning</h3>
                    <p>Choose courses that match your interests and goals.</p>
                </article>

                <article class="category-card">
                    <div class="category-icon icon-explore" aria-hidden="true">{!! $icons['book'] !!}</div>
                    <h3>Explore More</h3>
                    <p>Discover different subjects and new ideas.</p>
                </article>

                <article class="category-card">
                    <div class="category-icon icon-pace" aria-hidden="true">{!! $icons['clock'] !!}</div>
                    <h3>Your Own Pace</h3>
                    <p>Build a learning routine that works for you.</p>
                </article>

                <article class="category-card">
                    <div class="category-icon icon-grow" aria-hidden="true">{!! $icons['rocket'] !!}</div>
                    <h3>Keep Growing</h3>
                    <p>Continue developing your knowledge and skills.</p>
                </article>
            </div>
        </div>
    </section>

    {{-- CTA --}}
    <section class="cta-section">
        <div class="container">
            <div class="cta-box">
                <div class="cta-copy">
                    <h2>Your Next Chapter Starts Here.</h2>
                    <p>Join EduSphere and discover courses that inspire you to grow.</p>
                </div>
                <a href="{{ route('register') }}" class="btn btn-gold">JOIN NOW {!! $icons['arrow'] !!}</a>
            </div>
        </div>
    </section>

</main>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var carousel = document.querySelector('[data-course-carousel]');
    if (!carousel) return;

    var track = carousel.querySelector('[data-course-track]');
    var cards = Array.prototype.slice.call(track.querySelectorAll('.course-card'));
    var prevBtn = carousel.querySelector('[data-carousel-prev]');
    var nextBtn = carousel.querySelector('[data-carousel-next]');
    if (!cards.length) return;

    var index = 0;
    var autoTimer = null;
    var gap = 26;

    function cardsPerView() {
        var w = window.innerWidth;
        if (w <= 600) return 1;
        if (w <= 1024) return 2;
        return 3;
    }

    function maxIndex() {
        return Math.max(0, cards.length - cardsPerView());
    }

    function update() {
        var perView = cardsPerView();
        var cardWidth = cards[0].getBoundingClientRect().width;
        var offset = index * (cardWidth + gap);
        track.style.transform = 'translateX(-' + offset + 'px)';

        var disableArrows = cards.length <= perView;
        if (prevBtn) prevBtn.style.display = disableArrows ? 'none' : 'flex';
        if (nextBtn) nextBtn.style.display = disableArrows ? 'none' : 'flex';
    }

    function goNext() {
        index = index >= maxIndex() ? 0 : index + 1;
        update();
    }

    function goPrev() {
        index = index <= 0 ? maxIndex() : index - 1;
        update();
    }

    function startAuto() {
        stopAuto();
        if (cards.length > cardsPerView()) {
            autoTimer = setInterval(goNext, 4000);
        }
    }

    function stopAuto() {
        if (autoTimer) { clearInterval(autoTimer); autoTimer = null; }
    }

    if (nextBtn) nextBtn.addEventListener('click', function () { goNext(); startAuto(); });
    if (prevBtn) prevBtn.addEventListener('click', function () { goPrev(); startAuto(); });

    carousel.addEventListener('mouseenter', stopAuto);
    carousel.addEventListener('mouseleave', startAuto);

    var resizeTimer;
    window.addEventListener('resize', function () {
        clearTimeout(resizeTimer);
        resizeTimer = setTimeout(function () {
            index = Math.min(index, maxIndex());
            update();
        }, 150);
    });

    update();
    startAuto();
});
</script>
@endsection