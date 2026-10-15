@extends('layouts.frontend')
@section('title','Course Categories')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Categories</div><h1>Explore by category</h1><p>Find a learning topic that matches your interests and goals.</p></div></section>
<section class="section-pad"><div class="container"><div class="content-grid">
@forelse(($categories ?? collect()) as $category)
<a class="info-card" style="padding:26px;display:block" href="{{ url('/courses?category='.$category->id) }}"><div style="font-size:34px">📚</div><h3 style="margin:12px 0 6px">{{ $category->name }}</h3><p class="muted">{{ \Illuminate\Support\Str::limit($category->description ?? 'Explore courses and build skills in this subject.',100) }}</p><strong>Explore category →</strong></a>
@empty
<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">🗂️</div><h2>Categories coming soon</h2><p class="muted">Our course categories will appear here.</p><a class="btn btn-purple" href="{{ url('/courses') }}">Browse all courses</a></div>
@endforelse
</div></div></section>
@endsection
