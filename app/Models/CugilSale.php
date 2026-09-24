<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CugilSale extends Model
{
    protected $table = 'cugil_sales';

    protected $fillable = [
        'id_penjualan', 'tanggal', 'kode_customer', 'nama_buyer', 'tanggal_kirim',
        'sales', 'broker', 'fee_makelar', 'truk', 'status_broker_truk_lunas',
        'ongkos_kuli', 'ongkos_angkut', 'down_payment', 'total_qty', 'total_sak',
        'total_bruto', 'total_diskon', 'tagihan', 'payment', 'sisa_piutang',
        'status_pelunasan', 'status_timbangan', 'foto_timbangan', 'invoiced', 'status', 'remark',
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tanggal_kirim' => 'date',
        'fee_makelar' => 'decimal:2',
        'ongkos_kuli' => 'decimal:2',
        'ongkos_angkut' => 'decimal:2',
        'down_payment' => 'decimal:2',
        'total_qty' => 'decimal:2',
        'total_sak' => 'decimal:2',
        'total_bruto' => 'decimal:2',
        'total_diskon' => 'decimal:2',
        'tagihan' => 'decimal:2',
        'payment' => 'decimal:2',
        'sisa_piutang' => 'decimal:2',
    ];

    protected $appends = ['foto_timbangan_url'];

    public function customer()
    {
        return $this->belongsTo(CugilCustomer::class, 'kode_customer', 'kode_customer');
    }

    public function items()
    {
        return $this->hasMany(CugilSaleItem::class, 'cugil_sale_id');
    }

    /**
     * Get accessible URL for scale slip image.
     */
    public function getFotoTimbanganUrlAttribute(): ?string
    {
        if (!$this->foto_timbangan) {
            return null;
        }

        if (str_starts_with($this->foto_timbangan, 'http://') || str_starts_with($this->foto_timbangan, 'https://')) {
            return $this->foto_timbangan;
        }

        return asset('storage/' . ltrim($this->foto_timbangan, '/'));
    }
}
