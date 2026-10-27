<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Admin extends Authenticatable
{
    protected $table = 'admin';
    protected $primaryKey = 'idAdmin';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = ['idAdmin', 'nama', 'email', 'kataSandi'];

    protected $hidden = ['kataSandi'];

    public function getAuthPassword()
    {
        return $this->kataSandi;
    }
}
