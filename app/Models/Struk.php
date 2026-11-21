<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Struk extends Model
{
    protected $table = 'struk';
    protected $primaryKey = 'idStruk';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idStruk',
        'noReferensi',
        'jenisTransaksi',
        'idDenda',
        'idTransaksiPremium',
        'tanggalCetak',
        'rincianTransaksi',
        'totalBiaya',
    ];
}
