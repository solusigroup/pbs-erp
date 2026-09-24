<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilRawMaterial extends Model
{
    protected $table = 'cugil_raw_materials';

    protected $fillable = [
        'nomor_po', 'tanggal', 'kode_supplier', 'nama_pemasok', 'batch_produksi',
        'total_qty', 'total_bruto', 'total_rafaksi', 'ongkos_angkut',
        'tagihan', 'payment', 'sisa_tagihan', 'status_lunas',
        'truk', 'status_truk_lunas', 'timbangan', 'invoiced', 'petugas', 'remark',
        'bulan', 'tahun',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'total_qty' => 'decimal:2',
        'total_bruto' => 'decimal:2',
        'total_rafaksi' => 'decimal:2',
        'ongkos_angkut' => 'decimal:2',
        'tagihan' => 'decimal:2',
        'payment' => 'decimal:2',
        'sisa_tagihan' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(CugilSupplier::class, 'kode_supplier', 'kode_supplier');
    }

    public function purchaseOrder()
    {
        return $this->belongsTo(CugilPurchaseOrder::class, 'nomor_po', 'nomor_po');
    }

    public function items()
    {
        return $this->hasMany(CugilRawItem::class, 'cugil_raw_id');
    }
}
