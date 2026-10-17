@extends('layouts.frontend')
@section('title', $heading)
@section('content')

<section class="page-hero">
    <div class="container">
        <div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / {{ $heading }}</div>
        <h1>{{ $heading }}</h1>
    </div>
</section>

<section class="section-pad">
    <div class="container">
        @if ($siteSettings[$field])
            {{-- Content saved before the rich-text editor was added is plain
                 text (literal newlines, no tags) — nl2br it so old saves
                 still display correctly until the admin re-saves via TinyMCE. --}}
            <div class="prose">{!! str_contains($siteSettings[$field], '<') ? $siteSettings[$field] : nl2br(e($siteSettings[$field])) !!}</div>
        @else
            <p class="prose">This page hasn't been set up yet.</p>
        @endif
    </div>
</section>

@endsection
