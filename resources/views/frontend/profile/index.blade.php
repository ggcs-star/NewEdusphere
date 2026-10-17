@extends('layouts.frontend')
@section('title','My Profile')
@section('content')
<section class="page-hero"><div class="container"><div class="breadcrumbs"><a href="{{ route('home') }}">Home</a> / Profile</div><h1>Account profile</h1><p>Keep your contact details up to date.</p></div></section>
<section class="section-pad"><div class="container" style="max-width:820px"><div class="panel">
@if(session('success'))<div class="notice notice-success">{{ session('success') }}</div>@endif
@if($errors->any())<div class="notice notice-error"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('profile.update') }}" enctype="multipart/form-data">
    @csrf @method('PUT')

    <h3 style="margin-bottom:16px">Basic Info</h3>

    <div class="field" style="display:flex;align-items:center;gap:16px;margin-bottom:24px">
        @if ($user->avatarUrl())
            <img src="{{ $user->avatarUrl() }}" alt="{{ $user->name }}" style="width:64px;height:64px;border-radius:50%;object-fit:cover;flex-shrink:0">
        @else
            <div style="width:64px;height:64px;border-radius:50%;background:var(--purple);color:#fff;display:flex;align-items:center;justify-content:center;font-size:22px;font-weight:700;flex-shrink:0">
                {{ strtoupper(substr($user->name, 0, 1)) }}
            </div>
        @endif
        <div>
            <label for="avatar" style="display:block;margin-bottom:6px">Profile Photo</label>
            <input id="avatar" type="file" name="avatar" accept="image/*">
        </div>
    </div>

    <div class="field"><label for="name">Full name</label><input id="name" name="name" value="{{ old('name', $user->name) }}" required></div>
    <div class="field"><label for="email">Email address</label><input id="email" type="email" value="{{ $user->email }}" disabled><small class="muted">Email changes should be handled through your account verification flow.</small></div>
    <div class="field"><label for="phone">Phone</label><input id="phone" name="phone" value="{{ old('phone', $user->phone) }}"></div>
    <div class="field"><label for="biography">About me</label><textarea id="biography" name="biography" rows="4">{{ old('biography', $user->biography) }}</textarea></div>
    <div class="field"><label for="short_title">A Short Title About Yourself</label><input id="short_title" name="short_title" placeholder="e.g. Senior Web Developer" value="{{ old('short_title', $user->short_title) }}"></div>

    <hr style="margin:32px 0;border:none;border-top:1px solid var(--border)">

    <h3 style="margin-bottom:16px">Social Links</h3>
    <div class="field"><label for="facebook_link">Facebook Link</label><input id="facebook_link" name="facebook_link" placeholder="facebook.com/yourprofile" value="{{ old('facebook_link', $user->facebook_link) }}"></div>
    <div class="field"><label for="twitter_link">Twitter Link</label><input id="twitter_link" name="twitter_link" placeholder="x.com/yourprofile" value="{{ old('twitter_link', $user->twitter_link) }}"></div>
    <div class="field"><label for="linkedin_link">Linkedin Link</label><input id="linkedin_link" name="linkedin_link" placeholder="linkedin.com/in/yourprofile" value="{{ old('linkedin_link', $user->linkedin_link) }}"></div>

    <hr style="margin:32px 0;border:none;border-top:1px solid var(--border)">

    <h3 style="margin-bottom:4px">Change Password</h3>
    <p class="muted" style="font-size:13px;margin-top:0;margin-bottom:16px">Leave these blank to keep your current password.</p>
    <div class="two-col">
        <div class="field" x-data="{ show: false }">
            <label for="current_password">Current password</label>
            <div style="position:relative">
                <input id="current_password" :type="show ? 'text' : 'password'" name="current_password" autocomplete="current-password" style="padding-right:40px">
                <button type="button" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" style="position:absolute;right:0;top:0;height:100%;width:40px;border:none;background:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>
        <div class="field" x-data="{ show: false }">
            <label for="new_password">New password</label>
            <div style="position:relative">
                <input id="new_password" :type="show ? 'text' : 'password'" name="new_password" autocomplete="new-password" style="padding-right:40px">
                <button type="button" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" style="position:absolute;right:0;top:0;height:100%;width:40px;border:none;background:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;justify-content:center">
                    <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
                </button>
            </div>
        </div>
    </div>
    <div class="field" x-data="{ show: false }">
        <label for="new_password_confirmation">Confirm new password</label>
        <div style="position:relative">
            <input id="new_password_confirmation" :type="show ? 'text' : 'password'" name="new_password_confirmation" autocomplete="new-password" style="padding-right:40px">
            <button type="button" @click="show = !show" :aria-label="show ? 'Hide password' : 'Show password'" style="position:absolute;right:0;top:0;height:100%;width:40px;border:none;background:none;cursor:pointer;color:var(--muted);display:flex;align-items:center;justify-content:center">
                <i class="fa-solid" :class="show ? 'fa-eye-slash' : 'fa-eye'"></i>
            </button>
        </div>
    </div>

    <button class="btn btn-purple" type="submit" style="margin-top:24px">Save Changes</button>
</form>
</div></div></section>
@endsection
