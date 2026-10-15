
@extends('layouts.frontend')

@section('title', 'Complete Payment')

@section('content')
<section class="section-pad">
    <div class="container" style="max-width: 720px;">
        <div class="panel" style="text-align: center; padding: 38px;">

            <div style="font-size: 48px;">🔐</div>

            <h1>Complete Your Payment</h1>

            <p class="muted">
                Complete your secure payment to get access to your course.
            </p>

            <div class="notice" style="margin: 24px 0;">
                <div>Order ID</div>
                <strong>{{ $orderId }}</strong>

                <div style="margin-top: 12px;">Total Amount</div>
                <strong style="font-size: 24px;">
                    {{ $currency ?? 'INR' }} {{ number_format(((int) $amount) / 100, 2) }}
                </strong>
            </div>

            <button
                type="button"
                id="payNow"
                class="btn btn-purple"
            >
                Pay Securely Now
            </button>

            <p
                id="paymentMessage"
                class="muted"
                style="font-size: 13px; margin-top: 16px;"
                role="status"
                aria-live="polite"
            >
                Your payment will be verified securely by our server.
            </p>

            <form
                id="verifyPaymentForm"
                method="POST"
                action="{{ route('checkout.verify') }}"
                style="display: none;"
            >
                @csrf

                <input
                    type="hidden"
                    name="razorpay_payment_id"
                    id="razorpay_payment_id"
                >

                <input
                    type="hidden"
                    name="razorpay_order_id"
                    id="razorpay_order_id"
                >

                <input
                    type="hidden"
                    name="razorpay_signature"
                    id="razorpay_signature"
                >
            </form>

        </div>
    </div>
</section>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>

<script>
(function () {
    'use strict';

    const payButton = document.getElementById('payNow');
    const paymentMessage = document.getElementById('paymentMessage');

    const verifyForm = document.getElementById('verifyPaymentForm');

    const razorpayKey = @json($razorpayKey);
    const orderId = @json($orderId);
    const amount = @json((int) $amount);
    const currency = @json($currency ?? 'INR');

    const customerName = @json($name ?? '');
    const customerEmail = @json($email ?? '');

    if (!payButton || !verifyForm) {
        return;
    }

    function setMessage(message) {
        paymentMessage.textContent = message;
    }

    function openCheckout() {
        if (!razorpayKey || !orderId || amount <= 0) {
            setMessage('Payment configuration is incomplete. Please contact support.');
            return;
        }

        if (typeof window.Razorpay === 'undefined') {
            setMessage('Payment gateway could not load. Please refresh and try again.');
            return;
        }

        payButton.disabled = true;
        payButton.textContent = 'Opening Secure Checkout...';

        const options = {
            key: razorpayKey,
            amount: amount,
            currency: currency,
            name: 'EduSphere',
            description: 'Course enrolment',
            order_id: orderId,

            prefill: {
                name: customerName,
                email: customerEmail
            },

            theme: {
                color: '#32105f'
            },

            handler: function (response) {
                if (
                    !response.razorpay_payment_id ||
                    !response.razorpay_order_id ||
                    !response.razorpay_signature
                ) {
                    setMessage('Payment response is incomplete. Please contact support.');
                    payButton.disabled = false;
                    payButton.textContent = 'Try Payment Again';
                    return;
                }

                document.getElementById('razorpay_payment_id').value =
                    response.razorpay_payment_id;

                document.getElementById('razorpay_order_id').value =
                    response.razorpay_order_id;

                document.getElementById('razorpay_signature').value =
                    response.razorpay_signature;

                payButton.textContent = 'Verifying Payment...';
                setMessage('Please wait while we verify your payment.');

                verifyForm.submit();
            },

            modal: {
                ondismiss: function () {
                    payButton.disabled = false;
                    payButton.textContent = 'Pay Securely Now';

                    setMessage(
                        'Checkout closed. You can retry payment using the button.'
                    );
                }
            }
        };

        try {
            const checkout = new window.Razorpay(options);

            checkout.on('payment.failed', function () {
                payButton.disabled = false;
                payButton.textContent = 'Try Payment Again';

                setMessage(
                    'Payment failed or was not completed. Please try again.'
                );
            });

            checkout.open();
        } catch (error) {
            payButton.disabled = false;
            payButton.textContent = 'Try Payment Again';

            setMessage('Unable to open payment checkout. Please try again.');
        }
    }

    payButton.addEventListener('click', openCheckout);
})();
</script>
@endpush
