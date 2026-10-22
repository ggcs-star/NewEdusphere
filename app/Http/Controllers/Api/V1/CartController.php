<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\CartResource;
use App\Services\CartService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class CartController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly CartService $cart)
    {
    }

    public function index(Request $request): JsonResponse
    {
        $user = $request->user('api');

        return $this->ok(new CartResource(
            $this->cart->items($user),
            $this->cart->subtotal($user)
        ));
    }

    public function add(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user('api');

        try {
            $this->cart->add($user, $courseId);
        } catch (\RuntimeException $e) {
            return $this->fail($e->getMessage(), 403);
        }

        return $this->ok(new CartResource(
            $this->cart->items($user),
            $this->cart->subtotal($user)
        ), 'Course added to your cart.');
    }

    public function remove(Request $request, int $courseId): JsonResponse
    {
        $user = $request->user('api');

        $this->cart->remove($user, $courseId);

        return $this->ok(new CartResource(
            $this->cart->items($user),
            $this->cart->subtotal($user)
        ), 'Course removed from cart.');
    }

    public function clear(Request $request): JsonResponse
    {
        $user = $request->user('api');

        $this->cart->clear($user);

        return $this->ok(null, 'Cart cleared.');
    }
}
