<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-slate-100 p-4">
    <div class="mx-auto grid min-h-[calc(100vh-2rem)] w-full max-w-4xl items-center gap-6 lg:grid-cols-2">
        <section class="hidden rounded-3xl bg-slate-900 p-8 text-white lg:block">
            <p class="mb-3 text-xs uppercase tracking-widest text-slate-300">Master Kuliner Indonesia</p>
            <h1 class="mb-2 text-3xl font-bold">Admin Dashboard</h1>
            <p class="text-sm text-slate-300">Manage all support tickets from the Laravel admin panel.</p>
        </section>

        <form method="POST" action="{{ route('admin.login') }}" class="rounded-3xl border border-slate-200 bg-white p-6 shadow-lg">
            @csrf
            <h2 class="mb-1 text-2xl font-bold text-slate-800">Admin Sign In</h2>
            <p class="mb-5 text-sm text-slate-500">Use your admin account credentials</p>

            <label class="mb-2 block text-sm font-medium text-slate-700">Email atau No. Telp</label>
            <input type="text" name="login" value="{{ old('login') }}" class="mb-4 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" placeholder="admin@company.com / 08123456789" required>

            <label class="mb-2 block text-sm font-medium text-slate-700">Password</label>
            <input type="password" name="password" class="mb-4 w-full rounded-xl border border-slate-300 px-3 py-2.5 text-sm" required>

            @if ($errors->any())
                <div class="mb-4 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-700">{{ $errors->first() }}</div>
            @endif

            <button type="submit" class="w-full rounded-xl bg-blue-600 py-2.5 text-sm font-semibold text-white">Sign In</button>
        </form>
    </div>
</body>
</html>
