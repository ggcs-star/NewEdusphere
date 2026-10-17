@extends('layouts.frontend')
@section('title', 'Your Cart')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Cart</div><h1>Your learning cart</h1><p>Review your selected courses before continuing to checkout.</p></div></section>
<section class="section-pad"><div class="container">
@if(session('success'))<div class="notice notice-success">{{ session('success') }}</div>@endif
@if(session('error'))<div class="notice notice-error">{{ session('error') }}</div>@endif
@if(isset($items) && count($items))
<div class="two-col"><div class="panel">
@foreach($items as $item)
    @php($course = $item['course'] ?? $item)
    <div class="list-row" style="align-items:flex-start">
        <div style="display:flex;gap:15px;min-width:0"><div style="width:84px;height:72px;border-radius:8px;background:#f4edf9;display:grid;place-items:center;font-size:28px;flex-shrink:0">📘</div><div><h3 style="font-size:16px;margin:0 0 5px">{{ $course->title ?? 'Course' }}</h3><p class="muted" style="font-size:12px;margin:0">{{ \Illuminate\Support\Str::limit($course->short_description ?? '', 80) }}</p><strong>{{ ($course->is_free ?? false) ? 'Free' : format_price((float)($item['price'] ?? $course->price ?? 0)) }}</strong></div></div>
        <form method="POST" action="{{ url('/cart/remove/'.$course->id) }}">@csrf @method('DELETE')<button type="submit" class="btn btn-light">Remove</button></form>
    </div>
@endforeach
<form method="POST" action="{{ url('/cart/clear') }}" style="margin-top:18px">@csrf @method('DELETE')<button type="submit" class="btn btn-light">Clear cart</button></form>
</div><aside class="panel" style="align-self:start"><h2>Order summary</h2><div class="list-row"><span class="muted">Subtotal</span><strong>{{ format_price((float)($subtotal ?? 0)) }}</strong></div><div class="list-row"><span class="muted">Discount</span><strong>—</strong></div><div class="list-row"><span>Total</span><strong class="price">{{ format_price((float)($subtotal ?? 0)) }}</strong></div><a href="{{ url('/checkout') }}" class="btn btn-purple" style="width:100%;margin-top:20px">Continue to checkout →</a><a href="{{ url('/courses') }}" class="btn btn-light" style="width:100%;margin-top:10px">Continue learning</a></aside></div>
@else
<div class="empty-state"><div class="empty-icon">🛒</div><h2>Your cart is empty</h2><p class="muted">Find a course that matches your goals and add it to your cart.</p><a class="btn btn-purple" href="{{ url('/courses') }}">Explore courses</a></div>
@endif
</div></section>
@endsection
