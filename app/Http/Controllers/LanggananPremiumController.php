<?php

namespace App\Http\Controllers;

use App\Models\Admin;
use App\Models\Diskon;
use App\Models\Member;
use App\Models\Struk;
use App\Models\TransaksiPremium;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class LanggananPremiumController extends Controller
{
    /**
     * Daftar paket resmi (harga dikunci di server agar aman dari manipulasi frontend)
     */
    private array $daftarPaket = [
        '1_bulan' => [
            'nama'   => 'Premium 1 Bulan',
            'harga'  => 20000,
            'durasi' => 30,
        ],
        '6_bulan' => [
            'nama'   => 'Premium 6 Bulan (Hemat 16.6%)',
            'harga'  => 100000,
            'durasi' => 180,
        ]
    ];

    /**
     * Menampilkan halaman upgrade langganan di sisi Member
     */
    public function index()
    {
        // Ambil member yang sedang login, atau fallback ke M001 saat pengujian langsung tanpa login
        $member = Auth::guard('member')->user() ?? Member::where('idMember', 'M001')->first();

        $riwayatMember = $member
            ? TransaksiPremium::with('diskon')
            ->where('idMember', $member->idMember)
            ->orderBy('tanggalTransaksi', 'desc')
            ->get()
            : collect();

        return view('member.upgrade-premium', [
            'member'        => $member,
            'daftarPaket'   => $this->daftarPaket,
            'riwayatMember' => $riwayatMember,
        ]);
    }

    /**
     * Memproses checkout langganan cashless & auto-upgrade ke Premium
     */
    public function store(Request $request)
    {
        // 1. Validasi Input Form Sisi User (Menangani TC-PRM-01, 02, 04, 06, 07)
        $validated = $request->validate([
            'paketLangganan'   => ['required', 'string', 'in:1_bulan,6_bulan'],
            'kodeVoucher'      => ['nullable', 'string', 'exists:diskon,kodeVoucher'],
            'metodePembayaran' => ['required', 'string', 'in:QRIS,Virtual Account,Transfer E-Wallet'],
            'nomorAkun'        => ['required', 'digits_between:10,14'],
            'syaratKetentuan'  => ['accepted'],
        ], [
            'paketLangganan.required'   => 'Silakan pilih paket langganan terlebih dahulu.',
            'paketLangganan.in'         => 'Paket langganan yang dipilih tidak valid.',
            'kodeVoucher.exists'        => 'Kode voucher tidak valid atau tidak ditemukan.',
            'metodePembayaran.required' => 'Pilih metode pembayaran cashless yang tersedia.',
            'metodePembayaran.in'       => 'Metode pembayaran tidak valid (Hanya mendukung QRIS, Virtual Account, atau Transfer E-Wallet).',
            'nomorAkun.required'        => 'Nomor HP / E-Wallet wajib diisi untuk verifikasi Payment Gateway.',
            'nomorAkun.digits_between'  => 'Nomor HP / E-Wallet harus berupa angka 10 hingga 14 digit.',
            'syaratKetentuan.accepted'  => 'Anda wajib menyetujui Syarat & Ketentuan berlangganan.',
        ]);

        // Ambil data member aktif
        $member = Auth::guard('member')->user() ?? Member::where('idMember', 'M001')->first();

        if (!$member) {
            return back()->withErrors(['akun' => 'Sesi member tidak ditemukan. Silakan login terlebih dahulu.']);
        }

        // 2. Validasi Aturan Bisnis: Cek apakah sudah Premium (Menangani TC-PRM-03)
        if ($member->isPremium || $member->tipeMember === 'Premium') {
            return back()
                ->withInput()
                ->withErrors([
                    'akun' => 'Akun Anda saat ini sudah berstatus Member Premium.',
                ]);
        }

        // Ambil detail harga & durasi paket dari server
        $paketTerpilih = $this->daftarPaket[$validated['paketLangganan']];
        $biayaLangganan = (float) $paketTerpilih['harga'];
        $durasiHari = (int) $paketTerpilih['durasi'];

        // 3. Validasi Voucher & Masa Berlaku (Menangani TC-PRM-05)
        $diskon = null;
        $nominalDiskon = 0;

        if (!empty($validated['kodeVoucher'])) {
            $diskon = Diskon::where('kodeVoucher', $validated['kodeVoucher'])->first();

            if (Carbon::parse($diskon->tanggalKadaluarsa)->lt(Carbon::today())) {
                return back()
                    ->withInput()
                    ->withErrors([
                        'kodeVoucher' => 'Kode voucher sudah melewati batas masa berlaku.',
                    ]);
            }

            if ($diskon->persenDiskon > 0) {
                $nominalDiskon = ($diskon->persenDiskon / 100) * $biayaLangganan;
            } else {
                $nominalDiskon = (float) $diskon->nominalPotongan;
            }
        }

        $totalBayar = max(0, $biayaLangganan - $nominalDiskon);

        // 4. Hook Simulasi Payment Gateway (Nanti diganti API Midtrans / Xendit)
        $gatewayResponse = $this->prosesSimulasiPaymentGateway(
            $validated['metodePembayaran'],
            $validated['nomorAkun'],
            $totalBayar
        );

        if (!$gatewayResponse['status']) {
            return back()
                ->withInput()
                ->withErrors(['payment_gateway' => 'Pembayaran gagal diproses oleh Payment Gateway.']);
        }

        // ID Admin sistem sebagai handler otomatis agar memenuhi Foreign Key db_bukubook
        $systemAdminId = Admin::value('idAdmin') ?? 'ADM001';

        // 5. Simpan Transaksi, Upgrade Member ke Premium, & Terbitkan Struk Otomatis
        DB::beginTransaction();
        try {
            $countTrx = TransaksiPremium::count() + 1;
            $idTransaksi = 'TP-' . str_pad($countTrx, 4, '0', STR_PAD_LEFT);

            $waktuSekarang = Carbon::now();
            $masaBerlaku = Carbon::today()->addDays($durasiHari);

            $transaksi = TransaksiPremium::create([
                'idTransaksiPremium' => $idTransaksi,
                'idMember'           => $member->idMember,
                'idAdmin'            => $systemAdminId,
                'idDiskon'           => $diskon ? $diskon->idDiskon : null,
                'biayaLangganan'     => $biayaLangganan,
                'nominalDiskon'      => $nominalDiskon,
                'totalBayar'         => $totalBayar,
                'metodePembayaran'   => $validated['metodePembayaran'] . ' (Auto-PG)',
                'tanggalTransaksi'   => $waktuSekarang,
                'masaBerlakuHingga'  => $masaBerlaku,
            ]);

            // Otomatis ubah status member menjadi Premium tanpa perlu approval Admin
            $member->update([
                'tipeMember'       => 'Premium',
                'isPremium'        => true,
                'maxPinjam'        => 5,
                'durasiPinjamHari' => 30,
            ]);

            // Terbitkan bukti pembayaran digital (Tabel Struk)
            $countStruk = Struk::count() + 1;
            $idStruk = 'STR-' . str_pad($countStruk, 4, '0', STR_PAD_LEFT);
            $noReferensi = $gatewayResponse['reference_id'];

            $rincian = "{$paketTerpilih['nama']} - {$member->nama} ({$member->idMember}) | "
                . "Via {$validated['metodePembayaran']} ({$validated['nomorAkun']}) | "
                . "Diskon: Rp " . number_format($nominalDiskon, 0, ',', '.');

            $struk = Struk::create([
                'idStruk'            => $idStruk,
                'noReferensi'        => $noReferensi,
                'jenisTransaksi'     => 'Premium',
                'idDenda'            => null,
                'idTransaksiPremium' => $transaksi->idTransaksiPremium,
                'tanggalCetak'       => $waktuSekarang,
                'rincianTransaksi'   => $rincian,
                'totalBiaya'         => $totalBayar,
            ]);

            DB::commit();

            return redirect()
                ->route('member.premium.index')
                ->with('success', 'Pembayaran cashless berhasil! Akun Anda kini otomatis aktif sebagai Member Premium.')
                ->with('struk_digital', $struk->toArray());
        } catch (\Exception $e) {
            DB::rollBack();
            return back()
                ->withInput()
                ->withErrors(['system' => 'Gagal memproses transaksi: ' . $e->getMessage()]);
        }
    }

    /**
     * Method integrasi Payment Gateway (siap disambungkan ke Midtrans / Xendit)
     */
    private function prosesSimulasiPaymentGateway(string $metode, string $nomorAkun, float $amount): array
    {
        $prefix = match ($metode) {
            'QRIS'              => 'PG-QRIS-',
            'Virtual Account'   => 'PG-VA-',
            'Transfer E-Wallet' => 'PG-EWLT-',
            default             => 'PG-TRX-',
        };

        return [
            'status'       => true,
            'reference_id' => $prefix . Carbon::now()->format('YmdHis'),
            'paid_amount'  => $amount,
            'account'      => $nomorAkun,
        ];
    }

    /**
     * Halaman Monitoring Admin (Read-Only: Admin hanya memantau, tanpa konfirmasi manual)
     */
    public function adminMonitoring()
    {
        $semuaTransaksi = TransaksiPremium::with(['member', 'diskon'])
            ->orderBy('tanggalTransaksi', 'desc')
            ->get();

        $totalPendapatan = $semuaTransaksi->sum('totalBayar');
        $totalMemberPremium = Member::where('isPremium', true)->count();

        return view('admin.monitoring-premium', compact(
            'semuaTransaksi',
            'totalPendapatan',
            'totalMemberPremium'
        ));
    }
}
