<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitoring Langganan Premium | Admin BukuBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-slate-100 text-slate-800 antialiased">

    <nav class="bg-slate-900 text-white shadow">
        <div class="max-w-6xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-3">
                <span class="bg-indigo-500 text-white font-bold px-2.5 py-1 rounded text-xs">ADMIN PANEL</span>
                <h1 class="text-base font-semibold">Monitoring Transaksi Langganan Premium (Auto-Payment Gateway)</h1>
            </div>
            <span class="text-xs text-slate-400">Mode: Pantau Otomatis (Read-Only)</span>
        </div>
    </nav>

    <main class="max-w-6xl mx-auto px-6 py-8 space-y-6">

        <!-- Ringkasan Statistik -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-medium text-slate-500">Total Transaksi Masuk</p>
                <p class="text-2xl font-bold text-slate-900 mt-1">{{ $semuaTransaksi->count() }} Transaksi</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-medium text-slate-500">Total Member Premium Aktif</p>
                <p class="text-2xl font-bold text-indigo-600 mt-1">{{ $totalMemberPremium }} Member</p>
            </div>
            <div class="bg-white p-5 rounded-xl border border-slate-200 shadow-sm">
                <p class="text-xs font-medium text-slate-500">Total Pendapatan Langganan</p>
                <p class="text-2xl font-bold text-emerald-600 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</p>
            </div>
        </div>

        <!-- Tabel Log Transaksi -->
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                <h2 class="font-bold text-slate-800 text-sm">Log Aktivasi Member Premium Real-Time</h2>
                <span class="text-xs bg-emerald-100 text-emerald-800 font-semibold px-2.5 py-1 rounded-full">● Terintegrasi Payment Gateway</span>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-slate-50 text-slate-500 text-xs uppercase">
                        <tr>
                            <th class="px-6 py-3">ID Transaksi</th>
                            <th class="px-6 py-3">Member</th>
                            <th class="px-6 py-3">Metode Cashless</th>
                            <th class="px-6 py-3">Total Bayar</th>
                            <th class="px-6 py-3">Masa Berlaku</th>
                            <th class="px-6 py-3">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($semuaTransaksi as $trx)
                            <tr class="hover:bg-slate-50">
                                <td class="px-6 py-3.5 font-mono text-xs font-semibold">{{ $trx->idTransaksiPremium }}</td>
                                <td class="px-6 py-3.5">
                                    <p class="font-semibold text-slate-800">{{ $trx->member->nama ?? $trx->idMember }}</p>
                                    <p class="text-xs text-slate-400">{{ $trx->idMember }}</p>
                                </td>
                                <td class="px-6 py-3.5 text-xs text-slate-600">{{ $trx->metodePembayaran }}</td>
                                <td class="px-6 py-3.5 font-semibold text-slate-900">Rp {{ number_format($trx->totalBayar, 0, ',', '.') }}</td>
                                <td class="px-6 py-3.5 text-xs text-slate-600">{{ $trx->masaBerlakuHingga }}</td>
                                <td class="px-6 py-3.5">
                                    <span class="bg-emerald-100 text-emerald-700 text-xs font-bold px-2.5 py-1 rounded-full">Lunas & Aktif</span>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="px-6 py-8 text-center text-slate-400 text-sm">Belum ada transaksi langganan masuk.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </main>

</body>
</html>