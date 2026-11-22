<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pembayaran {{ $detail->idDenda }} - Bukubook</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 text-gray-800 p-6 md:p-10">
    <div class="max-w-md mx-auto bg-white p-6 rounded-lg shadow-sm border border-gray-200">
        <div class="text-center border-b border-dashed border-gray-300 pb-4 mb-4">
            <h1 class="text-lg font-bold uppercase tracking-wide text-gray-900">Bukubook Library</h1>
            <p class="text-xs text-gray-500">Bukti Pembayaran Denda Keterlambatan</p>
            <p class="mt-2 text-xs font-mono bg-gray-100 inline-block px-2 py-1 rounded">
                {{ $detail->noReferensi ?? 'REF-' . $detail->idDenda }}
            </p>
        </div>

        <div class="space-y-2 text-sm border-b border-dashed border-gray-300 pb-4 mb-4">
            <div class="flex justify-between">
                <span class="text-gray-500">ID Transaksi</span>
                <span class="font-medium">{{ $detail->idDenda }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Tanggal Bayar</span>
                <span>{{ \Carbon\Carbon::parse($detail->tanggalBayar)->format('d/m/Y H:i') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Nama Member</span>
                <span class="font-medium">{{ $detail->namaMember }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Judul Buku</span>
                <span class="text-right max-w-[200px]">{{ $detail->judulBuku }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Petugas Admin</span>
                <span>{{ $detail->namaAdmin }}</span>
            </div>
        </div>

        <div class="space-y-2 text-sm border-b border-dashed border-gray-300 pb-4 mb-6">
            <div class="flex justify-between">
                <span class="text-gray-500">Keterlambatan</span>
                <span>{{ $detail->jumlahHariTerlambat }} Hari</span>
            </div>
            <div class="flex justify-between font-bold text-gray-900">
                <span>Total Denda</span>
                <span>Rp {{ number_format($detail->totalDenda, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Jumlah Bayar ({{ $detail->metodePembayaran }})</span>
                <span>Rp {{ number_format($detail->jumlahBayar, 0, ',', '.') }}</span>
            </div>
            <div class="flex justify-between">
                <span class="text-gray-500">Kembalian</span>
                <span>Rp {{ number_format($detail->kembalian, 0, ',', '.') }}</span>
            </div>
        </div>

        <div class="flex gap-3 print:hidden">
            <a href="{{ route('denda.index') }}" class="flex-1 text-center py-2 px-4 border border-gray-300 rounded-md text-sm font-medium hover:bg-gray-50">
                Kembali
            </a>
            <button onclick="window.print()" class="flex-1 py-2 px-4 bg-gray-900 text-white rounded-md text-sm font-medium hover:bg-gray-800">
                Cetak Struk
            </button>
        </div>
    </div>
</body>
</html>