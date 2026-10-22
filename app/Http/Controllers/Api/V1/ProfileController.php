<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Concerns\ApiResponses;
use App\Http\Controllers\Controller;
use App\Http\Resources\UserResource;
use App\Services\ProfileService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Mirrors ProfileController (web), both backed by ProfileService.
 */
class ProfileController extends Controller
{
    use ApiResponses;

    public function __construct(private readonly ProfileService $profile)
    {
    }

    public function show(Request $request): JsonResponse
    {
        return $this->ok(new UserResource($request->user('api')));
    }

    public function update(Request $request): JsonResponse
    {
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

        // ValidationException (wrong current_password) is converted to a
        // 422 JSON response automatically by Laravel's exception handler.
        $user = $this->profile->update($request->user('api'), $validated, $request->file('avatar'));

        return $this->ok(new UserResource($user), 'Profile updated successfully.');
    }
}
