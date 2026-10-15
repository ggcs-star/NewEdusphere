@php
    // Cart + login state (computed once, used in the header)
    $headerCartIds = session()->get('cart_course_ids', []);

    if (!is_array($headerCartIds)) {
        $headerCartIds = [];
    }

    $headerCartIds   = array_values(array_unique(array_map('intval', $headerCartIds)));
    $headerCartCount = count($headerCartIds);

    $isUserLoggedIn = session()->has('user_token') && session()->has('user');

    $headerUser = $isUserLoggedIn ? current_admin_user() : null;

    $isAdmin = $headerUser && $headerUser->role === 'admin';

    $isInstructor = $headerUser
        && (bool) $headerUser->is_instructor
        && ! $isAdmin;

    if ($isAdmin) {
        $headerDashboardUrl = route('admin.dashboard');
        $headerDashboardLabel = 'Admin Dashboard';
    } elseif ($isInstructor) {
        $headerDashboardUrl = route('instructor.dashboard');
        $headerDashboardLabel = 'Instructor Dashboard';
    } else {
        $headerDashboardUrl = route('profile.edit');
        $headerDashboardLabel = 'My Profile';
    }

    // Active navigation state
    $navItems = [
        ['label' => 'Home',        'url' => route('home'),          'active' => request()->routeIs('home')],
        ['label' => 'Courses',     'url' => url('/courses'),        'active' => request()->is('courses', 'courses/*')],
        ['label' => 'About Us',    'url' => url('/about'),          'active' => request()->is('about', 'about/*')],
        ['label' => 'Categories',  'url' => url('/categories'),     'active' => request()->is('categories', 'categories/*')],
        ['label' => 'My Learning', 'url' => url('/my-courses'),     'active' => request()->is('my-courses', 'my-courses/*')],
        ['label' => 'Contact Us',  'url' => url('/contact'),        'active' => request()->is('contact', 'contact/*')],
    ];
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    {{-- viewport-fit=cover => notch / safe-area devices me bhi full screen fit --}}
    <meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', 'Learn, Grow, Succeed') · EduSphere</title>

    @stack('meta')

    <meta name="description" content="Explore courses, develop new skills and build your future with EduSphere.">
    <meta name="theme-color" content="#32105f">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <style>
        :root {
            --purple: #32105f;
            --purple-dark: #250b49;
            --purple-light: #4a2677;
            --gold: #f5bd32;
            --gold-dark: #d8a017;
            --white: #fff;
            --text: #211735;
            --muted: #777083;
            --border: #e9e3f0;
            --light-bg: #faf8fd;
            --safe-left: env(safe-area-inset-left, 0px);
            --safe-right: env(safe-area-inset-right, 0px);
            --safe-bottom: env(safe-area-inset-bottom, 0px);
        }

        *, *::before, *::after { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
            -webkit-text-size-adjust: 100%;
            text-size-adjust: 100%;
            max-width: 100%;
        }

        body {
            margin: 0;
            min-width: 320px;
            max-width: 100%;
            min-height: 100vh;
            min-height: 100dvh;
            display: flex;
            flex-direction: column;
            overflow-x: clip; /* horizontal scroll kabhi nahi aayega (sticky header safe) */
            padding-left: var(--safe-left);
            padding-right: var(--safe-right);
            font-family: "DM Sans", sans-serif;
            color: var(--text);
            background: white;
            line-height: 1.6;
            -webkit-tap-highlight-color: transparent;
        }

        /* Footer hamesha bottom me, chhota content ho tab bhi */
        body > main, body > .page-content { flex: 1 0 auto; }
        .footer { margin-top: auto; }

        h1, h2, h3, h4, p { margin-top: 0; }
        h1, h2, h3, h4 { font-family: "Plus Jakarta Sans", sans-serif; overflow-wrap: anywhere; }

        a { color: inherit; text-decoration: none; }
        img, video, svg, iframe { max-width: 100%; }
        img, video { display: block; height: auto; }
        table { max-width: 100%; }
        button, input, select, textarea { font: inherit; max-width: 100%; }
        pre, code { max-width: 100%; overflow-x: auto; }

        .container { width: min(1200px, calc(100% - 44px)); margin: 0 auto; }

        .btn {
            min-height: 43px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 9px;
            padding: 11px 21px;
            border: 1px solid transparent;
            border-radius: 3px;
            font-size: 12px;
            font-weight: 800;
            cursor: pointer;
            transition: background .2s, transform .2s;
        }

        .btn:hover { transform: translateY(-2px); }
        .btn-gold { color: var(--purple-dark); background: var(--gold); }
        .btn-gold:hover { background: var(--gold-dark); }
        .btn-outline-light { color: white; background: transparent; border-color: rgba(255,255,255,.45); }

        /* Top bar */
        .topbar { color: white; background: var(--purple-dark); font-size: 12px; }

        .topbar-inner {
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .contact-info, .social-links { display: flex; align-items: center; flex-wrap: wrap; gap: 18px; }
        .contact-info a { color: #f2eafc; }
        .contact-info a:hover, .social-links a:hover { color: var(--gold); }
        .social-links a { font-weight: 700; }

        /* Navbar */
        .main-header {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255,255,255,.97);
            box-shadow: 0 3px 15px rgba(37,11,73,.07);
        }

        .navbar {
            min-height: 73px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .brand { display: inline-flex; align-items: center; gap: 9px; flex-shrink: 0; min-width: 0; }
        .brand-symbol { width: 43px; height: 43px; display: grid; place-items: center; font-size: 30px; }

        .brand-name {
            display: block;
            color: var(--purple);
            font: 800 24px/1.15 "Plus Jakarta Sans", sans-serif;
            letter-spacing: -1px;
        }

        .brand-name span { color: var(--gold-dark); }

        .brand-tagline {
            display: block;
            margin-top: 4px;
            color: #655a76;
            font-size: 8px;
            font-weight: 800;
            letter-spacing: 1px;
            white-space: nowrap;
        }

        .nav-links { display: flex; align-items: center; gap: 22px; min-width: 0; }

        .nav-links a {
            position: relative;
            padding: 26px 0;
            color: #292137;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .nav-links a:hover, .nav-links a.active { color: var(--gold-dark); }

        .nav-links a.active::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 17px;
            left: 0;
            height: 3px;
            background: var(--gold);
        }

        /* Guest login links inside the mobile menu (hidden on desktop) */
        .nav-auth-mobile { display: none; }

        .nav-actions { display: flex; align-items: center; gap: 10px; flex-shrink: 0; }
        .guest-actions { display: flex; align-items: center; gap: 10px; }

        /* Header cart icon and item count */
        .header-cart {
            position: relative;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
            width: 43px;
            height: 43px;
            color: var(--purple);
            background: #f7f3fc;
            border: 1px solid var(--border);
            border-radius: 10px;
            transition: background .2s, transform .2s;
        }

        .header-cart:hover { background: #eee5fa; transform: translateY(-1px); }

        .header-cart-count {
            position: absolute;
            top: -6px;
            right: -6px;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 19px;
            height: 19px;
            padding: 0 4px;
            color: #fff;
            background: #e53945;
            border: 2px solid #fff;
            border-radius: 999px;
            font-size: 10px;
            font-weight: 800;
            line-height: 1;
        }

        .login-link { font-size: 12px; font-weight: 700; white-space: nowrap; }
        .login-link:hover { color: var(--purple); }
        .nav-actions .btn-gold:active { background: var(--gold-dark); box-shadow: inset 0 0 0 2px var(--purple-dark); }
        .nav-divider { color: #d7d0e0; }

        .menu-toggle {
            display: none;
            flex-shrink: 0;
            width: 43px;
            height: 43px;
            padding: 0;
            border: 1px solid var(--border);
            border-radius: 8px;
            color: var(--purple);
            background: white;
            font-size: 22px;
            line-height: 1;
            cursor: pointer;
        }

        /* Hero */
        .hero { position: relative; overflow: hidden; color: white; background: var(--purple); }

        .hero-grid { min-height: 390px; display: grid; grid-template-columns: 1.12fr .88fr; }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: 45px 25px 45px 0;
            min-width: 0;
        }

        .hero-content::before {
            content: "";
            position: absolute;
            z-index: -1;
            width: 140px;
            height: 140px;
            top: -95px;
            right: 15%;
            transform: rotate(45deg);
            border: 28px solid rgba(123,83,193,.3);
        }

        .hero-kicker {
            margin-bottom: 17px;
            color: var(--gold);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
            text-transform: uppercase;
        }

        .hero-kicker::after { content: ""; display: block; width: 48px; height: 3px; margin-top: 10px; background: var(--gold); }

        .hero h1 {
            max-width: 600px;
            margin-bottom: 16px;
            font-size: clamp(32px, 4.2vw, 56px);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -1.8px;
        }

        .hero h1 span { color: var(--gold); }

        .hero-description { max-width: 490px; margin-bottom: 25px; color: #e6dcf3; font-size: 15px; line-height: 1.8; }

        .hero-buttons { display: flex; flex-wrap: wrap; gap: 12px; }
        .hero-buttons .btn { min-width: 145px; min-height: 47px; }

        .hero-image {
            position: relative;
            min-height: 390px;
            background: var(--gold);
            clip-path: polygon(23% 0,100% 0,100% 100%,23% 100%,0 50%);
        }

        .hero-image img {
            position: absolute;
            inset: 0;
            width: 100%;
            height: 100%;
            object-fit: cover;
            object-position: center;
            clip-path: polygon(25% 0,100% 0,100% 100%,25% 100%,2% 50%);
        }

        .hero-decoration {
            position: absolute;
            bottom: -80px;
            left: 5%;
            width: 190px;
            height: 190px;
            transform: rotate(45deg);
            border: 25px solid rgba(123,83,193,.2);
            pointer-events: none;
        }

        /* Shared section headings */
        .section { padding: 72px 0; }
        .section-heading { margin-bottom: 35px; text-align: center; }

        .section-kicker { margin-bottom: 8px; color: var(--gold-dark); font-size: 12px; font-weight: 800; letter-spacing: 2px; }

        .section-title { margin-bottom: 0; font-size: clamp(26px,3vw,39px); font-weight: 800; line-height: 1.25; }

        .section-line { width: 120px; height: 4px; margin: 17px auto 0; background: var(--gold); }

        .section-description { max-width: 650px; margin: 15px auto 0; color: var(--muted); font-size: 14px; line-height: 1.8; }

        /* Featured courses (legacy home blocks, kept for other pages) */
        .featured-section {
            background: #fff;
        }

        .featured-section .section-title { color: var(--purple); }
        .featured-section .section-kicker { color: var(--purple); }
        .featured-section .section-description { color: var(--muted); }

        .course-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 25px; }

        .course-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.07);
            background: #4a2d76;
            transition: transform .25s,box-shadow .25s;
        }

        .course-card:hover { transform: translateY(-5px); box-shadow: 0 15px 35px rgba(0,0,0,.22); }

        .course-image { position: relative; height: 205px; margin: 16px 16px 0; overflow: hidden; background: #5a3b83; }
        .course-image img { width: 100%; height: 100%; object-fit: cover; transition: transform .35s; }
        .course-card:hover .course-image img { transform: scale(1.05); }

        .course-label {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 5px 9px;
            color: var(--purple-dark);
            background: var(--gold);
            font-size: 10px;
            font-weight: 800;
            text-transform: uppercase;
        }

        .course-content { padding: 16px 20px 21px; }

        .course-category {
            margin-bottom: 7px;
            color: var(--purple);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .course-title {
            min-height: 27px;
            margin-bottom: 10px;
            color: var(--purple);
            font-size: 19px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .course-description {
            min-height: 48px;
            margin-bottom: 16px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.8;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            color: var(--muted);
            font-size: 11px;
        }

        .course-price { color: var(--purple); font-size: 16px; font-weight: 800; }

        .course-old-price {
            margin-left: 5px;
            color: #9b96a8;
            font-size: 11px;
            font-weight: 400;
            text-decoration: line-through;
        }

        .course-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            padding-top: 14px;
            border-top: 1px solid var(--border);
        }

        .course-actions .btn {
            min-height: 37px;
            padding: 9px 17px;
            font-size: 11px;
            letter-spacing: .5px;
        }

        .empty-state {
            grid-column: 1/-1;
            padding: 40px 20px;
            border: 1px dashed var(--border);
            color: var(--muted);
            text-align: center;
        }

        .empty-state h3 { margin-bottom: 8px; color: var(--purple); }
        .empty-state p { margin-bottom: 0; font-size: 13px; }

        /* Learning goals (legacy) */
        .learning-goals-section { padding: 75px 0; background: #f8f6fc; }
        .goals-heading { margin-bottom: 35px; }
        .goals-kicker { color: var(--gold-dark); font-size: 11px; font-weight: 800; letter-spacing: 2px; }
        .goals-heading h2 { margin: 10px 0; color: var(--purple); font-size: clamp(25px,3vw,36px); font-weight: 800; letter-spacing: -.8px; }
        .goals-heading > p { max-width: 650px; margin-bottom: 0; color: var(--muted); font-size: 14px; line-height: 1.8; }
        .goals-layout { display: grid; grid-template-columns: 1fr 1fr; align-items: stretch; gap: 30px; }
        .goals-features { display: flex; flex-direction: column; gap: 15px; min-width: 0; }

        .goal-feature {
            padding: 21px 22px;
            border: 1px solid #e6e0ee;
            border-radius: 10px;
            background: white;
            transition: border-color .2s,transform .2s,box-shadow .2s;
        }

        .goal-feature:hover { transform: translateY(-2px); border-color: #d6b34d; box-shadow: 0 8px 22px rgba(50,16,95,.06); }
        .goal-feature.active { border: 1px solid #6546e8; box-shadow: inset 4px 0 #6546e8; }
        .goal-feature-title { display: flex; align-items: center; flex-wrap: wrap; gap: 11px; margin-bottom: 8px; }
        .goal-icon { width: 29px; height: 29px; display: grid; place-items: center; border-radius: 8px; background: #f1eaff; font-size: 16px; }
        .goal-feature h3 { margin: 0; color: var(--purple); font-size: 16px; }
        .goal-feature > p { margin: 0 0 10px; color: var(--muted); font-size: 13px; line-height: 1.8; }
        .goal-feature > a { color: #6546e8; font-size: 12px; font-weight: 800; }
        .goal-feature > a:hover { color: var(--purple); }

        .plan-label {
            display: inline-block;
            padding: 4px 7px;
            border-radius: 4px;
            background: #eee6ff;
            color: #6546e8;
            font-family: "DM Sans",sans-serif;
            font-size: 9px;
            vertical-align: middle;
        }

        .learning-panel {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: 25px;
            min-width: 0;
            padding: 29px;
            border: 1px solid #ece6f3;
            border-radius: 16px;
            background: white;
            box-shadow: 0 12px 35px rgba(37,11,73,.06);
        }

        .panel-top { display: flex; align-items: flex-start; justify-content: space-between; gap: 15px; }
        .panel-label, .progress-label { color: #bd8d12; font-size: 10px; font-weight: 800; letter-spacing: 1.2px; }
        .panel-top h3 { margin: 8px 0; color: var(--purple); font-size: 22px; }
        .panel-top p { margin-bottom: 0; color: var(--muted); font-size: 13px; line-height: 1.8; }
        .panel-icon { width: 49px; height: 49px; display: grid; place-items: center; flex-shrink: 0; border-radius: 13px; background: #f9edc9; font-size: 24px; }
        .progress-card { padding: 24px; border: 1px solid #e9e0f5; border-radius: 12px; background: linear-gradient(145deg,#fbf9ff,#fffdf7); }
        .progress-card-heading { display: flex; align-items: center; justify-content: space-between; gap: 10px; margin-bottom: 20px; }
        .progress-card h4 { margin: 8px 0 0; color: var(--purple); font-size: 16px; line-height: 1.5; }
        .progress-emoji { font-size: 25px; }
        .progress-track { height: 8px; overflow: hidden; border-radius: 20px; background: #e9e2f4; }
        .progress-track span { display: block; width: 0; height: 100%; background: var(--gold); }
        .progress-note { margin: 13px 0 20px; color: var(--muted); font-size: 12px; line-height: 1.8; }

        .goals-button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
            min-height: 42px;
            padding: 10px 17px;
            border-radius: 3px;
            color: var(--purple);
            background: var(--gold);
            font-size: 12px;
            font-weight: 800;
            transition: background .2s,transform .2s;
        }

        .goals-button:hover { transform: translateY(-2px); background: var(--gold-dark); }
        .panel-bottom { display: flex; align-items: flex-start; gap: 13px; padding-top: 20px; border-top: 1px solid #eee8f5; }
        .panel-bottom-icon { width: 38px; height: 38px; display: grid; place-items: center; flex-shrink: 0; border-radius: 10px; background: #f9edc9; font-size: 19px; }
        .panel-bottom strong { color: var(--purple); font-size: 13px; }
        .panel-bottom p { margin: 5px 0 0; color: var(--muted); font-size: 12px; line-height: 1.8; }

        /* Categories */
        .categories-section { background: var(--light-bg); }
        .category-grid { display: grid; grid-template-columns: repeat(4,minmax(0,1fr)); gap: 20px; }

        .category-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            min-width: 0;
            padding: 26px 16px;
            border: 1px solid var(--border);
            background: white;
            text-align: center;
            transition: border-color .2s,transform .2s,box-shadow .2s;
        }

        .category-card:hover { transform: translateY(-4px); border-color: var(--gold); box-shadow: 0 12px 25px rgba(50,16,95,.07); }

        .category-icon {
            width: 68px;
            height: 68px;
            display: grid;
            place-items: center;
            margin-bottom: 15px;
            overflow: hidden;
            color: var(--purple);
            background: #f3ecfc;
            font-size: 27px;
        }

        .category-icon img { width: 100%; height: 100%; object-fit: cover; }
        .category-card h3 { margin-bottom: 6px; font-size: 15px; }
        .category-card p { margin-bottom: 0; color: var(--muted); font-size: 11px; }

        /* About */
        .about-grid { display: grid; grid-template-columns: 1fr 1fr; align-items: center; gap: 55px; }
        .about-grid > * { min-width: 0; }
        .about-image { min-height: 340px; overflow: hidden; background: #f0e8fb; }
        .about-image img { width: 100%; height: 340px; object-fit: cover; }
        .about-content .section-kicker { text-align: left; }
        .about-content h2 { margin-bottom: 16px; color: var(--purple); font-size: clamp(25px,3vw,37px); line-height: 1.3; }
        .about-content > p { color: var(--muted); font-size: 14px; line-height: 1.9; }
        .about-points { display: grid; grid-template-columns: 1fr 1fr; gap: 13px; margin: 23px 0; }
        .about-point { display: flex; align-items: center; gap: 9px; font-size: 12px; font-weight: 700; }
        .check { width: 23px; height: 23px; display: grid; place-items: center; flex-shrink: 0; border-radius: 50%; color: var(--purple); background: #fdf0c9; }

        /* CTA */
        .cta-section { padding: 0 0 75px; }

        .cta-box {
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 30px;
            padding: 42px;
            overflow: hidden;
            color: white;
            background: var(--purple);
        }

        .cta-box::after {
            content: "";
            position: absolute;
            top: -150px;
            right: 15%;
            width: 230px;
            height: 230px;
            transform: rotate(45deg);
            border: 25px solid rgba(255,255,255,.06);
        }

        .cta-copy { position: relative; z-index: 1; min-width: 0; }
        .cta-copy h2 { margin-bottom: 10px; font-size: clamp(22px,3vw,32px); }
        .cta-copy p { margin-bottom: 0; color: #e1d5f1; font-size: 13px; }
        .cta-box .btn { position: relative; z-index: 1; flex-shrink: 0; }

        /* Footer */
        .footer { color: #e9dff4; background: #21083f; }
        .footer-main { display: grid; grid-template-columns: 1.5fr 1fr 1fr 1fr; gap: 35px; padding: 48px 0 35px; }
        .footer-main > * { min-width: 0; }
        .footer .brand-name { color: white; }
        .footer .brand-tagline { color: #d2c0e6; }
        .footer-about p { max-width: 300px; margin: 17px 0 0; color: #c5b4d8; font-size: 12px; line-height: 1.9; }
        .footer-col h4 { margin-bottom: 16px; color: var(--gold); font-size: 13px; }
        .footer-col a { display: block; width: fit-content; max-width: 100%; margin-bottom: 11px; color: #d1c2e1; font-size: 12px; overflow-wrap: anywhere; }
        .footer-col a:hover { color: var(--gold); }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 8px 15px;
            padding: 18px 0;
            padding-bottom: calc(18px + var(--safe-bottom));
            border-top: 1px solid rgba(255,255,255,.12);
            color: #b9a8ce;
            font-size: 11px;
        }

        /* Shared inner-page components */
        .page-hero { background: linear-gradient(120deg,var(--purple-dark),var(--purple-light)); color: #fff; padding: 54px 0; }
        .page-hero h1 { font-size: clamp(28px,4vw,44px); line-height: 1.15; margin-bottom: 12px; }
        .page-hero p { max-width: 720px; color: #e9def7; margin: 0; }
        .breadcrumbs { font-size: 12px; color: #e5d8f4; margin-bottom: 16px; overflow-wrap: anywhere; }
        .section-pad { padding: 58px 0; }
        .section-head { display: flex; justify-content: space-between; align-items: end; gap: 18px; margin-bottom: 26px; }
        .section-head h2 { margin-bottom: 6px; font-size: clamp(22px,3vw,34px); }
        .section-head p { margin: 0; color: var(--muted); }
        .content-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 22px; }
        .course-card, .panel, .info-card { border: 1px solid var(--border); border-radius: 12px; background: #fff; overflow: hidden; box-shadow: 0 8px 24px rgba(37,11,73,.045); }
        .course-thumb { height: 180px; background: linear-gradient(135deg,#eee4fb,#f8e8b8); display: grid; place-items: center; font-size: 48px; }
        .course-body { padding: 20px; }
        .eyebrow { color: var(--gold-dark); font-size: 11px; font-weight: 800; letter-spacing: 1px; text-transform: uppercase; }
        .course-body h3 { font-size: 18px; line-height: 1.35; margin: 8px 0; }
        .muted { color: var(--muted); }
        .meta-row { display: flex; justify-content: space-between; flex-wrap: wrap; gap: 8px 12px; color: var(--muted); font-size: 12px; margin: 14px 0; }
        .price { font-weight: 800; color: var(--purple); font-size: 20px; }
        .page-layout { display: grid; grid-template-columns: 260px minmax(0,1fr); gap: 28px; }
        .filter-panel { padding: 22px; align-self: start; }
        .filter-panel h3 { margin-bottom: 16px; }
        .field { display: flex; flex-direction: column; gap: 7px; margin-bottom: 16px; }
        .field label { font-size: 12px; font-weight: 800; }
        .field input, .field select, .field textarea, .form-control { width: 100%; border: 1px solid #ded5e9; border-radius: 6px; padding: 12px 13px; background: #fff; color: var(--text); outline: none; }
        .field input:focus, .field select:focus, .field textarea:focus, .form-control:focus { border-color: var(--purple-light); box-shadow: 0 0 0 3px rgba(74,38,119,.1); }
        .btn-purple { color: #fff; background: var(--purple); }
        .btn-purple:hover { background: var(--purple-light); }
        .btn-light { background: #fff; border-color: var(--border); color: var(--purple); }
        .empty-state { text-align: center; padding: 52px 24px; border: 1px dashed #d9cde7; border-radius: 12px; background: #fcfaff; }
        .empty-state .empty-icon { font-size: 46px; margin-bottom: 12px; }
        .two-col { display: grid; grid-template-columns: 1.1fr .9fr; gap: 28px; }
        .two-col > *, .page-layout > *, .content-grid > *, .stat-grid > * { min-width: 0; }
        .panel { padding: 25px; }
        .list-row { display: flex; justify-content: space-between; align-items: center; gap: 16px; padding: 16px 0; border-bottom: 1px solid var(--border); }
        .list-row:last-child { border-bottom: 0; }
        .stat-grid { display: grid; grid-template-columns: repeat(3,minmax(0,1fr)); gap: 16px; margin-bottom: 24px; }
        .stat-box { padding: 20px; border-radius: 10px; background: #f8f4fc; border: 1px solid var(--border); }
        .stat-box strong { display: block; color: var(--purple); font-size: 26px; }
        .notice { padding: 13px 16px; border-radius: 7px; margin-bottom: 18px; background: #f1eafa; color: var(--purple); }
        .notice-success { background: #e9f8ef; color: #17663a; }
        .notice-error { background: #fff0f0; color: #a52222; }
        .prose { max-width: 820px; line-height: 1.85; color: #5d5669; overflow-wrap: anywhere; }
        .prose h2, .prose h3 { color: var(--text); margin-top: 28px; }
        .prose img { height: auto; }
        .pagination-links { display: flex; gap: 8px; flex-wrap: wrap; margin-top: 28px; }
        .pagination-links a, .pagination-links span { padding: 8px 12px; border: 1px solid var(--border); border-radius: 5px; }

        /* Tables kisi bhi page par screen se bahar na jayein: wrapper me daalo ya .table-responsive use karo */
        .table-responsive, .panel, .prose { -webkit-overflow-scrolling: touch; }
        .table-responsive { width: 100%; overflow-x: auto; }
        .panel table, .prose table, .table-responsive table { width: 100%; border-collapse: collapse; }
        .panel, .prose { overflow-x: auto; }

        /* Account (logged in) */
        .header-account-link { display: inline-flex; align-items: center; gap: 7px; white-space: nowrap; }
        .header-logout-form { display: inline-flex; margin: 0; }

        .header-logout {
            border: 1px solid var(--border);
            border-radius: 6px;
            background: #fff;
            color: var(--purple);
            padding: 9px 12px;
            font-size: 12px;
            font-weight: 700;
            cursor: pointer;
            white-space: nowrap;
        }

        .header-logout:hover { background: #f7f3fc; }

        .account-dropdown { position: relative; display: inline-flex; align-items: center; }

        .account-dropdown-trigger {
            display: flex;
            align-items: center;
            gap: 9px;
            min-height: 43px;
            padding: 6px 9px;
            border: 1px solid var(--border);
            border-radius: 10px;
            background: #fff;
            color: var(--text);
            cursor: pointer;
            text-align: left;
            max-width: 245px;
        }

        .account-dropdown-trigger:hover,
        .account-dropdown.is-open .account-dropdown-trigger {
            background: #f8f4fc;
            border-color: #d8c8eb;
        }

        .account-avatar {
            width: 38px;
            height: 38px;
            flex-shrink: 0;
            display: grid;
            place-items: center;
            border-radius: 50%;
            background: #eee4fb;
            color: var(--purple);
            font-weight: 800;
        }

        .account-trigger-text { display: flex; flex-direction: column; min-width: 0; }
        .account-trigger-name { font-size: 12px; font-weight: 800; white-space: nowrap; }

        .account-trigger-email {
            max-width: 145px;
            overflow: hidden;
            color: var(--muted);
            font-size: 10px;
            text-overflow: ellipsis;
            white-space: nowrap;
        }

        .account-chevron { margin-left: auto; color: var(--muted); }

        .account-dropdown-menu {
            position: absolute;
            top: calc(100% + 10px);
            right: 0;
            z-index: 1000;
            width: 290px;
            max-width: calc(100vw - 30px);
            max-height: calc(100vh - 110px);
            max-height: calc(100dvh - 110px);
            overflow-y: auto;
            padding: 8px 0;
            border: 1px solid var(--border);
            border-radius: 8px;
            background: #fff;
            box-shadow: 0 14px 35px rgba(37, 11, 73, .17);
        }

        .account-dropdown-menu[hidden] { display: none !important; }

        .account-dropdown-header {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 15px 16px;
            border-bottom: 1px solid var(--border);
            margin-bottom: 6px;
        }

        .account-dropdown-header > div { min-width: 0; }
        .account-avatar-large { width: 44px; height: 44px; }
        .account-dropdown-header strong,
        .account-dropdown-header small { display: block; overflow-wrap: anywhere; }
        .account-dropdown-header strong { font-size: 13px; }
        .account-dropdown-header small { margin-top: 3px; color: var(--muted); font-size: 11px; }

        .account-menu-item {
            display: flex;
            align-items: center;
            gap: 13px;
            width: 100%;
            min-height: 44px; /* touch friendly */
            padding: 11px 17px;
            border: 0;
            background: transparent;
            color: #352b43;
            font-size: 13px;
            text-align: left;
            cursor: pointer;
        }

        .account-menu-item > span { width: 20px; color: #9299a8; text-align: center; font-size: 17px; }
        .account-menu-item:hover { background: #f7f3fc; color: var(--purple); }
        .account-menu-divider { height: 1px; margin: 6px 0; background: var(--border); }
        .account-dropdown-menu form { margin: 0; }
        .account-logout { color: #a22b35; }

        /* Keyboard focus */
        a:focus-visible, button:focus-visible, input:focus-visible, select:focus-visible, textarea:focus-visible {
            outline: 3px solid rgba(245,189,50,.75);
            outline-offset: 2px;
        }

        /* ============================================================
           RESPONSIVE
           ============================================================ */

        /* Laptop chhota */
        @media (max-width: 1200px) {
            .nav-links { gap: 15px; }
            .nav-links a { font-size: 10.5px; }
        }

        /* Tablet + mobile navigation (hamburger) */
        @media (max-width: 1024px) {
            .navbar { min-height: 70px; gap: 12px; }
            .menu-toggle { display: grid; place-items: center; }

            .nav-links {
                position: absolute;
                top: 100%;
                right: 0;
                left: 0;
                display: none;
                align-items: stretch;
                gap: 0;
                max-height: calc(100vh - 110px);
                max-height: calc(100dvh - 110px);
                overflow-y: auto;
                padding: 10px max(22px, var(--safe-left)) 18px max(22px, var(--safe-right));
                border-top: 1px solid var(--border);
                background: white;
                box-shadow: 0 12px 20px rgba(0,0,0,.08);
            }

            .nav-links.open { display: flex; flex-direction: column; }
            .nav-links a { padding: 14px 0; font-size: 13px; border-bottom: 1px solid #f1ecf7; }
            .nav-links a:last-child { border-bottom: 0; }
            .nav-links a.active::after { display: none; }

            /* Account dropdown ko poori header ke hisaab se position karo (screen se bahar na jaye) */
            .account-dropdown { position: static; }
            .account-dropdown-menu {
                top: 100%;
                right: 15px;
                width: min(310px, calc(100vw - 30px));
                margin-top: 6px;
            }
        }

        @media (max-width: 1100px) {
            .course-grid { gap: 17px; }
            .course-image { height: 175px; margin: 12px 12px 0; }
            .course-content { padding: 15px; }
            .course-title { font-size: 17px; }
        }

        /* Tablet */
        @media (max-width: 900px) {
            .content-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
            .page-layout { grid-template-columns: 1fr; }
            .two-col { grid-template-columns: 1fr; }
            .stat-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
        }

        @media (max-width: 850px) {
            .topbar-inner { justify-content: center; }
            .social-links { display: none; }

            .hero-grid { min-height: 350px; }
            .hero-image { min-height: 350px; }
            .hero-content { padding-right: 10px; }
            .hero-description { font-size: 14px; }

            .course-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
            .course-image { height: 200px; }

            .goals-layout { grid-template-columns: 1fr; }
            .learning-panel { min-height: 350px; }

            .category-grid { grid-template-columns: repeat(2,minmax(0,1fr)); }
            .about-grid { gap: 30px; }
            .footer-main { grid-template-columns: repeat(2,minmax(0,1fr)); }
            .footer-about { grid-column: 1 / -1; }
        }

        /* Mobile */
        @media (max-width: 700px) {
            /* account trigger me sirf avatar dikhao, space bache */
            .account-trigger-text { display: none; }
            .account-dropdown-trigger { padding: 4px 8px 4px 4px; gap: 4px; min-height: 43px; }
        }

        @media (max-width: 600px) {
            .content-grid { grid-template-columns: 1fr; }
            .section-pad { padding: 38px 0; }
            .section-head { align-items: flex-start; flex-direction: column; }
            .stat-grid { grid-template-columns: 1fr; }
            .page-hero { padding: 40px 0; }
            .panel { padding: 18px; }
            .list-row { flex-wrap: wrap; align-items: flex-start; }
            .pagination-links a, .pagination-links span { padding: 9px 13px; }

            /* iOS auto-zoom rokne ke liye inputs 16px */
            input, select, textarea, .field input, .field select, .field textarea, .form-control { font-size: 16px; }
        }

        @media (max-width: 560px) {
            .container { width: calc(100% - 30px); }
            .contact-info { justify-content: center; gap: 6px 14px; font-size: 11px; }
            .contact-info .location-item { display: none; }

            .brand { gap: 6px; }
            .brand-symbol { width: 34px; height: 38px; font-size: 26px; }
            .brand-name { font-size: 20px; letter-spacing: -.8px; }
            .brand-tagline { font-size: 7px; letter-spacing: .6px; }
            .navbar { gap: 8px; }
            .nav-actions { gap: 7px; }
            .nav-divider { display: none; }
            .login-link { font-size: 12px; }
            .nav-actions .btn { min-height: 40px; padding: 8px 12px; font-size: 11px; }
            .header-cart { width: 40px; height: 40px; border-radius: 9px; }
            .menu-toggle { width: 40px; height: 40px; }
            .account-dropdown-trigger { min-height: 40px; }
            .account-avatar { width: 30px; height: 30px; font-size: 13px; }

            .hero-grid { grid-template-columns: 1fr; }
            .hero-content { min-height: 300px; padding: 38px 0; }
            .hero h1 { font-size: clamp(30px, 9.5vw, 41px); letter-spacing: -1.2px; }
            .hero-buttons .btn { flex: 1 1 140px; }
            .hero-image { min-height: 240px; margin: 0 -15px; clip-path: none; }
            .hero-image img { clip-path: none; }

            .section { padding: 53px 0; }
            .section-heading { margin-bottom: 27px; }
            .section-title { font-size: 26px; }

            .course-grid { grid-template-columns: 1fr; gap: 20px; }
            .course-image { height: auto; aspect-ratio: 16 / 10; margin: 13px 13px 0; }
            .course-content { padding: 18px; }
            .course-title { font-size: 19px; }
            .course-description { min-height: unset; }
            .course-actions .btn { flex: 1 1 auto; }

            .learning-goals-section { padding: 52px 0; }
            .goals-heading { margin-bottom: 25px; }
            .goal-feature { padding: 18px; }
            .goal-feature h3 { font-size: 14px; }
            .learning-panel { min-height: unset; padding: 20px; }
            .panel-top h3 { font-size: 19px; }
            .progress-card { padding: 18px; }

            .category-grid { gap: 12px; }
            .category-card { padding: 19px 10px; }
            .category-card h3 { font-size: 13px; }

            .about-grid { grid-template-columns: 1fr; gap: 28px; }
            .about-image, .about-image img { min-height: unset; height: 250px; }
            .about-content h2 { font-size: 26px; }
            .about-points { grid-template-columns: 1fr; }

            .cta-section { padding-bottom: 52px; }
            .cta-box { align-items: flex-start; flex-direction: column; padding: 28px 22px; }
            .cta-box .btn { width: 100%; }

            .footer-main { gap: 28px 18px; padding-top: 40px; }
            .footer-col a { padding: 3px 0; margin-bottom: 6px; }
            .footer-bottom { flex-direction: column; text-align: center; align-items: center; }
        }

        /* Bahut chhote phones (320px - 400px): Login/Register menu me shift */
        @media (max-width: 420px) {
            .guest-actions { display: none; }
            .nav-auth-mobile { display: block; }
            .nav-links a.nav-auth-mobile.register { color: var(--purple); font-weight: 800; }
            .brand-tagline { display: none; }
            .brand-name { font-size: 19px; }
            .footer-main { grid-template-columns: 1fr; }
            .category-grid { grid-template-columns: 1fr 1fr; gap: 10px; }
            .category-icon { width: 56px; height: 56px; }
        }

        @media (max-width: 340px) {
            .container { width: calc(100% - 24px); }
            .category-grid { grid-template-columns: 1fr; }
            .brand-symbol { display: none; }
            .stat-box strong { font-size: 22px; }
        }

        /* Landscape phones: short height */
        @media (max-height: 480px) and (orientation: landscape) {
            .nav-links { max-height: calc(100vh - 90px); max-height: calc(100dvh - 90px); }
            .hero-grid, .hero-image { min-height: 280px; }
        }

        @media (prefers-reduced-motion: reduce) {
            *, *::before, *::after { scroll-behavior: auto !important; transition-duration: .01ms !important; }
        }
    </style>

    @stack('styles')
</head>

<body>

{{-- Top contact bar --}}
<div class="topbar">
    <div class="container topbar-inner">
        <div class="contact-info">
            <a href="tel:+919876543210">☎ +91 98765 43210</a>
            <a href="mailto:support@edusphere.in">✉ support@edusphere.in</a>
            <span class="location-item">⌖ Bhopal, Madhya Pradesh</span>
        </div>

        <div class="social-links" aria-label="Social links">
            <a href="https://facebook.com/" target="_blank" rel="noopener noreferrer">f</a>
            <a href="https://instagram.com/" target="_blank" rel="noopener noreferrer">◎</a>
            <a href="https://linkedin.com/" target="_blank" rel="noopener noreferrer">in</a>
            <a href="https://youtube.com/" target="_blank" rel="noopener noreferrer">▶</a>
        </div>
    </div>
</div>

{{-- Navbar --}}
<header class="main-header">
    <div class="container navbar">
        <a href="{{ route('home') }}" class="brand" aria-label="EduSphere home">
            <span class="brand-symbol" aria-hidden="true">🎓</span>
            <span>
                <span class="brand-name">Edu<span>Sphere</span></span>
                <span class="brand-tagline">LEARN · GROW · SUCCEED</span>
            </span>
        </a>

        <nav class="nav-links" id="mainNavigation" aria-label="Main navigation">
            <a href="{{ route('home') }}" class="{{ request()->is('/') ? 'active' : '' }}">Home</a>
            <a href="{{ url('/courses') }}" class="{{ request()->is('courses*') ? 'active' : '' }}">Courses</a>
            <a href="{{ url('/about') }}" class="{{ request()->is('about*') ? 'active' : '' }}">About Us</a>
            <a href="{{ url('/categories') }}" class="{{ request()->is('categories*') ? 'active' : '' }}">Categories</a>
            <a href="{{ url('/my-courses') }}" class="{{ request()->is('my-courses*') ? 'active' : '' }}">My Learning</a>
            <a href="{{ url('/contact') }}" class="{{ request()->is('contact*') ? 'active' : '' }}">Contact Us</a>
        </nav>

        <div class="nav-actions">
            {{-- Cart: visible to guests and logged-in users --}}
            <a href="{{ route('cart.index') }}" class="header-cart" aria-label="Shopping cart ({{ $headerCartCount }} items)" title="Shopping Cart">
                <svg viewBox="0 0 24 24" width="23" height="23" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true">
                    <circle cx="9" cy="21" r="1"/>
                    <circle cx="20" cy="21" r="1"/>
                    <path d="M1 1h4l2.68 13.39a2 2 0 0 0 2 1.61h9.72a2 2 0 0 0 2-1.61L23 6H6"/>
                </svg>
                @if ($headerCartCount > 0)
                    <span class="header-cart-count">{{ $headerCartCount }}</span>
                @endif
            </a>

            @if ($isUserLoggedIn && $headerUser)
                <div class="account-dropdown" id="accountDropdown">
                    <button
                        type="button"
                        class="account-dropdown-trigger"
                        id="accountDropdownTrigger"
                        aria-label="Account menu"
                        aria-haspopup="true"
                        aria-expanded="false"
                        aria-controls="accountDropdownMenu"
                    >
                        <span class="account-avatar" aria-hidden="true">
                            {{ strtoupper(substr($headerUser->name ?? 'U', 0, 1)) }}
                        </span>

                        <span class="account-trigger-text">
                            <span class="account-trigger-name">
                                {{ $isAdmin ? 'Admin Dashboard' : ($isInstructor ? 'Instructor Dashboard' : 'My Profile') }}
                            </span>
                            <span class="account-trigger-email">
                                {{ $headerUser->email }}
                            </span>
                        </span>

                        <span class="account-chevron" aria-hidden="true">▾</span>
                    </button>

                    <div class="account-dropdown-menu" id="accountDropdownMenu" hidden>
                        <div class="account-dropdown-header">
                            <span class="account-avatar account-avatar-large" aria-hidden="true">
                                {{ strtoupper(substr($headerUser->name ?? 'U', 0, 1)) }}
                            </span>
                            <div>
                                <strong>{{ $headerUser->name }}</strong>
                                <small>{{ $headerUser->email }}</small>
                            </div>
                        </div>

                        @if ($isAdmin)
                            <a href="{{ route('admin.dashboard') }}" class="account-menu-item">
                                <span>⚙</span> Admin Dashboard
                            </a>
                        @elseif ($isInstructor)
                            <a href="{{ route('instructor.dashboard') }}" class="account-menu-item">
                                <span>▣</span> Instructor Dashboard
                            </a>
                        @endif

                        <a href="{{ route('my-courses.index') }}" class="account-menu-item">
                            <span>◇</span> My Courses
                        </a>

                        <a href="{{ route('courses.index') }}" class="account-menu-item">
                            <span>♡</span> Browse Courses
                        </a>

                        <a href="{{ route('purchase-history.index') }}" class="account-menu-item">
                            <span>🛒</span> Purchase History
                        </a>

                        <a href="{{ route('profile.edit') }}" class="account-menu-item">
                            <span>👤</span> User Profile
                        </a>

                        <div class="account-menu-divider"></div>

                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="account-menu-item account-logout">
                                <span>↪</span> Log Out
                            </button>
                        </form>
                    </div>
                </div>
            @else
                <div class="guest-actions">
                    <a href="{{ route('login') }}" class="btn btn-gold">Login</a>
                    <a href="{{ route('register') }}" class="btn btn-gold">Register</a>
                </div>
            @endif

            <button type="button"
                    class="menu-toggle"
                    id="menuToggle"
                    aria-label="Toggle navigation"
                    aria-controls="mainNavigation"
                    aria-expanded="false">☰</button>
        </div>
    </div>
</header>

@yield('content')

{{-- Footer --}}
<footer class="footer" id="footer">
    <div class="container">
        <div class="footer-main">
            <div class="footer-about">
                <a href="{{ route('home') }}" class="brand">
                    <span class="brand-symbol" aria-hidden="true">🎓</span>
                    <span>
                        <span class="brand-name">Edu<span>Sphere</span></span>
                        <span class="brand-tagline">LEARN · GROW · SUCCEED</span>
                    </span>
                </a>

                <p>Discover knowledge, explore new interests and build your future with EduSphere.</p>
            </div>

            <div class="footer-col">
                <h4>Quick Links</h4>
                <a href="{{ route('home') }}">Home</a>
                <a href="{{ url('/about') }}">About Us</a>
                <a href="{{ url('/categories') }}">Categories</a>
                <a href="{{ url('/courses') }}">Courses</a>
            </div>

            <div class="footer-col">
                <h4>Account</h4>
                <a href="{{ route('login') }}">Login</a>
                <a href="{{ route('register') }}">Register</a>
                <a href="{{ url('/my-courses') }}">My Learning</a>
            </div>

            <div class="footer-col">
                <h4>Contact Us</h4>
                <a href="mailto:support@edusphere.in">support@edusphere.in</a>
                <a href="tel:+919876543210">+91 98765 43210</a>
                <a href="{{ url('/contact') }}">Contact us</a>
            </div>
        </div>

        <div class="footer-bottom">
            <span>© {{ date('Y') }} EduSphere. All rights reserved.</span>
            <span>Learn · Grow · Succeed</span>
        </div>
    </div>
</footer>

<script>
document.addEventListener('DOMContentLoaded', function () {
    var NAV_BREAKPOINT = 1024; // CSS ke hamburger breakpoint ke saath same rakhna

    var menuToggle = document.getElementById('menuToggle');
    var navigation = document.getElementById('mainNavigation');

    var accountDropdown = document.getElementById('accountDropdown');
    var accountTrigger = document.getElementById('accountDropdownTrigger');
    var accountMenu = document.getElementById('accountDropdownMenu');

    var setMenuOpen = function (open) {
        if (!menuToggle || !navigation) return;
        navigation.classList.toggle('open', open);
        menuToggle.setAttribute('aria-expanded', String(open));
        menuToggle.textContent = open ? '×' : '☰';
    };

    var setAccountMenuOpen = function (open) {
        if (!accountDropdown || !accountTrigger || !accountMenu) return;
        accountDropdown.classList.toggle('is-open', open);
        accountTrigger.setAttribute('aria-expanded', String(open));
        accountMenu.hidden = !open;
    };

    if (menuToggle && navigation) {
        menuToggle.addEventListener('click', function (e) {
            e.stopPropagation();
            var willOpen = !navigation.classList.contains('open');
            setAccountMenuOpen(false);
            setMenuOpen(willOpen);
        });

        navigation.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () { setMenuOpen(false); });
        });
    }

    if (accountDropdown && accountTrigger && accountMenu) {
        accountTrigger.addEventListener('click', function (e) {
            e.stopPropagation();
            var willOpen = accountMenu.hidden;
            setMenuOpen(false);
            setAccountMenuOpen(willOpen);
        });

        accountMenu.addEventListener('click', function (e) { e.stopPropagation(); });
    }

    // Bahar click karne par dono menu band
    document.addEventListener('click', function (e) {
        if (accountDropdown && !accountDropdown.contains(e.target)) setAccountMenuOpen(false);
        if (navigation && menuToggle && !navigation.contains(e.target) && !menuToggle.contains(e.target)) setMenuOpen(false);
    });

    // Escape key
    document.addEventListener('keydown', function (e) {
        if (e.key !== 'Escape') return;
        var accountWasOpen = accountMenu && !accountMenu.hidden;
        setMenuOpen(false);
        setAccountMenuOpen(false);
        if (accountWasOpen && accountTrigger) accountTrigger.focus();
    });

    // Screen resize / rotate par menu reset
    window.addEventListener('resize', function () {
        if (window.innerWidth > NAV_BREAKPOINT) setMenuOpen(false);
        setAccountMenuOpen(false);
    });
});
</script>

@stack('scripts')

</body>
</html>