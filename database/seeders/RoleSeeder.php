<?php

namespace Database\Seeders;

use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleSeeder extends Seeder
{
    public function run(): void
    {
        $roles = [
            [
                'slug' => 'bod',
                'name' => 'Board of Director',
                'description' => 'Dewan Direksi (Finance & Tax) - Hak Akses Penuh & Otorisasi Keputusan Tertinggi',
                'permissions' => ['*'],
                'is_system' => true,
            ],
            [
                'slug' => 'admin',
                'name' => 'System Administrator',
                'description' => 'Administrator Sistem ERP, Manajemen Pengguna & Tata Kelola Konfigurasi',
                'permissions' => ['*'],
                'is_system' => true,
            ],
            [
                'slug' => 'finance_manager',
                'name' => 'Finance & Accounting Manager',
                'description' => 'Manajer Keuangan, Verifikasi Jurnal Umum, Laporan Keuangan & Approval Anggaran',
                'permissions' => [
                    'dashboard.view',
                    'cugil.view',
                    'akuntansi.view',
                    'akuntansi.jurnal',
                    'akuntansi.laporan',
                    'anggaran.create',
                    'anggaran.approve',
                    'proyek.view',
                    'proyek.manage',
                    'laporan.view',
                    'analisis.view',
                ],
                'is_system' => true,
            ],
            [
                'slug' => 'tax_officer',
                'name' => 'Tax Specialist',
                'description' => 'Spesialis Perpajakan, Pengawasan PPN/PPh, Bukti Potong, NTPN & Kepatuhan Fiskal',
                'permissions' => [
                    'dashboard.view',
                    'cugil.view',
                    'pajak.manage',
                    'laporan.view',
                    'analisis.view',
                ],
                'is_system' => true,
            ],
            [
                'slug' => 'cugil_operator',
                'name' => 'Operator Pabrik & Logistik CUGIL',
                'description' => 'Kepala Pabrik, Pencatatan Timbangan Bahan Masuk, PO & Pengiriman Gilingan',
                'permissions' => [
                    'dashboard.view',
                    'cugil.view',
                    'cugil.po',
                    'cugil.raw',
                    'cugil.sales',
                    'cugil.master',
                    'laporan.view',
                ],
                'is_system' => true,
            ],
            [
                'slug' => 'staff',
                'name' => 'Staf Operasional',
                'description' => 'Staf Umum Pengajuan Anggaran Operasional & Dashboard Terbatas',
                'permissions' => [
                    'dashboard.view',
                    'anggaran.create',
                ],
                'is_system' => true,
            ],
            [
                'slug' => 'komisaris',
                'name' => 'Dewan Komisaris (Read-Only)',
                'description' => 'Dewan Pengawas & Pemegang Saham: Pemantauan Kinerja Finansial, Audit Operasional & KPI Perusahaan Tanpa Wewenang Eksekusi Transaksi',
                'permissions' => [
                    'dashboard.view',
                    'cugil.view',
                    'akuntansi.view',
                    'akuntansi.laporan',
                    'laporan.view',
                    'analisis.view',
                ],
                'is_system' => true,
            ],
            [
                'slug' => 'auditor',
                'name' => 'Auditor Independen / Kepatuhan',
                'description' => 'Pemeriksaan & Kepatuhan: Akses Penuh Dokumen Transaksi, Foto Timbangan, Jurnal, Faktur Pajak & Rekapitulasi untuk Keperluan Audit',
                'permissions' => [
                    'dashboard.view',
                    'cugil.view',
                    'akuntansi.view',
                    'akuntansi.laporan',
                    'laporan.view',
                    'analisis.view',
                ],
                'is_system' => true,
            ],
        ];

        foreach ($roles as $r) {
            Role::updateOrCreate(['slug' => $r['slug']], $r);
        }
    }
}
