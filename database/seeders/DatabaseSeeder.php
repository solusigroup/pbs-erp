<?php

namespace Database\Seeders;

use App\Models\Akun;
use App\Models\InvoiceProyek;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\PengajuanDana;
use App\Models\Perusahaan;
use App\Models\Proyek;
use App\Models\TransaksiPajak;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database for PT Pinastika Bhakti Semesta (PBS-ERP).
     */
    public function run(): void
    {
        // 1. Users Intern PT Pinastika Bhakti Semesta
        $bod = User::updateOrCreate(
            ['email' => 'kurniawan@pinastika.co.id'],
            [
                'name' => 'Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.',
                'position' => 'Board of Director (Finance & Tax)',
                'department' => 'Finance & Tax',
                'role' => 'bod',
                'phone' => '+62 821 4164 3495',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        $finance = User::updateOrCreate(
            ['email' => 'finance@pinastika.co.id'],
            [
                'name' => 'Finance & Accounting Team',
                'position' => 'Finance & Accounting Manager',
                'department' => 'Finance & Tax',
                'role' => 'finance_manager',
                'phone' => '+62 812 3456 7890',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );

        // 2. Profil Perusahaan PT Pinastika Bhakti Semesta
        Perusahaan::updateOrCreate(
            ['id' => 1],
            [
                'nama_perusahaan' => 'PT Pinastika Bhakti Semesta',
                'singkatan' => 'PBS',
                'npwp' => '43.688.232.8-602.000',
                'alamat' => 'Jl. Suromulang Barat VI/20, Mojokerto',
                'kota' => 'Mojokerto',
                'provinsi' => 'Jawa Timur',
                'telepon' => '+62 821 4164 3495',
                'email' => 'kurniawan@petalmail.com',
                'website' => 'https://simpleakunting.id',
                'bank_nama' => 'Bank Mandiri',
                'bank_rekening' => '142-00-1234567-8',
                'bank_atas_nama' => 'PT Pinastika Bhakti Semesta',
                'bod_finance_tax' => 'Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.',
            ]
        );

        // 3. Chart of Accounts (COA) Korporasi PBS (Saldo Awal Default Nol)
        $akuns = [
            ['1-1100', 'Kas Operasional PBS', 'Aset Lancar', 'Kas & Bank', 'Debit', 0, 0],
            ['1-1200', 'Bank Mandiri Rek Giro PBS', 'Aset Lancar', 'Kas & Bank', 'Debit', 0, 0],
            ['1-1210', 'Bank BCA Rek Operasional', 'Aset Lancar', 'Kas & Bank', 'Debit', 0, 0],
            ['1-1300', 'Piutang Usaha Proyek', 'Aset Lancar', 'Piutang', 'Debit', 0, 0],
            ['1-1400', 'Uang Muka Biaya & Pajak Dimuka', 'Aset Lancar', 'Piutang', 'Debit', 0, 0],
            ['1-2100', 'Aset Tetap - Kendaraan Operasional', 'Aset Tetap', 'Aset Tetap', 'Debit', 0, 0],
            ['1-2110', 'Akumulasi Penyusutan Kendaraan', 'Aset Tetap', 'Aset Tetap', 'Kredit', 0, 0],
            ['1-2200', 'Aset Tetap - Peralatan & IT', 'Aset Tetap', 'Aset Tetap', 'Debit', 0, 0],
            ['1-2210', 'Akumulasi Penyusutan Peralatan', 'Aset Tetap', 'Aset Tetap', 'Kredit', 0, 0],
            ['2-1100', 'Hutang Usaha / Mitra Vendor', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['2-1200', 'Hutang Gaji & Operasional', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['2-1310', 'Hutang Pajak PPh 21 (Gaji/Honor)', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['2-1320', 'Hutang Pajak PPh 23 (Jasa/Sewa)', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['2-1330', 'Hutang Pajak PPh 4 ayat 2 (Final)', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['2-1340', 'Hutang Pajak PPN Keluaran', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['1-1500', 'PPN Masukan (Dapat Dikreditkan)', 'Aset Lancar', 'Piutang', 'Debit', 0, 0],
            ['3-1100', 'Modal Saham Disetor PBS', 'Ekuitas', 'Modal', 'Kredit', 0, 0],
            ['3-2100', 'Laba Ditahan (Retained Earnings)', 'Ekuitas', 'Modal', 'Kredit', 0, 0],
            ['4-1100', 'Pendapatan Kontrak Jasa & Pengadaan', 'Pendapatan', 'Pendapatan', 'Kredit', 0, 0],
            ['4-1200', 'Pendapatan Konsultansi & Supervisi', 'Pendapatan', 'Pendapatan', 'Kredit', 0, 0],
            ['5-1100', 'Beban Pokok Pendapatan (HPP Proyek)', 'Beban', 'Beban', 'Debit', 0, 0],
            ['6-1100', 'Beban Gaji, Honor & Tunjangan', 'Beban', 'Beban', 'Debit', 0, 0],
            ['6-1200', 'Beban Operasional Kantor & Utilitas', 'Beban', 'Beban', 'Debit', 0, 0],
            ['6-1300', 'Beban Transportasi & Perjalanan Dinas', 'Beban', 'Beban', 'Debit', 0, 0],
            ['6-1400', 'Beban Pemeliharaan & Legalitas', 'Beban', 'Beban', 'Debit', 0, 0],
            ['7-1100', 'Beban Pajak Penghasilan (PPh Badan)', 'Beban', 'Beban', 'Debit', 0, 0],
        ];

        foreach ($akuns as $item) {
            Akun::updateOrCreate(
                ['kode_akun' => $item[0]],
                [
                    'nama_akun' => $item[1],
                    'kategori' => $item[2],
                    'tipe_akun' => $item[3],
                    'saldo_normal' => $item[4],
                    'saldo_awal' => $item[5],
                    'saldo_berjalan' => $item[6],
                    'is_active' => true,
                ]
            );
        }

        $this->call(RoleSeeder::class);
        $this->call(DirectorAndSignatureSeeder::class);
        // Data simulasi Cugil, jika ingin dihapus nanti bisa di-comment
        $this->call(CugilSeeder::class);
    }
}
