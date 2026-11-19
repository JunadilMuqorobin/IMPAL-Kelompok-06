@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
    @php
        $member = Auth::guard('member')->user();
        $isPremium = $member->isPremium || $member->tipeMember === 'Premium';
    @endphp

    <a href="{{ route('member.books.index') }}" class="text-sm text-blue-600 font-semibold hover:underline">← Kembali ke katalog</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
                <div class="flex flex-wrap justify-between items-start gap-3">
                    <div>
                        <span class="px-2.5 py-1 text-[11px] font-bold bg-blue-100 text-blue-700 rounded-full">{{ $buku->kategori }}</span>
                        <h1 class="text-2xl font-extrabold text-slate-900 mt-3">{{ $buku->judul }}</h1>
                        <p class="text-sm text-slate-500 mt-1">Oleh: {{ $buku->pengarang }}</p>
                    </div>
                    <span class="px-3 py-1.5 text-xs font-bold rounded-full
                        {{ $buku->status === 'Tersedia' ? 'bg-emerald-100 text-emerald-700' : ($buku->status === 'Dipinjam' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                        {{ $buku->status }}
                    </span>
                </div>

                <div class="grid grid-cols-3 gap-4 mt-6 pt-6 border-t border-slate-100">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">ID Buku</p>
                        <p class="text-sm font-bold text-slate-800 font-mono">{{ $buku->idBuku }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">Rating Buku</p>
                        <p class="text-sm font-bold text-amber-500">★ {{ $buku->rating }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">Rata-rata Ulasan</p>
                        <p class="text-sm font-bold text-slate-800">★ {{ $average }} ({{ $ulasans->count() }})</p>
                    </div>
                </div>

                <div class="mt-6 pt-6 border-t border-slate-100">
                    @if($buku->status === 'Tersedia')
                        <p class="text-sm text-emerald-700 font-semibold mb-3">✓ Buku ini tersedia untuk dipinjam.</p>
                        <form method="POST" action="{{ route('member.books.borrow', $buku->idBuku) }}">
                            @csrf
                            <button type="submit" class="px-5 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700">Pinjam Buku Ini</button>
                        </form>
                    @elseif($buku->status === 'Dipinjam')
                        <p class="text-sm text-blue-700 font-semibold mb-3">Buku sedang dipinjam member lain. Anda dapat masuk antrean reservasi (khusus Premium).</p>
                        <div class="flex flex-wrap gap-2">
                            @if($isPremium && ! $myBooking)
                                <form method="POST" action="{{ route('member.books.reserve', $buku->idBuku) }}">
                                    @csrf
                                    <button type="submit" class="px-5 py-3 bg-amber-500 text-white text-sm font-semibold rounded-xl hover:bg-amber-600">Reservasi Buku</button>
                                </form>
                            @elseif(! $isPremium)
                                <span class="px-4 py-3 bg-slate-100 text-slate-500 text-xs font-semibold rounded-xl">🔒 Reservasi khusus member Premium</span>
                            @elseif($myBooking)
                                <span class="px-4 py-3 bg-blue-50 text-blue-700 text-xs font-semibold rounded-xl">Anda berada di antrean reservasi.</span>
                            @endif
                        </div>
                    @else
                        <p class="text-sm text-amber-700 font-semibold mb-3">Buku sedang dalam masa pengambilan reservasi.</p>
                        @if($myBooking && $firstBooking && $firstBooking->idBooking === $myBooking->idBooking)
                            <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl space-y-3">
                                <p class="text-sm font-bold text-amber-800">Anda berada di posisi pertama antrean!</p>
                                <p class="text-xs text-amber-700">Ambil buku paling lambat tanggal {{ $deadline ? $deadline->format('d M Y') : '-' }}.</p>
                                <form method="POST" action="{{ route('member.bookings.claim', $myBooking->idBooking) }}">
                                    @csrf
                                    <button type="submit" class="px-5 py-2.5 bg-amber-600 text-white text-sm font-semibold rounded-xl hover:bg-amber-700">Ambil Buku Sekarang</button>
                                </form>
                            </div>
                        @elseif($myBooking)
                            <p class="text-sm text-slate-600">Antrean Anda: <strong>#{{ $myBooking->posisiAntrian }}</strong>. Tunggu hingga giliran Anda.</p>
                        @else
                            <p class="text-sm text-slate-600">Antrean sedang diproses. Reservasi baru tidak dapat dibuat.</p>
                        @endif
                    @endif

                    @if($myBooking)
                        <form method="POST" action="{{ route('member.bookings.cancel', $myBooking->idBooking) }}" class="mt-3">
                            @csrf
                            <button type="submit" class="text-xs font-semibold text-rose-600 hover:underline">Batalkan reservasi saya</button>
                        </form>
                    @endif
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
                <h2 class="text-lg font-extrabold text-slate-900 mb-1">Ulasan Member</h2>
                <p class="text-xs text-slate-500 mb-5">{{ $ulasans->count() }} ulasan · rata-rata ★ {{ $average }}</p>

                <div class="space-y-4">
                    @forelse($ulasans as $ulasan)
                        <div class="p-4 bg-slate-50 rounded-2xl">
                            <div class="flex justify-between items-center">
                                <p class="text-sm font-semibold text-slate-800">{{ $ulasan->member->nama }}</p>
                                <span class="text-sm font-bold text-amber-500">★ {{ $ulasan->nilaiRating }}</span>
                            </div>
                            <p class="text-sm text-slate-600 mt-1">{{ $ulasan->komentar ?: '(tanpa komentar)' }}</p>
                            <p class="text-[11px] text-slate-400 mt-1">{{ $ulasan->tanggalUlasan }}</p>
                        </div>
                    @empty
                        <p class="text-sm text-slate-400">Belum ada ulasan. Jadilah yang pertama!</p>
                    @endforelse
                </div>
            </div>
        </div>

        <div>
            <div class="bg-white rounded-2xl border border-slate-200 p-6">
                <h2 class="text-lg font-extrabold text-slate-900 mb-2">Tulis Ulasan</h2>
                @if($sudahPinjam)
                    <p class="text-xs text-slate-500 mb-4">
                        {{ $sudahUlas ? 'Anda sudah memiliki ulasan. Mengirim ulasan baru akan memperbaruinya.' : 'Bagikan pengalaman Anda membaca buku ini.' }}
                    </p>
                    <form method="POST" action="{{ route('member.books.review', $buku->idBuku) }}" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Rating (1–5)</label>
                            <select name="nilaiRating" required class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                                <option value="">Pilih rating</option>
                                @for($i = 1; $i <= 5; $i++)
                                    <option value="{{ $i }}">{{ $i }} bintang</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Komentar</label>
                            <textarea name="komentar" rows="4" maxlength="1000" placeholder="Tulis komentar Anda..."
                                class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('komentar') }}</textarea>
                        </div>
                        <button type="submit" class="w-full py-3 bg-slate-900 text-white text-sm font-semibold rounded-xl hover:bg-slate-800">
                            {{ $sudahUlas ? 'Perbarui Ulasan' : 'Kirim Ulasan' }}
                        </button>
                    </form>
                @else
                    <div class="p-4 bg-amber-50 border border-amber-200 rounded-xl text-xs text-amber-800">
                        Anda hanya dapat mengulas buku yang pernah Anda pinjam.
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
