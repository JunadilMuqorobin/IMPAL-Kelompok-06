<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Member - BukuBook</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; }
    </style>
</head>
<body class="bg-slate-50 min-h-screen flex items-center justify-center p-4 md:p-6">

    <div class="w-full max-w-4xl bg-white rounded-3xl border border-slate-200 shadow-xl overflow-hidden flex flex-col md:flex-row min-h-[580px]">
        
        <!-- Sisi Kiri: Branding & Visual Banner -->
        <div class="md:w-1/2 bg-gradient-to-br from-blue-600 via-blue-700 to-indigo-800 p-8 md:p-12 text-white flex flex-col justify-between relative overflow-hidden">
            <div>
                <!-- Logo & Brand Link -->
                <a href="{{ route('landing') }}" class="inline-flex items-center gap-2">
                    <div class="bg-white text-blue-600 font-bold px-2.5 py-1 rounded-lg text-lg shadow-sm">BB</div>
                    <span class="text-2xl font-bold tracking-tight text-white">BukuBook</span>
                </a>

                <div class="mt-12 space-y-4">
                    <span class="px-3 py-1 text-xs font-semibold bg-white/20 rounded-full inline-block backdrop-blur-sm">Registrasi Keanggotaan</span>
                    <h2 class="text-3xl font-extrabold leading-tight">Bergabunglah Sekarang!</h2>
                    <p class="text-blue-100 text-sm leading-relaxed">
                        Buat akun anggota perpustakaan secara gratis dan mulai nikmati pengalaman membaca yang fleksibel.
                    </p>
                </div>
            </div>

            <!-- Info Card Kiri -->
            <div class="mt-8 p-4 bg-white/10 rounded-2xl backdrop-blur-md border border-white/10 text-xs space-y-2">
                <p class="font-semibold text-white">Status Awal Anggota:</p>
                <p class="text-blue-100">✓ Akun langsung aktif sebagai <strong>Member Basic</strong></p>
                <p class="text-blue-100">✓ Upgrade ke <strong>Premium</strong> kapan saja via Kasir POS</p>
            </div>
        </div>

        <!-- Sisi Kanan: Form Signup -->
        <div class="md:w-1/2 p-8 md:p-12 flex flex-col justify-between bg-white">
            <div>
                <div class="flex justify-between items-center mb-8">
                    <h3 class="text-2xl font-bold text-slate-900">Daftar Member</h3>
                    <a href="{{ route('landing') }}" class="text-xs font-medium text-slate-500 hover:text-blue-600 transition">← Beranda</a>
                </div>

                @if($errors->any())
                    <div class="mb-4 p-3.5 bg-rose-50 text-rose-600 text-sm rounded-xl border border-rose-200">
                        {{ $errors->first() }}
                    </div>
                @endif

                <form action="{{ route('signup') }}" method="POST" class="space-y-4">
                    @csrf
                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Lengkap</label>
                        <input type="text" name="nama" value="{{ old('nama') }}" placeholder="Masukkan nama lengkap" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Email Member</label>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="nama@email.com" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition text-sm">
                    </div>

                    <div>
                        <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kata Sandi</label>
                        <input type="password" name="kataSandi" placeholder="Minimal 6 karakter" required
                            class="w-full px-4 py-3 bg-slate-50 border border-slate-200 text-slate-800 placeholder-slate-400 rounded-xl focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white transition text-sm">
                    </div>

                    <div class="pt-2">
                        <button type="submit" 
                            class="w-full py-3.5 bg-blue-600 hover:bg-blue-700 text-white font-semibold rounded-xl shadow-md shadow-blue-200 transition duration-200 text-sm">
                            Buat Akun Member
                        </button>
                    </div>
                </form>
            </div>

            <div class="mt-8 text-center pt-4 border-t border-slate-100">
                <p class="text-xs text-slate-600">
                    Sudah memiliki akun? 
                    <a href="{{ route('login') }}" class="text-blue-600 font-semibold hover:underline">Masuk Akun</a>
                </p>
            </div>
        </div>

    </div>

</body>
</html>