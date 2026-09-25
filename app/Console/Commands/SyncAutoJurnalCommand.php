<?php

namespace App\Console\Commands;

use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Services\JurnalAutoService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class SyncAutoJurnalCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jurnal:sync-retro {--year= : Tahun spesifik jika ingin filter}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate draft jurnal otomatis untuk transaksi CUGIL yang sudah ada di database (retroaktif).';

    /**
     * Execute the console command.
     */
    public function handle(JurnalAutoService $jurnalService)
    {
        $year = $this->option('year');
        
        $this->info('Memulai proses sinkronisasi jurnal retroaktif...');

        // 1. Sinkronisasi Pembelian Bahan Baku (Raw Material)
        $rawQuery = CugilRawMaterial::query();
        if ($year) {
            $rawQuery->whereYear('tanggal', $year);
        }
        $rawMaterials = $rawQuery->get();

        $this->info('Memproses ' . $rawMaterials->count() . ' data Pembelian Bahan Baku...');
        $countBeli = 0;
        
        $bar = $this->output->createProgressBar($rawMaterials->count());
        foreach ($rawMaterials as $raw) {
            // JurnalAutoService memiliki fungsi anti-duplikasi, jadi aman jika dijalankan berulang
            if ($jurnalService->createJurnalPembelian($raw)) {
                $countBeli++;
            }
            $bar->advance();
        }
        $bar->finish();
        $this->newLine();
        $this->info("Berhasil membuat {$countBeli} jurnal pembelian baru.");

        // 2. Sinkronisasi Penjualan (Sales)
        $saleQuery = CugilSale::query();
        if ($year) {
            $saleQuery->whereYear('tanggal', $year);
        }
        $sales = $saleQuery->get();

        $this->info('Memproses ' . $sales->count() . ' data Penjualan...');
        $countJual = 0;
        
        $bar2 = $this->output->createProgressBar($sales->count());
        foreach ($sales as $sale) {
            if ($jurnalService->createJurnalPenjualan($sale)) {
                $countJual++;
            }
            $bar2->advance();
        }
        $bar2->finish();
        $this->newLine();
        $this->info("Berhasil membuat {$countJual} jurnal penjualan baru.");

        $this->info('Selesai! Semua data historis telah digenerate jurnalnya ke status DRAFT.');
    }
}
