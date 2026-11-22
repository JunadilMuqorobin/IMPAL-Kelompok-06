<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pembayaran Denda - Bukubook</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 p-6 md:p-10">
    <div class="max-w-6xl mx-auto">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Riwayat Pembayaran Denda</h1>
                <p class="text-sm text-gray-500">Daftar transaksi pelunasan denda keterlambatan pengembalian buku.</p>
            </div>
            <a href="{{ route('denda.create') }}" class="inline-flex items-center justify-center px-4 py-2 bg-gray-900 text-white text-sm font-medium rounded-md hover:bg-gray-800">
                + Catat Pembayaran Denda
            </a>
        </div>

        @if (session('success'))
            <div class="mb-4 p-4 bg-green-50 border border-green-200 text-green-700 rounded-md text-sm">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white shadow-sm border border-gray-200 rounded-lg overflow-x-auto">
            <table class="w-full text-left border-collapse text-sm">
                <thead>
                    <tr class="bg-gray-100 border-b border-gray-200 text-gray-600 uppercase text-xs">
                        <th class="py-3 px-4">ID Denda</th>
                        <th class="py-3 px-4">Peminjam & Buku</th>
                        <th class="py-3 px-4">Terlambat</th>
                        <th class="py-3 px-4">Total Denda</th>
                        <th class="py-3 px-4">Metode</th>
                        <th class="py-3 px-4">Tanggal Bayar</th>
                        <th class="py-3 px-4">Admin</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-200">
                    @forelse ($pembayaran as $item)
                        <tr class="hover:bg-gray-50">
                            <td class="py-3 px-4 font-medium">{{ $item->idDenda }}</td>
                            <td class="py-3 px-4">
                                <div class="font-medium text-gray-900">{{ $item->namaMember }}</div>
                                <div class="text-xs text-gray-500">{{ $item->judulBuku }} ({{ $item->idPeminjaman }})</div>
                            </td>
                            <td class="py-3 px-4">{{ $item->jumlahHariTerlambat }} hari</td>
                            <td class="py-3 px-4 font-semibold">Rp {{ number_format($item->totalDenda, 0, ',', '.') }}</td>
                            <td class="py-3 px-4">{{ $item->metodePembayaran }}</td>
                            <td class="py-3 px-4 text-gray-500">{{ \Carbon\Carbon::parse($item->tanggalBayar)->format('d M Y H:i') }}</td>
                            <td class="py-3 px-4">{{ $item->namaAdmin }}</td>
                            <td class="py-3 px-4 text-center">
                                <a href="{{ route('denda.show', $item->idDenda) }}" class="text-blue-600 hover:underline font-medium">
                                    Lihat Struk
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-6 px-4 text-center text-gray-500">Belum ada data pembayaran denda.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</body>
</html>