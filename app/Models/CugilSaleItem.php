<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilSaleItem extends Model
{
    protected $table = 'cugil_sale_items';

    protected $fillable = [
        'cugil_sale_id', 'kode_barang', 'nama_barang', 'qty_gudang', 'qty_terjual',
        'jumlah_sak', 'harga_satuan', 'harga_total', 'diskon_persen', 'diskon_rupiah',
        'subtotal', 'catatan',
    ];

    protected $casts = [
        'qty_gudang' => 'decimal:2',
        'qty_terjual' => 'decimal:2',
        'harga_satuan' => 'decimal:2',
        'harga_total' => 'decimal:2',
        'diskon_persen' => 'decimal:2',
        'diskon_rupiah' => 'decimal:2',
        'subtotal' => 'decimal:2',
    ];

    public function sale()
    {
        return $this->belongsTo(CugilSale::class, 'cugil_sale_id');
    }

    public function barang()
    {
        return $this->belongsTo(CugilBarang::class, 'kode_barang', 'kode_barang');
    }
}
