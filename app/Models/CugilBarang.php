<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilBarang extends Model
{
    protected $table = 'cugil_barang';

    protected $fillable = [
        'kode_barang', 'nama_barang', 'kategori', 'kode_kategori', 'satuan',
        'stok_awal', 'barang_masuk', 'barang_keluar', 'stok_akhir',
        'harga_beli', 'harga_jual', 'foto_produk', 'is_active',
    ];

    protected $casts = [
        'stok_awal' => 'decimal:2',
        'barang_masuk' => 'decimal:2',
        'barang_keluar' => 'decimal:2',
        'stok_akhir' => 'decimal:2',
        'harga_beli' => 'decimal:2',
        'harga_jual' => 'decimal:2',
        'is_active' => 'boolean',
    ];
}
