<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations for PBS-ERP (PT Pinastika Bhakti Semesta).
     */
    public function up(): void
    {
        // 1. Profil Perusahaan PT Pinastika Bhakti Semesta
        Schema::create('perusahaan', function (Blueprint $table) {
            $table->id();
            $table->string('nama_perusahaan')->default('PT Pinastika Bhakti Semesta');
            $table->string('singkatan', 20)->default('PBS');
            $table->string('npwp', 50)->nullable();
            $table->text('alamat')->nullable();
            $table->string('kota', 100)->default('Mojokerto');
            $table->string('provinsi', 100)->default('Jawa Timur');
            $table->string('telepon', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('website', 100)->nullable();
            $table->string('bank_nama', 50)->nullable();
            $table->string('bank_rekening', 50)->nullable();
            $table->string('bank_atas_nama', 100)->nullable();
            $table->string('bod_finance_tax', 150)->default('Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.');
            $table->timestamps();
        });

        // 2. Chart of Accounts (COA) / Akun Keuangan
        Schema::create('akun', function (Blueprint $table) {
            $table->string('kode_akun', 20)->primary();
            $table->string('nama_akun');
            $table->string('kategori'); // Aset Lancar, Aset Tetap, Kewajiban Jangka Pendek, Modal, Pendapatan, Beban Operasional, Beban Pajak, dll.
            $table->string('tipe_akun', 50); // Kas & Bank, Piutang, Hutang, Modal, Pendapatan, Beban
            $table->string('saldo_normal', 10); // Debit / Kredit
            $table->decimal('saldo_awal', 18, 2)->default(0);
            $table->decimal('saldo_berjalan', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. Jurnal Umum (Header)
        Schema::create('jurnal_umum', function (Blueprint $table) {
            $table->id('id_jurnal');
            $table->string('no_transaksi', 50)->unique();
            $table->date('tanggal');
            $table->string('tipe_jurnal', 30)->default('Umum'); // Umum, Kas Masuk, Kas Keluar, Pajak, Penyesuaian
            $table->text('deskripsi')->nullable();
            $table->string('sumber_referensi', 100)->nullable(); // No Invoice, No Bukti Kas, Pengajuan Dana
            $table->decimal('total_debit', 18, 2)->default(0);
            $table->decimal('total_kredit', 18, 2)->default(0);
            $table->string('created_by')->nullable();
            $table->boolean('is_posted')->default(true);
            $table->timestamps();
        });

        // 4. Jurnal Detail (Lines)
        Schema::create('jurnal_detail', function (Blueprint $table) {
            $table->id('id_detail');
            $table->unsignedBigInteger('id_jurnal');
            $table->string('kode_akun', 20);
            $table->text('keterangan_baris')->nullable();
            $table->decimal('debit', 18, 2)->default(0);
            $table->decimal('kredit', 18, 2)->default(0);
            $table->timestamps();

            $table->foreign('id_jurnal')->references('id_jurnal')->on('jurnal_umum')->onDelete('cascade');
            $table->foreign('kode_akun')->references('kode_akun')->on('akun')->onUpdate('cascade');
        });

        // 5. Modul Perpajakan (Tax Management PT Pinastika Bhakti Semesta)
        Schema::create('transaksi_pajak', function (Blueprint $table) {
            $table->id();
            $table->string('kode_referensi', 50)->unique(); // e.g. TAX-2026-001
            $table->string('jenis_pajak', 30); // PPN_KELUARAN, PPN_MASUKAN, PPH_21, PPH_23, PPH_4_2, PPH_25_29
            $table->string('masa_pajak', 20); // Januari 2026, Februari 2026, dll
            $table->integer('tahun_pajak')->default(2026);
            $table->date('tanggal_faktur_potong');
            $table->string('nomor_dokumen', 100)->nullable(); // No Faktur Pajak / Bukti Potong
            $table->string('lawan_transaksi', 150); // Nama Vendor / Klien / Penerima Penghasilan
            $table->string('npwp_lawan_transaksi', 50)->nullable();
            $table->decimal('dpp', 18, 2)->default(0); // Dasar Pengenaan Pajak
            $table->decimal('tarif_persen', 5, 2)->default(0);
            $table->decimal('nominal_pajak', 18, 2)->default(0);
            $table->string('status_bayar', 30)->default('Belum Disetor'); // Belum Disetor, Sudah Disetor, Kompensasi
            $table->string('ntpn', 50)->nullable(); // Nomor Transaksi Penerimaan Negara
            $table->date('tanggal_setor')->nullable();
            $table->string('status_lapor', 30)->default('Belum Dilapor'); // Belum Dilapor, Sudah Dilapor (SPT)
            $table->string('bpe_spt', 50)->nullable(); // Bukti Penerimaan Elektronik DJP
            $table->date('tanggal_lapor')->nullable();
            $table->text('catatan')->nullable();
            $table->string('reviewed_by')->nullable(); // Reviewed by BOD Tax (Kurniawan)
            $table->timestamps();
        });

        // 6. Modul Pengajuan Dana & Approval Anggaran (BOD Finance Approval)
        Schema::create('pengajuan_dana', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_pengajuan', 50)->unique(); // e.g. REQ-2026-0001
            $table->date('tanggal_pengajuan');
            $table->string('pemohon');
            $table->string('departemen', 50); // Finance & Tax, Operations, HR, Legal, IT
            $table->string('kategori_biaya', 50); // Operasional, Capex, Pajak, Perjalanan Dinas, Project
            $table->string('keperluan');
            $table->decimal('nominal_diajukan', 18, 2);
            $table->decimal('nominal_disetujui', 18, 2)->default(0);
            $table->string('status', 30)->default('Menunggu Approval'); // Menunggu Approval, Disetujui BOD, Ditolak, Dicairkan
            $table->text('catatan_bod')->nullable(); // Catatan Pak Kurniawan (BOD Finance)
            $table->dateTime('approved_at')->nullable();
            $table->string('approved_by')->nullable();
            $table->string('metode_pencairan', 30)->nullable(); // Transfer Mandiri, Kas Kecil, Cek
            $table->string('no_bukti_cair', 50)->nullable();
            $table->timestamps();
        });

        // 7. Modul Proyek & Billing Kontrak Klien PT Pinastika Bhakti Semesta
        Schema::create('proyek', function (Blueprint $table) {
            $table->id();
            $table->string('kode_proyek', 50)->unique(); // PRJ-PBS-2026-01
            $table->string('nama_proyek');
            $table->string('nama_klien');
            $table->string('pic_klien', 100)->nullable();
            $table->string('telepon_klien', 50)->nullable();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai_target')->nullable();
            $table->decimal('nilai_kontrak', 18, 2);
            $table->decimal('total_tertagih', 18, 2)->default(0);
            $table->decimal('total_terbayar', 18, 2)->default(0);
            $table->integer('progress_persen')->default(0);
            $table->string('status_proyek', 30)->default('Berjalan'); // Perencanaan, Berjalan, Selesai, Ditunda
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        // 8. Invoice / Penagihan Termin Proyek
        Schema::create('invoice_proyek', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_invoice', 50)->unique();
            $table->unsignedBigInteger('id_proyek');
            $table->string('termin_ke', 50)->default('Termin 1');
            $table->date('tanggal_invoice');
            $table->date('jatuh_tempo');
            $table->decimal('nominal_tagihan', 18, 2);
            $table->decimal('ppn_nominal', 18, 2)->default(0);
            $table->decimal('pph_nominal', 18, 2)->default(0);
            $table->decimal('total_bersih', 18, 2);
            $table->string('status_bayar', 30)->default('Belum Bayar'); // Belum Bayar, Sebagian, Lunas
            $table->date('tanggal_lunas')->nullable();
            $table->timestamps();

            $table->foreign('id_proyek')->references('id')->on('proyek')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_proyek');
        Schema::dropIfExists('proyek');
        Schema::dropIfExists('pengajuan_dana');
        Schema::dropIfExists('transaksi_pajak');
        Schema::dropIfExists('jurnal_detail');
        Schema::dropIfExists('jurnal_umum');
        Schema::dropIfExists('akun');
        Schema::dropIfExists('perusahaan');
    }
};
