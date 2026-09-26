<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        $now = now();

        // 1. Akuntansi — Workflow Kontrol & Approval Jurnal
        $jurnalDefinitions = [
            [
                'module'        => 'akuntansi_jurnal',
                'step_code'     => 'jurnal_draft',
                'step_name'     => 'Pencatatan Dokumen Jurnal',
                'description'   => 'Jurnal dicatat (manual memorial atau otomatis dari transaksi CUGIL/Kas) dengan status Draft',
                'step_order'    => 1,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_jurnal',
                'step_code'     => 'jurnal_balance_checked',
                'step_name'     => 'Validasi Keseimbangan Debit = Kredit & COA',
                'description'   => 'Memastikan total debit sama dengan kredit dan kode akun sesuai Standar Akuntansi PBS',
                'step_order'    => 2,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_jurnal',
                'step_code'     => 'jurnal_supporting_doc',
                'step_name'     => 'Verifikasi Dokumen Bukti & Referensi',
                'description'   => 'Dokumen pendukung (nota/kuitansi/faktur/slip timbangan) telah diverifikasi keabsahannya',
                'step_order'    => 3,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_jurnal',
                'step_code'     => 'jurnal_approved_posted',
                'step_name'     => 'Approval BOD & Posting ke Buku Besar (GL)',
                'description'   => 'Persetujuan resmi Direksi (BOD Finance) dan pembaruan saldo berjalan pada General Ledger',
                'step_order'    => 4,
                'is_required'   => true,
                'required_role' => 'bod',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_jurnal',
                'step_code'     => 'jurnal_voucher_archived',
                'step_name'     => 'Penerbitan Bukti Voucher & Pengarsipan',
                'description'   => 'Voucher jurnal resmi dicetak atau diarsipkan secara tertib untuk kebutuhan audit',
                'step_order'    => 5,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        // 2. Akuntansi — Workflow Siklus Tutup Buku Bulanan
        $closingDefinitions = [
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_ops_recorded',
                'step_name'     => 'Pencatatan Transaksi Operasional Lengkap',
                'description'   => 'Seluruh PO, Penerimaan RAW, Penjualan CUGIL, dan Kas periode ini tuntas dibukukan',
                'step_order'    => 1,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_draft_cleared',
                'step_name'     => 'Kliring Jurnal Draft (0 Belum Approve)',
                'description'   => 'Seluruh jurnal berstatus draft telah di-approve BOD, tidak ada yang tertinggal',
                'step_order'    => 2,
                'is_required'   => true,
                'required_role' => 'bod',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_hpp_adjusted',
                'step_name'     => 'Penyesuaian HPP CUGIL & Stok Akhir',
                'description'   => 'Eksekusi Jurnal Penyesuaian HPP akhir bulan untuk zeroing akumulasi persediaan bahan baku',
                'step_order'    => 3,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_cash_reconciled',
                'step_name'     => 'Rekonsiliasi Kas & Rekening Koran Bank',
                'description'   => 'Saldo buku kas dan bank di sistem sinkron dengan rekening koran bank fisik',
                'step_order'    => 4,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_trial_balance',
                'step_name'     => 'Pemeriksaan Keseimbangan Neraca Saldo',
                'description'   => 'Memastikan total debit = total kredit pada Trial Balance dan tidak ada saldo anomali',
                'step_order'    => 5,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_tax_finalized',
                'step_name'     => 'Finalisasi & Penyetoran Pajak Periode',
                'description'   => 'PPN Masa & PPh terutang telah disetor dan dilaporkan sesuai SPT',
                'step_order'    => 6,
                'is_required'   => true,
                'required_role' => null,
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
            [
                'module'        => 'akuntansi_closing',
                'step_code'     => 'close_reports_approved',
                'step_name'     => 'Pengesahan Laporan Keuangan oleh BOD',
                'description'   => 'Laba Rugi Manufaktur PBS A-G, Neraca, dan Arus Kas disahkan oleh Direksi',
                'step_order'    => 7,
                'is_required'   => true,
                'required_role' => 'bod',
                'is_active'     => true,
                'created_at'    => $now,
                'updated_at'    => $now,
            ],
        ];

        DB::table('workflow_definitions')->insertOrIgnore(array_merge($jurnalDefinitions, $closingDefinitions));
    }

    public function down(): void
    {
        DB::table('workflow_definitions')->whereIn('module', ['akuntansi_jurnal', 'akuntansi_closing'])->delete();
        DB::table('workflow_checklists')->whereIn('module', ['akuntansi_jurnal', 'akuntansi_closing'])->delete();
    }
};
