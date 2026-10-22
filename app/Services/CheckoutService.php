<?php

namespace App\Services;

use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Razorpay\Api\Api;
use Throwable;

/**
 * Pricing, Razorpay order creation and payment verification — shared by
 * the web CheckoutController (which resolves course ids from the
 * session cart) and Api\V1\CheckoutController (which takes course ids
 * straight from the request body / the persistent CartService).
 *
 * Every public method returns a plain ['outcome' => ..., ...] array
 * instead of a Response, so each controller can translate the same
 * outcome into a web redirect+flash or a JSON response.
 */
class CheckoutService
{
    /**
     * Admin-configured Razorpay credentials (Settings -> Payment) take
     * priority; .env is only a fallback for deployments that haven't set
     * them through the admin panel yet.
     *
     * @return array{key: ?string, secret: ?string}
     */
    public function razorpayCredentials(): array
    {
        return [
            'key' => Setting::get('razorpay_key') ?: config('services.razorpay.key'),
            'secret' => Setting::get('razorpay_secret') ?: config('services.razorpay.secret'),
        ];
    }

    /**
     * Splits a sale amount between the platform and the course's instructor,
     * per Settings -> Instructor. The admin's own courses keep 100% (nothing
     * to pay out), and so does every course when "Allow users to become
     * instructors and sell courses" is off.
     *
     * @return array{adminRevenue: float, instructorRevenue: float, instructorPaidOut: bool}
     */
    private function splitRevenue(Course $course, float $amount): array
    {
        if ($course->instructor?->isAdmin() || Setting::get('allow_instructor', '1') !== '1') {
            return ['adminRevenue' => $amount, 'instructorRevenue' => 0.0, 'instructorPaidOut' => true];
        }

        $percent = max(0, min(100, (float) Setting::get('instructor_revenue_percent', '70')));
        $instructorRevenue = round($amount * $percent / 100, 2);
        $adminRevenue = round($amount - $instructorRevenue, 2);

        return ['adminRevenue' => $adminRevenue, 'instructorRevenue' => $instructorRevenue, 'instructorPaidOut' => false];
    }

    /**
     * Active courses for the given ids plus their price breakdown —
     * used to render the checkout page / preview before paying.
     *
     * @return array{items: Collection, subtotal: float, courses: Collection}
     */
    public function previewItems(array $courseIds): array
    {
        $courses = Course::query()
            ->with(['instructor', 'category'])
            ->where('status', 'active')
            ->whereIn('id', $courseIds)
            ->get();

        $items = $courses->map(fn (Course $course) => [
            'course' => $course,
            'price' => $course->effectivePrice(),
        ]);

        return [
            'items' => $items,
            'subtotal' => (float) $courses->sum(fn (Course $course) => $course->effectivePrice()),
            'courses' => $courses,
        ];
    }

    /**
     * Create a Razorpay order, or enroll the user directly for free
     * courses. Returns one of:
     *   empty_cart | unavailable | already_enrolled | free_enrolled |
     *   gateway_not_configured | gateway_error | order_created
     */
    public function pay(User $user, array $courseIds): array
    {
        if (empty($courseIds)) {
            return ['outcome' => 'empty_cart'];
        }

        $courses = Course::query()
            ->with('instructor')
            ->where('status', 'active')
            ->whereIn('id', $courseIds)
            ->get();

        if ($courses->count() !== count($courseIds)) {
            return ['outcome' => 'unavailable'];
        }

        // Do not charge again for courses the user already owns.
        $alreadyEnrolledIds = Enrollment::query()
            ->where('user_id', $user->id)
            ->whereIn('course_id', $courseIds)
            ->pluck('course_id')
            ->all();

        $courses = $courses
            ->reject(fn (Course $course) => in_array($course->id, $alreadyEnrolledIds))
            ->values();

        if ($courses->isEmpty()) {
            return ['outcome' => 'already_enrolled'];
        }

        $subtotal = round($courses->sum(fn (Course $course) => $course->effectivePrice()), 2);

        // Free courses require no payment gateway.
        if ($subtotal <= 0) {
            DB::transaction(function () use ($user, $courses) {
                foreach ($courses as $course) {
                    Payment::firstOrCreate(
                        [
                            'user_id' => $user->id,
                            'course_id' => $course->id,
                            'payment_method' => 'free',
                        ],
                        [
                            'amount' => 0,
                            'admin_revenue' => 0,
                            'instructor_revenue' => 0,
                            'instructor_paid_out' => true,
                            'status' => 'paid',
                        ]
                    );

                    Enrollment::firstOrCreate([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                    ]);
                }
            });

            return ['outcome' => 'free_enrolled'];
        }

        ['key' => $key, 'secret' => $secret] = $this->razorpayCredentials();

        if (empty($key) || empty($secret)) {
            return ['outcome' => 'gateway_not_configured'];
        }

        try {
            $api = new Api($key, $secret);

            // Razorpay expects the amount in the smallest currency unit.
            $amountInPaise = (int) round($subtotal * 100);

            $order = $api->order->create([
                'receipt' => 'edu_' . bin2hex(random_bytes(10)),
                'amount' => $amountInPaise,
                'currency' => 'INR',
                'notes' => [
                    'user_id' => (string) $user->id,
                ],
            ]);

            DB::transaction(function () use ($user, $courses, $order) {
                foreach ($courses as $course) {
                    $split = $this->splitRevenue($course, $course->effectivePrice());

                    Payment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'payment_method' => 'razorpay',
                        'amount' => $course->effectivePrice(),
                        'admin_revenue' => $split['adminRevenue'],
                        'instructor_revenue' => $split['instructorRevenue'],
                        'instructor_paid_out' => $split['instructorPaidOut'],
                        'razorpay_order_id' => $order['id'],
                        'status' => 'pending',
                    ]);
                }
            });

            return [
                'outcome' => 'order_created',
                'razorpay' => [
                    'key' => $key,
                    'order_id' => $order['id'],
                    'amount' => $amountInPaise,
                    'currency' => 'INR',
                    'name' => $user->name,
                    'email' => $user->email,
                ],
            ];
        } catch (Throwable $e) {
            Log::error('Razorpay order creation failed.', [
                'user_id' => $user->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return ['outcome' => 'gateway_error'];
        }
    }

    /**
     * Verify the Razorpay signature and confirm the payment server-side.
     * Returns one of: order_not_found | already_paid | verified | mismatch | error
     *
     * @param  array{razorpay_order_id: string, razorpay_payment_id: string, razorpay_signature: string}  $validated
     */
    public function verify(User $user, array $validated): array
    {
        $payments = Payment::query()
            ->where('user_id', $user->id)
            ->where('payment_method', 'razorpay')
            ->where('razorpay_order_id', $validated['razorpay_order_id'])
            ->get();

        if ($payments->isEmpty()) {
            return ['outcome' => 'order_not_found'];
        }

        if ($payments->every(fn (Payment $payment) => $payment->status === 'paid')) {
            return ['outcome' => 'already_paid'];
        }

        try {
            ['key' => $key, 'secret' => $secret] = $this->razorpayCredentials();

            if (empty($key) || empty($secret)) {
                throw new \RuntimeException('Razorpay credentials are not configured.');
            }

            $api = new Api($key, $secret);

            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);

            $gatewayOrder = $api->order->fetch($validated['razorpay_order_id']);
            $gatewayPayment = $api->payment->fetch($validated['razorpay_payment_id']);

            $recordsValid = $payments->every(
                fn (Payment $payment) => (int) $payment->user_id === (int) $user->id
                    && $payment->payment_method === 'razorpay'
                    && $payment->razorpay_order_id === $validated['razorpay_order_id']
                    && (float) $payment->amount > 0
            );

            $expectedAmount = (int) round(
                $payments->sum(fn (Payment $payment) => (float) $payment->amount) * 100
            );

            if (
                !$recordsValid
                || $expectedAmount <= 0
                || (int) $gatewayOrder['amount'] !== $expectedAmount
                || ($gatewayOrder['currency'] ?? '') !== 'INR'
                || ($gatewayPayment['order_id'] ?? '') !== $validated['razorpay_order_id']
                || ($gatewayPayment['status'] ?? '') !== 'captured'
                || (int) $gatewayPayment['amount'] !== $expectedAmount
                || ($gatewayPayment['currency'] ?? '') !== 'INR'
            ) {
                Log::warning('Razorpay payment details did not match.', [
                    'user_id' => $user->id,
                    'order_id' => $validated['razorpay_order_id'],
                    'payment_id' => $validated['razorpay_payment_id'],
                ]);

                return ['outcome' => 'mismatch'];
            }

            DB::transaction(function () use ($payments, $validated, $user) {
                foreach ($payments as $payment) {
                    $lockedPayment = Payment::query()
                        ->whereKey($payment->id)
                        ->where('user_id', $user->id)
                        ->where('payment_method', 'razorpay')
                        ->where('razorpay_order_id', $validated['razorpay_order_id'])
                        ->lockForUpdate()
                        ->firstOrFail();

                    if ($lockedPayment->status === 'paid') {
                        continue;
                    }

                    $lockedPayment->update([
                        'razorpay_payment_id' => $validated['razorpay_payment_id'],
                        'razorpay_signature' => $validated['razorpay_signature'],
                        'status' => 'paid',
                    ]);

                    Enrollment::firstOrCreate([
                        'user_id' => $user->id,
                        'course_id' => $lockedPayment->course_id,
                    ]);
                }
            });

            return ['outcome' => 'verified'];
        } catch (Throwable $e) {
            Log::warning('Razorpay verification failed.', [
                'user_id' => $user->id,
                'order_id' => $validated['razorpay_order_id'],
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return ['outcome' => 'error'];
        }
    }
}
