<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // 1. SUPPLIER
        Schema::create('cugil_suppliers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_supplier', 20)->unique();
            $table->string('nama_supplier', 100);
            $table->text('alamat')->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->text('item_barang')->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->decimal('hutang', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 2. CUSTOMER / KASTAMER
        Schema::create('cugil_customers', function (Blueprint $table) {
            $table->id();
            $table->string('kode_customer', 20)->unique();
            $table->string('nama_customer', 100);
            $table->text('alamat')->nullable();
            $table->string('kota', 50)->nullable();
            $table->string('telepon', 30)->nullable();
            $table->text('item_barang')->nullable();
            $table->string('bank', 50)->nullable();
            $table->string('no_rekening', 50)->nullable();
            $table->decimal('piutang', 18, 2)->default(0);
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        // 3. DAFTAR BARANG
        Schema::create('cugil_barang', function (Blueprint $table) {
            $table->id();
            $table->string('kode_barang', 20)->unique();
            $table->string('nama_barang', 100);
            $table->string('kategori', 50);
            $table->string('kode_kategori', 10)->nullable();
            $table->string('satuan', 10)->default('Kg');
            $table->decimal('stok_awal', 18, 2)->default(0);
            $table->decimal('barang_masuk', 18, 2)->default(0);
            $table->decimal('barang_keluar', 18, 2)->default(0);
            $table->decimal('stok_akhir', 18, 2)->default(0);
            $table->decimal('harga_beli', 18, 2)->default(0);
            $table->decimal('harga_jual', 18, 2)->default(0);
            $table->string('foto_produk', 255)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
            $table->index('kategori');
        });

        // 4. PURCHASE ORDER (HEADER)
        Schema::create('cugil_purchase_orders', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po', 30)->unique();
            $table->date('tanggal');
            $table->string('kode_supplier', 20);
            $table->string('nama_vendor', 100)->nullable();
            $table->decimal('total_qty', 18, 2)->default(0);
            $table->decimal('total_nilai', 18, 2)->default(0);
            $table->string('termin_payment', 50)->nullable();
            $table->decimal('ongkos_angkut', 18, 2)->default(0);
            $table->date('batas_tanggal')->nullable();
            $table->string('petugas', 150)->nullable();
            $table->text('keterangan')->nullable();
            $table->string('status_terima', 20)->default('Belum'); // YA / Belum
            $table->timestamps();
            $table->index('kode_supplier');
            $table->index('tanggal');
        });

        // 4b. PURCHASE ORDER ITEMS (DETAIL MULTI-SKU)
        Schema::create('cugil_po_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cugil_po_id')->constrained('cugil_purchase_orders')->onDelete('cascade');
            $table->string('kode_barang', 20)->nullable();
            $table->string('nama_barang', 100);
            $table->decimal('qty', 18, 2);
            $table->string('satuan', 10)->default('Kg');
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('harga_total', 18, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('kode_barang');
        });

        // 5. RAW MATERIAL (HEADER TERIMA BAHAN BAKU)
        Schema::create('cugil_raw_materials', function (Blueprint $table) {
            $table->id();
            $table->string('nomor_po', 30)->nullable();
            $table->date('tanggal');
            $table->string('kode_supplier', 20);
            $table->string('nama_pemasok', 100)->nullable();
            $table->string('batch_produksi', 50)->nullable();
            $table->decimal('total_qty', 18, 2)->default(0);
            $table->decimal('total_bruto', 18, 2)->default(0);
            $table->decimal('total_rafaksi', 18, 2)->default(0);
            $table->decimal('ongkos_angkut', 18, 2)->default(0);
            $table->decimal('tagihan', 18, 2)->default(0);
            $table->decimal('payment', 18, 2)->default(0);
            $table->decimal('sisa_tagihan', 18, 2)->default(0);
            $table->string('status_lunas', 20)->default('BELUM LUNAS');
            $table->string('truk', 50)->nullable();
            $table->string('status_truk_lunas', 20)->nullable();
            $table->string('timbangan', 50)->nullable();
            $table->string('invoiced', 20)->default('BELUM');
            $table->string('petugas', 150)->nullable();
            $table->text('remark')->nullable();
            $table->integer('bulan')->nullable();
            $table->integer('tahun')->nullable();
            $table->timestamps();
            $table->index('kode_supplier');
            $table->index('status_lunas');
            $table->index('tanggal');
        });

        // 5b. RAW MATERIAL ITEMS (DETAIL MULTI-SKU BAHAN BAKU)
        Schema::create('cugil_raw_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cugil_raw_id')->constrained('cugil_raw_materials')->onDelete('cascade');
            $table->string('kode_barang', 20)->nullable();
            $table->string('nama_barang', 100);
            $table->decimal('qty', 18, 2);
            $table->string('satuan', 10)->default('Kg');
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('harga_total', 18, 2);
            $table->decimal('diskon_rafaksi', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('kode_barang');
        });

        // 6. PENJUALAN (HEADER CUGIL SALES)
        Schema::create('cugil_sales', function (Blueprint $table) {
            $table->id();
            $table->string('id_penjualan', 50)->unique();
            $table->date('tanggal');
            $table->string('kode_customer', 20);
            $table->string('nama_buyer', 100)->nullable();
            $table->date('tanggal_kirim')->nullable();
            $table->string('sales', 50)->nullable();
            $table->string('broker', 50)->nullable();
            $table->decimal('fee_makelar', 18, 2)->default(0);
            $table->string('truk', 50)->nullable();
            $table->string('status_broker_truk_lunas', 20)->nullable();
            $table->decimal('ongkos_kuli', 18, 2)->default(0);
            $table->decimal('ongkos_angkut', 18, 2)->default(0);
            $table->decimal('down_payment', 18, 2)->default(0);
            $table->decimal('total_qty', 18, 2)->default(0);
            $table->decimal('total_sak', 18, 2)->default(0);
            $table->decimal('total_bruto', 18, 2)->default(0);
            $table->decimal('total_diskon', 18, 2)->default(0);
            $table->decimal('tagihan', 18, 2)->default(0);
            $table->decimal('payment', 18, 2)->default(0);
            $table->decimal('sisa_piutang', 18, 2)->default(0);
            $table->string('status_pelunasan', 20)->default('BELUM LUNAS');
            $table->string('status_timbangan', 20)->nullable();
            $table->string('invoiced', 20)->default('BELUM');
            $table->string('status', 20)->default('TERJUAL');
            $table->text('remark')->nullable();
            $table->timestamps();
            $table->index('kode_customer');
            $table->index('status_pelunasan');
            $table->index('tanggal');
        });

        // 6b. PENJUALAN ITEMS (DETAIL MULTI-SKU PENJUALAN)
        Schema::create('cugil_sale_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('cugil_sale_id')->constrained('cugil_sales')->onDelete('cascade');
            $table->string('kode_barang', 20)->nullable();
            $table->string('nama_barang', 100);
            $table->decimal('qty_gudang', 18, 2)->default(0);
            $table->decimal('qty_terjual', 18, 2)->default(0);
            $table->integer('jumlah_sak')->default(0);
            $table->decimal('harga_satuan', 18, 2);
            $table->decimal('harga_total', 18, 2);
            $table->decimal('diskon_persen', 5, 2)->default(0);
            $table->decimal('diskon_rupiah', 18, 2)->default(0);
            $table->decimal('subtotal', 18, 2);
            $table->text('catatan')->nullable();
            $table->timestamps();
            $table->index('kode_barang');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('cugil_sale_items');
        Schema::dropIfExists('cugil_sales');
        Schema::dropIfExists('cugil_raw_items');
        Schema::dropIfExists('cugil_raw_materials');
        Schema::dropIfExists('cugil_po_items');
        Schema::dropIfExists('cugil_purchase_orders');
        Schema::dropIfExists('cugil_barang');
        Schema::dropIfExists('cugil_customers');
        Schema::dropIfExists('cugil_suppliers');
    }
};
