<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Portal Admin - BukuBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4">

    <div class="w-full max-w-md bg-white rounded-3xl border border-slate-200 shadow-xl p-8 md:p-10">
        <a href="{{ route('landing') }}" class="inline-flex items-center gap-2 mb-8">
            <div class="bg-slate-900 text-white font-bold px-2.5 py-1 rounded-lg text-lg">BB</div>
            <span class="text-xl font-bold text-slate-900">BukuBook <span class="text-blue-600">Admin</span></span>
        </a>

        <h2 class="text-2xl font-extrabold text-slate-900">Masuk Portal Admin</h2>
        <p class="text-sm text-slate-500 mt-1 mb-6">Kelola koleksi buku dan status perpustakaan.</p>

        @if(session('success'))
            <div class="mb-4 p-3.5 bg-emerald-50 text-emerald-700 text-sm rounded-xl border border-emerald-200">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="mb-4 p-3.5 bg-rose-50 text-rose-600 text-sm rounded-xl border border-rose-200">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="{{ route('admin.login') }}" method="POST" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Admin</label>
                <input type="email" name="email" value="{{ old('email') }}" placeholder="admin@email.com" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                <input type="password" name="kataSandi" placeholder="••••••••" required
                    class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-slate-900 text-sm">
            </div>
            <button type="submit" class="w-full py-3.5 bg-slate-900 hover:bg-slate-800 text-white font-semibold rounded-xl transition text-sm">
                Masuk sebagai Admin
            </button>
        </form>

        <p class="mt-6 text-center text-xs text-slate-400">
            <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">← Kembali ke portal member</a>
        </p>
    </div>

</body>
</html>
