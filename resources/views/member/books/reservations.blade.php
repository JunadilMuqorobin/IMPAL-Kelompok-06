@extends('layouts.app')

@section('title', 'Reservasi Saya')

@section('content')
    <div>
        <h1 class="text-2xl font-extrabold text-slate-900">Reservasi Saya</h1>
        <p class="text-sm text-slate-500 mt-1">Pantau antrean buku yang Anda pesan. Batas pengambilan {{ config('booking.pickup_days', 2) }} hari setelah buku dikembalikan.</p>
    </div>

    <div class="space-y-4">
        @forelse($bookings as $booking)
            <div class="bg-white rounded-2xl border border-slate-200 p-5 sm:p-6 flex flex-wrap gap-4 justify-between items-center">
                <div class="space-y-1">
                    <div class="flex flex-wrap items-center gap-2">
                        <a href="{{ route('member.books.show', $booking->idBuku) }}" class="font-bold text-slate-900 hover:text-blue-600">{{ $booking->buku->judul }}</a>
                        <span class="px-2.5 py-0.5 text-[10px] font-bold rounded-full
                            {{ $booking->statusBooking === 'Aktif' ? 'bg-amber-100 text-amber-700' : ($booking->statusBooking === 'Selesai' ? 'bg-emerald-100 text-emerald-700' : 'bg-slate-100 text-slate-500') }}">
                            {{ $booking->statusBooking }}
                        </span>
                    </div>
                    <p class="text-xs text-slate-500">Waktu antrean: {{ $booking->waktuBooking }} · Status buku: <strong>{{ $booking->buku->status }}</strong></p>
                    @if($booking->statusBooking === 'Aktif')
                        <p class="text-xs font-semibold text-amber-600">Posisi antrean: #{{ $booking->posisiAntrian }}</p>
                    @endif
                </div>

                @if($booking->statusBooking === 'Aktif')
                    <div class="flex flex-wrap gap-2">
                        @if($booking->posisiAntrian == 1 && $booking->buku->status === 'Dibooking')
                            <form method="POST" action="{{ route('member.bookings.claim', $booking->idBooking) }}">
                                @csrf
                                <button type="submit" class="px-4 py-2 bg-amber-600 text-white text-xs font-bold rounded-xl hover:bg-amber-700">Ambil Buku</button>
                            </form>
                        @endif
                        <form method="POST" action="{{ route('member.bookings.cancel', $booking->idBooking) }}">
                            @csrf
                            <button type="submit" class="px-4 py-2 bg-rose-50 text-rose-600 text-xs font-bold rounded-xl hover:bg-rose-100">Batalkan</button>
                        </form>
                    </div>
                @endif
            </div>
        @empty
            <div class="py-16 text-center text-slate-400 bg-white rounded-2xl border border-dashed border-slate-200">
                Anda belum memiliki reservasi.
                <a href="{{ route('member.books.index') }}" class="block mt-2 text-blue-600 font-semibold">Jelajahi katalog buku</a>
            </div>
        @endforelse
    </div>
@endsection
