<x-layouts.admin title="Users">
    <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
        <div>
            <h2 class="text-2xl font-bold text-brand-950">Users</h2>
            <p class="text-sm text-slate-500 mt-1">Manage every account on the platform.</p>
        </div>
        <a href="{{ route('admin.users.create') }}"
            class="inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-5 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg hover:-translate-y-0.5 transition">
            <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
            Add User
        </a>
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <x-admin.search-input :value="$search" placeholder="Search name or email..." />
        <select name="role" onchange="this.form.submit()" class="rounded-full bg-white border border-slate-200 px-4 py-2.5 text-sm text-slate-600 focus:ring-2 focus:ring-brand-200 outline-none">
            <option value="all" @selected($selectedRole == 'all')>All Roles</option>
            <option value="admin" @selected($selectedRole == 'admin')>Admin</option>
            <option value="user" @selected($selectedRole == 'user')>User</option>
        </select>
        <button type="submit" class="rounded-full bg-brand-900 px-5 py-2.5 text-sm font-semibold text-white hover:bg-brand-950 transition">Filter</button>
    </form>

    <div class="rounded-2xl bg-white border border-slate-200 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead>
                    <tr class="bg-brand-50/60 text-left text-sm font-semibold uppercase tracking-wide text-brand-700">
                        <th class="px-5 py-3">User</th>
                        <th class="px-5 py-3">Role</th>
                        <th class="px-5 py-3">Courses</th>
                        <th class="px-5 py-3">Enrollments</th>
                        <th class="px-5 py-3">Status</th>
                        <th class="px-5 py-3 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse ($users as $user)
                        <tr class="hover:bg-brand-50/30 transition">
                            <td class="px-5 py-3.5">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-full bg-brand-900 text-white flex items-center justify-center font-semibold text-xs flex-shrink-0">
                                        {{ strtoupper(substr($user->name, 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <p class="text-lg font-semibold text-brand-950 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <x-admin.badge :color="$user->role === 'admin' ? 'brand' : 'slate'">{{ ucfirst($user->role) }}</x-admin.badge>
                                @if ($user->is_instructor)
                                    <x-admin.badge color="accent">Instructor</x-admin.badge>
                                @endif
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $user->courses_count }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $user->enrollments_count }}</td>
                            <td class="px-5 py-3.5">
                                <x-admin.badge :color="$user->status === 'active' ? 'emerald' : 'rose'">{{ ucfirst($user->status) }}</x-admin.badge>
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <form action="{{ route('admin.users.toggle-status', $user) }}" method="POST">
                                        @csrf
                                        <button type="submit"
                                            class="h-8 w-8 rounded-lg flex items-center justify-center {{ $user->status === 'active' ? 'bg-amber-50 text-amber-600 hover:bg-amber-100' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}"
                                            title="{{ $user->status === 'active' ? 'Suspend' : 'Activate' }}">
                                            <i class="fa-solid {{ $user->status === 'active' ? 'fa-ban' : 'fa-check' }} text-xs"></i>
                                        </button>
                                    </form>
                                    <a href="{{ route('admin.users.edit', $user) }}" class="h-8 w-8 rounded-lg bg-slate-100 text-slate-600 hover:bg-slate-200 flex items-center justify-center" title="Edit">
                                        <i class="fa-solid fa-pen text-xs"></i>
                                    </a>
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST" onsubmit="return confirm('Delete this user?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="h-8 w-8 rounded-lg bg-rose-50 text-rose-600 hover:bg-rose-100 flex items-center justify-center" title="Delete">
                                            <i class="fa-solid fa-trash text-xs"></i>
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-5 py-16 text-center text-slate-400">
                                <i class="fa-solid fa-users text-2xl mb-2 block"></i>
                                No users found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <x-admin.pagination :paginator="$users" />
</x-layouts.admin>
