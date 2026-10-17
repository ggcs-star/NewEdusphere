<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CartController extends Controller
{
    private function ids(Request $request): array
    {
        return array_values(array_unique(array_map('intval', $request->session()->get('cart_course_ids', []))));
    }

    public function index(Request $request): View
    {
        $ids = $this->ids($request);
        $courses = Course::with(['instructor', 'category'])->where('status', 'active')->whereIn('id', $ids)->get();
        $subtotal = $courses->sum(fn (Course $course) => $course->effectivePrice());
        $items = $courses->map(fn (Course $course) => ['course' => $course, 'price' => $course->effectivePrice()]);
        return view('frontend.cart.index', compact('items', 'subtotal'));
    }

    public function add(Request $request, int $courseId): RedirectResponse
    {
        if ($blocked = $this->blockNonStudents()) {
            return $blocked;
        }

        $course = Course::where('status', 'active')->findOrFail($courseId);
        $ids = $this->ids($request);
        if (!in_array($course->id, $ids, true)) $ids[] = $course->id;
        $request->session()->put('cart_course_ids', $ids);
        return redirect()->route('cart.index')->with('success', 'Course added to your cart.');
    }

    public function buyNow(Request $request, int $courseId): RedirectResponse
    {
        if ($blocked = $this->blockNonStudents()) {
            return $blocked;
        }

        $course = Course::where('status', 'active')->findOrFail($courseId);
        $ids = $this->ids($request);
        if (!in_array($course->id, $ids, true)) $ids[] = $course->id;
        $request->session()->put('cart_course_ids', $ids);
        return redirect()->route('checkout.index');
    }

    /**
     * Admins and instructors don't buy courses — only students do. Blocking
     * it here (not just at final checkout) avoids letting them build up a
     * cart they can never actually pay for.
     */
    private function blockNonStudents(): ?RedirectResponse
    {
        $user = current_admin_user();

        if (! $user) {
            return null;
        }

        if ($user->isAdmin()) {
            return redirect()->route('admin.dashboard')->with('info', 'Admins cannot purchase courses.');
        }

        if ($user->isInstructor()) {
            return redirect()->route('instructor.dashboard')->with('info', 'Instructors cannot purchase courses.');
        }

        return null;
    }

    public function remove(Request $request, int $courseId): RedirectResponse
    {
        $request->session()->put('cart_course_ids', array_values(array_diff($this->ids($request), [$courseId])));
        return redirect()->route('cart.index')->with('success', 'Course removed from cart.');
    }

    public function clear(Request $request): RedirectResponse
    {
        $request->session()->forget('cart_course_ids');
        return redirect()->route('cart.index')->with('success', 'Cart cleared.');
    }
}
