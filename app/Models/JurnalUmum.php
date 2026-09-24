<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class JurnalUmum extends Model
{
    protected $table = 'jurnal_umum';
    protected $primaryKey = 'id_jurnal';

    protected $fillable = [
        'no_transaksi',
        'tanggal',
        'tipe_jurnal',
        'deskripsi',
        'sumber_referensi',
        'total_debit',
        'total_kredit',
        'created_by',
        'is_posted',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_debit' => 'decimal:2',
        'total_kredit' => 'decimal:2',
        'is_posted' => 'boolean',
    ];

    public function details(): HasMany
    {
        return $this->hasMany(JurnalDetail::class, 'id_jurnal', 'id_jurnal');
    }
}
