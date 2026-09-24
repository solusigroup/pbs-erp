<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilCustomer extends Model
{
    protected $table = 'cugil_customers';

    protected $fillable = [
        'kode_customer', 'nama_customer', 'alamat', 'kota', 'telepon',
        'item_barang', 'bank', 'no_rekening', 'piutang', 'is_active',
    ];

    protected $casts = [
        'piutang' => 'decimal:2',
        'is_active' => 'boolean',
    ];

    public function sales()
    {
        return $this->hasMany(CugilSale::class, 'kode_customer', 'kode_customer');
    }
}
