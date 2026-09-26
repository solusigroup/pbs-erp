<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class WorkflowDefinition extends Model
{
    protected $table = 'workflow_definitions';

    protected $fillable = [
        'module',
        'step_code',
        'step_name',
        'description',
        'step_order',
        'is_required',
        'required_role',
        'is_active',
    ];

    protected $casts = [
        'is_required' => 'boolean',
        'is_active' => 'boolean',
    ];

    /**
     * Label modul yang ramah tampilan.
     */
    public static function moduleLabels(): array
    {
        return [
            'akuntansi_jurnal'  => 'Akuntansi — Kontrol & Approval Jurnal',
            'akuntansi_closing' => 'Akuntansi — Siklus Tutup Buku',
            'cugil_po'          => 'CUGIL — Purchase Order',
            'cugil_raw'         => 'CUGIL — Terima Bahan Baku',
            'cugil_sales'       => 'CUGIL — Penjualan',
            'pengajuan_dana'    => 'Pengajuan Dana / Anggaran',
            'proyek'            => 'Proyek & Kontrak',
            'pajak'             => 'Perpajakan',
        ];
    }

    /**
     * Ikon Font Awesome per modul.
     */
    public static function moduleIcons(): array
    {
        return [
            'akuntansi_jurnal'  => 'fa-stamp',
            'akuntansi_closing' => 'fa-book-bookmark',
            'cugil_po'          => 'fa-file-invoice',
            'cugil_raw'         => 'fa-truck-ramp-box',
            'cugil_sales'       => 'fa-hand-holding-dollar',
            'pengajuan_dana'    => 'fa-money-check-dollar',
            'proyek'            => 'fa-diagram-project',
            'pajak'             => 'fa-shield-halved',
        ];
    }

    /**
     * Warna CSS per modul.
     */
    public static function moduleColors(): array
    {
        return [
            'akuntansi_jurnal'  => 'indigo',
            'akuntansi_closing' => 'teal',
            'cugil_po'          => 'blue',
            'cugil_raw'         => 'amber',
            'cugil_sales'       => 'emerald',
            'pengajuan_dana'    => 'purple',
            'proyek'            => 'sky',
            'pajak'             => 'rose',
        ];
    }

    public function checklists(): HasMany
    {
        return $this->hasMany(WorkflowChecklist::class, 'step_code', 'step_code')
            ->where('module', $this->module);
    }
}
