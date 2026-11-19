<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BukuBook - Sistem Perpustakaan</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 text-slate-800">

    <!-- Navbar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center gap-2">
                <div class="bg-blue-600 text-white font-bold p-2 rounded-lg text-xl">BB</div>
                <span class="text-2xl font-bold text-blue-600">BukuBook</span>
            </div>
            
            <div class="flex items-center gap-4">
                <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-semibold text-blue-600 hover:text-blue-700 hover:bg-blue-50 rounded-lg transition">
                    Masuk
                </a>
                <a href="{{ route('login') }}" class="px-5 py-2 text-sm font-semibold text-white bg-blue-600 hover:bg-blue-700 rounded-lg shadow-md transition">
                    Daftar Member
                </a>
                <a href="{{ route('admin.login') }}" class="px-4 py-2 text-xs font-semibold text-slate-500 hover:text-slate-800 bg-slate-100 hover:bg-slate-200 rounded-lg transition border border-slate-300">
                    Portal Admin
                </a>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="max-w-7xl mx-auto px-6 py-20 text-center md:text-left md:flex md:items-center md:justify-between">
        <div class="md:w-1/2 space-y-6">
            <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 rounded-full">Perpustakaan Digital</span>
            <h1 class="text-4xl md:text-5xl font-extrabold text-slate-900 leading-tight">
                Akses Ribuan Buku & Nikmati Layanan <span class="text-blue-600">Premium</span>
            </h1>
            <p class="text-slate-600 text-lg leading-relaxed">
                Pinjam buku favoritmu secara mandiri, atur pemesanan (booking), dan nikmati benefit keanggotaan Premium hingga 5 buku sekaligus!
            </p>
            <div class="flex flex-wrap gap-4 pt-2 justify-center md:justify-start">
                <a href="{{ route('login') }}" class="px-7 py-3.5 bg-blue-600 text-white font-semibold rounded-xl hover:bg-blue-700 shadow-lg shadow-blue-200 transition">
                    Mulai Pinjam Buku
                </a>
                <a href="#fitur" class="px-7 py-3.5 bg-white text-slate-700 font-semibold rounded-xl border border-slate-300 hover:bg-slate-100 transition">
                    Lihat Fitur Keanggotaan
                </a>
            </div>
        </div>

        <div class="md:w-5/12 mt-12 md:mt-0">
            <div class="bg-gradient-to-tr from-blue-600 to-indigo-600 rounded-2xl p-8 text-white shadow-2xl relative overflow-hidden">
                <div class="space-y-4">
                    <div class="inline-block px-3 py-1 bg-white/20 rounded-full text-xs font-semibold">Keuntungan Akun Premium</div>
                    <h3 class="text-2xl font-bold">Upgrade Ke Premium!</h3>
                    <ul class="space-y-2 text-sm text-blue-100">
                        <li class="flex items-center gap-2">✓ Pinjam hingga 5 buku sekaligus</li>
                        <li class="flex items-center gap-2">✓ Masa pinjam lebih lama (30 Hari)</li>
                        <li class="flex items-center gap-2">✓ Akses fitur Booking & Antrean Buku</li>
                        <li class="flex items-center gap-2">✓ Diskon promo khusus</li>
                    </ul>
                </div>
            </div>
        </div>
    </section>

    <!-- Cards Fitur Paket -->
    <section id="fitur" class="bg-white py-16 border-t border-slate-200">
        <div class="max-w-7xl mx-auto px-6">
            <div class="text-center max-w-2xl mx-auto mb-12">
                <h2 class="text-3xl font-bold text-slate-900">Pilih Tipe Keanggotaan</h2>
                <p class="text-slate-600 mt-2">Dapatkan fleksibilitas membaca sesuai kebutuhanmu</p>
            </div>

            <div class="grid md:grid-cols-2 gap-8 max-w-4xl mx-auto">
                <!-- Basic Card -->
                <div class="border border-slate-200 rounded-2xl p-8 bg-slate-50 hover:border-slate-300 transition">
                    <h3 class="text-xl font-bold text-slate-800">Member Basic</h3>
                    <p class="text-slate-500 text-sm mt-1">Layanan gratis standar perpustakaan</p>
                    <div class="my-6 text-3xl font-extrabold text-slate-900">Gratis</div>
                    <ul class="space-y-3 text-sm text-slate-600 mb-8">
                        <li>• Maksimal pinjam 1 buku</li>
                        <li>• Durasi pinjam 7 hari</li>
                        <li>• Pencarian & Ulasan Buku</li>
                        <li class="text-slate-400 line-through">• Akses Booking Buku</li>
                    </ul>
                    <a href="{{ route('login') }}" class="block text-center w-full py-3 bg-slate-200 hover:bg-slate-300 text-slate-800 font-semibold rounded-xl transition">Daftar Basic</a>
                </div>

                <!-- Premium Card -->
                <div class="border-2 border-blue-600 rounded-2xl p-8 bg-white relative shadow-xl">
                    <span class="absolute -top-3 right-6 bg-blue-600 text-white text-xs font-bold px-3 py-1 rounded-full">POPULER</span>
                    <h3 class="text-xl font-bold text-blue-600">Member Premium</h3>
                    <p class="text-slate-500 text-sm mt-1">Akses penuh dan prioritas utama</p>
                    <div class="my-6 text-3xl font-extrabold text-slate-900">Berlangganan</div>
                    <ul class="space-y-3 text-sm text-slate-600 mb-8">
                        <li>• Maksimal pinjam 5 buku</li>
                        <li>• Durasi pinjam 30 hari</li>
                        <li>• Fitur Booking & Prioritas Antrean</li>
                        <li>• Promo Diskon khusus transaksi</li>
                    </ul>
                    <a href="{{ route('login') }}" class="block text-center w-full py-3 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl transition">Pilih Premium</a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="bg-slate-900 text-slate-400 py-8 text-center text-sm">
        <p>&copy; {{ date('Y') }} BukuBook Perpustakaan & POS System. All rights reserved.</p>
    </footer>

</body>
</html>