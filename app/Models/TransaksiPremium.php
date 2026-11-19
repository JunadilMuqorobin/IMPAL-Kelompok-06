<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPremium extends Model
{
    protected $table = 'transaksi_premium';
    protected $primaryKey = 'idTransaksiPremium';
    public $timestamps = false;

    public function member()
    {
        return $this->belongsTo(Member::class, 'idMember', 'idMember');
    }
}
