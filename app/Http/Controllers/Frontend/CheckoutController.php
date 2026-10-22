<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function __construct(private readonly CheckoutService $checkout)
    {
    }

    /**
     * Display checkout page.
     */
    public function index(Request $request): View|RedirectResponse
    {
        $ids = $this->cartCourseIds($request);

        if (empty($ids)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $preview = $this->checkout->previewItems($ids);

        if ($preview['courses']->isEmpty()) {
            return redirect()->route('cart.index')->with('error', 'Your cart has no available courses.');
        }

        return view('frontend.checkout.index', [
            'items' => $preview['items'],
            'subtotal' => $preview['subtotal'],
            'isAuthenticated' => $this->hasAuthenticatedSession($request),
        ]);
    }

    /**
     * Create a Razorpay order or enroll the user in free courses.
     */
    public function pay(Request $request): View|RedirectResponse
    {
        if (!$this->hasAuthenticatedSession($request)) {
            $request->session()->put('url.intended', route('checkout.index'));

            return redirect()->route('login')->with('info', 'Please log in to complete your purchase.');
        }

        $user = $this->authenticatedUser($request);

        if (!$user) {
            $request->session()->forget(['user_token', 'user', 'user_email', 'user_name']);

            return redirect()->route('login')->with('error', 'Your session is invalid. Please log in again.');
        }

        // Admin is allowed to visit checkout, but cannot purchase courses.
        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('info', 'Admins cannot purchase courses.');
        }

        // Instructors create and sell courses, not buy them.
        if ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard')->with('info', 'Instructors cannot purchase courses.');
        }

        $ids = $this->cartCourseIds($request);

        if (empty($ids)) {
            return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        }

        $result = $this->checkout->pay($user, $ids);

        switch ($result['outcome']) {
            case 'unavailable':
                return redirect()->route('cart.index')->with('error', 'One or more courses are unavailable. Please review your cart.');

            case 'already_enrolled':
                $request->session()->forget('cart_course_ids');

                return redirect()->route('my-courses.index')->with('success', 'You are already enrolled in these courses.');

            case 'free_enrolled':
                $request->session()->forget('cart_course_ids');

                return redirect()->route('my-courses.index')->with('success', 'You are enrolled successfully.');

            case 'gateway_not_configured':
                return redirect()->route('checkout.index')->with('error', 'Payment gateway configuration is missing.');

            case 'order_created':
                return view('frontend.checkout.razorpay', [
                    'razorpayKey' => $result['razorpay']['key'],
                    'orderId' => $result['razorpay']['order_id'],
                    'amount' => $result['razorpay']['amount'],
                    'currency' => $result['razorpay']['currency'],
                    'name' => $result['razorpay']['name'],
                    'email' => $result['razorpay']['email'],
                ]);

            case 'gateway_error':
            default:
                return redirect()->route('checkout.index')->with('error', 'Unable to start payment. Please try again.');
        }
    }

    /**
     * Verify the Razorpay signature and confirm the payment server-side.
     */
    public function verify(Request $request): RedirectResponse
    {
        if (!$this->hasAuthenticatedSession($request)) {
            return redirect()->route('login')->with('error', 'Please log in to verify your payment.');
        }

        $user = $this->authenticatedUser($request);

        if (!$user) {
            return redirect()->route('login')->with('error', 'Your session is invalid. Please log in again.');
        }

        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string', 'max:255'],
            'razorpay_payment_id' => ['required', 'string', 'max:255'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        $result = $this->checkout->verify($user, $validated);

        switch ($result['outcome']) {
            case 'order_not_found':
                return redirect()->route('checkout.index')->with('error', 'Payment order not found.');

            case 'already_paid':
                return redirect()->route('my-courses.index')->with('success', 'Your courses are already available.');

            case 'verified':
                $request->session()->forget('cart_course_ids');

                return redirect()->route('my-courses.index')->with('success', 'Payment verified successfully. Your courses are now available.');

            case 'mismatch':
                return redirect()->route('checkout.index')->with('error', 'Payment is not confirmed yet. If money was deducted, please contact support.');

            case 'error':
            default:
                return redirect()->route('checkout.index')->with('error', 'Payment verification failed. If money was deducted, please contact support.');
        }
    }

    /**
     * Return valid, unique course IDs from the session cart.
     */
    private function cartCourseIds(Request $request): array
    {
        return array_values(array_unique(array_filter(
            array_map('intval', $request->session()->get('cart_course_ids', [])),
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
            : (is_array($sessionUser) ? ($sessionUser['id'] ?? null) : null);

        if (!$userId) {
            return null;
        }

        return User::query()->find($userId);
    }
}
