@extends('layouts.frontend')
@section('title','Contact Us')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Contact</div><h1>How can we help?</h1><p>Have a question about a course or your account? Send our team a message.</p></div></section>
<section class="section-pad"><div class="container two-col"><div class="panel"><h2>Send a message</h2>
@if(session('success'))<div class="notice notice-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="notice notice-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ url('/contact') }}">@csrf
<div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name', auth()->user()->name ?? '') }}" required maxlength="120"></div>
<div class="field"><label for="email">Email address</label><input id="email" name="email" type="email" value="{{ old('email', auth()->user()->email ?? '') }}" required maxlength="190"></div>
<div class="field"><label for="subject">Subject</label><input id="subject" name="subject" value="{{ old('subject') }}" required maxlength="150"></div>
<div class="field"><label for="message">Message</label><textarea id="message" name="message" rows="5" required maxlength="5000">{{ old('message') }}</textarea></div>
<button class="btn btn-purple" type="submit">Send message →</button>
</form></div><aside><div class="panel"><h2>Contact details</h2><div class="list-row"><span>✉ Email</span><a href="mailto:support@edusphere.in">support@edusphere.in</a></div><div class="list-row"><span>☎ Phone</span><a href="tel:+919876543210">+91 98765 43210</a></div><div class="list-row"><span>📍 Location</span><strong>India</strong></div><p class="muted" style="font-size:12px;margin-top:18px">Update these contact details in your site settings before launch.</p></div></aside></div></section>
@endsection
