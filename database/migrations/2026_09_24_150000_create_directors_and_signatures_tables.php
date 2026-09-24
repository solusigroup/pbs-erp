<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Table Board of Directors (BOD) & Pengurus Korporasi
        Schema::create('company_directors', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 150);
            $table->string('jabatan', 100);
            $table->string('nik', 50)->nullable();
            $table->string('npwp', 50)->nullable();
            $table->string('email', 100)->nullable();
            $table->string('telepon', 50)->nullable();
            $table->string('foto', 255)->nullable();
            $table->string('tanda_tangan', 255)->nullable();
            $table->text('keterangan')->nullable();
            $table->integer('urutan')->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. Table Customisasi Otorisasi Signature Dokumen Komersial (SO, Invoice, Faktur Pajak, DO, PO)
        Schema::create('document_signatures', function (Blueprint $table) {
            $table->id();
            $table->string('jenis_dokumen', 50); // sales_order, invoice, faktur_pajak, surat_jalan, purchase_order, raw_material
            $table->string('posisi_kode', 50);  // signer_1, signer_2, signer_3, signer_4
            $table->string('label_judul', 100); // Disiapkan Oleh,, Disahkan Oleh,, Disetujui Pemesan,, Kuasa Wajib Pajak, dll.
            $table->string('nama_penandatangan', 150);
            $table->string('jabatan_penandatangan', 100);
            $table->string('organisasi', 150)->nullable(); // PT Pinastika Bhakti Semesta, PT Semen Indonesia Group, dll.
            $table->text('catatan')->nullable(); // METERAI ELEKTRONIK / RP 10.000, Mojokerto, dll.
            $table->unsignedBigInteger('director_id')->nullable();
            $table->boolean('show_signature_line')->default(true);
            $table->integer('urutan')->default(1);
            $table->timestamps();

            $table->foreign('director_id')->references('id')->on('company_directors')->nullOnDelete();
            $table->index(['jenis_dokumen', 'urutan']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('document_signatures');
        Schema::dropIfExists('company_directors');
    }
};
