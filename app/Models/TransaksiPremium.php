<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPremium extends Model
{
    protected $table = 'transaksi_premium';
    protected $primaryKey = 'idTransaksiPremium';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idTransaksiPremium',
        'idMember',
        'idAdmin',
        'idDiskon',
        'biayaLangganan',
        'nominalDiskon',
        'totalBayar',
        'metodePembayaran',
        'tanggalTransaksi',
        'masaBerlakuHingga',
    ];

    public function member()
    {
        return $this->belongsTo(Member::class, 'idMember', 'idMember');
    }

    public function diskon()
    {
        return $this->belongsTo(Diskon::class, 'idDiskon', 'idDiskon');
    }
}
