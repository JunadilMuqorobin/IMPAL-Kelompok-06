<?php

namespace App\Http\Controllers;

use App\Models\Member;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // 1. AUTENTIKASI MEMBER

    public function showMemberLoginForm()
    {
        return view('auth.member-login');
    }

    public function loginMember(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'kataSandi' => 'required',
        ]);

        $attemptData = [
            'email' => $credentials['email'],
            'password' => $credentials['kataSandi'],
        ];

        if (Auth::guard('member')->attempt($attemptData, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/member/dashboard')->with('success', 'Selamat datang kembali!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi yang dimasukkan salah.',
        ])->onlyInput('email');
    }

    public function showSignupForm()
    {
        return view('auth.member-signup');
    }

    public function registerMember(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:100',
            'email' => 'required|email|unique:member,email',
            'kataSandi' => 'required|min:6',
        ], [
            'email.unique' => 'Email ini sudah terdaftar pada sistem.',
            'kataSandi.min' => 'Kata sandi minimal 6 karakter.',
        ]);

        // Auto-generate idMember (Contoh: M001, M002, dst)
        $lastMember = Member::orderBy('idMember', 'desc')->first();
        if ($lastMember && preg_match('/M(\d+)/', $lastMember->idMember, $matches)) {
            $nextNum = intval($matches[1]) + 1;
            $idMember = 'M' . str_pad($nextNum, 3, '0', STR_PAD_LEFT);
        } else {
            $idMember = 'M001';
        }

        // Simpan data ke database db_bukubook
        Member::create([
            'idMember' => $idMember,
            'nama' => $request->nama,
            'email' => $request->email,
            'kataSandi' => Hash::make($request->kataSandi),
            'tipeMember' => 'Basic',
            'maxPinjam' => 1,
            'durasiPinjamHari' => 7,
            'isPremium' => false,
        ]);

        return redirect()->route('login')->with('success', 'Pendaftaran berhasil! Silakan login dengan akun Anda.');
    }


    // 2. AUTENTIKASI ADMIN

    public function showAdminLoginForm()
    {
        return view('auth.admin-login');
    }

    public function loginAdmin(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'kataSandi' => 'required',
        ]);

        $attemptData = [
            'email' => $credentials['email'],
            'password' => $credentials['kataSandi'],
        ];

        if (Auth::guard('admin')->attempt($attemptData, $request->boolean('remember'))) {
            $request->session()->regenerate();
            return redirect()->intended('/admin/dashboard')->with('success', 'Selamat bertugas, Admin!');
        }

        return back()->withErrors([
            'email' => 'Email atau kata sandi Admin tidak valid.',
        ])->onlyInput('email');
    }


    // 3. LOGOUT SYSTEM

    public function logout(Request $request)
    {
        if (Auth::guard('member')->check()) {
            Auth::guard('member')->logout();
        } elseif (Auth::guard('admin')->check()) {
            Auth::guard('admin')->logout();
        }

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda telah berhasil keluar dari sistem.');
    }
}
