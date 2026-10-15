@extends('layouts.frontend')
@section('title', 'My Learning')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / My learning</div><h1>My learning</h1><p>Pick up where you left off and keep making progress.</p></div></section>
<section class="section-pad"><div class="container">
    <div class="stat-grid"><div class="stat-box"><span class="muted">Enrolled courses</span><strong>{{ isset($courses) ? $courses->count() : 0 }}</strong></div><div class="stat-box"><span class="muted">Learning status</span><strong style="font-size:20px">Keep going</strong></div><div class="stat-box"><span class="muted">Your next step</span><strong style="font-size:20px">Learn today</strong></div></div>
    @if(isset($courses) && $courses->count())
        <div class="content-grid">@foreach($courses as $course)
            <article class="course-card"><div class="course-thumb">@if(!empty($course->thumbnail))<img src="{{ asset('storage/'.$course->thumbnail) }}" alt="{{ $course->title }}" style="width:100%;height:100%;object-fit:cover">@else📘@endif</div><div class="course-body"><div class="eyebrow">Your course</div><h3>{{ $course->title }}</h3><p class="muted">{{ \Illuminate\Support\Str::limit(strip_tags($course->short_description ?? ''),100) }}</p><div class="meta-row"><span>Self-paced learning</span><span>Enrolled</span></div><a href="{{ url('/courses/'.($course->slug ?? $course->id)) }}" class="btn btn-purple" style="width:100%">Open course</a></div></article>
        @endforeach</div>
    @else
        <div class="empty-state"><div class="empty-icon">🎯</div><h2>Your learning journey starts here</h2><p class="muted">You have not enrolled in any courses yet. Explore the course library to find something you love.</p><a href="{{ url('/courses') }}" class="btn btn-purple">Explore courses</a></div>
    @endif
</div></section>
@endsection
