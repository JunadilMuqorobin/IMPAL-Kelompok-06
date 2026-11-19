<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Buku;
use App\Models\Member;
use App\Models\Peminjaman;
use App\Models\Ulasan;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BukuController extends Controller
{
    /* ============================================================
     | ADMIN
     ============================================================ */

    public function adminIndex(Request $request)
    {
        $buku = $this->filterBuku($request, false)->orderBy('idBuku')->get();
        $kategoris = Buku::query()->distinct()->orderBy('kategori')->pluck('kategori');

        return view('admin.books.index', compact('buku', 'kategoris'));
    }

    public function adminCreate()
    {
        return view('admin.books.create');
    }

    public function adminStore(Request $request)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'pengarang' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
        ]);

        return $this->handle(function () use ($data) {
            DB::transaction(function () use ($data) {
                Buku::create([
                    'idBuku' => $this->generateId('buku', 'idBuku', 1, 'B'),
                    'judul' => $data['judul'],
                    'pengarang' => $data['pengarang'],
                    'kategori' => $data['kategori'],
                    'status' => 'Tersedia',
                    'rating' => '0.0',
                ]);
            });
        }, 'Buku baru berhasil ditambahkan.', 'admin.books.index');
    }

    public function adminShow(string $id)
    {
        $buku = Buku::with(['peminjamans.member', 'bookings.member', 'ulasans.member'])->findOrFail($id);
        $ulasans = $buku->ulasans->sortByDesc('tanggalUlasan');
        $average = $this->averageRating($ulasans);

        return view('admin.books.show', compact('buku', 'ulasans', 'average'));
    }

    public function adminEdit(string $id)
    {
        $buku = Buku::findOrFail($id);

        return view('admin.books.edit', compact('buku'));
    }

    public function adminUpdate(Request $request, string $id)
    {
        $data = $request->validate([
            'judul' => 'required|string|max:150',
            'pengarang' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
        ]);

        $buku = Buku::findOrFail($id);
        $buku->update($data);

        return redirect()->route('admin.books.index')->with('success', 'Data buku berhasil diperbarui.');
    }

    public function adminDestroy(string $id)
    {
        return $this->handle(function () use ($id) {
            DB::transaction(function () use ($id) {
                $buku = Buku::where('idBuku', $id)->lockForUpdate()->firstOrFail();

                if (Peminjaman::where('idBuku', $id)->where('statusPengembalian', 'Dipinjam')->exists()) {
                    $this->reject('Buku tidak dapat dihapus karena sedang dipinjam.');
                }

                if ($this->activeBookings($id)->exists()) {
                    $this->reject('Buku tidak dapat dihapus karena masih memiliki reservasi aktif.');
                }

                $buku->delete();
            });
        }, 'Buku berhasil dihapus.', 'admin.books.index');
    }

    public function updateStatus(Request $request, string $id)
    {
        $request->validate([
            'status' => 'required|in:Tersedia,Dipinjam,Dibooking',
        ]);

        return $this->handle(function () use ($id) {
            DB::transaction(function () use ($id) {
                Buku::where('idBuku', $id)->lockForUpdate()->firstOrFail();
                $this->syncStatus($id);
            });
        }, 'Status buku berhasil disesuaikan dengan data transaksi.', 'admin.books.index');
    }

    public function returnBook(string $id)
    {
        return $this->handle(function () use ($id) {
            DB::transaction(function () use ($id) {
                Buku::where('idBuku', $id)->lockForUpdate()->firstOrFail();

                $loan = Peminjaman::where('idBuku', $id)
                    ->where('statusPengembalian', 'Dipinjam')
                    ->orderBy('tanggalPinjam')
                    ->lockForUpdate()
                    ->first();

                if (! $loan) {
                    $this->reject('Tidak ada peminjaman aktif pada buku ini.');
                }

                $loan->update([
                    'statusPengembalian' => 'Dikembalikan',
                    'tanggalKembali' => Carbon::today()->toDateString(),
                ]);

                $this->expireStaleReservations($id);
                $this->renumberQueue($id);
                $this->syncStatus($id);
            });
        }, 'Buku berhasil dikembalikan.', 'admin.books.index');
    }

    public function statistics()
    {
        $total = Buku::count();
        $available = Buku::where('status', 'Tersedia')->count();
        $borrowed = Buku::where('status', 'Dipinjam')->count();
        $booked = Buku::where('status', 'Dibooking')->count();
        $average = number_format((float) (Ulasan::avg('nilaiRating') ?? 0), 1, '.', '');

        $kategori = Buku::select('kategori', DB::raw('COUNT(*) as total'))
            ->groupBy('kategori')
            ->orderByDesc('total')
            ->get();

        return view('admin.books.statistics', compact(
            'total', 'available', 'borrowed', 'booked', 'average', 'kategori'
        ));
    }

    /* ============================================================
     | MEMBER
     ============================================================ */

    public function index(Request $request)
    {
        $buku = $this->filterBuku($request, true)->orderBy('judul')->get();
        $kategoris = Buku::query()->distinct()->orderBy('kategori')->pluck('kategori');

        return view('member.books.index', compact('buku', 'kategoris'));
    }

    public function show(string $id)
    {
        $this->expireForBook($id);

        $buku = Buku::with('ulasans.member')->findOrFail($id);
        $ulasans = $buku->ulasans->sortByDesc('tanggalUlasan');
        $average = $this->averageRating($ulasans);

        $member = Auth::guard('member')->user();
        $sudahPinjam = Peminjaman::where('idMember', $member->idMember)->where('idBuku', $id)->exists();
        $sudahUlas = Ulasan::where('idMember', $member->idMember)->where('idBuku', $id)->exists();

        $myBooking = Booking::where('idMember', $member->idMember)
            ->where('idBuku', $id)
            ->where('statusBooking', 'Aktif')
            ->first();

        $firstBooking = $this->activeBookings($id)->orderBy('posisiAntrian')->first();
        $deadline = $this->pickupDeadline($id);

        return view('member.books.show', compact(
            'buku', 'ulasans', 'average', 'sudahPinjam', 'sudahUlas',
            'myBooking', 'firstBooking', 'deadline'
        ));
    }

    public function borrow(string $id)
    {
        return $this->handle(function () use ($id) {
            DB::transaction(function () use ($id) {
                $buku = Buku::where('idBuku', $id)->lockForUpdate()->firstOrFail();
                $member = $this->lockedMember();

                $this->expireStaleReservations($id);

                if ($buku->fresh()->status !== 'Tersedia') {
                    $this->reject('Buku ini belum tersedia untuk dipinjam.');
                }

                $aktif = Peminjaman::where('idMember', $member->idMember)
                    ->where('statusPengembalian', 'Dipinjam')
                    ->count();

                if ($aktif >= $member->maxPinjam) {
                    $this->reject('Kuota peminjaman Anda sudah penuh ('.$member->maxPinjam.' buku).');
                }

                $duplikat = Peminjaman::where('idMember', $member->idMember)
                    ->where('idBuku', $id)
                    ->where('statusPengembalian', 'Dipinjam')
                    ->exists();

                if ($duplikat) {
                    $this->reject('Anda sedang meminjam buku yang sama.');
                }

                Peminjaman::create([
                    'idPeminjaman' => $this->generateId('peminjaman', 'idPeminjaman', 1, 'P'),
                    'idMember' => $member->idMember,
                    'idBuku' => $id,
                    'tanggalPinjam' => Carbon::today()->toDateString(),
                    'batasKembali' => Carbon::today()->addDays((int) $member->durasiPinjamHari)->toDateString(),
                    'tanggalKembali' => null,
                    'statusPengembalian' => 'Dipinjam',
                ]);

                $this->syncStatus($id);
            });
        }, 'Buku berhasil dipinjam. Jangan lupa dikembalikan tepat waktu.', 'member.books.show', [$id]);
    }

    public function reserve(string $id)
    {
        return $this->handle(function () use ($id) {
            DB::transaction(function () use ($id) {
                $buku = Buku::where('idBuku', $id)->lockForUpdate()->firstOrFail();
                $member = $this->lockedMember();

                $this->expireStaleReservations($id);

                if (! $member->isPremium && $member->tipeMember !== 'Premium') {
                    $this->reject('Fitur reservasi hanya tersedia untuk member Premium.');
                }

                if ($buku->fresh()->status !== 'Dipinjam') {
                    $this->reject('Reservasi hanya dapat dilakukan pada buku yang sedang dipinjam.');
                }

                $duplikat = $this->activeBookings($id)
                    ->where('idMember', $member->idMember)
                    ->exists();

                if ($duplikat) {
                    $this->reject('Anda sudah memiliki reservasi aktif untuk buku ini.');
                }

                $posisi = $this->activeBookings($id)->count() + 1;

                Booking::create([
                    'idBooking' => $this->generateId('booking', 'idBooking', 2, 'BK'),
                    'idMember' => $member->idMember,
                    'idBuku' => $id,
                    'waktuBooking' => Carbon::now()->format('Y-m-d H:i:s'),
                    'posisiAntrian' => $posisi,
                    'statusBooking' => 'Aktif',
                ]);

                $this->syncStatus($id);
            });
        }, 'Reservasi berhasil dibuat. Status buku tetap Dipinjam hingga dikembalikan.', 'member.books.show', [$id]);
    }

    public function cancelReservation(string $booking)
    {
        return $this->handle(function () use ($booking) {
            DB::transaction(function () use ($booking) {
                $reservasi = Booking::where('idBooking', $booking)->lockForUpdate()->firstOrFail();

                if ($reservasi->idMember !== Auth::guard('member')->user()->idMember) {
                    $this->reject('Reservasi ini bukan milik Anda.');
                }

                if ($reservasi->statusBooking !== 'Aktif') {
                    $this->reject('Reservasi ini sudah tidak aktif.');
                }

                $idBuku = $reservasi->idBuku;
                Buku::where('idBuku', $idBuku)->lockForUpdate()->firstOrFail();

                $reservasi->update(['statusBooking' => 'Dibatalkan']);
                $this->renumberQueue($idBuku);
                $this->syncStatus($idBuku);
            });
        }, 'Reservasi berhasil dibatalkan.', 'member.books.reservations');
    }

    public function claimReservation(string $booking)
    {
        return $this->handle(function () use ($booking) {
            DB::transaction(function () use ($booking) {
                $reservasi = Booking::where('idBooking', $booking)->lockForUpdate()->firstOrFail();
                $idBuku = $reservasi->idBuku;
                $buku = Buku::where('idBuku', $idBuku)->lockForUpdate()->firstOrFail();
                $member = $this->lockedMember();

                $this->expireStaleReservations($idBuku);

                if ($reservasi->idMember !== $member->idMember) {
                    $this->reject('Reservasi ini bukan milik Anda.');
                }

                if ($reservasi->statusBooking !== 'Aktif') {
                    $this->reject('Reservasi ini sudah tidak aktif.');
                }

                if ($buku->fresh()->status !== 'Dibooking') {
                    $this->reject('Buku belum siap diambil.');
                }

                $first = $this->activeBookings($idBuku)->orderBy('posisiAntrian')->first();

                if (! $first || $first->idBooking !== $reservasi->idBooking) {
                    $this->reject('Baru giliran Anda yang dapat mengambil buku ini.');
                }

                $aktif = Peminjaman::where('idMember', $member->idMember)
                    ->where('statusPengembalian', 'Dipinjam')
                    ->count();

                if ($aktif >= $member->maxPinjam) {
                    $this->reject('Kuota peminjaman Anda sudah penuh.');
                }

                Peminjaman::create([
                    'idPeminjaman' => $this->generateId('peminjaman', 'idPeminjaman', 1, 'P'),
                    'idMember' => $member->idMember,
                    'idBuku' => $idBuku,
                    'tanggalPinjam' => Carbon::today()->toDateString(),
                    'batasKembali' => Carbon::today()->addDays((int) $member->durasiPinjamHari)->toDateString(),
                    'tanggalKembali' => null,
                    'statusPengembalian' => 'Dipinjam',
                ]);

                $reservasi->update(['statusBooking' => 'Selesai']);
                $this->renumberQueue($idBuku);
                $this->syncStatus($idBuku);
            });
        }, 'Buku berhasil diambil. Status buku kembali Dipinjam.', 'member.books.borrowed');
    }

    public function myBorrowed()
    {
        $idMember = Auth::guard('member')->user()->idMember;

        $peminjamans = Peminjaman::with('buku')
            ->where('idMember', $idMember)
            ->orderByDesc('tanggalPinjam')
            ->get();

        return view('member.books.borrowed', compact('peminjamans'));
    }

    public function myReservations()
    {
        $idMember = Auth::guard('member')->user()->idMember;

        Booking::where('idMember', $idMember)
            ->where('statusBooking', 'Aktif')
            ->pluck('idBuku')
            ->unique()
            ->each(function ($idBuku) {
                $this->expireForBook($idBuku);
            });

        $bookings = Booking::with('buku')
            ->where('idMember', $idMember)
            ->orderByRaw("FIELD(statusBooking, 'Aktif', 'Selesai', 'Dibatalkan')")
            ->orderBy('posisiAntrian')
            ->get();

        return view('member.books.reservations', compact('bookings'));
    }

    public function storeReview(Request $request, string $id)
    {
        $data = $request->validate([
            'nilaiRating' => 'required|integer|min:1|max:5',
            'komentar' => 'nullable|string|max:1000',
        ]);

        return $this->handle(function () use ($data, $id) {
            DB::transaction(function () use ($data, $id) {
                Buku::where('idBuku', $id)->lockForUpdate()->firstOrFail();
                $idMember = Auth::guard('member')->user()->idMember;

                $pernahPinjam = Peminjaman::where('idMember', $idMember)->where('idBuku', $id)->exists();

                if (! $pernahPinjam) {
                    $this->reject('Anda hanya dapat mengulas buku yang pernah dipinjam.');
                }

                $ulasan = Ulasan::where('idMember', $idMember)->where('idBuku', $id)->first();

                if ($ulasan) {
                    $ulasan->update([
                        'nilaiRating' => $data['nilaiRating'],
                        'komentar' => $data['komentar'],
                        'tanggalUlasan' => Carbon::today()->toDateString(),
                    ]);
                } else {
                    Ulasan::create([
                        'idUlasan' => $this->generateId('ulasan', 'idUlasan', 1, 'U'),
                        'idMember' => $idMember,
                        'idBuku' => $id,
                        'nilaiRating' => $data['nilaiRating'],
                        'komentar' => $data['komentar'],
                        'tanggalUlasan' => Carbon::today()->toDateString(),
                    ]);
                }

                $rata = (float) (Ulasan::where('idBuku', $id)->avg('nilaiRating') ?? 0);
                Buku::where('idBuku', $id)->update([
                    'rating' => number_format($rata, 1, '.', ''),
                ]);
            });
        }, 'Ulasan Anda berhasil disimpan.', 'member.books.show', [$id]);
    }

    public function reviewHistory()
    {
        $ulasans = Ulasan::with('buku')
            ->where('idMember', Auth::guard('member')->user()->idMember)
            ->orderByDesc('tanggalUlasan')
            ->get();

        return view('member.books.review-history', compact('ulasans'));
    }

    /* ============================================================
     | HELPERS
     ============================================================ */

    private function filterBuku(Request $request, bool $denganRating): Builder
    {
        $query = Buku::query();

        if ($request->filled('q')) {
            $q = $request->input('q');
            $query->where(function (Builder $builder) use ($q) {
                $builder->where('judul', 'like', "%{$q}%")
                    ->orWhere('pengarang', 'like', "%{$q}%");
            });
        }

        if ($request->filled('kategori')) {
            $query->where('kategori', $request->input('kategori'));
        }

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }

        if ($denganRating && $request->filled('rating')) {
            $query->whereRaw('CAST(rating AS DECIMAL(4,1)) >= ?', [(float) $request->input('rating')]);
        }

        return $query;
    }

    private function activeBookings(string $idBuku): Builder
    {
        return Booking::where('idBuku', $idBuku)->where('statusBooking', 'Aktif');
    }

    private function lockedMember(): Member
    {
        return Member::where('idMember', Auth::guard('member')->user()->idMember)
            ->lockForUpdate()
            ->firstOrFail();
    }

    private function syncStatus(string $idBuku): void
    {
        $adaPinjaman = Peminjaman::where('idBuku', $idBuku)
            ->where('statusPengembalian', 'Dipinjam')
            ->exists();

        $adaBooking = $this->activeBookings($idBuku)->exists();

        $status = $adaPinjaman ? 'Dipinjam' : ($adaBooking ? 'Dibooking' : 'Tersedia');

        Buku::where('idBuku', $idBuku)->update(['status' => $status]);
    }

    private function renumberQueue(string $idBuku): void
    {
        $antrian = $this->activeBookings($idBuku)
            ->orderBy('posisiAntrian')
            ->orderBy('waktuBooking')
            ->get();

        foreach ($antrian as $index => $booking) {
            $posisi = $index + 1;
            $perubahan = [];

            if ((int) $booking->posisiAntrian !== $posisi) {
                $perubahan['posisiAntrian'] = $posisi;
            }

            if ($posisi === 1) {
                $perubahan['waktuBooking'] = Carbon::now()->format('Y-m-d H:i:s');
            }

            if ($perubahan) {
                $booking->update($perubahan);
            }
        }
    }

    private function pickupDeadline(string $idBuku): ?Carbon
    {
        $pinjamanAktif = Peminjaman::where('idBuku', $idBuku)
            ->where('statusPengembalian', 'Dipinjam')
            ->exists();

        if ($pinjamanAktif) {
            return null;
        }

        $first = $this->activeBookings($idBuku)->orderBy('posisiAntrian')->first();

        if (! $first) {
            return null;
        }

        return Carbon::parse($first->waktuBooking)
            ->addDays((int) config('booking.pickup_days', 2))
            ->endOfDay();
    }

    private function expireStaleReservations(string $idBuku): void
    {
        $adaYangKedaluwarsa = false;

        while ($first = $this->activeBookings($idBuku)->orderBy('posisiAntrian')->first()) {
            $deadline = $this->pickupDeadline($idBuku);

            if (! $deadline || Carbon::now()->lessThanOrEqualTo($deadline)) {
                break;
            }

            $first->update(['statusBooking' => 'Dibatalkan']);
            $this->renumberQueue($idBuku);
            $adaYangKedaluwarsa = true;
        }

        if ($adaYangKedaluwarsa) {
            $this->syncStatus($idBuku);
        }
    }

    private function expireForBook(string $idBuku): void
    {
        DB::transaction(function () use ($idBuku) {
            if (Buku::where('idBuku', $idBuku)->lockForUpdate()->exists()) {
                $this->expireStaleReservations($idBuku);
            }
        });
    }

    private function averageRating(Collection $ulasan): string
    {
        $ratings = $ulasan->pluck('nilaiRating')->filter(fn ($nilai) => ! is_null($nilai));

        return $ratings->isEmpty()
            ? '0.0'
            : number_format((float) $ratings->avg(), 1, '.', '');
    }

    private function generateId(string $tabel, string $kolom, int $panjangPrefix, string $prefix): string
    {
        $offset = $panjangPrefix + 1;
        $terakhir = DB::table($tabel)
            ->orderByRaw("CAST(SUBSTRING({$kolom}, {$offset}) AS UNSIGNED) DESC")
            ->lockForUpdate()
            ->value($kolom);

        $nomor = ($terakhir && preg_match('/(\d+)$/', $terakhir, $cocok))
            ? (int) $cocok[1] + 1
            : 1;

        return $prefix.str_pad((string) $nomor, 3, '0', STR_PAD_LEFT);
    }

    private function handle(callable $callback, string $pesan, string $rute, array $parameter = [])
    {
        try {
            $callback();
        } catch (ValidationException $e) {
            return back()->withErrors($e->errors());
        }

        return redirect()->route($rute, $parameter)->with('success', $pesan);
    }

    private function reject(string $pesan): never
    {
        throw ValidationException::withMessages(['error' => $pesan]);
    }
}
