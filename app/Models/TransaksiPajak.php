<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TransaksiPajak extends Model
{
    protected $table = 'transaksi_pajak';

    protected $fillable = [
        'kode_referensi',
        'jenis_pajak',
        'masa_pajak',
        'tahun_pajak',
        'tanggal_faktur_potong',
        'nomor_dokumen',
        'lawan_transaksi',
        'npwp_lawan_transaksi',
        'dpp',
        'tarif_persen',
        'nominal_pajak',
        'status_bayar',
        'ntpn',
        'tanggal_setor',
        'status_lapor',
        'bpe_spt',
        'tanggal_lapor',
        'catatan',
        'reviewed_by',
    ];

    protected $casts = [
        'tanggal_faktur_potong' => 'date',
        'tanggal_setor' => 'date',
        'tanggal_lapor' => 'date',
        'dpp' => 'decimal:2',
        'tarif_persen' => 'decimal:2',
        'nominal_pajak' => 'decimal:2',
    ];
}
