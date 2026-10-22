<?php

namespace App\Services;

use App\Models\CartItem;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;

/**
 * Persistent per-user cart for the API. Mirrors the rules in
 * App\Http\Controllers\Frontend\CartController (session-based, for
 * guests on the web) so both surfaces agree on who may buy courses and
 * how the subtotal is calculated — see CheckoutService for the next
 * step (turning cart items into an order).
 */
class CartService
{
    /**
     * Active courses currently in the user's cart, each with its
     * effective (discounted/free) price.
     */
    public function items(User $user): Collection
    {
        $courseIds = $this->courseIds($user);

        return Course::with(['instructor', 'category'])
            ->where('status', 'active')
            ->whereIn('id', $courseIds)
            ->get()
            ->map(fn (Course $course) => [
                'course' => $course,
                'price' => $course->effectivePrice(),
            ]);
    }

    public function subtotal(User $user): float
    {
        return (float) $this->items($user)->sum(fn (array $item) => $item['price']);
    }

    public function courseIds(User $user): array
    {
        return CartItem::where('user_id', $user->id)->pluck('course_id')->all();
    }

    /**
     * @throws \RuntimeException if the viewer isn't allowed to buy courses
     */
    public function add(User $user, int $courseId): void
    {
        $this->assertCanPurchase($user);

        $course = Course::where('status', 'active')->findOrFail($courseId);

        CartItem::firstOrCreate([
            'user_id' => $user->id,
            'course_id' => $course->id,
        ]);
    }

    public function remove(User $user, int $courseId): void
    {
        CartItem::where('user_id', $user->id)->where('course_id', $courseId)->delete();
    }

    public function clear(User $user): void
    {
        CartItem::where('user_id', $user->id)->delete();
    }

    /**
     * Admins and instructors don't buy courses — only students do.
     *
     * @throws \RuntimeException
     */
    public function assertCanPurchase(User $user): void
    {
        if ($user->isAdmin()) {
            throw new \RuntimeException('Admins cannot purchase courses.');
        }

        if ($user->isInstructor()) {
            throw new \RuntimeException('Instructors cannot purchase courses.');
        }
    }
}
