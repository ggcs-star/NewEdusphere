@extends('layouts.frontend')

@section('title', 'My Dashboard')

@section('content')
<section class="section-pad db-page">
    <div class="container">

        <header class="db-header">
            <div>
                <span class="db-eyebrow">EDUSPHERE / DASHBOARD</span>
                <h1>Welcome back{{ !empty($user) && !empty($user->name) ? ', ' . explode(' ', trim($user->name))[0] : '' }}!</h1>
                <p>Your learning space: access your courses, track your progress and discover what to learn next.</p>
            </div>
            <form method="POST" action="{{ route('logout') }}" onsubmit="return confirm('Are you sure you want to logout?');">
                @csrf
                <button type="submit" class="db-logout-btn">Logout</button>
            </form>
        </header>

        @if(session('success'))
            <div class="notice notice-success">{{ session('success') }}</div>
        @endif
        @if(session('error'))
            <div class="notice notice-error">{{ session('error') }}</div>
        @endif

        <div class="db-stats">
            <div class="db-stat">
                <span class="db-stat-icon">▤</span>
                <div><small>My courses</small><strong>{{ $enrolledCount ?? (isset($courses) ? $courses->count() : 0) }}</strong></div>
            </div>
            <div class="db-stat">
                <span class="db-stat-icon">✦</span>
                <div><small>Learning goal</small><strong>Stay curious</strong></div>
            </div>
            <div class="db-stat">
                <span class="db-stat-icon">●</span>
                <div><small>Account</small><strong>Active</strong></div>
            </div>
        </div>

        <div class="db-actions">
            <a class="db-action" href="{{ url('/my-courses') }}">
                <span class="db-action-icon">📚</span>
                <div>
                    <h2>Continue learning</h2>
                    <p>Open your enrolled courses and continue your learning journey.</p>
                    <strong>View my courses <span>→</span></strong>
                </div>
            </a>

            <a class="db-action" href="{{ url('/courses') }}">
                <span class="db-action-icon">🔎</span>
                <div>
                    <h2>Discover courses</h2>
                    <p>Explore new subjects, skills and learning paths.</p>
                    <strong>Browse courses <span>→</span></strong>
                </div>
            </a>

            <a class="db-action" href="{{ url('/cart') }}">
                <span class="db-action-icon">🛒</span>
                <div>
                    <h2>My cart</h2>
                    <p>Review the courses you have selected.</p>
                    <strong>Open cart <span>→</span></strong>
                </div>
            </a>

            <a class="db-action" href="{{ route('profile.edit') }}">
                <span class="db-action-icon">👤</span>
                <div>
                    <h2>Account settings</h2>
                    <p>Review your profile details and account information.</p>
                    <strong>Manage profile <span>→</span></strong>
                </div>
            </a>
        </div>

        <div class="db-list-heading">
            <div>
                <h2>Continue learning</h2>
                <p>Pick up right where you left off.</p>
            </div>
            @if (isset($courses) && $courses->count())
                <a href="{{ url('/my-courses') }}" class="db-view-all">View all <span>→</span></a>
            @endif
        </div>

        @if (isset($courses) && $courses->count())
            <div class="db-grid">
                @foreach ($courses as $course)
                    @include('components.frontend.course-card', ['course' => $course])
                @endforeach
            </div>
        @else
            <div class="db-empty">
                <div>▤</div>
                <h3>Your learning journey starts here</h3>
                <p>You haven't enrolled in any course yet — explore the catalog to get started.</p>
                <a href="{{ url('/courses') }}" class="db-primary-btn">Browse courses ↗</a>
            </div>
        @endif

    </div>
</section>
@endsection

@push('styles')
<style>
.db-page { min-height: 70vh; background: #faf9fc; color: #251735; }
.db-header { display:flex; justify-content:space-between; align-items:center; gap:20px; margin-bottom:28px; flex-wrap:wrap; }
.db-eyebrow { color:#7953a0; font-size:10px; font-weight:800; letter-spacing:1.6px; }
.db-header h1 { font-size:clamp(27px,3vw,36px); margin:8px 0; color:#28113f; font-weight:800; }
.db-header p { color:#82798d; margin:0; font-size:14px; }
.db-logout-btn {
    padding:12px 17px; background:#fff; color:#32105f; border:1px solid #e9e3f0;
    border-radius:10px; font-weight:700; font-size:13px; cursor:pointer; transition:background .15s;
}
.db-logout-btn:hover { background:#f6f1fb; }

.db-stats { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:15px; margin-bottom:28px; }
.db-stat { display:flex; align-items:center; gap:14px; padding:19px; border:1px solid #eee7f4; border-radius:14px; background:#fff; }
.db-stat-icon { width:43px; height:43px; display:grid; place-items:center; background:#f1e9fa; color:#5d328a; border-radius:12px; font-size:21px; flex-shrink:0; }
.db-stat small { display:block; color:#82798d; font-size:12px; margin-bottom:5px; }
.db-stat strong { display:block; color:#28113f; font-size:22px; }

.db-actions { display:grid; grid-template-columns:repeat(2,minmax(0,1fr)); gap:18px; margin-bottom:36px; }
.db-action {
    display:flex; gap:16px; align-items:flex-start; padding:22px; border:1px solid #eee7f4;
    border-radius:16px; background:#fff; text-decoration:none; color:inherit;
    box-shadow:0 5px 18px rgba(41,16,63,.035); transition:transform .15s, box-shadow .15s;
}
.db-action:hover { transform:translateY(-3px); box-shadow:0 12px 26px rgba(41,16,63,.08); }
.db-action-icon { font-size:28px; flex-shrink:0; }
.db-action h2 { margin:0 0 5px; font-size:16px; color:#28113f; }
.db-action p { margin:0 0 10px; color:#82798d; font-size:12.5px; line-height:1.6; }
.db-action strong { display:inline-flex; align-items:center; gap:6px; color:#5d328a; font-size:12.5px; font-weight:700; }

.db-list-heading { display:flex; justify-content:space-between; align-items:center; gap:15px; margin-bottom:18px; }
.db-list-heading h2 { margin:0 0 5px; font-size:23px; color:#28113f; }
.db-list-heading p { margin:0; color:#82798d; font-size:14px; }
.db-view-all { color:#5d328a; font-weight:700; font-size:13px; text-decoration:none; display:inline-flex; align-items:center; gap:6px; white-space:nowrap; }
.db-view-all:hover { color:#32105f; }

.db-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; align-items:start; }

.db-empty { padding:55px 20px; border:1px dashed #ded0ec; border-radius:15px; text-align:center; background:#fff; }
.db-empty > div { font-size:40px; color:#5d328a; }
.db-empty h3 { color:#28113f; margin:12px 0 6px; }
.db-empty p { color:#82798d; margin-bottom:20px; }
.db-primary-btn {
    display:inline-flex; align-items:center; gap:10px; padding:12px 18px; background:#32105f;
    color:#fff!important; text-decoration:none; border-radius:10px; font-weight:700; font-size:13px;
}
.db-primary-btn:hover { background:#4b2081; }

@media(max-width:900px) {
    .db-actions { grid-template-columns:1fr; }
    .db-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:650px) {
    .db-header { align-items:flex-start; flex-direction:column; }
    .db-stats { grid-template-columns:1fr; gap:10px; }
    .db-grid { grid-template-columns:1fr; }
}
</style>
@endpush
