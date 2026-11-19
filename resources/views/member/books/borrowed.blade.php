@extends('layouts.app')

@section('title', 'Buku Pinjaman Saya')

@section('content')
    <div class="flex justify-between items-center gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Buku Pinjaman Saya</h1>
            <p class="text-sm text-slate-500 mt-1">Peminjaman aktif dan riwayat pengembalian Anda.</p>
        </div>
        <a href="{{ route('member.books.index') }}" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700">+ Cari Buku</a>
    </div>

    @php
        $aktif = $peminjamans->where('statusPengembalian', 'Dipinjam');
        $riwayat = $peminjamans->where('statusPengembalian', '!=', 'Dipinjam');
    @endphp

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 font-bold text-slate-800">Sedang Dipinjam ({{ $aktif->count() }})</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-semibold">
                    <tr><th class="p-4">Buku</th><th class="p-4">Tanggal Pinjam</th><th class="p-4">Batas Kembali</th><th class="p-4">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($aktif as $pinjam)
                        <tr>
                            <td class="p-4">
                                <a href="{{ route('member.books.show', $pinjam->idBuku) }}" class="font-semibold text-blue-600 hover:underline">{{ $pinjam->buku->judul }}</a>
                                <p class="text-xs text-slate-400">{{ $pinjam->buku->pengarang }}</p>
                            </td>
                            <td class="p-4 text-slate-600">{{ $pinjam->tanggalPinjam }}</td>
                            <td class="p-4 font-semibold text-blue-600">{{ $pinjam->batasKembali }}</td>
                            <td class="p-4"><span class="px-2.5 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded-full">{{ $pinjam->statusPengembalian }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-slate-400">Anda sedang tidak meminjam buku.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-5 border-b border-slate-200 font-bold text-slate-800">Riwayat Peminjaman ({{ $riwayat->count() }})</div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-semibold">
                    <tr><th class="p-4">Buku</th><th class="p-4">Dipinjam</th><th class="p-4">Dikembalikan</th><th class="p-4">Status</th></tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($riwayat as $pinjam)
                        <tr>
                            <td class="p-4 font-semibold text-slate-800">{{ $pinjam->buku->judul }}</td>
                            <td class="p-4 text-slate-600">{{ $pinjam->tanggalPinjam }}</td>
                            <td class="p-4 text-slate-600">{{ $pinjam->tanggalKembali ?: '-' }}</td>
                            <td class="p-4"><span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full">{{ $pinjam->statusPengembalian }}</span></td>
                        </tr>
                    @empty
                        <tr><td colspan="4" class="p-8 text-center text-slate-400">Belum ada riwayat pengembalian.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
