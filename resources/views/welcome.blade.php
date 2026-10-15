@extends('layouts.frontend')

@section('title', 'Home')

@push('meta')
    <link rel="stylesheet" href="{{ asset('css/home.css') }}">
@endpush

@section('content')
<main class="edu-home">
    <x-home.hero />
    <x-home.featured-courses :courses="$featuredCourses" />
    <x-home.learning-goals />
    <x-home.categories :categories="$categories" />
    <x-home.about />
    <x-home.benefits />
    <x-home.cta />
</main>

<script src="{{ asset('js/home.js') }}"></script>
@endsection
