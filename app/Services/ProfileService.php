<?php

namespace App\Services;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

/**
 * Profile update logic shared by ProfileController (web, used by
 * students/instructors/admins alike) and Api\V1\ProfileController.
 */
class ProfileService
{
    public function __construct(private readonly FileUploadService $uploads)
    {
    }

    /**
     * @param  array{name: string, phone?: ?string, biography?: ?string, short_title?: ?string, facebook_link?: ?string, twitter_link?: ?string, linkedin_link?: ?string, current_password?: ?string, new_password?: ?string}  $validated
     *
     * @throws ValidationException if a new password is given with the wrong current password
     */
    public function update(User $user, array $validated, ?UploadedFile $avatar = null): User
    {
        if (!empty($validated['new_password']) && !Hash::check($validated['current_password'], $user->password)) {
            throw ValidationException::withMessages([
                'current_password' => 'Your current password is incorrect.',
            ]);
        }

        $user->name = $validated['name'];
        $user->phone = $validated['phone'] ?? null;
        $user->biography = $validated['biography'] ?? null;
        $user->short_title = $validated['short_title'] ?? null;
        $user->facebook_link = $validated['facebook_link'] ?? null;
        $user->twitter_link = $validated['twitter_link'] ?? null;
        $user->linkedin_link = $validated['linkedin_link'] ?? null;

        if ($avatar) {
            $user->avatar = $this->uploads->replace($user->avatar, $avatar, 'avatars');
        }

        if (!empty($validated['new_password'])) {
            $user->password = Hash::make($validated['new_password']);
        }

        $user->save();

        return $user;
    }
}
