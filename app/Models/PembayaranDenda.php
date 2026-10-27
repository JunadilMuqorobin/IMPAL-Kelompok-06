<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PembayaranDenda extends Model
{
    protected $table = 'pembayaran_denda';
    protected $primaryKey = 'idPembayaranDenda';
    public $timestamps = false;
}
