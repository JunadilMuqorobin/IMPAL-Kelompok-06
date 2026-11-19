<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Buku extends Model
{
    protected $table = 'buku';

    protected $primaryKey = 'idBuku';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'idBuku',
        'judul',
        'pengarang',
        'kategori',
        'status',
        'rating',
    ];

    public function peminjamans()
    {
        return $this->hasMany(Peminjaman::class, 'idBuku', 'idBuku');
    }

    public function bookings()
    {
        return $this->hasMany(Booking::class, 'idBuku', 'idBuku');
    }

    public function ulasans()
    {
        return $this->hasMany(Ulasan::class, 'idBuku', 'idBuku');
    }
}
