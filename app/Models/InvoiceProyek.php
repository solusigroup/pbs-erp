<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InvoiceProyek extends Model
{
    protected $table = 'invoice_proyek';

    protected $fillable = [
        'nomor_invoice',
        'id_proyek',
        'termin_ke',
        'tanggal_invoice',
        'jatuh_tempo',
        'nominal_tagihan',
        'ppn_nominal',
        'pph_nominal',
        'total_bersih',
        'status_bayar',
        'tanggal_lunas',
    ];

    protected $casts = [
        'tanggal_invoice' => 'date',
        'jatuh_tempo' => 'date',
        'tanggal_lunas' => 'date',
        'nominal_tagihan' => 'decimal:2',
        'ppn_nominal' => 'decimal:2',
        'pph_nominal' => 'decimal:2',
        'total_bersih' => 'decimal:2',
    ];

    public function proyek(): BelongsTo
    {
        return $this->belongsTo(Proyek::class, 'id_proyek');
    }
}
