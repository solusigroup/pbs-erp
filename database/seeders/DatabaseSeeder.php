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
                'npwp' => '01.234.567.8-602.000',
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

        // 3. Chart of Accounts (COA) Korporasi PBS
        $akuns = [
            ['1-1100', 'Kas Operasional PBS', 'Aset Lancar', 'Kas & Bank', 'Debit', 25000000, 25000000],
            ['1-1200', 'Bank Mandiri Rek Giro PBS', 'Aset Lancar', 'Kas & Bank', 'Debit', 1250000000, 1250000000],
            ['1-1210', 'Bank BCA Rek Operasional', 'Aset Lancar', 'Kas & Bank', 'Debit', 450000000, 450000000],
            ['1-1300', 'Piutang Usaha Proyek', 'Aset Lancar', 'Piutang', 'Debit', 380000000, 380000000],
            ['1-1400', 'Uang Muka Biaya & Pajak Dimuka', 'Aset Lancar', 'Piutang', 'Debit', 45000000, 45000000],
            ['1-2100', 'Aset Tetap - Kendaraan Operasional', 'Aset Tetap', 'Aset Tetap', 'Debit', 350000000, 350000000],
            ['1-2110', 'Akumulasi Penyusutan Kendaraan', 'Aset Tetap', 'Aset Tetap', 'Kredit', 70000000, 70000000],
            ['1-2200', 'Aset Tetap - Peralatan & IT', 'Aset Tetap', 'Aset Tetap', 'Debit', 120000000, 120000000],
            ['1-2210', 'Akumulasi Penyusutan Peralatan', 'Aset Tetap', 'Aset Tetap', 'Kredit', 24000000, 24000000],
            ['2-1100', 'Hutang Usaha / Mitra Vendor', 'Kewajiban', 'Hutang', 'Kredit', 150000000, 150000000],
            ['2-1200', 'Hutang Gaji & Operasional', 'Kewajiban', 'Hutang', 'Kredit', 0, 0],
            ['2-1310', 'Hutang Pajak PPh 21 (Gaji/Honor)', 'Kewajiban', 'Hutang', 'Kredit', 14500000, 14500000],
            ['2-1320', 'Hutang Pajak PPh 23 (Jasa/Sewa)', 'Kewajiban', 'Hutang', 'Kredit', 8750000, 8750000],
            ['2-1330', 'Hutang Pajak PPh 4 ayat 2 (Final)', 'Kewajiban', 'Hutang', 'Kredit', 5200000, 5200000],
            ['2-1340', 'Hutang Pajak PPN Keluaran', 'Kewajiban', 'Hutang', 'Kredit', 42000000, 42000000],
            ['1-1500', 'PPN Masukan (Dapat Dikreditkan)', 'Aset Lancar', 'Piutang', 'Debit', 18500000, 18500000],
            ['3-1100', 'Modal Saham Disetor PBS', 'Ekuitas', 'Modal', 'Kredit', 2000000000, 2000000000],
            ['3-2100', 'Laba Ditahan (Retained Earnings)', 'Ekuitas', 'Modal', 'Kredit', 284050000, 284050000],
            ['4-1100', 'Pendapatan Kontrak Jasa & Pengadaan', 'Pendapatan', 'Pendapatan', 'Kredit', 850000000, 850000000],
            ['4-1200', 'Pendapatan Konsultansi & Supervisi', 'Pendapatan', 'Pendapatan', 'Kredit', 250000000, 250000000],
            ['5-1100', 'Beban Pokok Pendapatan (HPP Proyek)', 'Beban', 'Beban', 'Debit', 520000000, 520000000],
            ['6-1100', 'Beban Gaji, Honor & Tunjangan', 'Beban', 'Beban', 'Debit', 185000000, 185000000],
            ['6-1200', 'Beban Operasional Kantor & Utilitas', 'Beban', 'Beban', 'Debit', 34500000, 34500000],
            ['6-1300', 'Beban Transportasi & Perjalanan Dinas', 'Beban', 'Beban', 'Debit', 22000000, 22000000],
            ['6-1400', 'Beban Pemeliharaan & Legalitas', 'Beban', 'Beban', 'Debit', 12500000, 12500000],
            ['7-1100', 'Beban Pajak Penghasilan (PPh Badan)', 'Beban', 'Beban', 'Debit', 25000000, 25000000],
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

        // 4. Sample Transaksi Jurnal Umum
        $jurnal1 = JurnalUmum::updateOrCreate(
            ['no_transaksi' => 'JU-2026-0001'],
            [
                'tanggal' => now()->subDays(5),
                'tipe_jurnal' => 'Kas Masuk',
                'deskripsi' => 'Penerimaan pembayaran Termin 1 Proyek Pengadaan Sistem Klien PT Mitra Sejahtera',
                'sumber_referensi' => 'INV-PBS-2026-001',
                'total_debit' => 275000000,
                'total_kredit' => 275000000,
                'created_by' => 'Kurniawan, S.E.',
                'is_posted' => true,
            ]
        );

        JurnalDetail::updateOrCreate(
            ['id_jurnal' => $jurnal1->id_jurnal, 'kode_akun' => '1-1200'],
            [
                'keterangan_baris' => 'Kas Masuk Bank Mandiri',
                'debit' => 275000000,
                'kredit' => 0,
            ]
        );
        JurnalDetail::updateOrCreate(
            ['id_jurnal' => $jurnal1->id_jurnal, 'kode_akun' => '4-1100'],
            [
                'keterangan_baris' => 'Pendapatan Kontrak Termin 1',
                'debit' => 0,
                'kredit' => 275000000,
            ]
        );

        // 5. Sample Modul Perpajakan (Tax Management)
        TransaksiPajak::updateOrCreate(
            ['kode_referensi' => 'TAX-2026-001'],
            [
                'jenis_pajak' => 'PPN_KELUARAN',
                'masa_pajak' => 'September 2026',
                'tahun_pajak' => 2026,
                'tanggal_faktur_potong' => now()->subDays(10),
                'nomor_dokumen' => '010.002-26.00000123',
                'lawan_transaksi' => 'PT Mitra Sejahtera Sentosa',
                'npwp_lawan_transaksi' => '02.456.789.1-012.000',
                'dpp' => 250000000,
                'tarif_persen' => 11,
                'nominal_pajak' => 27500000,
                'status_bayar' => 'Sudah Disetor',
                'ntpn' => 'A1B2C3D4E5F6G7H8',
                'tanggal_setor' => now()->subDays(3),
                'status_lapor' => 'Sudah Dilapor',
                'bpe_spt' => 'BPE-DJP-2026-09-9871',
                'tanggal_lapor' => now()->subDays(2),
                'catatan' => 'Faktur PPN Keluaran Proyek Tahap I diverifikasi oleh BOD Tax',
                'reviewed_by' => 'Kurniawan, S.E. (BOD)',
            ]
        );

        TransaksiPajak::updateOrCreate(
            ['kode_referensi' => 'TAX-2026-002'],
            [
                'jenis_pajak' => 'PPH_23',
                'masa_pajak' => 'September 2026',
                'tahun_pajak' => 2026,
                'tanggal_faktur_potong' => now()->subDays(8),
                'nomor_dokumen' => 'BPU-PPH23-2026-042',
                'lawan_transaksi' => 'CV Konsultan Dinamika Solusi',
                'npwp_lawan_transaksi' => '03.111.222.3-602.000',
                'dpp' => 45000000,
                'tarif_persen' => 2,
                'nominal_pajak' => 900000,
                'status_bayar' => 'Sudah Disetor',
                'ntpn' => 'K9L8M7N6P5Q4R3S2',
                'tanggal_setor' => now()->subDays(2),
                'status_lapor' => 'Belum Dilapor',
                'catatan' => 'Pemotongan PPh Pasal 23 Jasa Pemeliharaan IT & Server',
                'reviewed_by' => 'Kurniawan, S.E. (BOD)',
            ]
        );

        // 6. Sample Pengajuan Dana & Approval Anggaran (BOD Finance Approval)
        PengajuanDana::updateOrCreate(
            ['nomor_pengajuan' => 'REQ-PBS-2026-001'],
            [
                'tanggal_pengajuan' => now()->subDays(3),
                'pemohon' => 'Bambang Irawan (IT Ops)',
                'departemen' => 'Operations',
                'kategori_biaya' => 'Operasional & Server',
                'keperluan' => 'Perpanjangan dedicated server cloud & backup storage enterprise PBS Q4',
                'nominal_diajukan' => 18500000,
                'nominal_disetujui' => 18500000,
                'status' => 'Disetujui BOD',
                'catatan_bod' => 'Disetujui sesuai pagu anggaran Q4. Pencairan via transfer Bank Mandiri.',
                'approved_at' => now()->subDays(1),
                'approved_by' => 'Kurniawan, S.E., Ak., CA. (BOD Finance)',
                'metode_pencairan' => 'Transfer Mandiri',
                'no_bukti_cair' => 'TRF-MDR-88912',
            ]
        );

        PengajuanDana::updateOrCreate(
            ['nomor_pengajuan' => 'REQ-PBS-2026-002'],
            [
                'tanggal_pengajuan' => now()->subDays(1),
                'pemohon' => 'Siti Rahmawati (Finance Staff)',
                'departemen' => 'Finance & Tax',
                'kategori_biaya' => 'Perjalanan Dinas & Audit',
                'keperluan' => 'Biaya dinas pendampingan audit perpajakan & supervisi aset lapangan',
                'nominal_diajukan' => 7500000,
                'nominal_disetujui' => 0,
                'status' => 'Menunggu Approval',
                'catatan_bod' => 'Dalam proses review efisiensi anggaran BOD Finance.',
                'approved_at' => null,
                'approved_by' => null,
                'metode_pencairan' => null,
                'no_bukti_cair' => null,
            ]
        );

        // 7. Sample Proyek & Invoicing Kontrak Klien
        $proyek = Proyek::updateOrCreate(
            ['kode_proyek' => 'PRJ-PBS-2026-01'],
            [
                'nama_proyek' => 'Implementasi ERP Terpadu & Tata Kelola Keuangan',
                'nama_klien' => 'PT Mitra Sejahtera Sentosa',
                'pic_klien' => 'Ir. Hendra Gunawan',
                'telepon_klien' => '+62 811 2233 4455',
                'tanggal_mulai' => now()->subMonths(1),
                'tanggal_selesai_target' => now()->addMonths(2),
                'nilai_kontrak' => 750000000,
                'total_tertagih' => 275000000,
                'total_terbayar' => 275000000,
                'progress_persen' => 45,
                'status_proyek' => 'Berjalan',
                'keterangan' => 'Supervisi langsung oleh BOD Finance & Tax (Kurniawan)',
            ]
        );

        InvoiceProyek::updateOrCreate(
            ['nomor_invoice' => 'INV-PBS-2026-001'],
            [
                'id_proyek' => $proyek->id,
                'termin_ke' => 'Termin 1 (DP 35%)',
                'tanggal_invoice' => now()->subDays(15),
                'jatuh_tempo' => now()->subDays(5),
                'nominal_tagihan' => 247747748,
                'ppn_nominal' => 27252252,
                'pph_nominal' => 0,
                'total_bersih' => 275000000,
                'status_bayar' => 'Lunas',
                'tanggal_lunas' => now()->subDays(5),
            ]
        );

        // 7. Seed RBAC Roles
        $this->call(RoleSeeder::class);

        // 8. Seed CUGIL Plastic Recycling Module Data
        $this->call(CugilSeeder::class);
    }
}
