<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class CheckoutController extends Controller
{
    public function index(Request $request): View|RedirectResponse
    {
        $ids = array_values(array_unique(array_map('intval', $request->session()->get('cart_course_ids', []))));
        $courses = Course::with(['instructor', 'category'])->where('status', 'active')->whereIn('id', $ids)->get();
        if ($courses->isEmpty()) return redirect()->route('cart.index')->with('error', 'Your cart is empty.');
        $subtotal = $courses->sum(fn (Course $course) => $course->effectivePrice());
        $isAuthenticated = !empty($request->session()->get('user_token')) && !empty($request->session()->get('user'));
        $items = $courses->map(fn (Course $course) => ['course' => $course, 'price' => $course->effectivePrice()]);
        return view('frontend.checkout.index', compact('items', 'subtotal', 'isAuthenticated'));
    }

    /** Payment must be initiated only after the authenticated account has been resolved. */
    public function pay(Request $request): RedirectResponse
    {
        if (empty($request->session()->get('user_token')) || empty($request->session()->get('user'))) {
            $request->session()->put('url.intended', route('checkout.index'));
            return redirect()->route('login')->with('info', 'Please log in or create an account to securely complete your purchase.');
        }

        return redirect()->route('checkout.index')->with('error', 'Payment gateway has not been configured for this project yet. No payment was taken.');
    }
}
