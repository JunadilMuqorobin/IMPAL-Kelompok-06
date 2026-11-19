@extends('layouts.app')

@section('title', 'Tambah Buku')

@section('content')
    <div class="max-w-2xl mx-auto">
        <a href="{{ route('admin.books.index') }}" class="text-sm text-blue-600 font-semibold hover:underline">← Kembali ke daftar buku</a>

        <div class="bg-white rounded-2xl border border-slate-200 p-6 sm:p-8 mt-4">
            <h1 class="text-xl font-extrabold text-slate-900 mb-1">Tambah Buku Baru</h1>
            <p class="text-sm text-slate-500 mb-6">ID buku dibuat otomatis oleh sistem.</p>

            <form method="POST" action="{{ route('admin.books.store') }}" class="space-y-4">
                @csrf
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Judul Buku</label>
                    <input type="text" name="judul" value="{{ old('judul') }}" maxlength="150" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pengarang</label>
                    <input type="text" name="pengarang" value="{{ old('pengarang') }}" maxlength="100" required
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori</label>
                    <input type="text" name="kategori" value="{{ old('kategori') }}" maxlength="50" required placeholder="Contoh: Fiksi, Teknologi, Sejarah"
                        class="w-full px-4 py-3 bg-slate-50 border border-slate-200 rounded-xl text-sm focus:outline-none focus:ring-2 focus:ring-blue-600">
                </div>
                <div class="pt-2 flex gap-3">
                    <button type="submit" class="px-5 py-3 bg-blue-600 text-white text-sm font-semibold rounded-xl hover:bg-blue-700">Simpan Buku</button>
                    <a href="{{ route('admin.books.index') }}" class="px-5 py-3 bg-slate-100 text-slate-600 text-sm font-semibold rounded-xl">Batal</a>
                </div>
            </form>
        </div>
    </div>
@endsection
