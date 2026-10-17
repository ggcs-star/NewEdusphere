
@extends('layouts.frontend')

@section('title', 'My Learning')

@section('content')
<section class="section-pad ml-page">
    <div class="container">

        <header class="ml-header">
            <div>
                <span class="ml-eyebrow">EDUSPHERE / LEARNING</span>
                <h1>My Learning</h1>
                <p>Welcome back, {{ $user->name }}. Continue where you left off.</p>
            </div>
            <a href="{{ url('/courses') }}" class="ml-primary-btn">Explore courses <span>↗</span></a>
        </header>

        <div class="ml-stats">
            <div class="ml-stat">
                <span class="ml-stat-icon">▤</span>
                <div><small>Enrolled courses</small><strong>{{ number_format($totalEnrollments) }}</strong></div>
            </div>
            <div class="ml-stat">
                <span class="ml-stat-icon">✓</span>
                <div><small>Successful payments</small><strong>{{ number_format($successfulPayments) }}</strong></div>
            </div>
            <div class="ml-stat">
                <span class="ml-stat-icon">₹</span>
                <div><small>Total paid</small><strong>₹{{ number_format((float) $totalSpent, 2) }}</strong></div>
            </div>
        </div>

        <div class="ml-list-heading">
            <div>
                <h2>Your courses</h2>
                <p>Access your learning and purchase records.</p>
            </div>
            <span class="ml-count">{{ $courses->total() }} courses</span>
        </div>

        <div class="ml-grid">
            @forelse ($courses as $course)
                @php
                    $enrollment = $course->enrollments->first();
                    $payment = $course->payments->first();
                    $paymentStatus = strtolower((string) ($payment?->status ?? 'unknown'));

                    $statusClass = match ($paymentStatus) {
                        'paid', 'captured', 'completed', 'success', 'successful' => 'paid',
                        'pending', 'processing' => 'pending',
                        'failed' => 'failed',
                        'refunded', 'partially_refunded' => 'refunded',
                        default => 'unknown',
                    };
                @endphp

                <article class="ml-card">
                    <a class="ml-image-wrap"
                       href="{{ url('/home/course/' . $course->slug . '/' . $course->id) }}">
                        <img src="{{ $course->thumbnailUrl() }}"
                             alt="{{ $course->title }}"
                             loading="lazy">
                        <span class="ml-category">
                            {{ $course->category?->name ?? 'Course' }}
                        </span>
                    </a>

                    <div class="ml-card-body">
                        <div class="ml-meta">
                            <span>{{ ucfirst($course->level ?? 'All levels') }}</span>
                            <span>{{ $course->lessons_count }} lessons</span>
                        </div>

                        <h3>
                            <a href="{{ url('/courses/' . $course->slug . '/' . $course->id) }}">
                                {{ $course->title }}
                            </a>
                        </h3>

                        <p class="ml-description">
                            {{ \Illuminate\Support\Str::limit(strip_tags($course->short_description ?? ''), 95) }}
                        </p>

                        <div class="ml-instructor">
                            <span class="ml-avatar">
                                {{ strtoupper(substr($course->instructor?->name ?? 'I', 0, 1)) }}
                            </span>
                            <div>
                                <small>Instructor</small>
                                <strong>{{ $course->instructor?->name ?? 'Course instructor' }}</strong>
                            </div>
                        </div>

                        <div class="ml-enrollment">
                            <span>Enrolled</span>
                            <strong>{{ $enrollment?->created_at?->format('d M Y') ?? '—' }}</strong>
                        </div>

                        <a href="{{ route('home.course.show', [
    'slug' => $course->slug,
    'id' => $course->id,
]) }}"
                           class="ml-continue">
                            View course <span>→</span>
                        </a>

                        <details class="ml-payment">
                            <summary>
                                <span>Purchase details</span>
                                <span class="ml-summary-right">
                                    @if ($payment)
                                        <span class="ml-status ml-status-{{ $statusClass }}">
                                            {{ ucfirst(str_replace('_', ' ', $paymentStatus)) }}
                                        </span>
                                    @endif
                                    <span class="ml-chevron">⌄</span>
                                </span>
                            </summary>

                            @if ($payment)
                                <div class="ml-payment-content">
                                    <div class="ml-price-row">
                                        <span>Amount paid / recorded</span>
                                        <strong>₹{{ number_format((float) $payment->amount, 2) }}</strong>
                                    </div>

                                    <div class="ml-detail-row">
                                        <span>Internal Payment ID</span>
                                        <strong>#{{ $payment->id }}</strong>
                                    </div>

                                    <div class="ml-detail-row">
                                        <span>Payment method</span>
                                        <strong>{{ ucfirst($payment->payment_method) }}</strong>
                                    </div>

                                    <div class="ml-detail-row">
                                        <span>Recorded on</span>
                                        <strong>{{ $payment->created_at?->format('d M Y, h:i A') ?? '—' }}</strong>
                                    </div>

                                    @if ($payment->razorpay_order_id)
                                        <div class="ml-id-row">
                                            <span>Razorpay Order ID</span>
                                            <div>
                                                <code>{{ $payment->razorpay_order_id }}</code>
                                                <button type="button" class="ml-copy"
                                                        data-copy="{{ $payment->razorpay_order_id }}">Copy</button>
                                            </div>
                                        </div>
                                    @endif

                                    @if ($payment->razorpay_payment_id)
                                        <div class="ml-id-row">
                                            <span>Razorpay Payment ID</span>
                                            <div>
                                                <code>{{ $payment->razorpay_payment_id }}</code>
                                                <button type="button" class="ml-copy"
                                                        data-copy="{{ $payment->razorpay_payment_id }}">Copy</button>
                                            </div>
                                        </div>
                                    @endif

                                    <div class="ml-detail-row">
                                        <span>Admin revenue recorded</span>
                                        <strong>₹{{ number_format((float) $payment->admin_revenue, 2) }}</strong>
                                    </div>

                                    <div class="ml-detail-row">
                                        <span>Instructor revenue recorded</span>
                                        <strong>₹{{ number_format((float) $payment->instructor_revenue, 2) }}</strong>
                                    </div>
                                </div>
                            @else
                                <p class="ml-no-payment">
                                    No payment record is linked to this course.
                                </p>
                            @endif
                        </details>
                    </div>
                </article>
            @empty
                <div class="ml-empty">
                    <div>▤</div>
                    <h3>Your learning journey starts here</h3>
                    <p>You haven't enrolled in any active courses yet.</p>
                    <a href="{{ url('/courses') }}" class="ml-primary-btn">Browse courses ↗</a>
                </div>
            @endforelse
        </div>

        @if ($courses->hasPages())
            <div class="ml-pagination">{{ $courses->links() }}</div>
        @endif

    </div>
</section>
@endsection

@push('styles')
<style>
.ml-page {
    min-height: 70vh;
    background: #faf9fc;
    color: #251735;
}
.ml-header {
    display:flex; justify-content:space-between; align-items:center;
    gap:20px; margin-bottom:28px;
}
.ml-eyebrow {
    color:#7953a0; font-size:10px; font-weight:800; letter-spacing:1.6px;
}
.ml-header h1 { font-size:clamp(27px,3vw,36px); margin:8px 0; color:#28113f; font-weight:800; }
.ml-header p, .ml-list-heading p { color:#82798d; margin:0; font-size:14px; }
.ml-primary-btn {
    display:inline-flex; align-items:center; justify-content:center; gap:12px;
    padding:12px 17px; background:#32105f; color:#fff!important;
    text-decoration:none; border-radius:10px; font-weight:700; font-size:13px;
    white-space:nowrap;
}
.ml-primary-btn:hover { background:#4b2081; }
.ml-stats {
    display:grid; grid-template-columns:repeat(3,minmax(0,1fr));
    gap:15px; margin-bottom:35px;
}
.ml-stat {
    display:flex; align-items:center; gap:14px; padding:19px;
    border:1px solid #eee7f4; border-radius:14px; background:#fff;
}
.ml-stat-icon {
    width:43px; height:43px; display:grid; place-items:center;
    background:#f1e9fa; color:#5d328a; border-radius:12px;
    font-size:21px; flex-shrink:0;
}
.ml-stat small { display:block; color:#82798d; font-size:12px; margin-bottom:5px; }
.ml-stat strong { display:block; color:#28113f; font-size:22px; overflow-wrap:anywhere; }
.ml-list-heading {
    display:flex; justify-content:space-between; align-items:center;
    gap:15px; margin-bottom:18px;
}
.ml-list-heading h2 { margin:0 0 5px; font-size:23px; color:#28113f; }
.ml-count { padding:7px 11px; border-radius:30px; background:#f0e8f8; color:#5d328a; font-size:12px; font-weight:700; white-space:nowrap; }
.ml-grid { display:grid; grid-template-columns:repeat(3,minmax(0,1fr)); gap:20px; align-items:start; }
.ml-card {
    min-width:0; overflow:hidden; border:1px solid #eee7f4;
    border-radius:15px; background:#fff;
    box-shadow:0 5px 18px rgba(41,16,63,.035);
}
.ml-image-wrap { display:block; height:165px; position:relative; background:#f0ebf6; overflow:hidden; }
.ml-image-wrap img { width:100%; height:100%; object-fit:cover; display:block; transition:transform .25s; }
.ml-image-wrap:hover img { transform:scale(1.035); }
.ml-category {
    position:absolute; left:11px; bottom:11px; padding:5px 9px;
    border-radius:6px; background:rgba(255,255,255,.95); color:#49246c;
    font-size:10px; font-weight:800; max-width:calc(100% - 22px);
    overflow:hidden; text-overflow:ellipsis; white-space:nowrap;
}
.ml-card-body { padding:16px; }
.ml-meta { display:flex; justify-content:space-between; gap:8px; color:#81778d; font-size:11px; }
.ml-card h3 { margin:10px 0 7px; font-size:17px; line-height:1.4; }
.ml-card h3 a { color:#28113f; text-decoration:none; }
.ml-card h3 a:hover { color:#71429b; }
.ml-description { color:#81778d; font-size:12px; line-height:1.6; min-height:38px; margin:0 0 14px; }
.ml-instructor { display:flex; align-items:center; gap:10px; padding:11px 0; border-top:1px solid #f0ebf5; }
.ml-avatar { width:34px; height:34px; display:grid; place-items:center; border-radius:50%; background:#eee4f8; color:#542c7e; font-weight:800; }
.ml-instructor small { display:block; font-size:10px; color:#8b8194; }
.ml-instructor strong { display:block; margin-top:2px; font-size:12px; color:#382748; }
.ml-enrollment { display:flex; justify-content:space-between; gap:10px; padding:10px 0; border-top:1px solid #f0ebf5; font-size:11px; }
.ml-enrollment span { color:#82798d; }
.ml-enrollment strong { color:#392747; }
.ml-continue {
    display:flex; justify-content:space-between; align-items:center;
    padding:11px 12px; margin-top:6px; border-radius:8px;
    background:#32105f; color:#fff!important; text-decoration:none;
    font-size:12px; font-weight:700;
}
.ml-continue:hover { background:#4b2081; }
.ml-payment { margin-top:12px; border:1px solid #eee7f4; border-radius:9px; overflow:hidden; }
.ml-payment summary {
    list-style:none; display:flex; justify-content:space-between; align-items:center;
    gap:8px; cursor:pointer; padding:12px; color:#4a345c; font-size:12px; font-weight:700;
}
.ml-payment summary::-webkit-details-marker { display:none; }
.ml-summary-right { display:flex; align-items:center; gap:7px; }
.ml-chevron { font-size:16px; transition:transform .2s; }
.ml-payment[open] .ml-chevron { transform:rotate(180deg); }
.ml-status { padding:4px 7px; border-radius:20px; font-size:9px; font-weight:800; white-space:nowrap; }
.ml-status-paid { background:#e4f6eb; color:#176a3b; }
.ml-status-pending { background:#fff2d8; color:#895800; }
.ml-status-failed { background:#fde8e8; color:#a22222; }
.ml-status-refunded { background:#ece8ff; color:#4b399b; }
.ml-status-unknown { background:#f0edf3; color:#6c6276; }
.ml-payment-content { padding:0 12px 12px; border-top:1px solid #f0ebf5; }
.ml-price-row, .ml-detail-row {
    display:flex; justify-content:space-between; align-items:flex-start;
    gap:10px; padding:9px 0; border-bottom:1px solid #f3eef6; font-size:11px;
}
.ml-price-row span, .ml-detail-row span, .ml-id-row > span { color:#82798d; }
.ml-price-row strong { color:#32105f; font-size:14px; }
.ml-detail-row strong { color:#403049; text-align:right; overflow-wrap:anywhere; }
.ml-id-row { padding:10px 0; border-bottom:1px solid #f3eef6; }
.ml-id-row > span { display:block; font-size:10px; margin-bottom:7px; }
.ml-id-row > div { display:flex; gap:6px; align-items:center; }
.ml-id-row code { min-width:0; flex:1; font-size:10px; color:#5c377c; overflow-wrap:anywhere; word-break:break-word; }
.ml-copy {
    border:1px solid #e6daef; border-radius:6px; background:#f7f1fc;
    padding:5px 7px; color:#53287a; font-size:10px; cursor:pointer;
}
.ml-no-payment { padding:12px; margin:0; font-size:11px; color:#82798d; line-height:1.6; }
.ml-empty { grid-column:1/-1; padding:55px 20px; border:1px dashed #ded0ec; border-radius:15px; text-align:center; background:#fff; }
.ml-empty > div { font-size:40px; color:#5d328a; }
.ml-empty h3 { color:#28113f; }
.ml-empty p { color:#82798d; margin-bottom:20px; }
.ml-pagination { margin-top:28px; }
@media(max-width:1000px) {
    .ml-grid { grid-template-columns:repeat(2,minmax(0,1fr)); }
}
@media(max-width:650px) {
    .ml-header { align-items:flex-start; flex-direction:column; }
    .ml-stats { grid-template-columns:1fr; gap:10px; }
    .ml-stat { padding:15px; }
    .ml-grid { grid-template-columns:1fr; }
    .ml-image-wrap { height:205px; }
    .ml-list-heading { align-items:flex-start; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('click', async function (event) {
    const button = event.target.closest('.ml-copy');
    if (!button) return;

    const value = button.dataset.copy;
    const originalText = button.textContent;

    try {
        await navigator.clipboard.writeText(value);
        button.textContent = 'Copied';
    } catch (error) {
        window.prompt('Copy this ID:', value);
        return;
    }

    setTimeout(function () {
        button.textContent = originalText;
    }, 1200);
});
</script>
@endpush
