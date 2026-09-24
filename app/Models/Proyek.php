<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Proyek extends Model
{
    protected $table = 'proyek';

    protected $fillable = [
        'kode_proyek',
        'nama_proyek',
        'nama_klien',
        'pic_klien',
        'telepon_klien',
        'tanggal_mulai',
        'tanggal_selesai_target',
        'nilai_kontrak',
        'total_tertagih',
        'total_terbayar',
        'progress_persen',
        'status_proyek',
        'keterangan',
    ];

    protected $casts = [
        'tanggal_mulai' => 'date',
        'tanggal_selesai_target' => 'date',
        'nilai_kontrak' => 'decimal:2',
        'total_tertagih' => 'decimal:2',
        'total_terbayar' => 'decimal:2',
    ];

    public function invoices(): HasMany
    {
        return $this->hasMany(InvoiceProyek::class, 'id_proyek');
    }
}
