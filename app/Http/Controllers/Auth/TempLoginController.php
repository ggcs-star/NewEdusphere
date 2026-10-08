<?php

/**
 * TEMPORARY placeholder so protected routes (admin/instructor areas) are
 * testable before the real auth flow (login/register/forgot-password) is
 * merged in. Delete this file and its routes once that work lands.
 */

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class TempLoginController extends Controller
{
    public function create(): View
    {
        return view('auth.temp-login');
    }

    public function store(Request $request): RedirectResponse
    {
        $credentials = $request->validate([
            'email' => ['required', 'email'],
            'password' => ['required'],
        ]);

        if (! Auth::attempt($credentials)) {
            return back()->withErrors(['email' => 'Those credentials do not match any account.']);
        }

        $request->session()->regenerate();

        $user = Auth::user();

        return redirect()->to($user->isAdmin() ? route('admin.dashboard') : '/');
    }

    public function destroy(Request $request): RedirectResponse
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/login');
    }
}
