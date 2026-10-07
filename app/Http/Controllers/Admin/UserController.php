<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class UserController extends BaseAdminController
{
    public function index(Request $request): View
    {
        $query = User::withCount(['courses', 'enrollments'])->latest();

        if ($role = $request->get('role')) {
            if ($role !== 'all') {
                $query->where('role', $role);
            }
        }

        if ($search = $request->get('search')) {
            $query->where(fn ($q) => $q->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        return view('admin.users.index', [
            'users' => $query->paginate(10)->withQueryString(),
            'selectedRole' => $request->get('role', 'all'),
            'search' => $request->get('search', ''),
        ]);
    }

    public function create(): View
    {
        return view('admin.users.form', ['user' => new User()]);
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $this->validateUser($request);

        return $this->tryAction(function () use ($validated, $request) {
            $validated['password'] = Hash::make($validated['password']);
            $validated['is_instructor'] = $request->boolean('is_instructor');

            User::create($validated);
        }, 'User created successfully.', 'admin.users.index');
    }

    public function edit(User $user): View
    {
        return view('admin.users.form', ['user' => $user]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $validated = $this->validateUser($request, $user);

        return $this->tryAction(function () use ($validated, $request, $user) {
            $validated['is_instructor'] = $request->boolean('is_instructor');

            if (filled($validated['password'] ?? null)) {
                $validated['password'] = Hash::make($validated['password']);
            } else {
                unset($validated['password']);
            }

            $user->update($validated);
        }, 'User updated successfully.', 'admin.users.index');
    }

    public function toggleStatus(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('flash_error', 'You cannot suspend your own account.');
        }

        return $this->tryActionBack(function () use ($user) {
            $user->update(['status' => $user->status === 'active' ? 'suspended' : 'active']);
        }, fn () => "{$user->name} is now {$user->status}.");
    }

    public function destroy(User $user): RedirectResponse
    {
        if ($user->id === auth()->id()) {
            return back()->with('flash_error', 'You cannot delete your own account.');
        }

        if ($user->courses()->exists() || $user->enrollments()->exists()) {
            return back()->with('flash_error', 'This user has courses or enrollments and cannot be deleted. Suspend them instead.');
        }

        return $this->tryAction(function () use ($user) {
            $user->delete();
        }, 'User deleted.', 'admin.users.index');
    }

    private function validateUser(Request $request, ?User $user = null): array
    {
        return $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'unique:users,email,'.($user?->id ?? 'NULL')],
            'password' => [$user ? 'nullable' : 'required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
            'is_instructor' => ['sometimes', 'boolean'],
            'phone' => ['nullable', 'string', 'max:30'],
        ]);
    }
}
