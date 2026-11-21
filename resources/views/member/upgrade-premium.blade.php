<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade Member Premium | BukuBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #area-struk-cetak, #area-struk-cetak * {
                visibility: visible;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            #area-struk-cetak {
                position: fixed;
                top: 24px;
                left: 50%;
                transform: translateX(-50%);
                width: 360px;
                box-shadow: none !important;
            }
            .no-print {
                display: none !important;
            }
        }
    </style>
</head>
<body class="bg-slate-50 text-slate-800 antialiased">

    <!-- Navbar Member -->
    <nav class="bg-white border-b border-slate-200">
        <div class="max-w-5xl mx-auto px-6 py-4 flex justify-between items-center">
            <div class="flex items-center space-x-2">
                <span class="bg-indigo-600 text-white font-bold px-2.5 py-1 rounded-lg text-sm">BukuBook</span>
                <span class="text-sm font-medium text-slate-500">/ Langganan Keanggotaan</span>
            </div>
            @if ($member)
                <div class="flex items-center space-x-3 text-sm">
                    <span class="text-slate-600">{{ $member->nama }} ({{ $member->idMember }})</span>
                    @if ($member->isPremium)
                        <span class="bg-amber-100 text-amber-800 font-bold px-3 py-1 rounded-full text-xs">★ PREMIUM</span>
                    @else
                        <span class="bg-slate-200 text-slate-700 font-semibold px-3 py-1 rounded-full text-xs">BASIC</span>
                    @endif
                </div>
            @endif
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-6 py-8">
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

            <!-- Kolom Kiri: Form Checkout Langganan -->
            <div class="lg:col-span-2 bg-white rounded-2xl shadow-sm border border-slate-200 p-6 md:p-8">
                <div class="mb-6">
                    <h1 class="text-2xl font-bold text-slate-900">Upgrade ke BukuBook Premium</h1>
                    <p class="text-sm text-slate-500 mt-1">
                        Pinjam hingga 5 buku sekaligus, durasi 30 hari, dan akses fitur Booking Antrean Prioritas. Aktivasi otomatis setelah pembayaran cashless.
                    </p>
                </div>

                <!-- Notifikasi Sukses -->
                @if (session('success'))
                    <div class="mb-6 bg-emerald-50 border-l-4 border-emerald-500 p-4 rounded-r-lg">
                        <p class="text-sm font-semibold text-emerald-800">{{ session('success') }}</p>
                    </div>
                @endif

                <!-- Kotak Ringkasan Error Validasi (Untuk Dokumentasi Pengujian) -->
                @if ($errors->any())
                    <div class="mb-6 bg-red-50 border-l-4 border-red-600 p-4 rounded-r-lg">
                        <h2 class="text-sm font-bold text-red-800 mb-1">Pembayaran Tidak Dapat Diproses:</h2>
                        <ul class="list-disc list-inside text-sm text-red-700 space-y-1">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('member.premium.store') }}" method="POST" novalidate class="space-y-6">
                    @csrf

                    <!-- 1. Pilih Paket Langganan -->
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-2">
                            1. Pilih Paket Langganan <span class="text-red-500">*</span>
                        </label>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-3">
                            @foreach ($daftarPaket as $kode => $paket)
                                <label class="cursor-pointer border rounded-xl p-4 flex flex-col justify-between transition {{ old('paketLangganan') == $kode ? 'border-indigo-600 bg-indigo-50 ring-2 ring-indigo-200' : 'border-slate-200 hover:border-indigo-300' }}">
                                    <div class="flex items-center justify-between mb-2">
                                        <span class="text-xs font-bold uppercase tracking-wider text-indigo-600">{{ $paket['durasi'] }} Hari</span>
                                        <input type="radio" name="paketLangganan" value="{{ $kode }}" {{ old('paketLangganan') == $kode ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                    </div>
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $paket['nama'] }}</p>
                                        <p class="text-base font-extrabold text-slate-900 mt-1">Rp {{ number_format($paket['harga'], 0, ',', '.') }}</p>
                                    </div>
                                </label>
                            @endforeach
                        </div>
                        @error('paketLangganan')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 2. Metode Pembayaran Cashless -->
                    <div>
                        <label for="metodePembayaran" class="block text-sm font-semibold text-slate-700 mb-1">
                            2. Metode Pembayaran Cashless (Payment Gateway) <span class="text-red-500">*</span>
                        </label>
                        <select
                            id="metodePembayaran"
                            name="metodePembayaran"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 {{ $errors->has('metodePembayaran') ? 'border-red-500 bg-red-50 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200' }}"
                        >
                            <option value="">-- Pilih Metode Pembayaran Non-Tunai --</option>
                            <option value="QRIS" {{ old('metodePembayaran') == 'QRIS' ? 'selected' : '' }}>QRIS (GoPay, OVO, DANA, ShopeePay, Mobile Banking)</option>
                            <option value="Virtual Account" {{ old('metodePembayaran') == 'Virtual Account' ? 'selected' : '' }}>Virtual Account (BCA, Mandiri, BNI, BRI)</option>
                            <option value="Transfer E-Wallet" {{ old('metodePembayaran') == 'Transfer E-Wallet' ? 'selected' : '' }}>Transfer E-Wallet Instan</option>
                        </select>
                        @error('metodePembayaran')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 3. Nomor HP / Akun E-Wallet -->
                    <div>
                        <label for="nomorAkun" class="block text-sm font-semibold text-slate-700 mb-1">
                            3. Nomor HP / E-Wallet Konfirmasi Pembayaran <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            id="nomorAkun"
                            name="nomorAkun"
                            value="{{ old('nomorAkun') }}"
                            placeholder="Contoh: 081234567890"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 {{ $errors->has('nomorAkun') ? 'border-red-500 bg-red-50 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200' }}"
                        >
                        @error('nomorAkun')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 4. Kode Voucher Promo (Opsional) -->
                    <div>
                        <label for="kodeVoucher" class="block text-sm font-semibold text-slate-700 mb-1">
                            4. Kode Voucher Promo <span class="text-slate-400 font-normal">(Opsional)</span>
                        </label>
                        <input
                            type="text"
                            id="kodeVoucher"
                            name="kodeVoucher"
                            value="{{ old('kodeVoucher') }}"
                            placeholder="Masukkan kode voucher (contoh: HEMAT20)"
                            class="w-full rounded-xl border px-4 py-2.5 text-sm focus:outline-none focus:ring-2 {{ $errors->has('kodeVoucher') ? 'border-red-500 bg-red-50 focus:ring-red-200' : 'border-slate-300 focus:border-indigo-500 focus:ring-indigo-200' }}"
                        >
                        @error('kodeVoucher')
                            <p class="mt-1.5 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- 5. Syarat & Ketentuan -->
                    <div class="pt-1">
                        <label class="inline-flex items-start space-x-2.5 cursor-pointer">
                            <input
                                type="checkbox"
                                name="syaratKetentuan"
                                value="1"
                                {{ old('syaratKetentuan') ? 'checked' : '' }}
                                class="mt-1 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                            >
                            <span class="text-xs text-slate-600 leading-relaxed">
                                Saya menyetujui Syarat & Ketentuan layanan BukuBook Premium dan memahami bahwa aktivasi keanggotaan dilakukan secara otomatis setelah pembayaran terverifikasi.
                            </span>
                        </label>
                        @error('syaratKetentuan')
                            <p class="mt-1 text-xs font-medium text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Tombol Bayar -->
                    <div class="pt-4 border-t border-slate-100 flex items-center justify-between">
                        <span class="text-xs text-slate-400">Pembayaran terenkripsi otomatis</span>
                        <button type="submit" class="px-6 py-3 rounded-xl bg-indigo-600 text-white text-sm font-bold hover:bg-indigo-700 shadow-sm transition">
                            Bayar Sekarang & Aktifkan Premium
                        </button>
                    </div>
                </form>
            </div>

            <!-- Kolom Kanan: E-Receipt / Struk Digital & Riwayat -->
            <div class="space-y-6" id="area-struk-cetak">
                @if (session('struk_digital'))
                    @php $struk = (object) session('struk_digital'); @endphp
                    <div class="bg-white rounded-2xl shadow-sm border-2 border-emerald-400 p-5 font-mono text-xs">
                        <div class="flex items-center justify-between border-b border-dashed border-slate-300 pb-3 mb-3">
                            <div>
                                <p class="font-bold text-sm text-slate-900">E-RECEIPT BUKUBOOK</p>
                                <p class="text-emerald-600 font-semibold">STATUS: LUNAS (AUTO-VERIFIED)</p>
                            </div>
                        </div>
                        <div class="space-y-1.5 text-slate-700">
                            <p><span class="text-slate-400">ID Struk :</span> {{ $struk->idStruk }}</p>
                            <p><span class="text-slate-400">Ref PG   :</span> {{ $struk->noReferensi }}</p>
                            <p><span class="text-slate-400">Waktu    :</span> {{ $struk->tanggalCetak }}</p>
                            <hr class="border-dashed border-slate-300 my-2">
                            <p class="leading-relaxed">{{ $struk->rincianTransaksi }}</p>
                            <hr class="border-dashed border-slate-300 my-2">
                            <p class="text-sm font-bold text-slate-900">TOTAL DIBAYAR: Rp {{ number_format($struk->totalBiaya, 0, ',', '.') }}</p>
                        </div>
                        <div class="mt-4 pt-3 border-t border-dashed border-slate-300 no-print">
                            <button type="button" onclick="window.print()" class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 text-white font-sans text-xs font-bold hover:bg-emerald-700 transition shadow-sm cursor-pointer">
                                Cetak Struk / Simpan PDF
                            </button>
                        </div>
                    </div>
                @endif
            </div>
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h3 class="text-sm font-bold text-slate-800 border-b border-slate-100 pb-3 mb-3">
                        Riwayat Langganan Anda
                    </h3>
                    @if ($riwayatMember->isEmpty())
                        <p class="text-xs text-slate-400">Belum ada riwayat transaksi langganan.</p>
                    @else
                        <ul class="divide-y divide-slate-100 text-xs">
                            @foreach ($riwayatMember as $trx)
                                <li class="py-2.5">
                                    <div class="flex justify-between font-semibold text-slate-800">
                                        <span>{{ $trx->idTransaksiPremium }}</span>
                                        <span class="text-indigo-600">Rp {{ number_format($trx->totalBayar, 0, ',', '.') }}</span>
                                    </div>
                                    <p class="text-slate-500 mt-0.5">{{ $trx->metodePembayaran }}</p>
                                    <p class="text-slate-400">Aktif s/d: {{ $trx->masaBerlakuHingga }}</p>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
        </div>
    </main>

</body>
</html>