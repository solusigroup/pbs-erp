<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilPurchaseOrder extends Model
{
    protected $table = 'cugil_purchase_orders';

    protected $fillable = [
        'nomor_po', 'tanggal', 'kode_supplier', 'nama_vendor',
        'total_qty', 'total_nilai', 'termin_payment', 'ongkos_angkut',
        'batas_tanggal', 'petugas', 'keterangan', 'status_terima',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'batas_tanggal' => 'date',
        'total_qty' => 'decimal:2',
        'total_nilai' => 'decimal:2',
        'ongkos_angkut' => 'decimal:2',
    ];

    public function supplier()
    {
        return $this->belongsTo(CugilSupplier::class, 'kode_supplier', 'kode_supplier');
    }

    public function items()
    {
        return $this->hasMany(CugilPurchaseOrderItem::class, 'cugil_po_id');
    }

    public function rawMaterials()
    {
        return $this->hasMany(CugilRawMaterial::class, 'nomor_po', 'nomor_po');
    }
}
