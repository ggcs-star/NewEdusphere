@extends('layouts.frontend')
@section('title', 'About EduSphere')
@section('content')

<style>
.about-value-icon {
    width: 64px; height: 64px; margin-bottom: 16px; border-radius: 50%;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.6rem; color: #fff;
    background: linear-gradient(135deg, var(--purple) 0%, var(--purple-light) 100%);
    box-shadow: 0 8px 18px rgba(35,15,84,.25);
    transition: transform .3s ease, box-shadow .3s ease;
}
.info-card:hover .about-value-icon { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(245,189,50,.3); }
.about-purpose h2 { color: var(--purple); }
</style>

<section class="page-hero">
    <div class="container">
        <div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / About</div>
        <h1>Learning should move you forward</h1>
        <p>EduSphere brings learners and practical learning experiences together in one welcoming place.</p>
    </div>
</section>

<section class="section-pad">
    <div class="container two-col">
        <div class="about-purpose">
            <div class="eyebrow">Our purpose</div>
            <h2 style="font-size:34px">Learn. Grow. Succeed.</h2>
            <p class="prose">
                We believe that learning is a lifelong journey. EduSphere is designed to help learners
                discover new subjects, develop useful skills and make steady progress towards personal
                and professional goals.
            </p>
            <p class="prose">
                Explore at your own pace, revisit course material when you need it, and build a
                learning routine that fits your life.
            </p>
            <a class="btn btn-purple" href="{{ url('/courses') }}">Explore courses</a>
        </div>

        <div class="content-grid" style="grid-template-columns:1fr 1fr">
            <div class="info-card" style="padding:24px">
                <div class="about-value-icon"><i class="fa-solid fa-bullseye" aria-hidden="true"></i></div>
                <h3>Purposeful learning</h3>
                <p class="muted">Focus on skills and goals that matter to you.</p>
            </div>
            <div class="info-card" style="padding:24px">
                <div class="about-value-icon"><i class="fa-solid fa-compass" aria-hidden="true"></i></div>
                <h3>Your own pace</h3>
                <p class="muted">Build a routine that fits your schedule.</p>
            </div>
            <div class="info-card" style="padding:24px">
                <div class="about-value-icon"><i class="fa-solid fa-seedling" aria-hidden="true"></i></div>
                <h3>Keep growing</h3>
                <p class="muted">Keep exploring new ideas and interests.</p>
            </div>
            <div class="info-card" style="padding:24px">
                <div class="about-value-icon"><i class="fa-solid fa-handshake" aria-hidden="true"></i></div>
                <h3>Learning community</h3>
                <p class="muted">Make learning a habit, one step at a time.</p>
            </div>
        </div>
    </div>
</section>
@endsection
