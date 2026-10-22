<?php

namespace App\Http\Controllers;

use App\Services\ProfileService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ProfileController extends Controller
{
    public function __construct(private readonly ProfileService $profile)
    {
    }

    /**
     * Shared by students, instructors and admins — current_admin_user() works
     * for any of them, and the view simply picks whichever layout matches
     * where the request came in from.
     */
    public function edit(Request $request): View
    {
        $user = current_admin_user();

        abort_unless($user, 403);

        $view = $request->routeIs('admin.*') || $request->routeIs('instructor.*')
            ? 'admin.profile.edit'
            : 'frontend.profile.index';

        return view($view, compact('user'));
    }

    public function update(Request $request): RedirectResponse
    {
        $user = current_admin_user();

        abort_unless($user, 403);

        $validated = $request->validate([
            'name' => ['required', 'string', 'max:150'],
            'phone' => ['nullable', 'string', 'max:30'],
            'biography' => ['nullable', 'string', 'max:5000'],
            'short_title' => ['nullable', 'string', 'max:150'],
            'facebook_link' => ['nullable', 'string', 'max:255'],
            'twitter_link' => ['nullable', 'string', 'max:255'],
            'linkedin_link' => ['nullable', 'string', 'max:255'],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'current_password' => ['nullable', 'required_with:new_password', 'string'],
            'new_password' => ['nullable', 'confirmed', 'min:8'],
        ]);

        $user = $this->profile->update($user, $validated, $request->file('avatar'));

        // current_admin_user() reads session('user') directly rather than
        // re-querying the DB, so the session copy has to be refreshed here
        // or the header/sidebar would keep showing the pre-edit values.
        session([
            'user' => $user,
            'user_name' => $user->name,
            'user_email' => $user->email,
        ]);

        // The frontend layout reads 'success'; the admin layout (shared by
        // the admin and instructor portals) reads 'flash_success' — set
        // both since this controller serves all three.
        return back()
            ->with('success', 'Profile updated successfully.')
            ->with('flash_success', 'Profile updated successfully.');
    }
}
