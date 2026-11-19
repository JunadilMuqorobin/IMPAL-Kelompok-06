@extends('layouts.app')

@section('title', 'Riwayat Ulasan')

@section('content')
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Ulasan Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Daftar ulasan yang pernah Anda kirim.</p>
    </div>

    <div class="space-y-4">
        @forelse($ulasans as $ulasan)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6">
                <div class="flex flex-wrap justify-between gap-3 items-start">
                    <div>
                        <a href="{{ route('member.books.show', $ulasan->idBuku) }}" class="font-bold text-slate-900 hover:text-blue-600">{{ $ulasan->buku->judul }}</a>
                        <p class="text-xs text-slate-500">{{ $ulasan->buku->pengarang }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-lg font-extrabold text-amber-500">★ {{ $ulasan->nilaiRating }}</p>
                        <p class="text-[11px] text-slate-400">{{ $ulasan->tanggalUlasan }}</p>
                    </div>
                </div>
                <p class="text-sm text-slate-600 mt-3">{{ $ulasan->komentar ?: '(tanpa komentar)' }}</p>
            </div>
        @empty
            <div class="py-16 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                Anda belum pernah mengirim ulasan.
                <a href="{{ route('member.books.index') }}" class="block mt-2 text-blue-600 font-semibold">Cari buku untuk diulas</a>
            </div>
        @endforelse
    </div>
@endsection
