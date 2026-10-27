<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Member - BukuBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style> body { font-family: 'Inter', sans-serif; } </style>
</head>
<body class="bg-slate-50 text-slate-800 min-h-screen flex flex-col">

    <!-- Top Navigation Bar -->
    <nav class="bg-white border-b border-slate-200 sticky top-0 z-50">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-3.5 flex justify-between items-center">
            <div class="flex items-center gap-3">
                <a href="{{ route('member.dashboard') }}" class="flex items-center gap-2">
                    <div class="bg-blue-600 text-white font-bold px-2.5 py-1 rounded-lg text-lg">BB</div>
                    <span class="text-xl font-bold text-blue-600">BukuBook</span>
                </a>
            </div>

            <!-- Profile & Logout Bar -->
            <div class="flex items-center gap-4">
                <div class="text-right hidden sm:block">
                    <p class="text-xs text-slate-400">ID Member: <span class="font-semibold text-slate-600">{{ $member->idMember }}</span></p>
                    <p class="text-sm font-semibold text-slate-800">{{ $member->nama }}</p>
                </div>

                <!-- Tipe Member Badge -->
                @if($member->isPremium || $member->tipeMember === 'Premium')
                    <span class="px-3 py-1 bg-gradient-to-r from-amber-500 to-amber-600 text-white text-xs font-bold rounded-full shadow-sm">
                        ★ PREMIUM
                    </span>
                @else
                    <span class="px-3 py-1 bg-slate-200 text-slate-700 text-xs font-semibold rounded-full">
                        BASIC
                    </span>
                @endif

                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="px-3.5 py-2 text-xs font-semibold text-rose-600 hover:bg-rose-50 rounded-xl transition border border-rose-200">
                        Keluar
                    </button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Container -->
    <main class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 flex-grow w-full space-y-8">

        <!-- Welcome Banner -->
        <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white shadow-lg relative overflow-hidden flex flex-col md:flex-row justify-between items-start md:items-center gap-6">
            <div class="space-y-2 max-w-xl z-10">
                <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-semibold backdrop-blur-md">Panel Anggota</span>
                <h1 class="text-2xl sm:text-3xl font-extrabold">Halo, {{ $member->nama }}! 👋</h1>
                <p class="text-blue-100 text-sm leading-relaxed">
                    @if($member->isPremium)
                        Akun Premium kamu aktif! Kamu bisa meminjam hingga <strong>5 buku</strong> sekaligus dengan masa durasi <strong>30 hari</strong>.
                    @else
                        Saat ini kamu terdaftar sebagai Member Basic (Maksimal 1 buku, durasi 7 hari). Segera upgrade ke Premium!
                    @endif
                </p>
            </div>

            @if(!$member->isPremium)
                <div class="z-10 bg-white/10 backdrop-blur-md border border-white/20 rounded-2xl p-4 text-center sm:text-right">
                    <p class="text-xs text-blue-100 mb-2">Ingin menambah kuota & akses booking?</p>
                    <span class="inline-block px-4 py-2 bg-amber-400 text-slate-900 font-bold text-xs rounded-xl shadow">
                        Upgrade Premium
                    </span>
                </div>
            @endif
        </div>

        <!-- Quick Summary Cards -->
        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
            <!-- Card 1: Slot Peminjaman -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Peminjaman Aktif</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $peminjamanAktif->count() }} <span class="text-slate-400 text-sm font-normal">/ {{ $member->maxPinjam }} Buku</span>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center font-bold text-xl">
                    📚
                </div>
            </div>

            <!-- Card 2: Durasi Pinjam -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Batas Masa Pinjam</p>
                    <h3 class="text-2xl font-bold text-slate-800 mt-1">
                        {{ $member->durasiPinjamHari }} <span class="text-slate-400 text-sm font-normal">Hari / Buku</span>
                    </h3>
                </div>
                <div class="w-12 h-12 bg-indigo-50 text-indigo-600 rounded-2xl flex items-center justify-center font-bold text-xl">
                    ⏱️
                </div>
            </div>

            <!-- Card 3: Akses Booking -->
            <div class="bg-white rounded-2xl p-6 border border-slate-200 shadow-sm flex justify-between items-center">
                <div>
                    <p class="text-xs font-semibold text-slate-400 uppercase tracking-wider">Akses Booking Buku</p>
                    <h3 class="text-lg font-bold text-slate-800 mt-1">
                        @if($member->isPremium)
                            <span class="text-emerald-600">Aktif ({{ $bookingAktif->count() }} Antrean)</span>
                        @else
                            <span class="text-slate-400">Non-Aktif (Terkunci)</span>
                        @endif
                    </h3>
                </div>
                <div class="w-12 h-12 bg-amber-50 text-amber-600 rounded-2xl flex items-center justify-center font-bold text-xl">
                    📌
                </div>
            </div>
        </div>

        <!-- Interactive Tabs Section (Alpine.js) -->
        <div x-data="{ activeTab: 'peminjaman' }" class="bg-white rounded-3xl border border-slate-200 shadow-sm p-6 sm:p-8 space-y-6">
            
            <!-- Tab Headers -->
            <div class="flex border-b border-slate-200 gap-8 overflow-x-auto">
                <button @click="activeTab = 'peminjaman'" 
                    :class="activeTab === 'peminjaman' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'"
                    class="pb-4 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
                    <span>📖</span> Peminjaman Aktif Saya ({{ $peminjamanAktif->count() }})
                </button>

                <button @click="activeTab = 'booking'" 
                    :class="activeTab === 'booking' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'"
                    class="pb-4 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
                    <span>📌</span> Antrean Booking ({{ $bookingAktif->count() }})
                </button>

                <button @click="activeTab = 'katalog'" 
                    :class="activeTab === 'katalog' ? 'border-blue-600 text-blue-600 font-bold' : 'border-transparent text-slate-400 hover:text-slate-600 font-medium'"
                    class="pb-4 border-b-2 text-sm transition whitespace-nowrap flex items-center gap-2">
                    <span>🔍</span> Jelajah Katalog Buku
                </button>
            </div>

            <!-- Tab 1: Peminjaman Aktif -->
            <div x-show="activeTab === 'peminjaman'" class="space-y-4">
                @if($peminjamanAktif->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="p-4">Buku</th>
                                    <th class="p-4">Tanggal Pinjam</th>
                                    <th class="p-4">Batas Pengembalian</th>
                                    <th class="p-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($peminjamanAktif as $item)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-4 font-semibold text-slate-800">{{ $item->buku->judul }}</td>
                                        <td class="p-4">{{ $item->tanggalPinjam }}</td>
                                        <td class="p-4 text-blue-600 font-medium">{{ $item->batasKembali }}</td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-semibold rounded-full">
                                                {{ $item->statusPengembalian }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm">Kamu sedang tidak meminjam buku apa pun saat ini.</p>
                        <button @click="activeTab = 'katalog'" class="mt-3 px-4 py-2 bg-blue-600 text-white text-xs font-semibold rounded-xl hover:bg-blue-700 transition">
                            Cari Buku di Katalog
                        </button>
                    </div>
                @endif
            </div>

            <!-- Tab 2: Antrean Booking -->
            <div x-show="activeTab === 'booking'" class="space-y-4">
                @if(!$member->isPremium)
                    <div class="p-6 bg-amber-50 border border-amber-200 rounded-2xl text-amber-800 text-sm space-y-2">
                        <p class="font-bold">🔒 Fitur Booking Khusus Member Premium</p>
                        <p class="text-xs">Fitur booking digunakan untuk melakukan pemesanan prioritas saat buku favoritmu sedang dipinjam oleh orang lain. Upgrade akun kamu di Kasir POS untuk mengaktifkan fitur ini.</p>
                    </div>
                @elseif($bookingAktif->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm text-slate-600">
                            <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-semibold border-b border-slate-200">
                                <tr>
                                    <th class="p-4">Judul Buku</th>
                                    <th class="p-4">Waktu Booking</th>
                                    <th class="p-4">Posisi Antrean</th>
                                    <th class="p-4">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-100">
                                @foreach($bookingAktif as $booking)
                                    <tr class="hover:bg-slate-50/80 transition">
                                        <td class="p-4 font-semibold text-slate-800">{{ $booking->buku->judul }}</td>
                                        <td class="p-4">{{ $booking->waktuBooking }}</td>
                                        <td class="p-4 font-bold text-amber-600">Antrean Ke-{{ $booking->posisiAntrian }}</td>
                                        <td class="p-4">
                                            <span class="px-2.5 py-1 bg-amber-100 text-amber-700 text-xs font-semibold rounded-full">
                                                {{ $booking->statusBooking }}
                                            </span>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-12 bg-slate-50 rounded-2xl border border-dashed border-slate-200">
                        <p class="text-slate-400 text-sm">Tidak ada antrean booking aktif.</p>
                    </div>
                @endif
            </div>

            <!-- Tab 3: Katalog Buku -->
            <div x-show="activeTab === 'katalog'" class="space-y-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 gap-6">
                    @foreach($katalogBuku as $buku)
                        <div class="border border-slate-200 rounded-2xl p-5 hover:border-blue-300 transition bg-slate-50/50 flex flex-col justify-between">
                            <div class="space-y-2">
                                <span class="px-2.5 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-700 rounded-full">
                                    {{ $buku->kategori }}
                                </span>
                                <h4 class="font-bold text-slate-800 text-base leading-snug">{{ $buku->judul }}</h4>
                                <p class="text-xs text-slate-500">Pengarang: {{ $buku->pengarang }}</p>
                            </div>

                            <div class="pt-4 border-t border-slate-200/60 mt-4 flex justify-between items-center">
                                <span class="text-xs font-medium text-slate-600">Status: <strong>{{ $buku->status }}</strong></span>
                                <span class="text-xs text-amber-500 font-bold">★ {{ $buku->rating }}</span>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>

    </main>

    <!-- Simple Footer -->
    <footer class="bg-white border-t border-slate-200 py-4 text-center text-xs text-slate-400">
        &copy; {{ date('Y') }} BukuBook Perpustakaan & POS System. All rights reserved.
    </footer>

</body>
</html>