@extends('layouts.app')

@section('title', 'Kelola Buku')

@section('content')
    <div class="flex flex-wrap justify-between items-center gap-4">
        <div>
            <h1 class="text-2xl font-extrabold text-slate-900">Manajemen Koleksi Buku</h1>
            <p class="text-sm text-slate-500 mt-1">Kelola data buku, status, dan ketersediaan perpustakaan.</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.books.statistics') }}" class="px-4 py-2.5 bg-white border border-slate-200 text-slate-700 text-sm font-semibold rounded-xl hover:bg-slate-50">📊 Statistik</a>
            <a href="{{ route('admin.books.create') }}" class="px-4 py-2.5 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700">+ Tambah Buku</a>
        </div>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 p-5">
        <form method="GET" action="{{ route('admin.books.index') }}" class="grid grid-cols-1 md:grid-cols-4 gap-3 items-end">
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
                <label class="block text-xs font-semibold text-slate-600 mb-1.5">Status</label>
                <select name="status" class="w-full px-3.5 py-2.5 bg-slate-50 border border-slate-200 rounded-xl text-sm">
                    <option value="">Semua status</option>
                    @foreach(['Tersedia', 'Dipinjam', 'Dibooking'] as $opsi)
                        <option value="{{ $opsi }}" @selected(request('status') === $opsi)>{{ $opsi }}</option>
                    @endforeach
                </select>
            </div>
            <div class="md:col-span-4 flex gap-3">
                <button type="submit" class="px-4 py-2 bg-slate-900 text-white text-sm font-semibold rounded-xl">Terapkan Filter</button>
                <a href="{{ route('admin.books.index') }}" class="px-4 py-2 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl">Reset</a>
            </div>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 border-b border-slate-200 text-sm font-semibold text-slate-700">Total: {{ $buku->count() }} buku</div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm">
                <thead class="bg-slate-50 text-slate-400 uppercase text-xs font-semibold">
                    <tr>
                        <th class="p-4">ID</th>
                        <th class="p-4">Judul</th>
                        <th class="p-4">Pengarang</th>
                        <th class="p-4">Kategori</th>
                        <th class="p-4">Status</th>
                        <th class="p-4">Rating</th>
                        <th class="p-4">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($buku as $item)
                        <tr class="hover:bg-slate-50/70">
                            <td class="p-4 font-mono text-xs text-slate-500">{{ $item->idBuku }}</td>
                            <td class="p-4 font-semibold text-slate-800">{{ $item->judul }}</td>
                            <td class="p-4 text-slate-600">{{ $item->pengarang }}</td>
                            <td class="p-4 text-slate-600">{{ $item->kategori }}</td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 text-xs font-semibold rounded-full
                                    {{ $item->status === 'Tersedia' ? 'bg-emerald-100 text-emerald-700' : ($item->status === 'Dipinjam' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700') }}">
                                    {{ $item->status }}
                                </span>
                            </td>
                            <td class="p-4 text-amber-500 font-semibold">★ {{ $item->rating }}</td>
                            <td class="p-4">
                                <div class="flex flex-wrap gap-2 items-center text-xs">
                                    <a href="{{ route('admin.books.show', $item->idBuku) }}" class="text-blue-600 font-semibold hover:underline">Detail</a>
                                    <a href="{{ route('admin.books.edit', $item->idBuku) }}" class="text-slate-600 font-semibold hover:underline">Edit</a>

                                    <form method="POST" action="{{ route('admin.books.status', $item->idBuku) }}">
                                        @csrf
                                        @method('PATCH')
                                        <input type="hidden" name="status" value="{{ $item->status }}">
                                        <button type="submit" class="text-indigo-600 font-semibold hover:underline">Sinkron</button>
                                    </form>

                                    @if($item->status === 'Dipinjam')
                                        <form method="POST" action="{{ route('admin.books.return', $item->idBuku) }}">
                                            @csrf
                                            <button type="submit" class="text-emerald-600 font-semibold hover:underline">Kembalikan</button>
                                        </form>
                                    @endif

                                    <form method="POST" action="{{ route('admin.books.destroy', $item->idBuku) }}" onsubmit="return confirm('Yakin ingin menghapus buku ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-rose-600 font-semibold hover:underline">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="p-10 text-center text-slate-400">Tidak ada buku yang cocok dengan filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
@endsection
