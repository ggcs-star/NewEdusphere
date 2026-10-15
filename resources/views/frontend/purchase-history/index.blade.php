
@extends('layouts.frontend')

@section('title', 'Purchase History')

@section('content')
    <section class="page-hero">
        <div class="container">
            <div class="breadcrumbs">
                <a href="{{ route('home') }}">Home</a> / Purchase history
            </div>

            <h1>Purchase History</h1>
            <p>Review your course payments and enrolment records.</p>
        </div>
    </section>

    <section class="section-pad">
        <div class="container">
            <div class="panel">
                @if(isset($payments) && $payments->count())
                    <div style="overflow-x: auto;">
                        <table style="width: 100%; border-collapse: collapse; text-align: left;">
                            <thead>
                                <tr>
                                    <th style="padding: 14px; border-bottom: 1px solid var(--border);">Course</th>
                                    <th style="padding: 14px; border-bottom: 1px solid var(--border);">Date</th>
                                    <th style="padding: 14px; border-bottom: 1px solid var(--border);">Payment Method</th>
                                    <th style="padding: 14px; border-bottom: 1px solid var(--border);">Amount</th>
                                    <th style="padding: 14px; border-bottom: 1px solid var(--border);">Status</th>
                                </tr>
                            </thead>

                            <tbody>
                                @foreach($payments as $payment)
                                    <tr>
                                        <td style="padding: 14px; border-bottom: 1px solid var(--border);">
                                            {{ $payment->course?->title ?? ('Course #' . ($payment->course_id ?? 'N/A')) }}
                                        </td>

                                        <td style="padding: 14px; border-bottom: 1px solid var(--border);">
                                            @php
                                                $paymentDate = $payment->created_at ?? null;
                                            @endphp

                                            @if($paymentDate)
                                                {{ \Illuminate\Support\Carbon::parse($paymentDate)->format('d M Y, h:i A') }}
                                            @else
                                                —
                                            @endif
                                        </td>

                                        <td style="padding: 14px; border-bottom: 1px solid var(--border);">
                                            {{ ucfirst($payment->payment_method ?? 'N/A') }}
                                        </td>

                                        <td style="padding: 14px; border-bottom: 1px solid var(--border);">
                                            ₹{{ number_format((float) ($payment->amount ?? 0), 2) }}
                                        </td>

                                        <td style="padding: 14px; border-bottom: 1px solid var(--border);">
                                            {{ ucfirst($payment->status ?? 'pending') }}
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>

                    @if(method_exists($payments, 'links'))
                        <div class="pagination-links">
                            {{ $payments->links() }}
                        </div>
                    @endif
                @else
                    <div class="empty-state">
                        <div class="empty-icon">🧾</div>
                        <h2>No purchases yet</h2>
                        <p class="muted">
                            Your course purchases will appear here after checkout.
                        </p>

                        <a href="{{ url('/courses') }}" class="btn btn-purple">
                            Explore Courses
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </section>
@endsection
