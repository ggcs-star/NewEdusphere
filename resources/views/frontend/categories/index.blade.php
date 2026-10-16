@extends('layouts.frontend')
@section('title','Course Categories')
@section('content')
<style>
.category-page-icon {
    width: 72px; height: 72px; margin-bottom: 16px; border-radius: 50%; overflow: hidden;
    display: flex; align-items: center; justify-content: center;
    font-size: 1.8rem; line-height: 1; color: #fff;
    background: linear-gradient(135deg, var(--purple) 0%, var(--purple-light) 100%);
    box-shadow: 0 8px 18px rgba(35,15,84,.25);
    transition: transform .3s ease, box-shadow .3s ease;
}
.info-card:hover .category-page-icon { transform: translateY(-3px); box-shadow: 0 10px 22px rgba(245,189,50,.3); }
.category-page-icon img { width: 100%; height: 100%; object-fit: contain; box-sizing: border-box; padding: 8px; }
.category-page-icon i::before { color: #fff; }
</style>
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Categories</div><h1>Explore by category</h1><p>Find a learning topic that matches your interests and goals.</p></div></section>
<section class="section-pad"><div class="container"><div class="content-grid">
@forelse(($categories ?? collect()) as $category)
<a class="info-card" style="padding:26px;display:block" href="{{ url('/courses?category='.$category->id) }}">
    <div class="category-page-icon">
        <x-category-icon :category="$category" />
    </div>
    <h3 style="margin:0 0 6px">{{ $category->name }}</h3>
    <p class="muted">{{ \Illuminate\Support\Str::limit($category->description ?? 'Explore courses and build skills in this subject.',100) }}</p>
    <strong>Explore category →</strong>
</a>
@empty
<div class="empty-state" style="grid-column:1/-1"><div class="empty-icon">🗂️</div><h2>Categories coming soon</h2><p class="muted">Our course categories will appear here.</p><a class="btn btn-purple" href="{{ url('/courses') }}">Browse all courses</a></div>
@endforelse
</div></div></section>
@endsection
