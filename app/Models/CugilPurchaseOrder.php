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

    /**
     * Generate nomor PO berikutnya secara otomatis.
     * Format: XXXXX/PBS/M/YYYY
     * - XXXXX = urutan global 5-digit (zero-padded), melanjutkan dari nomor terbesar di DB
     * - M     = bulan masehi (tanpa leading zero)
     * - YYYY  = tahun masehi
     */
    public static function generateNextNomorPO(): string
    {
        $maxSeq = static::where('nomor_po', 'like', '%/PBS/%')
            ->selectRaw('MAX(CAST(SUBSTRING_INDEX(nomor_po, "/", 1) AS UNSIGNED)) as max_seq')
            ->value('max_seq');

        $nextSeq = ($maxSeq ?? 0) + 1;

        return str_pad($nextSeq, 5, '0', STR_PAD_LEFT) . '/PBS/' . date('n') . '/' . date('Y');
    }
}
