<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Booking extends Model
{
    protected $table = 'booking';

    protected $primaryKey = 'idBooking';

    public $incrementing = false;

    protected $keyType = 'string';

    public $timestamps = false;

    protected $fillable = [
        'idBooking',
        'idMember',
        'idBuku',
        'waktuBooking',
        'posisiAntrian',
        'statusBooking',
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
