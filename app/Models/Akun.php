<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Akun extends Model
{
    protected $table = 'akun';
    protected $primaryKey = 'kode_akun';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'kode_akun',
        'nama_akun',
        'kategori',
        'tipe_akun',
        'saldo_normal',
        'saldo_awal',
        'saldo_berjalan',
        'is_active',
    ];

    public function detailJurnal(): HasMany
    {
        return $this->hasMany(JurnalDetail::class, 'kode_akun', 'kode_akun');
    }
}
