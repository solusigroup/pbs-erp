<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilPurchaseOrderItem extends Model
{
    protected $table = 'cugil_po_items';

    protected $fillable = [
        'cugil_po_id', 'kode_barang', 'nama_barang', 'qty', 'satuan',
        'harga_satuan', 'harga_total', 'catatan',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'harga_total' => 'decimal:2',
    ];

    public function purchaseOrder()
    {
        return $this->belongsTo(CugilPurchaseOrder::class, 'cugil_po_id');
    }

    public function barang()
    {
        return $this->belongsTo(CugilBarang::class, 'kode_barang', 'kode_barang');
    }
}
