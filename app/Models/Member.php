<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Member extends Authenticatable
{
    protected $table = 'member';
    protected $primaryKey = 'idMember';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'idMember',
        'nama',
        'email',
        'kataSandi',
        'tipeMember',
        'maxPinjam',
        'durasiPinjamHari',
        'isPremium'
    ];

    protected $hidden = ['kataSandi'];

    // Mengarahkan Laravel Auth menggunakan kolom kataSandi
    public function getAuthPassword()
    {
        return $this->kataSandi;
    }
}
