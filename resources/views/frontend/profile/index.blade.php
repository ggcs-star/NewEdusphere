@extends('layouts.frontend')
@section('title','My Profile')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Profile</div><h1>Account profile</h1><p>Keep your contact details up to date.</p></div></section>
<section class="section-pad"><div class="container" style="max-width:820px"><div class="panel">
@if(session('success'))<div class="notice notice-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="notice notice-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ url('/profile') }}">@csrf @method('PUT')
<div class="two-col"><div class="field"><label for="first_name">First name</label><input id="first_name" name="first_name" value="{{ old('first_name', $user->first_name ?? '') }}" required></div><div class="field"><label for="last_name">Last name</label><input id="last_name" name="last_name" value="{{ old('last_name', $user->last_name ?? '') }}"></div></div>
<div class="field"><label for="email">Email address</label><input id="email" type="email" value="{{ $user->email ?? '' }}" disabled><small class="muted">Email changes should be handled through your account verification flow.</small></div>
<div class="field"><label for="biography">About me</label><textarea id="biography" name="biography" rows="4">{{ old('biography', $user->biography ?? '') }}</textarea></div>
<button class="btn btn-purple" type="submit">Save profile</button>
</form></div></div></section>
@endsection
