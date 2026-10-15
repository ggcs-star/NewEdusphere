
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>@yield('title', 'Learn, Grow, Succeed') · EduSphere</title>
    @stack('meta')
    <meta name="description" content="Explore courses, develop new skills and build your future with EduSphere.">
    <meta name="theme-color" content="#32105f">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">

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
        }

        * { box-sizing: border-box; }

        html {
            scroll-behavior: smooth;
            scroll-padding-top: 100px;
        }

        body {
            margin: 0;
            font-family: "DM Sans", sans-serif;
            color: var(--text);
            background: white;
            line-height: 1.6;
        }

        h1, h2, h3, h4, p { margin-top: 0; }

        h1, h2, h3, h4 {
            font-family: "Plus Jakarta Sans", sans-serif;
        }

        a { color: inherit; text-decoration: none; }
        img { display: block; max-width: 100%; }
        button, input { font: inherit; }

        .container {
            width: min(1200px, calc(100% - 44px));
            margin: 0 auto;
        }

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

        .btn-gold {
            color: var(--purple-dark);
            background: var(--gold);
        }

        .btn-gold:hover { background: var(--gold-dark); }

        .btn-outline-light {
            color: white;
            background: transparent;
            border-color: rgba(255,255,255,.45);
        }

        /* Top bar */

        .topbar {
            color: white;
            background: var(--purple-dark);
            font-size: 12px;
        }

        .topbar-inner {
            min-height: 36px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 20px;
        }

        .contact-info, .social-links {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 18px;
        }

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

        .brand {
            display: inline-flex;
            align-items: center;
            gap: 9px;
            flex-shrink: 0;
        }

        .brand-symbol {
            width: 43px;
            height: 43px;
            display: grid;
            place-items: center;
            font-size: 30px;
        }

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
        }

        .nav-links {
            display: flex;
            align-items: center;
            gap: 22px;
        }

        .nav-links a {
            position: relative;
            padding: 26px 0;
            color: #292137;
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            white-space: nowrap;
        }

        .nav-links a:hover,
        .nav-links a.active { color: var(--gold-dark); }

        .nav-links a.active::after {
            content: "";
            position: absolute;
            right: 0;
            bottom: 17px;
            left: 0;
            height: 3px;
            background: var(--gold);
        }

        .nav-actions {
            display: flex;
            align-items: center;
            gap: 10px;
            flex-shrink: 0;
        }

        .login-link { font-size: 12px; font-weight: 700; }
        .login-link:hover { color: var(--purple); }
        .nav-divider { color: #d7d0e0; }

        .menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: 1px solid var(--border);
            border-radius: 4px;
            color: var(--purple);
            background: white;
            font-size: 21px;
            cursor: pointer;
        }

        /* Hero */

        .hero {
            position: relative;
            overflow: hidden;
            color: white;
            background: var(--purple);
        }

        .hero-grid {
            min-height: 390px;
            display: grid;
            grid-template-columns: 1.12fr .88fr;
        }

        .hero-content {
            position: relative;
            z-index: 2;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            padding: 45px 25px 45px 0;
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

        .hero-kicker::after {
            content: "";
            display: block;
            width: 48px;
            height: 3px;
            margin-top: 10px;
            background: var(--gold);
        }

        .hero h1 {
            max-width: 600px;
            margin-bottom: 16px;
            font-size: clamp(36px, 4.2vw, 56px);
            font-weight: 800;
            line-height: 1.12;
            letter-spacing: -1.8px;
        }

        .hero h1 span { color: var(--gold); }

        .hero-description {
            max-width: 490px;
            margin-bottom: 25px;
            color: #e6dcf3;
            font-size: 15px;
            line-height: 1.8;
        }

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

        .section-heading {
            margin-bottom: 35px;
            text-align: center;
        }

        .section-kicker {
            margin-bottom: 8px;
            color: var(--gold-dark);
            font-size: 12px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .section-title {
            margin-bottom: 0;
            font-size: clamp(28px,3vw,39px);
            font-weight: 800;
            line-height: 1.25;
        }

        .section-line {
            width: 120px;
            height: 4px;
            margin: 17px auto 0;
            background: var(--gold);
        }

        .section-description {
            max-width: 650px;
            margin: 15px auto 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        /* Featured courses */

        .featured-section {
            color: white;
            background:
                radial-gradient(ellipse at 50% 0%,rgba(232, 234, 237, 0.24),transparent 60%),
                var(--purple-dark);
        }

        .featured-section .section-title { color: white; }
        .featured-section .section-kicker { color: var(--gold); }
        .featured-section .section-description { color: #af8ae3; }

        .course-grid {
            display: grid;
            grid-template-columns: repeat(3,minmax(0,1fr));
            gap: 25px;
        }

        .course-card {
            min-width: 0;
            overflow: hidden;
            border: 1px solid rgba(255,255,255,.07);
            background: #4a2d76;
            transition: transform .25s,box-shadow .25s;
        }

        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0,0,0,.22);
        }

        .course-image {
            position: relative;
            height: 205px;
            margin: 16px 16px 0;
            overflow: hidden;
            background: #5a3b83;
        }

        .course-image img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            transition: transform .35s;
        }

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
            color: var(--gold);
            font-size: 11px;
            font-weight: 800;
            text-transform: uppercase;
            letter-spacing: .8px;
        }

        .course-title {
            min-height: 27px;
            margin-bottom: 10px;
            color: var(--gold);
            font-size: 19px;
            font-weight: 700;
            overflow-wrap: anywhere;
        }

        .course-description {
            min-height: 48px;
            margin-bottom: 16px;
            color: #0d0c0e;
            font-size: 13px;
            line-height: 1.8;
        }

        .course-meta {
            display: flex;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
            color: #7147ac;
            font-size: 11px;
        }

        .course-price { color: white; font-size: 16px; font-weight: 800; }

        .course-old-price {
            margin-left: 5px;
            color: #c3b3d9;
            font-size: 11px;
            font-weight: 400;
            text-decoration: line-through;
        }

        .course-actions {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            padding-top: 14px;
            border-top: 1px solid rgba(255,255,255,.14);
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
            border: 1px dashed rgba(255,255,255,.25);
            color: #190d27;
            text-align: center;
        }

        .empty-state h3 { margin-bottom: 8px; color: var(--gold); }
        .empty-state p { margin-bottom: 0; font-size: 13px; }

        /* Learning goals — merged section */

        .learning-goals-section {
            padding: 75px 0;
            background: #f8f6fc;
        }

        .goals-heading { margin-bottom: 35px; }

        .goals-kicker {
            color: var(--gold-dark);
            font-size: 11px;
            font-weight: 800;
            letter-spacing: 2px;
        }

        .goals-heading h2 {
            margin: 10px 0;
            color: var(--purple);
            font-size: clamp(27px,3vw,36px);
            font-weight: 800;
            letter-spacing: -.8px;
        }

        .goals-heading > p {
            max-width: 650px;
            margin-bottom: 0;
            color: var(--muted);
            font-size: 14px;
            line-height: 1.8;
        }

        .goals-layout {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: stretch;
            gap: 30px;
        }

        .goals-features { display: flex; flex-direction: column; gap: 15px; }

        .goal-feature {
            padding: 21px 22px;
            border: 1px solid #e6e0ee;
            border-radius: 10px;
            background: white;
            transition: border-color .2s,transform .2s,box-shadow .2s;
        }

        .goal-feature:hover {
            transform: translateY(-2px);
            border-color: #d6b34d;
            box-shadow: 0 8px 22px rgba(50,16,95,.06);
        }

        .goal-feature.active {
            border: 1px solid #6546e8;
            box-shadow: inset 4px 0 #6546e8;
        }

        .goal-feature-title {
            display: flex;
            align-items: center;
            flex-wrap: wrap;
            gap: 11px;
            margin-bottom: 8px;
        }

        .goal-icon {
            width: 29px;
            height: 29px;
            display: grid;
            place-items: center;
            border-radius: 8px;
            background: #f1eaff;
            font-size: 16px;
        }

        .goal-feature h3 {
            margin: 0;
            color: var(--purple);
            font-size: 16px;
        }

        .goal-feature > p {
            margin: 0 0 10px;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.8;
        }

        .goal-feature > a {
            color: #6546e8;
            font-size: 12px;
            font-weight: 800;
        }

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
            padding: 29px;
            border: 1px solid #ece6f3;
            border-radius: 16px;
            background: white;
            box-shadow: 0 12px 35px rgba(37,11,73,.06);
        }

        .panel-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 15px;
        }

        .panel-label,.progress-label {
            color: #bd8d12;
            font-size: 10px;
            font-weight: 800;
            letter-spacing: 1.2px;
        }

        .panel-top h3 {
            margin: 8px 0;
            color: var(--purple);
            font-size: 22px;
        }

        .panel-top p {
            margin-bottom: 0;
            color: var(--muted);
            font-size: 13px;
            line-height: 1.8;
        }

        .panel-icon {
            width: 49px;
            height: 49px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 13px;
            background: #f9edc9;
            font-size: 24px;
        }

        .progress-card {
            padding: 24px;
            border: 1px solid #e9e0f5;
            border-radius: 12px;
            background: linear-gradient(145deg,#fbf9ff,#fffdf7);
        }

        .progress-card-heading {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 10px;
            margin-bottom: 20px;
        }

        .progress-card h4 {
            margin: 8px 0 0;
            color: var(--purple);
            font-size: 16px;
            line-height: 1.5;
        }

        .progress-emoji { font-size: 25px; }

        .progress-track {
            height: 8px;
            overflow: hidden;
            border-radius: 20px;
            background: #e9e2f4;
        }

        .progress-track span {
            display: block;
            width: 0;
            height: 100%;
            background: var(--gold);
        }

        .progress-note {
            margin: 13px 0 20px;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.8;
        }

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

        .goals-button:hover {
            transform: translateY(-2px);
            background: var(--gold-dark);
        }

        .panel-bottom {
            display: flex;
            align-items: flex-start;
            gap: 13px;
            padding-top: 20px;
            border-top: 1px solid #eee8f5;
        }

        .panel-bottom-icon {
            width: 38px;
            height: 38px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 10px;
            background: #f9edc9;
            font-size: 19px;
        }

        .panel-bottom strong { color: var(--purple); font-size: 13px; }

        .panel-bottom p {
            margin: 5px 0 0;
            color: var(--muted);
            font-size: 12px;
            line-height: 1.8;
        }

        /* Categories */

        .categories-section { background: var(--light-bg); }

        .category-grid {
            display: grid;
            grid-template-columns: repeat(4,minmax(0,1fr));
            gap: 20px;
        }

        .category-card {
            display: flex;
            flex-direction: column;
            align-items: center;
            padding: 26px 16px;
            border: 1px solid var(--border);
            background: white;
            text-align: center;
            transition: border-color .2s,transform .2s,box-shadow .2s;
        }

        .category-card:hover {
            transform: translateY(-4px);
            border-color: var(--gold);
            box-shadow: 0 12px 25px rgba(50,16,95,.07);
        }

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

        .about-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            align-items: center;
            gap: 55px;
        }

        .about-image { min-height: 340px; overflow: hidden; background: #f0e8fb; }
        .about-image img { width: 100%; height: 340px; object-fit: cover; }
        .about-content .section-kicker { text-align: left; }

        .about-content h2 {
            margin-bottom: 16px;
            color: var(--purple);
            font-size: clamp(27px,3vw,37px);
            line-height: 1.3;
        }

        .about-content > p {
            color: var(--muted);
            font-size: 14px;
            line-height: 1.9;
        }

        .about-points {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 13px;
            margin: 23px 0;
        }

        .about-point {
            display: flex;
            align-items: center;
            gap: 9px;
            font-size: 12px;
            font-weight: 700;
        }

        .check {
            width: 23px;
            height: 23px;
            display: grid;
            place-items: center;
            flex-shrink: 0;
            border-radius: 50%;
            color: var(--purple);
            background: #fdf0c9;
        }

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

        .cta-copy { position: relative; z-index: 1; }

        .cta-copy h2 { margin-bottom: 10px; font-size: clamp(23px,3vw,32px); }
        .cta-copy p { margin-bottom: 0; color: #e1d5f1; font-size: 13px; }
        .cta-box .btn { position: relative; z-index: 1; flex-shrink: 0; }

        /* Footer */

        .footer { color: #e9dff4; background: #21083f; }

        .footer-main {
            display: grid;
            grid-template-columns: 1.5fr 1fr 1fr 1fr;
            gap: 35px;
            padding: 48px 0 35px;
        }

        .footer .brand-name { color: white; }
        .footer .brand-tagline { color: #d2c0e6; }

        .footer-about p {
            max-width: 300px;
            margin: 17px 0 0;
            color: #c5b4d8;
            font-size: 12px;
            line-height: 1.9;
        }

        .footer-col h4 { margin-bottom: 16px; color: var(--gold); font-size: 13px; }

        .footer-col a {
            display: block;
            width: fit-content;
            margin-bottom: 11px;
            color: #d1c2e1;
            font-size: 12px;
        }

        .footer-col a:hover { color: var(--gold); }

        .footer-bottom {
            display: flex;
            justify-content: space-between;
            gap: 15px;
            padding: 18px 0;
            border-top: 1px solid rgba(255,255,255,.12);
            color: #b9a8ce;
            font-size: 11px;
        }

        @media (max-width: 1100px) {
            .nav-links { gap: 13px; }
            .nav-links a { font-size: 10px; }
            .course-grid { gap: 17px; }
            .course-image { height: 175px; margin: 12px 12px 0; }
            .course-content { padding: 15px; }
            .course-title { font-size: 17px; }
        }

        @media (max-width: 850px) {
            .topbar-inner { justify-content: center; }
            .social-links { display: none; }
            .navbar { min-height: 70px; }
            .menu-toggle { display: grid; place-items: center; }

            .nav-links {
                position: absolute;
                top: 100%;
                right: 0;
                left: 0;
                display: none;
                align-items: stretch;
                gap: 0;
                padding: 10px 24px 18px;
                border-top: 1px solid var(--border);
                background: white;
                box-shadow: 0 12px 20px rgba(0,0,0,.08);
            }

            .nav-links.open { display: flex; flex-direction: column; }
            .nav-links a { padding: 12px 0; font-size: 12px; }
            .nav-links a.active::after { display: none; }

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
        }

        @media (max-width: 560px) {
            .container { width: calc(100% - 30px); }

            .contact-info {
                justify-content: center;
                gap: 10px;
                font-size: 10px;
            }

            .contact-info .location-item { display: none; }
            .brand-symbol { width: 36px; height: 40px; font-size: 27px; }
            .brand-name { font-size: 21px; }
            .brand-tagline { font-size: 8px; }
            .navbar { gap: 10px; }
            .nav-actions { gap: 8px; }
            .nav-divider { display: none; }
            .login-link { font-size: 12px; }
            .nav-actions .btn { min-height: 38px; padding: 8px 12px; font-size: 11px; }
            .menu-toggle { width: 36px; height: 38px; }

            .hero-grid { grid-template-columns: 1fr; }
            .hero-content { min-height: 330px; padding: 42px 0; }
            .hero h1 { font-size: 41px; }

            .hero-image {
                min-height: 240px;
                margin-left: -15px;
                clip-path: none;
            }

            .hero-image img { clip-path: none; }

            .section { padding: 53px 0; }
            .section-heading { margin-bottom: 27px; }
            .section-title { font-size: 28px; }

            .course-grid { grid-template-columns: 1fr; gap: 20px; }
            .course-image { height: 225px; margin: 13px 13px 0; }
            .course-content { padding: 18px; }
            .course-title { font-size: 19px; }
            .course-description { min-height: unset; }

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
            .about-image,.about-image img { min-height: unset; height: 250px; }
            .about-content h2 { font-size: 29px; }
            .about-points { grid-template-columns: 1fr; }

            .cta-section { padding-bottom: 52px; }
            .cta-box { align-items: flex-start; flex-direction: column; padding: 28px 22px; }
            .footer-main { gap: 28px 18px; }
            .footer-bottom { flex-direction: column; }
        }

        @media (prefers-reduced-motion: reduce) {
            *,*::before,*::after {
                scroll-behavior: auto !important;
                transition-duration: .01ms !important;
            }
        }
    
        /* Shared inner-page components */
        .page-hero{background:linear-gradient(120deg,var(--purple-dark),var(--purple-light));color:#fff;padding:54px 0}
        .page-hero h1{font-size:clamp(30px,4vw,44px);line-height:1.15;margin-bottom:12px}
        .page-hero p{max-width:720px;color:#e9def7;margin:0}
        .breadcrumbs{font-size:12px;color:#e5d8f4;margin-bottom:16px}
        .section-pad{padding:58px 0}
        .section-head{display:flex;justify-content:space-between;align-items:end;gap:18px;margin-bottom:26px}
        .section-head h2{margin-bottom:6px;font-size:clamp(24px,3vw,34px)}
        .section-head p{margin:0;color:var(--muted)}
        .content-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:22px}
        .course-card,.panel,.info-card{border:1px solid var(--border);border-radius:12px;background:#fff;overflow:hidden;box-shadow:0 8px 24px rgba(37,11,73,.045)}
        .course-thumb{height:180px;background:linear-gradient(135deg,#eee4fb,#f8e8b8);display:grid;place-items:center;font-size:48px}
        .course-body{padding:20px}
        .eyebrow{color:var(--gold-dark);font-size:11px;font-weight:800;letter-spacing:1px;text-transform:uppercase}
        .course-body h3{font-size:18px;line-height:1.35;margin:8px 0}
        .muted{color:var(--muted)}
        .meta-row{display:flex;justify-content:space-between;gap:12px;color:var(--muted);font-size:12px;margin:14px 0}
        .price{font-weight:800;color:var(--purple);font-size:20px}
        .page-layout{display:grid;grid-template-columns:260px minmax(0,1fr);gap:28px}
        .filter-panel{padding:22px;align-self:start}
        .filter-panel h3{margin-bottom:16px}
        .field{display:flex;flex-direction:column;gap:7px;margin-bottom:16px}
        .field label{font-size:12px;font-weight:800}
        .field input,.field select,.field textarea,.form-control{width:100%;border:1px solid #ded5e9;border-radius:6px;padding:12px 13px;background:#fff;color:var(--text);outline:none}
        .field input:focus,.field select:focus,.field textarea:focus,.form-control:focus{border-color:var(--purple-light);box-shadow:0 0 0 3px rgba(74,38,119,.1)}
        .btn-purple{color:#fff;background:var(--purple)}
        .btn-purple:hover{background:var(--purple-light)}
        .btn-light{background:#fff;border-color:var(--border);color:var(--purple)}
        .empty-state{text-align:center;padding:52px 24px;border:1px dashed #d9cde7;border-radius:12px;background:#fcfaff}
        .empty-state .empty-icon{font-size:46px;margin-bottom:12px}
        .two-col{display:grid;grid-template-columns:1.1fr .9fr;gap:28px}
        .panel{padding:25px}
        .list-row{display:flex;justify-content:space-between;align-items:center;gap:16px;padding:16px 0;border-bottom:1px solid var(--border)}
        .list-row:last-child{border-bottom:0}
        .stat-grid{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px;margin-bottom:24px}
        .stat-box{padding:20px;border-radius:10px;background:#f8f4fc;border:1px solid var(--border)}
        .stat-box strong{display:block;color:var(--purple);font-size:26px}
        .notice{padding:13px 16px;border-radius:7px;margin-bottom:18px;background:#f1eafa;color:var(--purple)}
        .notice-success{background:#e9f8ef;color:#17663a}
        .notice-error{background:#fff0f0;color:#a52222}
        .prose{max-width:820px;line-height:1.85;color:#5d5669}
        .prose h2,.prose h3{color:var(--text);margin-top:28px}
        .pagination-links{display:flex;gap:8px;flex-wrap:wrap;margin-top:28px}
        .pagination-links a,.pagination-links span{padding:8px 12px;border:1px solid var(--border);border-radius:5px}
        @media(max-width:900px){.content-grid{grid-template-columns:repeat(2,minmax(0,1fr))}.page-layout{grid-template-columns:1fr}.two-col{grid-template-columns:1fr}.stat-grid{grid-template-columns:repeat(2,minmax(0,1fr))}}
        @media(max-width:600px){.content-grid{grid-template-columns:1fr}.section-pad{padding:38px 0}.section-head{align-items:start;flex-direction:column}.stat-grid{grid-template-columns:1fr}.page-hero{padding:40px 0}}

    </style>
</head>

<body>
@php($currentPath = request()->path())

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
            <a href="{{ route('home') }}" class="active">Home</a>
            <a href="{{ url('/courses') }}">Courses</a>
            <a href="{{ url('/about') }}">About Us</a>
            <a href="{{ url('/categories') }}">Categories</a>
            <a href="{{ url('/my-courses') }}">My Learning</a>
            <a href="{{ url('/contact') }}">Contact Us</a>
        </nav>

        <div class="nav-actions">
            @if(auth()->check())
                <a href="{{ url('/dashboard') }}" class="login-link">My dashboard</a>
                <a href="{{ url('/cart') }}" class="btn btn-gold">My cart</a>
            @else
                <a href="{{ route('login') }}" class="login-link">◉ Login</a>
                <span class="nav-divider">|</span>
                <a href="{{ route('register') }}" class="btn btn-gold">Register</a>
            @endif

            <button
                type="button"
                class="menu-toggle"
                id="menuToggle"
                aria-label="Toggle navigation"
                aria-expanded="false"
                aria-controls="mainNavigation"
            >☰</button>
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

                <p>
                    Discover knowledge, explore new interests and build your
                    future with EduSphere.
                </p>
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
    const menuToggle = document.getElementById('menuToggle');
    const navigation = document.getElementById('mainNavigation');
    if (menuToggle && navigation) {
        menuToggle.addEventListener('click', function () {
            const isOpen = navigation.classList.toggle('open');
            menuToggle.setAttribute('aria-expanded', String(isOpen));
            menuToggle.textContent = isOpen ? '×' : '☰';
        });
        navigation.querySelectorAll('a').forEach(function (link) {
            link.addEventListener('click', function () {
                navigation.classList.remove('open');
                menuToggle.setAttribute('aria-expanded', 'false');
                menuToggle.textContent = '☰';
            });
        });
    }
});
</script>
@stack('scripts')
</body>
</html>
