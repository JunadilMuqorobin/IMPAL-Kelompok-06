<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranDenda extends Model
{
    protected $table = 'pembayaran_denda';
    protected $primaryKey = 'idDenda';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idDenda',
        'idPeminjaman',
        'idAdmin',
        'jumlahHariTerlambat',
        'totalDenda',
        'jumlahBayar',
        'kembalian',
        'metodePembayaran',
        'tanggalBayar',
    ];

    public function peminjaman()
    {
        return $this->belongsTo(Peminjaman::class, 'idPeminjaman', 'idPeminjaman');
    }

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'idAdmin', 'idAdmin');
    }
}