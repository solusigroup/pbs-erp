<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Perusahaan extends Model
{
    protected $table = 'perusahaan';

    protected $fillable = [
        'nama_perusahaan',
        'singkatan',
        'npwp',
        'alamat',
        'kota',
        'provinsi',
        'telepon',
        'email',
        'website',
        'bank_nama',
        'bank_rekening',
        'bank_atas_nama',
        'bod_finance_tax',
    ];

    public function directors()
    {
        return CompanyDirector::where('is_active', true)->orderBy('urutan')->get();
    }
}
