@php $isEdit = $user->exists; @endphp
<x-layouts.admin :title="$isEdit ? 'Edit User' : 'Add User'">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('admin.users.index') }}" class="h-9 w-9 rounded-lg bg-white border border-slate-200 flex items-center justify-center text-slate-500 hover:text-brand-700">
            <i class="fa-solid fa-arrow-left text-xs"></i>
        </a>
        <div>
            <h2 class="text-2xl font-bold text-brand-950">{{ $isEdit ? 'Edit User' : 'Add User' }}</h2>
            <p class="text-sm text-slate-500 mt-0.5">{{ $isEdit ? $user->email : 'Create a new account.' }}</p>
        </div>
    </div>

    <form action="{{ $isEdit ? route('admin.users.update', $user) : route('admin.users.store') }}" method="POST" class="max-w-xl">
        @csrf
        @if ($isEdit) @method('PUT') @endif

        <div class="rounded-2xl bg-white border border-slate-200 p-6 shadow-sm space-y-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Full Name <span class="text-rose-500">*</span></label>
                <input type="text" name="name" required value="{{ old('name', $user->name) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-rose-500">*</span></label>
                <input type="email" name="email" required value="{{ old('email', $user->email) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Phone</label>
                <input type="text" name="phone" value="{{ old('phone', $user->phone) }}"
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">
                    Password {{ $isEdit ? '' : '' }}<span class="text-rose-500">{{ $isEdit ? '' : '*' }}</span>
                    @if ($isEdit) <span class="text-slate-400 font-normal">(leave blank to keep current)</span> @endif
                </label>
                <input type="password" name="password" {{ $isEdit ? '' : 'required' }}
                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1.5">Role</label>
                <x-admin.searchable-select
                    name="role"
                    :options="['user' => 'User', 'instructor' => 'Instructor', 'admin' => 'Admin']"
                    :selected="old('role', $user->role ?? 'user')"
                    required
                />
                <p class="text-xs text-slate-400 mt-1">Instructors can create and sell courses (pending your approval).</p>
            </div>
        </div>

        <div class="flex justify-end gap-2 mt-6">
            <a href="{{ route('admin.users.index') }}" class="rounded-lg px-5 py-2.5 text-sm font-medium text-slate-600 hover:bg-slate-100">Cancel</a>
            <button type="submit" class="rounded-full bg-gradient-to-r from-accent-400 to-accent-600 px-6 py-2.5 text-sm font-bold text-white shadow-md shadow-accent-600/30 hover:shadow-lg transition">
                {{ $isEdit ? 'Save Changes' : 'Create User' }}
            </button>
        </div>
    </form>
</x-layouts.admin>
