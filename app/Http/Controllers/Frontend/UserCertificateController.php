<?php

namespace App\Http\Controllers\Frontend;

use App\Http\Controllers\Controller;
use App\Models\Certificate;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;

class UserCertificateController extends Controller
{
    /**
     * Students log in through the custom session (`user` / `user_token`),
     * exactly like the other frontend controllers. The old version only
     * called current_admin_user(), so students got 401.
     */
    private function viewer(Request $request)
    {
        $sessionUser = $request->session()->get('user');

        $userId = is_object($sessionUser)
            ? ($sessionUser->id ?? null)
            : (is_array($sessionUser)
                ? ($sessionUser['id'] ?? null)
                : null);

        if ($request->session()->has('user_token') && $userId) {
            return User::query()->find($userId);
        }

        return function_exists('current_admin_user')
            ? current_admin_user()
            : null;
    }

    public function index(Request $request): View
    {
        $user = $this->viewer($request);

        abort_unless($user && $user->id, 401);

        $certificates = Certificate::with(['course', 'template'])
            ->where('user_id', $user->id)
            ->latest('issued_at')
            ->paginate(9);

        return view('frontend.certificates.index', compact(
            'user',
            'certificates'
        ));
    }

    public function show(Request $request, Certificate $certificate): View
    {
        $user = $this->viewer($request);

        abort_unless($user && $user->id, 401);

        // Students can only open their own certificates.
        abort_unless(
            (int) $certificate->user_id === (int) $user->id,
            403
        );

        $certificate->load(['course', 'template', 'user']);

        return view('frontend.certificates.show', compact(
            'user',
            'certificate'
        ));
    }
}