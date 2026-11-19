@extends('layouts.app')

@section('title', 'Statistik Buku')

@section('content')
    <div class="flex justify-between items-center gap-4 flex-wrap">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Statistik Koleksi</h1>
            <p class="text-sm text-slate-500 mt-1">Ringkasan kondisi buku di perpustakaan.</p>
        </div>
        <a href="{{ route('admin.books.index') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">← Daftar Buku</a>
    </div>

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Total Buku</p>
            <p class="text-3xl font-extrabold text-slate-900 mt-2">{{ $total }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Tersedia</p>
            <p class="text-3xl font-extrabold text-emerald-600 mt-2">{{ $available }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Dipinjam</p>
            <p class="text-3xl font-extrabold text-blue-600 mt-2">{{ $borrowed }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Dibooking</p>
            <p class="text-3xl font-extrabold text-amber-600 mt-2">{{ $booked }}</p>
        </div>
        <div class="bg-white rounded-2xl border border-slate-200 p-5">
            <p class="text-[11px] font-semibold text-slate-400 uppercase tracking-wider">Rata-rata Rating</p>
            <p class="text-3xl font-extrabold text-amber-500 mt-2">★ {{ $average }}</p>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden max-w-2xl">
        <div class="p-5 border-b border-slate-200 font-bold text-slate-800">Buku per Kategori</div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-semibold">
                <tr><th class="p-4">Kategori</th><th class="p-4 text-right">Jumlah</th></tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                @forelse($kategori as $item)
                    <tr>
                        <td class="p-4 font-medium text-slate-800">{{ $item->kategori }}</td>
                        <td class="p-4 text-right font-bold text-slate-800">{{ $item->total }}</td>
                    </tr>
                @empty
                    <tr><td colspan="2" class="p-8 text-center text-slate-400">Belum ada data kategori.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
@endsection
