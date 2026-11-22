<?php

namespace App\Http\Controllers;

use App\Models\PembayaranDenda;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PembayaranDendaController extends Controller
{
    // Menampilkan daftar riwayat pembayaran denda
    public function index()
    {
        $pembayaran = DB::table('pembayaran_denda')
            ->join('peminjaman', 'pembayaran_denda.idPeminjaman', '=', 'peminjaman.idPeminjaman')
            ->join('member', 'peminjaman.idMember', '=', 'member.idMember')
            ->join('buku', 'peminjaman.idBuku', '=', 'buku.idBuku')
            ->join('admin', 'pembayaran_denda.idAdmin', '=', 'admin.idAdmin')
            ->select(
                'pembayaran_denda.*',
                'member.nama as namaMember',
                'buku.judul as judulBuku',
                'admin.nama as namaAdmin'
            )
            ->orderBy('pembayaran_denda.tanggalBayar', 'desc')
            ->get();

        return view('denda.index', compact('pembayaran'));
    }

    // Menampilkan form pembayaran denda untuk peminjaman yang terlambat
    public function create()
    {
        // Mengambil data peminjaman yang statusnya 'Terlambat'
        $peminjamanTerlambat = DB::table('peminjaman')
            ->join('member', 'peminjaman.idMember', '=', 'member.idMember')
            ->join('buku', 'peminjaman.idBuku', '=', 'buku.idBuku')
            ->where('peminjaman.statusPengembalian', 'Terlambat')
            ->select('peminjaman.*', 'member.nama as namaMember', 'buku.judul as judulBuku')
            ->get();

        return view('denda.create', compact('peminjamanTerlambat'));
    }

    // Menyimpan transaksi pembayaran denda & membuat struk otomatis
    public function store(Request $request)
    {
        $request->validate([
            'idPeminjaman' => 'required|exists:peminjaman,idPeminjaman',
            'jumlahBayar' => 'required|numeric|min:0',
            'metodePembayaran' => 'required|in:Tunai,QRIS,Transfer E-Wallet',
        ]);

        $peminjaman = DB::table('peminjaman')->where('idPeminjaman', $request->idPeminjaman)->first();

        // Hitung selisih hari keterlambatan (asumsi denda Rp 5.000 / hari sesuai data dump)
        $batasKembali = Carbon::parse($peminjaman->batasKembali);
        $tanggalKembali = $peminjaman->tanggalKembali ? Carbon::parse($peminjaman->tanggalKembali) : Carbon::now();
        
        $hariTerlambat = $tanggalKembali->greaterThan($batasKembali) 
            ? $batasKembali->diffInDays($tanggalKembali) 
            : 0;

        $tarifPerHari = 5000;
        $totalDenda = $hariTerlambat * $tarifPerHari;

        // Validasi kecukupan pembayaran
        if ($request->jumlahBayar < $totalDenda) {
            return back()->withErrors(['jumlahBayar' => 'Nominal bayar kurang dari total denda (Rp ' . number_format($totalDenda, 0, ',', '.') . ').'])->withInput();
        }

        $kembalian = $request->jumlahBayar - $totalDenda;

        // Generate ID Denda otomatis (DND01, DND02, dst.)
        $lastDenda = DB::table('pembayaran_denda')->orderBy('idDenda', 'desc')->first();
        $nextNumber = $lastDenda ? ((int) substr($lastDenda->idDenda, 3)) + 1 : 1;
        $idDenda = 'DND' . str_pad($nextNumber, 2, '0', STR_PAD_LEFT);

        // Ambil ID Admin yang sedang login (fallback ke ADM01 jika belum pakai guard admin)
        $idAdmin = auth()->guard('admin')->id() ?? 'ADM01';
        $waktuSekarang = Carbon::now();

        DB::transaction(function () use ($idDenda, $request, $idAdmin, $hariTerlambat, $totalDenda, $kembalian, $waktuSekarang) {
            // 1. Simpan ke tabel pembayaran_denda
            PembayaranDenda::create([
                'idDenda' => $idDenda,
                'idPeminjaman' => $request->idPeminjaman,
                'idAdmin' => $idAdmin,
                'jumlahHariTerlambat' => $hariTerlambat,
                'totalDenda' => $totalDenda,
                'jumlahBayar' => $request->jumlahBayar,
                'kembalian' => $kembalian,
                'metodePembayaran' => $request->metodePembayaran,
                'tanggalBayar' => $waktuSekarang,
            ]);

            // 2. Update status peminjaman menjadi 'Dikembalikan'
            DB::table('peminjaman')
                ->where('idPeminjaman', $request->idPeminjaman)
                ->update([
                    'statusPengembalian' => 'Dikembalikan',
                    'tanggalKembali' => $waktuSekarang->toDateString(),
                ]);

            // 3. Buat catatan di tabel struk
            $lastStruk = DB::table('struk')->orderBy('idStruk', 'desc')->first();
            $nextStrukNum = $lastStruk ? ((int) substr($lastStruk->idStruk, 1)) + 1 : 1;
            $idStruk = 'S' . str_pad($nextStrukNum, 3, '0', STR_PAD_LEFT);
            $noReferensi = 'REF-DENDA-' . str_pad($nextStrukNum, 3, '0', STR_PAD_LEFT);

            DB::table('struk')->insert([
                'idStruk' => $idStruk,
                'noReferensi' => $noReferensi,
                'jenisTransaksi' => 'Denda',
                'idDenda' => $idDenda,
                'idTransaksiPremium' => null,
                'tanggalCetak' => $waktuSekarang,
                'rincianTransaksi' => "Pembayaran Denda Keterlambatan {$hariTerlambat} Hari",
                'totalBiaya' => $totalDenda,
            ]);
        });

        return redirect()->route('denda.index')->with('success', 'Pembayaran denda berhasil dicatat dan struk diterbitkan.');
    }

    // Menampilkan detail pembayaran & struk
    public function show($idDenda)
    {
        $detail = DB::table('pembayaran_denda')
            ->join('peminjaman', 'pembayaran_denda.idPeminjaman', '=', 'peminjaman.idPeminjaman')
            ->join('member', 'peminjaman.idMember', '=', 'member.idMember')
            ->join('buku', 'peminjaman.idBuku', '=', 'buku.idBuku')
            ->join('admin', 'pembayaran_denda.idAdmin', '=', 'admin.idAdmin')
            ->leftJoin('struk', 'pembayaran_denda.idDenda', '=', 'struk.idDenda')
            ->where('pembayaran_denda.idDenda', $idDenda)
            ->select(
                'pembayaran_denda.*',
                'member.nama as namaMember',
                'member.email as emailMember',
                'buku.judul as judulBuku',
                'admin.nama as namaAdmin',
                'struk.noReferensi',
                'struk.tanggalCetak',
                'struk.rincianTransaksi'
            )
            ->first();

        if (!$detail) {
            abort(404);
        }

        return view('denda.show', compact('detail'));
    }
}