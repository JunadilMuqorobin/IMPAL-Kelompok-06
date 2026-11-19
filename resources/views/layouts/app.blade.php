<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'BukuBook') - Perpustakaan BukuBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex flex-wrap gap-4 justify-between items-center">
            <div class="flex items-center gap-6">
                <a href="{{ Auth::guard('admin')->check() ? route('admin.dashboard') : route('member.dashboard') }}" class="flex items-center gap-2">
                    <div class="bg-blue-600 text-white font-bold px-2.5 py-1 rounded-lg text-lg">BB</div>
                    <span class="text-xl font-bold text-blue-600">BukuBook</span>
                </a>

                @if(Auth::guard('admin')->check())
                    <div class="hidden md:flex items-center gap-4 text-sm font-medium">
                        <a href="{{ route('admin.books.index') }}" class="hover:text-blue-600 transition">Daftar Buku</a>
                        <a href="{{ route('admin.books.statistics') }}" class="hover:text-blue-600 transition">Statistik</a>
                    </div>
                @else
                    <div class="hidden md:flex items-center gap-4 text-sm font-medium">
                        <a href="{{ route('member.books.index') }}" class="hover:text-blue-600 transition">Katalog</a>
                        <a href="{{ route('member.books.borrowed') }}" class="hover:text-blue-600 transition">Buku Saya</a>
                        <a href="{{ route('member.books.reservations') }}" class="hover:text-blue-600 transition">Reservasi</a>
                        <a href="{{ route('member.books.reviews') }}" class="hover:text-blue-600 transition">Ulasan Saya</a>
                    </div>
                @endif
            </div>

            <div class="flex items-center gap-3">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-slate-400">Masuk sebagai</p>
                    <p class="text-sm font-semibold text-slate-800">{{ Auth::guard('admin')->check() ? Auth::guard('admin')->user()->nama : Auth::guard('member')->user()->nama }}</p>
                </div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition border border-rose-200">Keluar</button>
                </form>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-6">
        @if(session('success'))
            <div class="p-4 bg-emerald-50 text-emerald-700 text-sm rounded-2xl border border-emerald-200">✓ {{ session('success') }}</div>
        @endif

        @if(session('error'))
            <div class="p-4 bg-rose-50 text-rose-700 text-sm rounded-2xl border border-rose-200">{{ session('error') }}</div>
        @endif

        @if($errors->any())
            <div class="p-4 bg-rose-50 text-rose-700 text-sm rounded-2xl border border-rose-200">
                <p class="font-semibold mb-1">Periksa kembali:</p>
                <ul class="list-disc list-inside space-y-0.5">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} BukuBook Perpustakaan & POS System. All rights reserved.
    </footer>

</body>
</html>
