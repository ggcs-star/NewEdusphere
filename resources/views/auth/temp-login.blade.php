<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign in · EduSphere</title>
    <link rel="icon" type="image/svg+xml" href="{{ asset('favicon.svg') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-50 font-sans min-h-screen flex items-center justify-center p-4">
    <div class="w-full max-w-sm">
        <div class="flex justify-center mb-6">
            <div class="flex items-center gap-2">
                <div class="h-10 w-10 rounded-full bg-accent-500 text-white flex items-center justify-center"><i class="fa-solid fa-graduation-cap"></i></div>
                <span class="font-extrabold text-xl"><span class="text-brand-950">Edu</span><span class="text-accent-500">Sphere</span></span>
            </div>
        </div>

        <div class="rounded-2xl bg-white border border-slate-200 shadow-sm p-7">
            <h1 class="text-lg font-semibold text-slate-900 mb-1">Sign in</h1>
            <p class="text-sm text-slate-500 mb-5">Temporary admin/testing login.</p>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-rose-50 border border-rose-200 px-3 py-2 text-sm text-rose-700">
                    {{ $errors->first() }}
                </div>
            @endif

            <form method="POST" action="{{ route('login') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email</label>
                    <input type="email" name="email" required autofocus value="{{ old('email') }}"
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required
                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-brand-500 focus:ring-1 focus:ring-brand-500 outline-none">
                </div>
                <button type="submit"
                    class="w-full rounded-lg bg-accent-600 px-4 py-2.5 text-sm font-semibold text-white hover:bg-accent-700 transition">
                    Sign in
                </button>
            </form>
        </div>

        <p class="text-center text-xs text-slate-400 mt-5">Placeholder screen — will be replaced by the real auth flow.</p>
    </div>
</body>
</html>
