<?php

use App\Models\Akun;
use Illuminate\Database\Migrations\Migration;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Akun::firstOrCreate(
            ['kode_akun' => '1-1610'],
            [
                'nama_akun' => 'Persediaan Bahan Baku (CUGIL)',
                'kategori' => 'Aset Lancar',
                'tipe_akun' => 'Persediaan',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ]
        );

        Akun::firstOrCreate(
            ['kode_akun' => '1-1620'],
            [
                'nama_akun' => 'Persediaan Barang Jadi (CUGIL)',
                'kategori' => 'Aset Lancar',
                'tipe_akun' => 'Persediaan',
                'saldo_normal' => 'Debit',
                'saldo_awal' => 0,
                'saldo_berjalan' => 0,
                'is_active' => true,
            ]
        );
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Akun::whereIn('kode_akun', ['1-1610', '1-1620'])->delete();
    }
};
