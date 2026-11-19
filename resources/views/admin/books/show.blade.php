@extends('layouts.app')

@section('title', 'Detail Buku')

@section('content')
    <a href="{{ route('admin.books.index') }}" class="text-sm text-blue-600 font-semibold hover:underline">← Kembali ke daftar buku</a>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mt-4">
        <div class="lg:col-span-2 space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8">
                <div class="flex flex-wrap gap-3 justify-between items-start">
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

                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mt-6 pt-6 border-t border-slate-100">
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">ID Buku</p>
                        <p class="text-sm font-bold text-slate-800 font-mono">{{ $buku->idBuku }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">Rating</p>
                        <p class="text-sm font-bold text-amber-500">★ {{ $buku->rating }}</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">Rata-rata Ulasan</p>
                        <p class="text-sm font-bold text-slate-800">★ {{ $average }} ({{ $ulasans->count() }} ulasan)</p>
                    </div>
                    <div>
                        <p class="text-[11px] font-semibold text-slate-400 uppercase">Total Transaksi</p>
                        <p class="text-sm font-bold text-slate-800">{{ $buku->peminjamans->count() }} pinjaman</p>
                    </div>
                </div>

                <div class="flex flex-wrap gap-2 mt-6 pt-6 border-t border-slate-100 text-xs font-semibold">
                    <a href="{{ route('admin.books.edit', $buku->idBuku) }}" class="px-3.5 py-2 bg-blue-600 text-white rounded-xl hover:bg-blue-700">Edit Data</a>

                    <form method="POST" action="{{ route('admin.books.status', $buku->idBuku) }}">
                        @csrf
                        @method('PATCH')
                        <input type="hidden" name="status" value="{{ $buku->status }}">
                        <button type="submit" class="px-3.5 py-2 bg-indigo-50 text-indigo-700 rounded-xl hover:bg-indigo-100">Sinkronkan Status</button>
                    </form>

                    @if($buku->status === 'Dipinjam')
                        <form method="POST" action="{{ route('admin.books.return', $buku->idBuku) }}">
                            @csrf
                            <button type="submit" class="px-3.5 py-2 bg-emerald-50 text-emerald-700 rounded-xl hover:bg-emerald-100">Kembalikan Buku</button>
                        </form>
                    @endif

                    <form method="POST" action="{{ route('admin.books.destroy', $buku->idBuku) }}" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="px-3.5 py-2 bg-rose-50 text-rose-700 rounded-xl hover:bg-rose-100">Hapus Buku</button>
                    </form>
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
                <div class="p-5 border-b border-slate-200 font-bold text-slate-800">Ulasan Member ({{ $ulasans->count() }})</div>
                @forelse($ulasans as $ulasan)
                    <div class="p-5 border-b border-slate-100 last:border-0">
                        <div class="flex justify-between items-center">
                            <p class="text-sm font-semibold text-slate-800">{{ $ulasan->member->nama }}</p>
                            <span class="text-sm text-amber-500 font-bold">★ {{ $ulasan->nilaiRating }}</span>
                        </div>
                        <p class="text-sm text-slate-600 mt-1">{{ $ulasan->komentar ?: '(tanpa komentar)' }}</p>
                        <p class="text-[11px] text-slate-400 mt-1">{{ $ulasan->tanggalUlasan }}</p>
                    </div>
                @empty
                    <div class="p-8 text-center text-sm text-slate-400">Belum ada ulasan untuk buku ini.</div>
                @endforelse
            </div>
        </div>

        <div class="space-y-6">
            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-bold text-slate-800 mb-4">Peminjaman</h2>
                <div class="space-y-3 text-xs">
                    @forelse($buku->peminjamans->sortByDesc('tanggalPinjam') as $pinjam)
                        <div class="p-3 bg-slate-50 rounded-xl">
                            <p class="font-semibold text-slate-800">{{ $pinjam->member->nama }}</p>
                            <p class="text-slate-500 mt-0.5">{{ $pinjam->tanggalPinjam }} → {{ $pinjam->batasKembali }}</p>
                            <p class="font-semibold {{ $pinjam->statusPengembalian === 'Dipinjam' ? 'text-blue-600' : 'text-emerald-600' }}">{{ $pinjam->statusPengembalian }}</p>
                        </div>
                    @empty
                        <p class="text-slate-400">Belum pernah dipinjam.</p>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-slate-200 p-5">
                <h2 class="font-bold text-slate-800 mb-4">Antrean Reservasi</h2>
                <div class="space-y-3 text-xs">
                    @forelse($buku->bookings->where('statusBooking', 'Aktif')->sortBy('posisiAntrian') as $booking)
                        <div class="p-3 bg-amber-50 rounded-xl flex justify-between">
                            <div>
                                <p class="font-semibold text-slate-800">{{ $booking->member->nama }}</p>
                                <p class="text-slate-500 mt-0.5">{{ $booking->waktuBooking }}</p>
                            </div>
                            <span class="font-bold text-amber-600">#{{ $booking->posisiAntrian }}</span>
                        </div>
                    @empty
                        <p class="text-slate-400">Tidak ada antrean aktif.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
@endsection
