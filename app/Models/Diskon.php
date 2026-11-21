<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Diskon extends Model
{
    protected $table = 'diskon';
    protected $primaryKey = 'idDiskon';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idDiskon',
        'kodeVoucher',
        'persenDiskon',
        'nominalPotongan',
        'tanggalKadaluarsa',
    ];
}
