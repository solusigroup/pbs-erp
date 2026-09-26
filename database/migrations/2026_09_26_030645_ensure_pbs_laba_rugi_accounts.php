<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        $accounts = [
            [
                'kode_akun' => '4-1110',
                'nama_akun' => 'Diskon Penjualan',
                'kategori' => 'Pendapatan',
                'tipe_akun' => 'Pendapatan',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '5-1200',
                'nama_akun' => 'FOH - Biaya Tenaga Kerja Langsung',
                'kategori' => 'Beban',
                'tipe_akun' => 'HPP',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '5-1300',
                'nama_akun' => 'FOH - Listrik Pabrik',
                'kategori' => 'Beban',
                'tipe_akun' => 'HPP',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '5-1400',
                'nama_akun' => 'FOH - Maintenance Pabrik',
                'kategori' => 'Beban',
                'tipe_akun' => 'HPP',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '5-2100',
                'nama_akun' => 'Ongkos Angkut Penjualan',
                'kategori' => 'Beban',
                'tipe_akun' => 'HPP',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '5-2200',
                'nama_akun' => 'Komisi Sales (Fee Marketing)',
                'kategori' => 'Beban',
                'tipe_akun' => 'HPP',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '5-2300',
                'nama_akun' => 'Komisi Lainnya (Ongkos Kuli, Satpam)',
                'kategori' => 'Beban',
                'tipe_akun' => 'HPP',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
            [
                'kode_akun' => '6-1150',
                'nama_akun' => 'Gaji Manajemen & Direksi',
                'kategori' => 'Beban',
                'tipe_akun' => 'Beban',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ],
        ];

        foreach ($accounts as $acc) {
            \App\Models\Akun::firstOrCreate(
                ['kode_akun' => $acc['kode_akun']],
                $acc
            );
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        \App\Models\Akun::whereIn('kode_akun', [
            '4-1110', '5-1200', '5-1300', '5-1400', '5-2100', '5-2200', '5-2300', '6-1150'
        ])->delete();
    }
};
