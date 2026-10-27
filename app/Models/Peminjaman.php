<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Peminjaman extends Model
{
    // Deklarasikan nama tabel secara eksplisit
    protected $table = 'peminjaman';
    protected $primaryKey = 'idPeminjaman'; // Sesuaikan jika PK menggunakan nama kustom
    public $timestamps = false;

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'idBuku', 'idBuku');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'idMember', 'idMember');
    }
}
