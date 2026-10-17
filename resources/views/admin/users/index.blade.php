<x-layouts.admin title="Users">
    {{-- Welcome banner --}}
    <div class="relative overflow-hidden rounded-3xl bg-gradient-to-br from-brand-50 to-brand-100 px-6 sm:px-8 py-5 mb-8">
        <div class="relative z-10 flex flex-wrap items-center justify-between gap-6">
            <div class="max-w-lg">
                <h2 class="text-3xl sm:text-4xl font-extrabold text-brand-950 tracking-tight">Users</h2>
                <p class="mt-1 text-sm font-semibold text-accent-600">Manage every account on the platform.</p>
            </div>

            <div class="flex items-center gap-8">
                {{-- Decorative cluster --}}
                <div class="hidden lg:block relative w-20 h-20 flex-shrink-0">
                    <div class="h-20 w-20 rounded-3xl bg-brand-500 rotate-6 shadow-xl flex items-center justify-center">
                        <i class="fa-solid fa-users text-3xl text-accent-400 -rotate-6"></i>
                    </div>
                    <div class="absolute -bottom-3 -left-4 h-10 w-14 rounded-xl bg-accent-500 shadow-lg flex items-center justify-center -rotate-12">
                        <i class="fa-solid fa-user-graduate text-base text-white"></i>
                    </div>
                </div>

                <a href="{{ route('admin.users.create') }}"
                    class="flex-shrink-0 inline-flex items-center gap-2 rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-3 text-sm font-bold text-white shadow-lg shadow-accent-600/30 hover:shadow-xl hover:-translate-y-0.5 transition">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4v16m8-8H4" /></svg>
                    Add User
                </a>
            </div>
        </div>

        <div class="pointer-events-none absolute -right-10 -top-10 h-44 w-44 rounded-full bg-brand-200/50 blur-2xl"></div>
        <div class="pointer-events-none absolute right-24 bottom-0 h-28 w-28 rounded-full bg-accent-400/20 blur-2xl"></div>
    </div>

    {{-- Stats --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5 mb-8">
        <x-admin.stat-card
            icon="fa-users"
            color="violet"
            :count="$stats['total']['count']"
            label="Total Users"
            :change="$stats['total']['change']"
            :direction="$stats['total']['direction']"
        />
        <x-admin.stat-card
            icon="fa-user-check"
            color="emerald"
            :count="$stats['active']['count']"
            label="Active Users"
            :change="$stats['active']['change']"
            :direction="$stats['active']['direction']"
        />
        <x-admin.stat-card
            icon="fa-chalkboard-user"
            color="amber"
            :count="$stats['instructors']['count']"
            label="Instructors"
            :change="$stats['instructors']['change']"
            :direction="$stats['instructors']['direction']"
        />
        <x-admin.stat-card
            icon="fa-user-shield"
            color="sky"
            :count="$stats['admins']['count']"
            label="Admins"
            :change="$stats['admins']['change']"
            :direction="$stats['admins']['direction']"
        />
    </div>

    <form method="GET" class="flex flex-wrap items-center gap-3 mb-6">
        <x-admin.search-input :value="$search" placeholder="Search name or email..." />
        <x-admin.searchable-select
            name="role"
            :options="['admin' => 'Admin', 'user' => 'User', 'instructor' => 'Instructor']"
            :selected="$selectedRole"
            all-label="All Roles"
            pill
            autosubmit
            class="w-44"
        />
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
                                        <p class="text-sm font-semibold text-brand-950 truncate">{{ $user->name }}</p>
                                        <p class="text-xs text-slate-400 truncate">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3.5">
                                <x-admin.badge :color="match ($user->role) { 'admin' => 'brand', 'instructor' => 'accent', default => 'slate' }">{{ ucfirst($user->role) }}</x-admin.badge>
                            </td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $user->courses_count }}</td>
                            <td class="px-5 py-3.5 text-slate-600">{{ $user->enrollments_count }}</td>
                            <td class="px-5 py-3.5">
                                @if ($user->id === current_admin_user()?->id)
                                    <x-admin.badge :color="$user->status ? 'emerald' : 'rose'">{{ $user->status ? 'Active' : 'Suspended' }}</x-admin.badge>
                                @else
                                    <form action="{{ route('admin.users.update-status', $user) }}" method="POST">
                                        @csrf
                                        @method('PATCH')
                                        <x-admin.searchable-select
                                            name="status"
                                            :options="['active' => 'Active', 'suspended' => 'Suspended']"
                                            :selected="$user->status ? 'active' : 'suspended'"
                                            :button-class="($user->status ? 'bg-emerald-50 text-emerald-700' : 'bg-rose-50 text-rose-700').' border-0 text-xs font-semibold'"
                                            pill
                                            autosubmit
                                            class="inline-block w-32"
                                        />
                                    </form>
                                @endif
                            </td>
                            <td class="px-5 py-3.5">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.users.show', $user) }}" class="h-8 w-8 rounded-lg bg-brand-50 text-brand-700 hover:bg-brand-100 flex items-center justify-center" title="Details">
                                        <i class="fa-solid fa-eye text-xs"></i>
                                    </a>
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
