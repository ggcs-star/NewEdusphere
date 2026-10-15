<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Enrollment;
use App\Models\Payment;
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

        $key = config('services.razorpay.key');
        $secret = config('services.razorpay.secret');

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
                    Payment::create([
                        'user_id' => $user->id,
                        'course_id' => $course->id,
                        'payment_method' => 'razorpay',
                        'amount' => $course->effectivePrice(),
                        'admin_revenue' => 0,
                        'instructor_revenue' => 0,
                        'razorpay_order_id' => $order['id'],
                        'status' => 'pending',
                    ]);
                }
            });

        return view('frontend.checkout.razorpay', [
    'razorpayKey' => config('services.razorpay.key'),
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
            $api = new Api(
                config('services.razorpay.key'),
                config('services.razorpay.secret')
            );

            // Validate Razorpay's order/payment signature.
            $api->utility->verifyPaymentSignature([
                'razorpay_order_id' => $validated['razorpay_order_id'],
                'razorpay_payment_id' => $validated['razorpay_payment_id'],
                'razorpay_signature' => $validated['razorpay_signature'],
            ]);

            // Fetch the payment directly from Razorpay.
            $gatewayPayment = $api->payment->fetch(
                $validated['razorpay_payment_id']
            );

            $expectedAmount = (int) round(
                $payments->sum(
                    fn (Payment $payment) => (float) $payment->amount
                ) * 100
            );

            if (
                $gatewayPayment['order_id'] !== $validated['razorpay_order_id'] ||
                $gatewayPayment['status'] !== 'captured' ||
                (int) $gatewayPayment['amount'] !== $expectedAmount ||
                $gatewayPayment['currency'] !== 'INR'
            ) {
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
