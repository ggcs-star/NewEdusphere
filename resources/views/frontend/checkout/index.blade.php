@extends('layouts.frontend')
@section('title', 'Checkout')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / <a href="{{ url('/cart') }}">Cart</a> / Checkout</div><h1>Secure checkout</h1><p>Confirm your course selection and continue to payment.</p></div></section>
<section class="section-pad"><div class="container two-col">
    <div class="panel"><h2>Order details</h2>
        @if(session('error'))<div class="notice notice-error">{{ session('error') }}</div>@endif
        @if($errors->any())<div class="notice notice-error"><ul style="margin:0;padding-left:18px">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
        @forelse(($items ?? []) as $item)
            @php($course = $item['course'] ?? $item)
            <div class="list-row"><div><strong>{{ $course->title ?? 'Course' }}</strong><div class="muted" style="font-size:12px">{{ !empty($course->is_free) ? 'Free course' : 'Lifetime course access' }}</div></div><strong>{{ !empty($course->is_free) ? 'Free' : '₹'.number_format((float)($item['price'] ?? $course->price ?? 0),2) }}</strong></div>
        @empty
            <div class="empty-state"><div class="empty-icon">📚</div><h3>No courses to checkout</h3><a class="btn btn-purple" href="{{ url('/courses') }}">Browse courses</a></div>
        @endforelse
    </div>
    <aside class="panel" style="align-self:start"><h2>Payment summary</h2><div class="list-row"><span class="muted">Subtotal</span><strong>₹{{ number_format((float)($subtotal ?? 0),2) }}</strong></div><div class="list-row"><span class="muted">Tax</span><strong>Included if applicable</strong></div><div class="list-row"><strong>Total payable</strong><strong class="price">₹{{ number_format((float)($subtotal ?? 0),2) }}</strong></div>
        <form method="POST" action="{{ url('/checkout/pay') }}" style="margin-top:20px">@csrf<button class="btn btn-purple" type="submit" style="width:100%" @disabled(empty($items))>Continue to secure payment →</button></form>
        <p class="muted" style="font-size:12px;margin-top:14px">Payments are processed through the configured payment gateway. Do not refresh or close the payment window while payment is being processed.</p>
        <a href="{{ url('/cart') }}" class="btn btn-light" style="width:100%">Back to cart</a>
    </aside>
</div></section>
@endsection
