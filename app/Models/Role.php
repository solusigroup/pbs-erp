<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    protected $table = 'roles';

    protected $fillable = [
        'slug',
        'name',
        'description',
        'permissions',
        'is_system',
    ];

    protected $casts = [
        'permissions' => 'array',
        'is_system' => 'boolean',
    ];

    /**
     * Master daftar modul dan permission di PBS-ERP
     */
    public static function availablePermissions(): array
    {
        return [
            'Dashboard' => [
                'dashboard.view' => 'Melihat Executive Dashboard & Metrik Utama',
            ],
            'Operasional CUGIL' => [
                'cugil.view' => 'Melihat Katalog Barang, Supplier & Customer',
                'cugil.po' => 'Membuat & Mengelola Purchase Order (PO)',
                'cugil.raw' => 'Mencatat Terima Bahan Baku & Timbangan (RAW)',
                'cugil.sales' => 'Mencatat Faktur & Pengiriman Penjualan (SALES)',
                'cugil.master' => 'Menambah & Mengubah Data Master Barang/Supplier/Customer',
            ],
            'Keuangan & Akuntansi' => [
                'akuntansi.view' => 'Melihat Chart of Accounts (COA) & Saldo',
                'akuntansi.jurnal' => 'Mencatat & Memposting Jurnal Umum',
                'akuntansi.laporan' => 'Melihat Laba Rugi & Neraca Akuntansi',
            ],
            'Direksi & Pengawasan' => [
                'pajak.manage' => 'Mengelola Pajak PPN, PPh & Validasi NTPN',
                'anggaran.approve' => 'Menyetujui (Approval) & Menolak Pengajuan Dana',
                'anggaran.create' => 'Mengajukan Permohonan Anggaran Dana',
                'proyek.manage' => 'Mengelola Kontrak Proyek & Menerbitkan Invoice',
            ],
            'Laporan & Analisis' => [
                'laporan.view' => 'Melihat Seluruh Rekapitulasi & Mencetak Laporan',
                'analisis.view' => 'Mengakses Executive Business Intelligence (BI)',
            ],
            'Administrasi & Sistem' => [
                'users.manage' => 'Mengelola Akun Pengguna (Tambah, Edit, Nonaktifkan)',
                'roles.manage' => 'Mengatur Role & Hak Akses Permission',
                'perusahaan.manage' => 'Mengubah Informasi & Profil Korporat PT PBS',
            ],
        ];
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role', 'slug');
    }

    public function hasPermission(string $permission): bool
    {
        if ($this->slug === 'bod' || $this->slug === 'admin') {
            return true; // BOD and Admin have full bypass access
        }

        $perms = $this->permissions ?? [];
        return in_array($permission, $perms) || in_array('*', $perms);
    }
}
