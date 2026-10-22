<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CourseResource;
use App\Services\CartService;
use App\Services\CheckoutService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mirrors Frontend\CheckoutController, both backed by CheckoutService.
 * Where the web pulls course ids from the session cart, this pulls them
 * from the request body — or, if omitted, from the user's persistent
 * CartService cart (see Api\V1\CartController).
 */
class CheckoutController extends Controller
{
    use ApiResponses;

    public function __construct(
        private readonly CheckoutService $checkout,
        private readonly CartService $cart,
    ) {
    }

    public function preview(Request $request): JsonResponse
    {
        $ids = $this->resolveCourseIds($request);

        if (empty($ids)) {
            return $this->fail('Your cart is empty.', 422);
        }

        $preview = $this->checkout->previewItems($ids);

        if ($preview['courses']->isEmpty()) {
            return $this->fail('No available courses for the given ids.', 422);
        }

        return $this->ok([
            'items' => $preview['items']->map(fn (array $item) => [
                'course' => new CourseResource($item['course']),
                'price' => $item['price'],
            ])->values(),
            'subtotal' => $preview['subtotal'],
        ]);
    }

    public function pay(Request $request): JsonResponse
    {
        $user = $request->user('api');

        try {
            $this->cart->assertCanPurchase($user);
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage(), 403);
        }

        $ids = $this->resolveCourseIds($request);

        if (empty($ids)) {
            return $this->fail('Your cart is empty.', 422);
        }

        $result = $this->checkout->pay($user, $ids);

        switch ($result['outcome']) {
            case 'unavailable':
                return $this->fail('One or more courses are unavailable. Please review your cart.', 422);

            case 'already_enrolled':
                $this->cart->clear($user);

                return $this->ok(null, 'You are already enrolled in these courses.');

            case 'free_enrolled':
                $this->cart->clear($user);

                return $this->ok(null, 'You are enrolled successfully.');

            case 'gateway_not_configured':
                return $this->fail('Payment gateway configuration is missing.', 503);

            case 'order_created':
                return $this->ok([
                    'razorpay_key' => $result['razorpay']['key'],
                    'order_id' => $result['razorpay']['order_id'],
                    'amount' => $result['razorpay']['amount'],
                    'currency' => $result['razorpay']['currency'],
                    'name' => $result['razorpay']['name'],
                    'email' => $result['razorpay']['email'],
                ], 'Razorpay order created. Complete payment then call /checkout/verify.');

            case 'gateway_error':
            default:
                return $this->fail('Unable to start payment. Please try again.', 502);
        }
    }

    public function verify(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $validated = $request->validate([
            'razorpay_order_id' => ['required', 'string', 'max:255'],
            'razorpay_payment_id' => ['required', 'string', 'max:255'],
            'razorpay_signature' => ['required', 'string', 'max:255'],
        ]);

        $result = $this->checkout->verify($user, $validated);

        return match ($result['outcome']) {
            'order_not_found' => $this->fail('Payment order not found.', 404),
            'already_paid' => $this->ok(null, 'Your courses are already available.'),
            'verified' => tap(
                $this->ok(null, 'Payment verified successfully. Your courses are now available.'),
                fn () => $this->cart->clear($user)
            ),
            'mismatch' => $this->fail('Payment is not confirmed yet. If money was deducted, please contact support.', 422),
            default => $this->fail('Payment verification failed. If money was deducted, please contact support.', 502),
        };
    }

    /**
     * course_ids from the request body if given, otherwise the user's
     * persistent cart — so a client can either "buy now" with an
     * explicit list, or check out whatever is in their cart.
     */
    private function resolveCourseIds(Request $request): array
    {
        if ($request->filled('course_ids')) {
            return array_values(array_unique(array_filter(
                array_map('intval', (array) $request->input('course_ids')),
                fn (int $id) => $id > 0
            )));
        }

        return $this->cart->courseIds($request->user('api'));
    }
}
