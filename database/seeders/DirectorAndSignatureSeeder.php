<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CompanyDirector;
use App\Models\DocumentSignature;

class DirectorAndSignatureSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Seed Board of Directors (BOD)
        $d1 = CompanyDirector::updateOrCreate(
            ['nama' => 'Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.'],
            [
                'jabatan' => 'Board of Director (Finance & Tax)',
                'nik' => '3516012345670001',
                'npwp' => '08.123.456.7-602.000',
                'email' => 'kurniawan@pinastika.co.id',
                'telepon' => '+62 821 4164 3495',
                'foto' => 'images/ayahrompi.png',
                'keterangan' => 'Kuasa Direksi Bidang Keuangan, Investasi & Kepatuhan Perpajakan',
                'urutan' => 1,
                'is_active' => true,
            ]
        );

        $d2 = CompanyDirector::updateOrCreate(
            ['nama' => 'M. Winardi'],
            [
                'jabatan' => 'Direktur Utama / President Director',
                'nik' => '3516012345670002',
                'npwp' => '08.234.567.8-602.000',
                'email' => 'winardi@pinastika.co.id',
                'telepon' => '+62 812 3456 7890',
                'foto' => null,
                'keterangan' => 'Penanggung Jawab Operasional & Hubungan Kemitraan Strategis',
                'urutan' => 2,
                'is_active' => true,
            ]
        );

        $d3 = CompanyDirector::updateOrCreate(
            ['nama' => 'Ach. Chumaidi'],
            [
                'jabatan' => 'Direktur Operasional & Commercial',
                'nik' => '3516012345670003',
                'npwp' => '08.345.678.9-602.000',
                'email' => 'chumaidi@pinastika.co.id',
                'telepon' => '+62 813 9876 5432',
                'foto' => null,
                'keterangan' => 'Divisi Penjualan RDF & Pengelolaan Pasokan Daur Ulang',
                'urutan' => 3,
                'is_active' => true,
            ]
        );

        // 2. Seed Document Signatures

        // ─── SALES ORDER ─────────────────────────────────────────────────────────────
        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'sales_order', 'posisi_kode' => 'signer_1'],
            [
                'label_judul' => 'Disiapkan Oleh,',
                'nama_penandatangan' => 'Ach. Chumaidi',
                'jabatan_penandatangan' => 'Commercial & Supply PBS',
                'organisasi' => 'PT Pinastika Bhakti Semesta',
                'catatan' => null,
                'director_id' => $d3->id,
                'show_signature_line' => true,
                'urutan' => 1,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'sales_order', 'posisi_kode' => 'signer_2'],
            [
                'label_judul' => 'Disetujui Pemesan,',
                'nama_penandatangan' => 'Procurement / Buyer SIG',
                'jabatan_penandatangan' => 'Divisi Pengadaan Bahan Bakar Alternatif',
                'organisasi' => 'PT Semen Indonesia (Persero) Tbk',
                'catatan' => null,
                'director_id' => null,
                'show_signature_line' => true,
                'urutan' => 2,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'sales_order', 'posisi_kode' => 'signer_3'],
            [
                'label_judul' => 'Disahkan Oleh,',
                'nama_penandatangan' => 'Kurniawan, S.E., Ak., CA., M.Ak.',
                'jabatan_penandatangan' => 'Board of Director (Finance & Tax)',
                'organisasi' => 'PT Pinastika Bhakti Semesta',
                'catatan' => null,
                'director_id' => $d1->id,
                'show_signature_line' => true,
                'urutan' => 3,
            ]
        );

        // ─── COMMERCIAL INVOICE ──────────────────────────────────────────────────────
        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'invoice', 'posisi_kode' => 'signer_1'],
            [
                'label_judul' => 'Disahkan Oleh,',
                'nama_penandatangan' => 'Kurniawan, S.E., Ak., CA., M.Ak.',
                'jabatan_penandatangan' => 'Board of Director (Finance & Tax)',
                'organisasi' => 'PT PINASTIKA BHAKTI SEMESTA',
                'catatan' => 'METERAI ELEKTRONIK / RP 10.000',
                'director_id' => $d1->id,
                'show_signature_line' => true,
                'urutan' => 1,
            ]
        );

        // ─── FAKTUR PAJAK ────────────────────────────────────────────────────────────
        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'faktur_pajak', 'posisi_kode' => 'signer_1'],
            [
                'label_judul' => 'Kuasa Wajib Pajak / Pejabat Penandatangan,',
                'nama_penandatangan' => 'Kurniawan, S.E., Ak., CA., M.Ak.',
                'jabatan_penandatangan' => 'Board of Director (Finance & Tax)',
                'organisasi' => 'PT PINASTIKA BHAKTI SEMESTA',
                'catatan' => 'Mojokerto',
                'director_id' => $d1->id,
                'show_signature_line' => true,
                'urutan' => 1,
            ]
        );

        // ─── SURAT JALAN / DELIVERY ORDER ────────────────────────────────────────────
        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'surat_jalan', 'posisi_kode' => 'signer_1'],
            [
                'label_judul' => 'Petugas Lapangan / Timbangan,',
                'nama_penandatangan' => 'M. Winardi',
                'jabatan_penandatangan' => 'Supervisor Logistik & Timbangan',
                'organisasi' => 'PT Pinastika Bhakti Semesta',
                'catatan' => null,
                'director_id' => $d2->id,
                'show_signature_line' => true,
                'urutan' => 1,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'surat_jalan', 'posisi_kode' => 'signer_2'],
            [
                'label_judul' => 'Pengemudi / Ekspedisi,',
                'nama_penandatangan' => 'Driver Truk',
                'jabatan_penandatangan' => 'Armada Pengangkut RDF',
                'organisasi' => 'Transporter Rekanan PBS',
                'catatan' => null,
                'director_id' => null,
                'show_signature_line' => true,
                'urutan' => 2,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'surat_jalan', 'posisi_kode' => 'signer_3'],
            [
                'label_judul' => 'Pemeriksa Pos Security,',
                'nama_penandatangan' => 'Security Gate',
                'jabatan_penandatangan' => 'Pos Keamanan Pabrik / Hub',
                'organisasi' => 'PT Pinastika Bhakti Semesta',
                'catatan' => null,
                'director_id' => null,
                'show_signature_line' => true,
                'urutan' => 3,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'surat_jalan', 'posisi_kode' => 'signer_4'],
            [
                'label_judul' => 'Diterima Oleh (Pabrik Tuban),',
                'nama_penandatangan' => 'Penerima Pabrik SIG',
                'jabatan_penandatangan' => 'Jembatan Timbang SIG Tuban',
                'organisasi' => 'PT Semen Indonesia (Persero) Tbk',
                'catatan' => null,
                'director_id' => null,
                'show_signature_line' => true,
                'urutan' => 4,
            ]
        );

        // ─── PURCHASE ORDER ──────────────────────────────────────────────────────────
        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'purchase_order', 'posisi_kode' => 'signer_1'],
            [
                'label_judul' => 'Dibuat Oleh,',
                'nama_penandatangan' => 'M. Winardi',
                'jabatan_penandatangan' => 'Procurement & Sourcing Mitra',
                'organisasi' => 'PT Pinastika Bhakti Semesta',
                'catatan' => null,
                'director_id' => $d2->id,
                'show_signature_line' => true,
                'urutan' => 1,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'purchase_order', 'posisi_kode' => 'signer_2'],
            [
                'label_judul' => 'Disetujui Direksi,',
                'nama_penandatangan' => 'Kurniawan, S.E., Ak., CA., M.Ak.',
                'jabatan_penandatangan' => 'Board of Director (Finance & Tax)',
                'organisasi' => 'PT Pinastika Bhakti Semesta',
                'catatan' => null,
                'director_id' => $d1->id,
                'show_signature_line' => true,
                'urutan' => 2,
            ]
        );

        DocumentSignature::updateOrCreate(
            ['jenis_dokumen' => 'purchase_order', 'posisi_kode' => 'signer_3'],
            [
                'label_judul' => 'Diterima & Disetujui Vendor,',
                'nama_penandatangan' => 'Pimpinan TPST Mitra',
                'jabatan_penandatangan' => 'Mitra Pemasok Bahan Baku RDF',
                'organisasi' => 'TPST Mitra Jawa Timur',
                'catatan' => null,
                'director_id' => null,
                'show_signature_line' => true,
                'urutan' => 3,
            ]
        );
    }
}
