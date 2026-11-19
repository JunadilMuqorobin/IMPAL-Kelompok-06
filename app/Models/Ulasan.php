<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Ulasan extends Model
{
    protected $table = 'ulasan';

    protected $primaryKey = 'idUlasan';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'idUlasan',
        'idMember',
        'idBuku',
        'nilaiRating',
        'komentar',
        'tanggalUlasan',
    ];

    public function buku()
    {
        return $this->belongsTo(Buku::class, 'idBuku', 'idBuku');
    }

    public function member()
    {
        return $this->belongsTo(Member::class, 'idMember', 'idMember');
    }
}
