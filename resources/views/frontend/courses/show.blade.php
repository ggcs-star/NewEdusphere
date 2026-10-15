@extends('layouts.frontend')
@section('title', $course->title ?? 'Course details')
@section('content')
@php
    $price = isset($course->discount_flag) && $course->discount_flag && isset($course->discounted_price) ? $course->discounted_price : ($course->price ?? 0);
    $image = $course->thumbnail ?? null;
@endphp
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / <a href="{{ url('/courses') }}">Courses</a> / Details</div><h1>{{ $course->title ?? 'Course details' }}</h1><p>{{ $course->short_description ?? 'A practical, structured learning experience to help you grow your skills.' }}</p></div></section>
<section class="section-pad"><div class="container two-col">
    <div>
        <div class="course-card" style="margin-bottom:24px">@if($image)<img src="{{ asset('storage/'.$image) }}" alt="{{ $course->title }}" style="width:100%;max-height:390px;object-fit:cover">@else<div class="course-thumb" style="height:300px;font-size:76px">🎓</div>@endif</div>
        <div class="panel"><h2>About this course</h2><div class="prose">{!! nl2br(e($course->description ?? $course->short_description ?? 'Course description will be available soon.')) !!}</div>
            @if(!empty($course->outcomes))<h3>What you will learn</h3><div class="prose">{!! nl2br(e($course->outcomes)) !!}</div>@endif
            @if(!empty($course->requirements))<h3>Requirements</h3><div class="prose">{!! nl2br(e($course->requirements)) !!}</div>@endif
        </div>
    </div>
    <aside class="panel" style="align-self:start"><div class="eyebrow">Course enrolment</div><div class="price" style="font-size:32px;margin:8px 0 18px">{{ !empty($course->is_free_course) ? 'Free' : '₹'.number_format((float)$price, 2) }}</div>
        <div class="list-row"><span class="muted">Level</span><strong>{{ ucfirst($course->level ?? 'All levels') }}</strong></div>
        <div class="list-row"><span class="muted">Language</span><strong>{{ $course->language ?? 'English' }}</strong></div>
        <div class="list-row"><span class="muted">Category</span><strong>{{ $course->category->name ?? 'General' }}</strong></div>
        <div class="list-row"><span class="muted">Format</span><strong>Self-paced</strong></div>
        @auth
            <form method="POST" action="{{ url('/cart/add/'.$course->id) }}" style="margin-top:20px">@csrf<button class="btn btn-purple" type="submit" style="width:100%">Add to cart</button></form>
            <form method="POST" action="{{ url('/cart/buy-now/'.$course->id) }}" style="margin-top:10px">@csrf<button class="btn btn-gold" type="submit" style="width:100%">Buy now</button></form>
        @else
            <a class="btn btn-purple" href="{{ route('login') }}" style="width:100%;margin-top:20px">Sign in to enrol</a><p class="muted" style="font-size:12px;margin-top:12px">Create an account to save your progress and access your courses.</p>
        @endauth
    </aside>
</div></section>
@endsection
