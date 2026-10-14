@extends('layouts.frontend')
@section('title', 'Complete Payment')
@section('content')
<section class="section-pad"><div class="container" style="max-width:720px"><div class="panel" style="text-align:center;padding:38px">
    <div style="font-size:48px">🔐</div><h1>Complete your payment</h1><p class="muted">Your secure Razorpay checkout is ready. Complete the payment in the payment window.</p>
    <div class="notice">Amount: <strong>₹{{ number_format(((int)($amount ?? 0))/100, 2) }}</strong></div>
    <button id="payNow" class="btn btn-purple">Open secure payment</button>
    <p class="muted" style="font-size:12px;margin-top:16px">If the payment window does not open, click the button again.</p>
    <form id="verifyPaymentForm" method="POST" action="{{ url('/checkout/verify') }}" style="display:none">@csrf
        <input type="hidden" name="razorpay_payment_id" id="razorpay_payment_id">
        <input type="hidden" name="razorpay_order_id" id="razorpay_order_id">
        <input type="hidden" name="razorpay_signature" id="razorpay_signature">
    </form>
</div></div></section>
@endsection
@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script>
(function () {
    const options = {
        key: @json($razorpayKey ?? config('services.razorpay.key')),
        amount: @json((int)($amount ?? 0)),
        currency: @json($currency ?? 'INR'),
        name: 'EduSphere',
        description: 'Course enrolment',
        order_id: @json($orderId ?? ''),
        prefill: { name: @json($name ?? ''), email: @json($email ?? '') },
        theme: { color: '#32105f' },
        handler: function (response) {
            document.getElementById('razorpay_payment_id').value = response.razorpay_payment_id || '';
            document.getElementById('razorpay_order_id').value = response.razorpay_order_id || '';
            document.getElementById('razorpay_signature').value = response.razorpay_signature || '';
            document.getElementById('verifyPaymentForm').submit();
        },
        modal: { ondismiss: function () { window.location.href = @json(url('/checkout')); } }
    };
    const openCheckout = function () {
        if (!options.key || !options.order_id) { alert('Payment is not configured correctly. Please contact support.'); return; }
        const checkout = new Razorpay(options);
        checkout.on('payment.failed', function () { alert('Payment failed. Please try again.'); });
        checkout.open();
    };
    document.getElementById('payNow').addEventListener('click', openCheckout);
    if (window.Razorpay) openCheckout();
})();
</script>
@endpush
