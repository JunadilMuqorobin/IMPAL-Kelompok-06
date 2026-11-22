<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Input Pembayaran Denda - Bukubook</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50 text-gray-800 p-6 md:p-10">
    <div class="max-w-xl mx-auto bg-white p-6 md:p-8 rounded-lg shadow-sm border border-gray-200">
        <div class="flex items-center justify-between mb-6">
            <h1 class="text-xl font-bold text-gray-900">Form Pembayaran Denda</h1>
            <a href="{{ route('denda.index') }}" class="text-sm text-gray-500 hover:text-gray-800">&larr; Kembali</a>
        </div>

        @if ($errors->any())
            <div class="mb-4 p-4 bg-red-50 border border-red-200 text-red-700 rounded-md text-sm">
                <ul class="list-disc pl-5 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('denda.store') }}" method="POST" class="space-y-4 text-sm">
            @csrf

            <div>
                <label for="idPeminjaman" class="block font-medium text-gray-700 mb-1">Pilih Peminjaman Terlambat</label>
                <select id="idPeminjaman" name="idPeminjaman" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-gray-900">
                    <option value="" data-hari="0" data-denda="0">-- Pilih Data Peminjaman --</option>
                    @foreach ($peminjamanTerlambat as $pinjam)
                        @php
                            $batas = \Carbon\Carbon::parse($pinjam->batasKembali);
                            $kembali = $pinjam->tanggalKembali ? \Carbon\Carbon::parse($pinjam->tanggalKembali) : \Carbon\Carbon::now();
                            $hari = $kembali->greaterThan($batas) ? $batas->diffInDays($kembali) : 0;
                            $denda = $hari * 5000;
                        @endphp
                        <option value="{{ $pinjam->idPeminjaman }}" data-hari="{{ $hari }}" data-denda="{{ $denda }}" {{ old('idPeminjaman') == $pinjam->idPeminjaman ? 'selected' : '' }}>
                            {{ $pinjam->idPeminjaman }} - {{ $pinjam->namaMember }} ({{ $pinjam->judulBuku }})
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="grid grid-cols-2 gap-4 p-4 bg-gray-50 rounded-md border border-gray-200">
                <div>
                    <span class="block text-xs text-gray-500">Keterlambatan</span>
                    <span id="infoHari" class="text-base font-semibold text-gray-900">0 Hari</span>
                </div>
                <div>
                    <span class="block text-xs text-gray-500">Total Denda (Rp 5.000/hari)</span>
                    <span id="infoDenda" class="text-base font-bold text-red-600">Rp 0</span>
                </div>
            </div>

            <div>
                <label for="metodePembayaran" class="block font-medium text-gray-700 mb-1">Metode Pembayaran</label>
                <select id="metodePembayaran" name="metodePembayaran" required class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-gray-900">
                    <option value="Tunai" {{ old('metodePembayaran') == 'Tunai' ? 'selected' : '' }}>Tunai</option>
                    <option value="QRIS" {{ old('metodePembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS</option>
                    <option value="Transfer E-Wallet" {{ old('metodePembayaran') == 'Transfer E-Wallet' ? 'selected' : '' }}>Transfer E-Wallet</option>
                </select>
            </div>

            <div>
                <label for="jumlahBayar" class="block font-medium text-gray-700 mb-1">Nominal Bayar (Rp)</label>
                <input type="number" id="jumlahBayar" name="jumlahBayar" value="{{ old('jumlahBayar') }}" min="0" required placeholder="Contoh: 10000" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:border-gray-900">
                <p class="mt-1 text-xs text-gray-500">Estimasi Kembalian: <strong id="infoKembalian" class="text-gray-800">Rp 0</strong></p>
            </div>

            <button type="submit" class="w-full py-2.5 px-4 bg-gray-900 text-white font-medium rounded-md hover:bg-gray-800 transition">
                Proses Pembayaran & Terbitkan Struk
            </button>
        </form>
    </div>

    <script>
        const selectPinjam = document.getElementById('idPeminjaman');
        const inputBayar = document.getElementById('jumlahBayar');
        const infoHari = document.getElementById('infoHari');
        const infoDenda = document.getElementById('infoDenda');
        const infoKembalian = document.getElementById('infoKembalian');

        function formatRupiah(angka) {
            return 'Rp ' + Number(angka).toLocaleString('id-ID');
        }

        function hitungEstimasi() {
            const selectedOption = selectPinjam.options[selectPinjam.selectedIndex];
            const hari = parseInt(selectedOption.getAttribute('data-hari')) || 0;
            const denda = parseInt(selectedOption.getAttribute('data-denda')) || 0;
            const bayar = parseInt(inputBayar.value) || 0;
            const kembalian = Math.max(0, bayar - denda);

            infoHari.textContent = hari + ' Hari';
            infoDenda.textContent = formatRupiah(denda);
            infoKembalian.textContent = formatRupiah(kembalian);
        }

        selectPinjam.addEventListener('change', hitungEstimasi);
        inputBayar.addEventListener('input', hitungEstimasi);
        hitungEstimasi();
    </script>
</body>
</html>