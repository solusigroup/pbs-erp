<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        // 1. Definisi template langkah-langkah workflow per modul bisnis
        Schema::create('workflow_definitions', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50);         // cugil_po, cugil_raw, cugil_sales, pengajuan_dana, proyek, pajak, invoice_proyek
            $table->string('step_code', 50);       // e.g. po_created, raw_received, payment_verified
            $table->string('step_name');           // Nama langkah (display)
            $table->text('description')->nullable(); // Penjelasan detail langkah
            $table->integer('step_order');          // Urutan langkah dalam workflow
            $table->boolean('is_required')->default(true); // Wajib dilengkapi?
            $table->string('required_role', 50)->nullable(); // Role yang berhak menyelesaikan (null = siapa saja)
            $table->boolean('is_active')->default(true);
            $table->timestamps();

            $table->unique(['module', 'step_code']);
            $table->index(['module', 'step_order']);
        });

        // 2. Checklist instance per dokumen/transaksi
        Schema::create('workflow_checklists', function (Blueprint $table) {
            $table->id();
            $table->string('module', 50);          // Mengacu ke workflow_definitions.module
            $table->string('reference_type', 100); // Model class: CugilPurchaseOrder, CugilSale, dll
            $table->unsignedBigInteger('reference_id'); // ID record yang dilacak
            $table->string('reference_code', 100); // Nomor dokumen (nomor_po, id_penjualan, dll) - human readable
            $table->string('step_code', 50);       // Mengacu ke workflow_definitions.step_code
            $table->string('step_name');            // Snapshot nama langkah
            $table->integer('step_order');          // Snapshot urutan
            $table->boolean('is_required')->default(true);
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->string('completed_by')->nullable(); // Nama/ID user yang menyelesaikan
            $table->text('notes')->nullable();      // Catatan opsional saat menyelesaikan
            $table->timestamps();

            $table->index(['module', 'reference_id']);
            $table->index(['reference_type', 'reference_id']);
            $table->index('is_completed');
            $table->unique(['module', 'reference_id', 'step_code']);
        });

        // ─── Seed default workflow definitions ───────────────────────────────

        $now = now();

        // CUGIL PO Workflow
        DB::table('workflow_definitions')->insert([
            ['module' => 'cugil_po', 'step_code' => 'po_created', 'step_name' => 'PO Dibuat & Ditandatangani', 'description' => 'Purchase Order dibuat dengan detail barang, harga, dan supplier', 'step_order' => 1, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_po', 'step_code' => 'po_approved', 'step_name' => 'Approval Direksi / BOD', 'description' => 'PO disetujui oleh BOD atau Manager yang berwenang', 'step_order' => 2, 'is_required' => true, 'required_role' => 'bod', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_po', 'step_code' => 'po_sent_supplier', 'step_name' => 'PO Dikirim ke Supplier', 'description' => 'Dokumen PO telah dikirim ke supplier/pemasok', 'step_order' => 3, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_po', 'step_code' => 'po_received', 'step_name' => 'Barang Diterima (RAW Input)', 'description' => 'Bahan baku telah diterima dan dicatat di modul RAW Material', 'step_order' => 4, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_po', 'step_code' => 'po_payment_verified', 'step_name' => 'Pembayaran Diverifikasi', 'description' => 'Pembayaran ke supplier telah diverifikasi dan dicatat di jurnal kas', 'step_order' => 5, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // CUGIL RAW Material Workflow
        DB::table('workflow_definitions')->insert([
            ['module' => 'cugil_raw', 'step_code' => 'raw_received', 'step_name' => 'Bahan Baku Diterima', 'description' => 'Bahan baku diterima dari supplier dengan timbangan dicatat', 'step_order' => 1, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_raw', 'step_code' => 'raw_weighed', 'step_name' => 'Timbangan Diverifikasi', 'description' => 'Berat bruto dan rafaksi dicek ulang dan diverifikasi', 'step_order' => 2, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_raw', 'step_code' => 'raw_invoiced', 'step_name' => 'Invoice Supplier Diterima', 'description' => 'Invoice/tagihan dari supplier diterima dan dicatat', 'step_order' => 3, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_raw', 'step_code' => 'raw_payment', 'step_name' => 'Pembayaran ke Supplier', 'description' => 'Hutang ke supplier telah dibayar lunas atau sesuai termin', 'step_order' => 4, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_raw', 'step_code' => 'raw_journal_posted', 'step_name' => 'Jurnal Pembelian Diposting', 'description' => 'Transaksi dicatat di jurnal akuntansi dan buku besar', 'step_order' => 5, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // CUGIL SALES Workflow
        DB::table('workflow_definitions')->insert([
            ['module' => 'cugil_sales', 'step_code' => 'sales_order_created', 'step_name' => 'Sales Order Dibuat', 'description' => 'Pesanan penjualan dibuat dengan detail barang, harga, dan customer', 'step_order' => 1, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_timbangan', 'step_name' => 'Foto Timbangan Dilampirkan', 'description' => 'Foto slip timbangan telah diunggah sebagai bukti qty pengiriman', 'step_order' => 2, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_surat_jalan', 'step_name' => 'Surat Jalan Diterbitkan', 'description' => 'Surat Jalan telah dicetak dan diserahkan ke driver/ekspedisi', 'step_order' => 3, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_shipped', 'step_name' => 'Barang Dikirim', 'description' => 'Barang sudah dikirim ke customer/buyer', 'step_order' => 4, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_invoiced', 'step_name' => 'Invoice Diterbitkan', 'description' => 'Invoice penjualan telah diterbitkan dan dikirim ke customer', 'step_order' => 5, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_faktur_pajak', 'step_name' => 'Faktur Pajak Diterbitkan', 'description' => 'Faktur pajak keluaran (PPN) telah diterbitkan dan dicatat', 'step_order' => 6, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_payment_received', 'step_name' => 'Pembayaran Diterima', 'description' => 'Pembayaran dari customer telah masuk dan diverifikasi', 'step_order' => 7, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'cugil_sales', 'step_code' => 'sales_journal_posted', 'step_name' => 'Jurnal Penjualan Diposting', 'description' => 'Transaksi penjualan dicatat di jurnal akuntansi', 'step_order' => 8, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Pengajuan Dana Workflow
        DB::table('workflow_definitions')->insert([
            ['module' => 'pengajuan_dana', 'step_code' => 'dana_submitted', 'step_name' => 'Pengajuan Diajukan', 'description' => 'Formulir pengajuan dana telah diisi dan disubmit oleh pemohon', 'step_order' => 1, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pengajuan_dana', 'step_code' => 'dana_reviewed', 'step_name' => 'Review BOD Finance', 'description' => 'Pengajuan sudah direview oleh BOD Finance & Tax', 'step_order' => 2, 'is_required' => true, 'required_role' => 'bod', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pengajuan_dana', 'step_code' => 'dana_approved', 'step_name' => 'Approval / Penolakan BOD', 'description' => 'BOD telah mengambil keputusan: disetujui atau ditolak', 'step_order' => 3, 'is_required' => true, 'required_role' => 'bod', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pengajuan_dana', 'step_code' => 'dana_disbursed', 'step_name' => 'Dana Dicairkan', 'description' => 'Dana sudah ditransfer/dicairkan ke pemohon sesuai metode pencairan', 'step_order' => 4, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pengajuan_dana', 'step_code' => 'dana_journal_posted', 'step_name' => 'Jurnal Pencairan Diposting', 'description' => 'Pencairan dana dicatat di jurnal kas keluar', 'step_order' => 5, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Proyek & Invoice Workflow
        DB::table('workflow_definitions')->insert([
            ['module' => 'proyek', 'step_code' => 'prj_registered', 'step_name' => 'Proyek Didaftarkan', 'description' => 'Data proyek dan kontrak klien telah diinput ke sistem', 'step_order' => 1, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'proyek', 'step_code' => 'prj_contract_signed', 'step_name' => 'Kontrak Ditandatangani', 'description' => 'Kontrak proyek telah ditandatangani oleh kedua belah pihak', 'step_order' => 2, 'is_required' => true, 'required_role' => 'bod', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'proyek', 'step_code' => 'prj_invoice_issued', 'step_name' => 'Invoice Termin Diterbitkan', 'description' => 'Invoice termin telah diterbitkan dan dikirim ke klien', 'step_order' => 3, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'proyek', 'step_code' => 'prj_payment_received', 'step_name' => 'Pembayaran Klien Diterima', 'description' => 'Pembayaran dari klien telah masuk ke rekening perusahaan', 'step_order' => 4, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'proyek', 'step_code' => 'prj_completed', 'step_name' => 'Proyek Selesai / BAST', 'description' => 'Berita Acara Serah Terima (BAST) telah ditandatangani', 'step_order' => 5, 'is_required' => true, 'required_role' => 'bod', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);

        // Pajak Workflow
        DB::table('workflow_definitions')->insert([
            ['module' => 'pajak', 'step_code' => 'tax_recorded', 'step_name' => 'Transaksi Pajak Dicatat', 'description' => 'Faktur pajak atau bukti potong telah diinput ke sistem', 'step_order' => 1, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pajak', 'step_code' => 'tax_verified', 'step_name' => 'Verifikasi DPP & Tarif', 'description' => 'DPP, tarif, dan nominal pajak telah diverifikasi kebenarannya', 'step_order' => 2, 'is_required' => true, 'required_role' => 'bod', 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pajak', 'step_code' => 'tax_paid', 'step_name' => 'Pajak Disetor ke Negara', 'description' => 'Pajak telah dibayar/disetor dan NTPN dicatat', 'step_order' => 3, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pajak', 'step_code' => 'tax_reported', 'step_name' => 'SPT Masa Dilaporkan', 'description' => 'SPT Masa telah dilaporkan ke DJP dan BPE diterima', 'step_order' => 4, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
            ['module' => 'pajak', 'step_code' => 'tax_archived', 'step_name' => 'Dokumen Diarsipkan', 'description' => 'Semua dokumen pajak telah diarsipkan secara digital & fisik', 'step_order' => 5, 'is_required' => true, 'required_role' => null, 'is_active' => true, 'created_at' => $now, 'updated_at' => $now],
        ]);
    }

    public function down(): void
    {
        Schema::dropIfExists('workflow_checklists');
        Schema::dropIfExists('workflow_definitions');
    }
};
