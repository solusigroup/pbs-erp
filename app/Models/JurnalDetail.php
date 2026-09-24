<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class JurnalDetail extends Model
{
    protected $table = 'jurnal_detail';
    protected $primaryKey = 'id_detail';

    protected $fillable = [
        'id_jurnal',
        'kode_akun',
        'keterangan_baris',
        'debit',
        'kredit',
    ];

    protected $casts = [
        'debit' => 'decimal:2',
        'kredit' => 'decimal:2',
    ];

    public function jurnal(): BelongsTo
    {
        return $this->belongsTo(JurnalUmum::class, 'id_jurnal', 'id_jurnal');
    }

    public function akun(): BelongsTo
    {
        return $this->belongsTo(Akun::class, 'kode_akun', 'kode_akun');
    }
}
