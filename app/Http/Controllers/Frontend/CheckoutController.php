<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Razorpay\Api\Api;
use Throwable;

class CheckoutController extends Controller
{
    /**
     * Admin-configured Razorpay credentials (Settings -> Payment) take
     * priority; .env is only a fallback for deployments that haven't set
     * them through the admin panel yet.
     *
     * @return array{key: ?string, secret: ?string}
     */
    private function razorpayCredentials(): array
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
     * instructors and sell courses" is off — matching how disabling that
     * setting behaves in the reference LMS.
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
     * Display checkout page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $ids = $this->cartCourseIds($request);

        if (empty($ids)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $courses = Course::query()
            ->with(['instructor', 'category'])
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get();

        if ($courses->isEmpty()) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart has no available courses.');
        }

        $subtotal = $courses->sum(
            fn (Course $course) => $course->effectivePrice()
        );

        $isAuthenticated = $this->hasAuthenticatedSession($request);

        $items = $courses->map(fn (Course $course) => [
            'course' => $course,
            'price' => $course->effectivePrice(),
        ]);

        return view('frontend.checkout.index', compact(
            'items',
            'subtotal',
            'isAuthenticated'
        ));
    }

    /**
     * Create a Razorpay order or enroll the user in free courses.
     */
    public function pay(Request $request): View|RedirectResponse
    {
        if (!$this->hasAuthenticatedSession($request)) {
            $request->session()->put(
                'url.intended',
                route('checkout.index')
            );

            return redirect()
                ->route('login')
                ->with(
                    'info',
                    'Please log in to complete your purchase.'
                );
        }

        $user = $this->authenticatedUser($request);
        
if (!$user) {
    $request->session()->forget([
        'user_token',
        'user',
        'user_email',
        'user_name',
    ]);

    return redirect()
        ->route('login')
        ->with('error', 'Your session is invalid. Please log in again.');
}

// Admin is allowed to visit checkout, but cannot purchase courses.
if ($user->isAdmin()) {
    return redirect()
        ->route('admin.dashboard')
        ->with('info', 'Admins cannot purchase courses.');
}

// Instructors create and sell courses, not buy them.
if ($user->isInstructor()) {
    return redirect()
        ->route('instructor.dashboard')
        ->with('info', 'Instructors cannot purchase courses.');
}


        if (!$user) {
            $request->session()->forget([
                'user_token',
                'user',
                'user_email',
                'user_name',
            ]);

            return redirect()
                ->route('login')
                ->with('error', 'Your session is invalid. Please log in again.');
        }

        $ids = $this->cartCourseIds($request);

        if (empty($ids)) {
            return redirect()
                ->route('cart.index')
                ->with('error', 'Your cart is empty.');
        }

        $courses = Course::query()
            ->with('instructor')
            ->where('status', 'active')
            ->whereIn('id', $ids)
            ->get();

        if ($courses->count() !== count($ids)) {
            return redirect()
                ->route('cart.index')
                ->with(
                    'error',
                    'One or more courses are unavailable. Please review your cart.'
                );
        }

        // Do not charge again for courses the user already owns.
        $alreadyEnrolledIds = Enrollment::query()
            ->where('user_id', $user->id)
            ->whereIn('course_id', $ids)
            ->pluck('course_id')
            ->all();

        $courses = $courses
            ->reject(fn (Course $course) => in_array(
                $course->id,
                $alreadyEnrolledIds
            ))
            ->values();

        if ($courses->isEmpty()) {
            $request->session()->forget('cart_course_ids');

            return redirect()
                ->route('my-courses.index')
                ->with('success', 'You are already enrolled in these courses.');
        }

        $subtotal = round(
            $courses->sum(
                fn (Course $course) => $course->effectivePrice()
            ),
            2
        );

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

            $request->session()->forget('cart_course_ids');

            return redirect()
                ->route('my-courses.index')
                ->with('success', 'You are enrolled successfully.');
        }

        ['key' => $key, 'secret' => $secret] = $this->razorpayCredentials();

        if (empty($key) || empty($secret)) {
            return redirect()
                ->route('checkout.index')
                ->with('error', 'Payment gateway configuration is missing.');
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

            // Save a payment record for each course in this order.
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

        return view('frontend.checkout.razorpay', [
    'razorpayKey' => $key,
    'orderId' => $order['id'],
    'amount' => $amountInPaise,
    'currency' => 'INR',
    'name' => $user->name,
    'email' => $user->email,
]);
        } catch (Throwable $e) {
            Log::error('Razorpay order creation failed.', [
                'user_id' => $user->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
            ]);

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Unable to start payment. Please try again.'
                );
        }
    }

/**
 * Verify the Razorpay signature and confirm the payment server-side.
 */
public function verify(Request $request): RedirectResponse
{
    if (!$this->hasAuthenticatedSession($request)) {
        return redirect()
            ->route('login')
            ->with('error', 'Please log in to verify your payment.');
    }

    $user = $this->authenticatedUser($request);

    if (!$user) {
        return redirect()
            ->route('login')
            ->with('error', 'Your session is invalid. Please log in again.');
    }

    $validated = $request->validate([
        'razorpay_order_id' => ['required', 'string', 'max:255'],
        'razorpay_payment_id' => ['required', 'string', 'max:255'],
        'razorpay_signature' => ['required', 'string', 'max:255'],
    ]);

    $payments = Payment::query()
        ->where('user_id', $user->id)
        ->where('payment_method', 'razorpay')
        ->where('razorpay_order_id', $validated['razorpay_order_id'])
        ->get();

    if ($payments->isEmpty()) {
        return redirect()
            ->route('checkout.index')
            ->with('error', 'Payment order not found.');
    }

    if ($payments->every(
        fn (Payment $payment) => $payment->status === 'paid'
    )) {
        return redirect()
            ->route('my-courses.index')
            ->with('success', 'Your courses are already available.');
    }

    try {
        ['key' => $key, 'secret' => $secret] = $this->razorpayCredentials();

        if (empty($key) || empty($secret)) {
            throw new \RuntimeException(
                'Razorpay credentials are not configured.'
            );
        }

        $api = new Api($key, $secret);

        // Verify the signature received from Razorpay.
        $api->utility->verifyPaymentSignature([
            'razorpay_order_id' => $validated['razorpay_order_id'],
            'razorpay_payment_id' => $validated['razorpay_payment_id'],
            'razorpay_signature' => $validated['razorpay_signature'],
        ]);

        // Fetch the order and payment directly from Razorpay.
        $gatewayOrder = $api->order->fetch(
            $validated['razorpay_order_id']
        );

        $gatewayPayment = $api->payment->fetch(
            $validated['razorpay_payment_id']
        );

        // Ensure this order contains valid pending payment records.
        $recordsValid = $payments->every(
            fn (Payment $payment) =>
                (int) $payment->user_id === (int) $user->id
                && $payment->payment_method === 'razorpay'
                && $payment->razorpay_order_id === $validated['razorpay_order_id']
                && (float) $payment->amount > 0
        );

        // Calculate the expected amount in paise.
        $expectedAmount = (int) round(
            $payments->sum(
                fn (Payment $payment) => (float) $payment->amount
            ) * 100
        );

        // Validate the actual Razorpay order and payment.
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

            return redirect()
                ->route('checkout.index')
                ->with(
                    'error',
                    'Payment is not confirmed yet. If money was deducted, please contact support.'
                );
        }

        DB::transaction(function () use (
            $payments,
            $validated,
            $user
        ) {
            foreach ($payments as $payment) {
                $lockedPayment = Payment::query()
                    ->whereKey($payment->id)
                    ->where('user_id', $user->id)
                    ->where('payment_method', 'razorpay')
                    ->where('razorpay_order_id', $validated['razorpay_order_id'])
                    ->lockForUpdate()
                    ->firstOrFail();

                // Do not overwrite a payment already marked as paid.
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

        $request->session()->forget('cart_course_ids');

        return redirect()
            ->route('my-courses.index')
            ->with(
                'success',
                'Payment verified successfully. Your courses are now available.'
            );

    } catch (Throwable $e) {
        Log::warning('Razorpay verification failed.', [
            'user_id' => $user->id,
            'order_id' => $validated['razorpay_order_id'],
            'exception' => get_class($e),
            'message' => $e->getMessage(),
        ]);

        return redirect()
            ->route('checkout.index')
            ->with(
                'error',
                'Payment verification failed. If money was deducted, please contact support.'
            );
    }
}
    /**
     * Return valid, unique course IDs from the session cart.
     */
    private function cartCourseIds(Request $request): array
    {
        return array_values(array_unique(array_filter(
            array_map(
                'intval',
                $request->session()->get('cart_course_ids', [])
            ),
            fn (int $id) => $id > 0
        )));
    }

    /**
     * Check the project's custom web session.
     */
    private function hasAuthenticatedSession(Request $request): bool
    {
        return !empty($request->session()->get('user_token'))
            && !empty($request->session()->get('user'));
    }

    /**
     * Resolve the session user against the database.
     */
    private function authenticatedUser(Request $request): ?User
    {
        $sessionUser = $request->session()->get('user');

        $userId = is_object($sessionUser)
            ? ($sessionUser->id ?? null)
            : (is_array($sessionUser)
                ? ($sessionUser['id'] ?? null)
                : null);

        if (!$userId) {
            return null;
        }

        return User::query()->find($userId);
    }
}
