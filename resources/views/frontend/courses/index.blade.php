@extends('layouts.frontend')
@section('title', 'Explore Courses')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Courses</div><h1>Find your next course</h1><p>Learn from structured lessons, build practical skills and move closer to your goals.</p></div></section>
<section class="section-pad"><div class="container">
    <div class="section-head"><div><h2>Explore all courses</h2><p>Choose a topic and learn at your own pace.</p></div><span class="muted">{{ isset($courses) ? $courses->count() : 0 }} courses</span></div>
    <form method="GET" action="{{ url('/courses') }}" class="panel" style="margin-bottom:24px">
        <div class="content-grid" style="grid-template-columns:2fr 1fr auto;align-items:end">
            <div class="field"><label for="q">Search courses</label><input id="q" name="q" value="{{ request('q') }}" placeholder="e.g. Web development, design..."></div>
            <div class="field"><label for="category">Category</label><select id="category" name="category"><option value="">All categories</option>@foreach(($categories ?? collect()) as $category)<option value="{{ $category->id }}" @selected((string)request('category') === (string)$category->id)>{{ $category->name }}</option>@endforeach</select></div>
            <button class="btn btn-purple" type="submit">Search courses</button>
        </div>
    </form>
    @if(isset($courses) && $courses->count())
        <div class="content-grid">@foreach($courses as $course)@include('components.frontend.course-card', ['course' => $course])@endforeach</div>
        @if(method_exists($courses, 'links'))<div class="pagination-links">{{ $courses->withQueryString()->links() }}</div>@endif
    @else
        <div class="empty-state"><div class="empty-icon">📚</div><h3>Courses are being prepared</h3><p class="muted">Try another search, or check back soon for new learning opportunities.</p><a class="btn btn-purple" href="{{ url('/courses') }}">Clear filters</a></div>
    @endif
</div></section>
@endsection
