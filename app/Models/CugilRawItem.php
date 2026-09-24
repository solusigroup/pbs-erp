<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilRawItem extends Model
{
    protected $table = 'cugil_raw_items';

    protected $fillable = [
        'cugil_raw_id', 'kode_barang', 'nama_barang', 'qty', 'satuan',
        'harga_satuan', 'harga_total', 'diskon_rafaksi', 'subtotal', 'catatan',
    ];

    protected $casts = [
        'qty' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'harga_total' => 'decimal:2',
        'diskon_rafaksi' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function rawMaterial()
    {
        return $this->belongsTo(CugilRawMaterial::class, 'cugil_raw_id');
    }

    public function barang()
    {
        return $this->belongsTo(CugilBarang::class, 'kode_barang', 'kode_barang');
    }
}
