<?php

use App\Http\Controllers\AkuntansiController;
use App\Http\Controllers\CugilBarangController;
use App\Http\Controllers\CugilCustomerController;
use App\Http\Controllers\CugilSupplierController;
use App\Http\Controllers\CugilTransaksiController;
use App\Http\Controllers\AnalisisController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\LaporanController;
use App\Http\Controllers\PajakController;
use App\Http\Controllers\PengajuanDanaController;
use App\Http\Controllers\PerusahaanController;
use App\Http\Controllers\ProyekController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| PBS-ERP Web Routes
|--------------------------------------------------------------------------
|
| Routing untuk ERP Internal PT Pinastika Bhakti Semesta.
| Landing page bersifat publik, semua modul lain dilindungi middleware auth.
|
*/

// ─── PUBLIC ──────────────────────────────────────────────────────────────────
Route::get('/', function () {
    $perusahaan = App\Models\Perusahaan::first();
    $directors = App\Models\CompanyDirector::where('is_active', true)->orderBy('urutan')->get();
    return view('landing', compact('perusahaan', 'directors'));
})->name('home');

// ─── AUTHENTICATED / INTERNAL ────────────────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // ── Dashboard Executive ──────────────────────────────────────────────────
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // ── Modul CUGIL (Cuci Giling Plastik & Daur Ulang) ────────────────────────
    Route::prefix('cugil')->name('cugil.')->group(function () {
        // Master Data Supplier
        Route::prefix('supplier')->name('supplier.')->group(function () {
            Route::get('/',         [CugilSupplierController::class, 'index'])->name('index');
            Route::post('/',        [CugilSupplierController::class, 'store'])->name('store');
            Route::put('/{id}',     [CugilSupplierController::class, 'update'])->name('update');
            Route::delete('/{id}',  [CugilSupplierController::class, 'destroy'])->name('destroy');
        });

        // Master Data Customer / Kastamer
        Route::prefix('customer')->name('customer.')->group(function () {
            Route::get('/',         [CugilCustomerController::class, 'index'])->name('index');
            Route::post('/',        [CugilCustomerController::class, 'store'])->name('store');
            Route::put('/{id}',     [CugilCustomerController::class, 'update'])->name('update');
            Route::delete('/{id}',  [CugilCustomerController::class, 'destroy'])->name('destroy');
        });

        // Master Daftar Barang
        Route::prefix('barang')->name('barang.')->group(function () {
            Route::get('/',         [CugilBarangController::class, 'index'])->name('index');
            Route::post('/',        [CugilBarangController::class, 'store'])->name('store');
            Route::put('/{id}',     [CugilBarangController::class, 'update'])->name('update');
            Route::delete('/{id}',  [CugilBarangController::class, 'destroy'])->name('destroy');
        });

        // Transaksi: Purchase Order (CUGIL PO)
        Route::prefix('po')->name('po.')->group(function () {
            Route::get('/',             [CugilTransaksiController::class, 'purchaseOrders'])->name('index');
            Route::post('/',            [CugilTransaksiController::class, 'storePurchaseOrder'])->name('store');
            Route::get('/{id}/print',   [CugilTransaksiController::class, 'printPurchaseOrder'])->name('print');
            Route::delete('/{id}',      [CugilTransaksiController::class, 'destroyPurchaseOrder'])->name('destroy');
        });

        // Transaksi: Terima Bahan Baku (CUGIL RAW)
        Route::prefix('raw')->name('raw.')->group(function () {
            Route::get('/',                 [CugilTransaksiController::class, 'rawMaterials'])->name('index');
            Route::post('/',                [CugilTransaksiController::class, 'storeRawMaterial'])->name('store');
            Route::put('/{id}/status',      [CugilTransaksiController::class, 'updateRawMaterialStatus'])->name('updateStatus');
            Route::get('/{id}/tanda-terima',[CugilTransaksiController::class, 'printTandaTerimaRaw'])->name('tanda-terima');
            Route::delete('/{id}',          [CugilTransaksiController::class, 'destroyRawMaterial'])->name('destroy');
        });

        // Transaksi: Penjualan Hasil Cuci Giling (CUGIL SALES)
        Route::prefix('sales')->name('sales.')->group(function () {
            Route::get('/',                 [CugilTransaksiController::class, 'sales'])->name('index');
            Route::post('/',                [CugilTransaksiController::class, 'storeSale'])->name('store');
            Route::put('/{id}/status',      [CugilTransaksiController::class, 'updateSaleStatus'])->name('updateStatus');
            Route::post('/{id}/upload-timbangan', [CugilTransaksiController::class, 'uploadFotoTimbangan'])->name('uploadTimbangan');
            Route::delete('/{id}/delete-timbangan', [CugilTransaksiController::class, 'deleteFotoTimbangan'])->name('deleteTimbangan');
            Route::get('/{id}/so',          [CugilTransaksiController::class, 'printSalesOrder'])->name('so');
            Route::get('/{id}/surat-jalan', [CugilTransaksiController::class, 'printSuratJalan'])->name('surat-jalan');
            Route::get('/{id}/invoice',     [CugilTransaksiController::class, 'printInvoice'])->name('invoice');
            Route::get('/{id}/faktur-pajak',[CugilTransaksiController::class, 'printFakturPajak'])->name('faktur-pajak');
            Route::delete('/{id}',          [CugilTransaksiController::class, 'destroySale'])->name('destroy');
        });
    });

    // ── Modul Akuntansi (SimpleAkunting 3-6) ──────────────────────────────────
    Route::prefix('akuntansi')->name('akuntansi.')->group(function () {
        Route::get('/',                    [AkuntansiController::class, 'index'])->name('index');
        
        // Jurnal Kas & Bank (BKM, BKK, Mutasi Transfer)
        Route::get('/jurnal-kas',          [AkuntansiController::class, 'jurnalKas'])->name('jurnal-kas');
        Route::post('/jurnal-kas/masuk',   [AkuntansiController::class, 'storeKasMasuk'])->name('storeKasMasuk');
        Route::post('/jurnal-kas/keluar',  [AkuntansiController::class, 'storeKasKeluar'])->name('storeKasKeluar');
        Route::post('/jurnal-kas/transfer',[AkuntansiController::class, 'storeTransferKas'])->name('storeTransferKas');
        Route::get('/jurnal-kas/template', [AkuntansiController::class, 'downloadTemplateKas'])->name('downloadTemplateKas');
        Route::post('/jurnal-kas/import',  [AkuntansiController::class, 'importExcelKas'])->name('importExcelKas');
        
        // Buku Kas & Bank (Mutasi & Running Balance)
        Route::get('/buku-kas',            [AkuntansiController::class, 'bukuKas'])->name('buku-kas');
        
        // Buku Besar (General Ledger per COA)
        Route::get('/buku-besar',          [AkuntansiController::class, 'bukuBesar'])->name('buku-besar');

        // Jurnal Umum Memorial
        Route::get('/jurnal',              [AkuntansiController::class, 'jurnal'])->name('jurnal');
        Route::post('/jurnal',             [AkuntansiController::class, 'storeJurnal'])->name('storeJurnal');

        // Hapus Jurnal & Rollback Saldo Buku Besar
        Route::delete('/jurnal/{id}',      [AkuntansiController::class, 'destroyJurnal'])->name('destroyJurnal');

        // Laporan Keuangan (Laba Rugi & Neraca)
        Route::get('/laporan',             [AkuntansiController::class, 'laporan'])->name('laporan');

        // Laporan Arus Kas Direct Method
        Route::get('/arus-kas',            [AkuntansiController::class, 'arusKas'])->name('arus-kas');

        // Cetak Bukti / Voucher
        Route::get('/voucher/{id}',        [AkuntansiController::class, 'printVoucher'])->name('voucher');
    });


    // ── Modul Pajak ──────────────────────────────────────────────────────────
    Route::prefix('pajak')->name('pajak.')->group(function () {
        Route::get('/',            [PajakController::class, 'index'])->name('index');
        Route::post('/',           [PajakController::class, 'store'])->name('store');
        Route::put('/{id}/status', [PajakController::class, 'updateStatus'])->name('updateStatus');
    });

    // ── Modul Anggaran / Pengajuan Dana ──────────────────────────────────────
    Route::prefix('anggaran')->name('anggaran.')->group(function () {
        Route::get('/',              [PengajuanDanaController::class, 'index'])->name('index');
        Route::post('/',             [PengajuanDanaController::class, 'store'])->name('store');
        Route::put('/{id}/approve',  [PengajuanDanaController::class, 'approve'])->name('approve');
        Route::put('/{id}/reject',   [PengajuanDanaController::class, 'reject'])->name('reject');
    });

    // ── Modul Proyek & Invoice ───────────────────────────────────────────────
    Route::prefix('proyek')->name('proyek.')->group(function () {
        Route::get('/',                     [ProyekController::class, 'index'])->name('index');
        Route::post('/',                    [ProyekController::class, 'store'])->name('store');
        Route::post('/{id}/invoice',        [ProyekController::class, 'storeInvoice'])->name('storeInvoice');
    });

    // ── Modul Laporan & Rekapitulasi ────────────────────────────────────────
    Route::prefix('laporan')->name('laporan.')->group(function () {
        Route::get('/',                     [LaporanController::class, 'index'])->name('index');
        Route::get('/cugil-rekap',          [LaporanController::class, 'cugilRekap'])->name('cugil-rekap');
        Route::get('/cugil-rekap/export',   [LaporanController::class, 'exportCugilRekap'])->name('cugil-rekap.export');
        Route::get('/cugil-rekap/print',    [LaporanController::class, 'printCugilRekap'])->name('cugil-rekap.print');

        Route::get('/pembelian',            [LaporanController::class, 'pembelian'])->name('pembelian');
        Route::get('/pembelian/export',     [LaporanController::class, 'exportPembelian'])->name('pembelian.export');
        Route::get('/pembelian/print',      [LaporanController::class, 'printPembelian'])->name('pembelian.print');

        Route::get('/penjualan',            [LaporanController::class, 'penjualan'])->name('penjualan');
        Route::get('/penjualan/export',     [LaporanController::class, 'exportPenjualan'])->name('penjualan.export');
        Route::get('/penjualan/print',      [LaporanController::class, 'printPenjualan'])->name('penjualan.print');

        Route::get('/keuangan',             [LaporanController::class, 'keuangan'])->name('keuangan');
        Route::get('/keuangan/export',      [LaporanController::class, 'exportKeuangan'])->name('keuangan.export');
        Route::get('/keuangan/print',       [LaporanController::class, 'printKeuangan'])->name('keuangan.print');

        Route::get('/pajak',                [LaporanController::class, 'pajak'])->name('pajak');
        Route::get('/pajak/export',         [LaporanController::class, 'exportPajak'])->name('pajak.export');
        Route::get('/pajak/print',          [LaporanController::class, 'printPajak'])->name('pajak.print');
    });

    // ── Modul Analisis & Business Intelligence ───────────────────────────────
    Route::prefix('analisis')->name('analisis.')->group(function () {
        Route::get('/', [AnalisisController::class, 'index'])->name('index');
    });

    // ── Modul Manajemen User & Otoritas (RBAC) ──────────────────────────────
    Route::middleware(['role:bod,admin'])->group(function () {
        // Manajemen Pengguna
        Route::prefix('users')->name('users.')->group(function () {
            Route::get('/',                    [App\Http\Controllers\UserController::class, 'index'])->name('index');
            Route::post('/',                   [App\Http\Controllers\UserController::class, 'store'])->name('store');
            Route::put('/{id}',                [App\Http\Controllers\UserController::class, 'update'])->name('update');
            Route::patch('/{id}/toggle-status',[App\Http\Controllers\UserController::class, 'toggleStatus'])->name('toggle-status');
            Route::delete('/{id}',             [App\Http\Controllers\UserController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Role & Hak Akses
        Route::prefix('roles')->name('roles.')->group(function () {
            Route::get('/',                    [App\Http\Controllers\RoleController::class, 'index'])->name('index');
            Route::post('/',                   [App\Http\Controllers\RoleController::class, 'store'])->name('store');
            Route::put('/{id}',                [App\Http\Controllers\RoleController::class, 'update'])->name('update');
            Route::delete('/{id}',             [App\Http\Controllers\RoleController::class, 'destroy'])->name('destroy');
        });
    });

    // ── Profil Perusahaan & Otorisasi Dokumen ──────────────────────────────
    Route::prefix('perusahaan')->name('perusahaan.')->group(function () {
        Route::get('/',                     [PerusahaanController::class, 'index'])->name('index');
        Route::put('/',                     [PerusahaanController::class, 'update'])->name('update');

        // CRUD Board of Directors (BOD)
        Route::post('/directors',           [PerusahaanController::class, 'storeDirector'])->name('directors.store');
        Route::put('/directors/{id}',       [PerusahaanController::class, 'updateDirector'])->name('directors.update');
        Route::delete('/directors/{id}',    [PerusahaanController::class, 'destroyDirector'])->name('directors.destroy');

        // Customisasi Otorisasi Signature Dokumen (SO, Invoice, Faktur Pajak, dll.)
        Route::put('/signatures',           [PerusahaanController::class, 'updateSignatures'])->name('signatures.update');
        Route::post('/signatures',          [PerusahaanController::class, 'storeSignature'])->name('signatures.store');
        Route::delete('/signatures/{id}',   [PerusahaanController::class, 'destroySignature'])->name('signatures.destroy');
        Route::post('/signatures/reset',    [PerusahaanController::class, 'resetSignatures'])->name('signatures.reset');
    });
});

// ─── AUTH ROUTES (Fortify) ───────────────────────────────────────────────────
require __DIR__.'/settings.php';
