@extends('layouts.app')

@section('title', 'Katalog Buku')

@section('content')
    <div class="bg-gradient-to-r from-blue-600 via-blue-700 to-indigo-800 rounded-3xl p-6 sm:p-8 text-white">
        <h1 class="text-2xl sm:text-3xl font-extrabold">Jelajahi Katalog Buku</h1>
        <p class="text-blue-100 text-sm mt-2">Cari, filter, dan pinjam buku favoritmu. Member Premium juga bisa membuat reservasi.</p>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <form method="GET" action="{{ route('member.books.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
            <div class="md:col-span-2">
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Cari judul atau pengarang</label>
                <input type="text" name="q" value="{{ request('q') }}" placeholder="Cari buku..."
                    class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Kategori</label>
                <select name="kategori" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                    <option value="">Semua kategori</option>
                    @foreach($kategoris as $kategori)
                        <option value="{{ $kategori }}" @selected(request('kategori') === $kategori)>{{ $kategori }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Ketersediaan</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                    <option value="">Semua status</option>
                    @foreach(['Tersedia', 'Dipinjam', 'Dibooking'] as $opsi)
                        <option value="{{ $opsi }}" @selected(request('status') === $opsi)>{{ $opsi }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Rating minimal</label>
                <select name="rating" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                    <option value="">Semua rating</option>
                    @foreach(['1', '2', '3', '4', '5'] as $opsi)
                        <option value="{{ $opsi }}" @selected(request('rating') === $opsi)>★ {{ $opsi }} ke atas</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-xl">Terapkan Filter</button>
                <a href="{{ route('member.books.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl">Reset</a>
            </div>
        </form>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-5">
        @forelse($buku as $item)
            <a href="{{ route('member.books.show', $item->idBuku) }}" class="bg-white rounded-2xl border border-slate-200 p-5 hover:border-blue-300 hover:shadow-md transition flex flex-col justify-between">
                <div class="space-y-2">
                    <div class="flex justify-between items-center gap-2">
                        <span class="px-2.5 py-0.5 text-[10px] font-bold bg-blue-100 text-blue-700 rounded-full">{{ $item->kategori }}</span>
                        <span class="px-2 py-0.5 text-[10px] font-bold rounded-full
                            {{ $item->status === 'Tersedia' ? 'bg-emerald-100 text-emerald-700' : ($item->status === 'Dipinjam' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                            {{ $item->status }}
                        </span>
                    </div>
                    <h3 class="font-bold text-slate-800 leading-snug">{{ $item->judul }}</h3>
                    <p class="text-xs text-slate-500">Pengarang: {{ $item->pengarang }}</p>
                </div>
                <div class="pt-4 border-t border-slate-100 mt-4 flex justify-between items-center text-xs">
                    <span class="text-slate-400 font-mono">{{ $item->idBuku }}</span>
                    <span class="text-amber-500 font-bold">★ {{ $item->rating }}</span>
                </div>
            </a>
        @empty
            <div class="col-span-full py-14 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                Tidak ada buku yang cocok dengan pencarian Anda.
            </div>
        @endforelse
    </div>
@endsection
