<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PengajuanDana extends Model
{
    protected $table = 'pengajuan_dana';

    protected $fillable = [
        'nomor_pengajuan',
        'tanggal_pengajuan',
        'pemohon',
        'departemen',
        'kategori_biaya',
        'keperluan',
        'nominal_diajukan',
        'nominal_disetujui',
        'status',
        'catatan_bod',
        'approved_at',
        'approved_by',
        'metode_pencairan',
        'no_bukti_cair',
    ];

    protected $casts = [
        'tanggal_pengajuan' => 'date',
        'approved_at' => 'datetime',
        'nominal_diajukan' => 'decimal:2',
        'nominal_disetujui' => 'decimal:2',
    ];
}
