<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilSupplier extends Model
{
    protected $table = 'cugil_suppliers';

    protected $fillable = [
        'kode_supplier', 'nama_supplier', 'alamat', 'kota', 'telepon',
        'item_barang', 'bank', 'no_rekening', 'hutang', 'is_active',
    ];

    protected $casts = [
        'hutang' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function purchaseOrders()
    {
        return $this->hasMany(CugilPurchaseOrder::class, 'kode_supplier', 'kode_supplier');
    }

    public function rawMaterials()
    {
        return $this->hasMany(CugilRawMaterial::class, 'kode_supplier', 'kode_supplier');
    }
}
