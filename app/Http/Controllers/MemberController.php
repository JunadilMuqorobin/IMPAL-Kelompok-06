<?php

namespace App\Http\Controllers;

use App\Models\Buku;
use App\Models\Booking;
use App\Models\Peminjaman;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class MemberController extends Controller
{
    public function dashboard()
    {
        $member = Auth::guard('member')->user();

        // Ambil data peminjaman aktif member
        $peminjamanAktif = Peminjaman::with('buku')
            ->where('idMember', $member->idMember)
            ->where('statusPengembalian', 'Dipinjam')
            ->get();

        // Ambil data booking aktif member
        $bookingAktif = Booking::with('buku')
            ->where('idMember', $member->idMember)
            ->where('statusBooking', 'Aktif')
            ->get();

        // Ambil katalog buku untuk penjelajahan ringkas
        $katalogBuku = Buku::limit(6)->get();

        return view('member.dashboard', compact('member', 'peminjamanAktif', 'bookingAktif', 'katalogBuku'));
    }
}
