<?php

namespace App\Console\Commands;

use App\Models\Akun;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;

class DetectAnomaliesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'jurnal:fix-anomalies';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Deteksi dan perbaiki anomali jurnal (header tanpa COA detail, kode akun invalid, dll)';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai deteksi anomali jurnal...');

        $validAkunCodes = Akun::pluck('kode_akun')->toArray();

        // 1. Cari JurnalUmum yang tidak memiliki detail baris ATAU detail barisnya berisi kode_akun invalid/kosong
        $anomalousJurnals = JurnalUmum::whereDoesntHave('details')
            ->orWhereHas('details', function ($q) use ($validAkunCodes) {
                $q->whereNull('kode_akun')
                  ->orWhere('kode_akun', '')
                  ->orWhereNotIn('kode_akun', $validAkunCodes);
            })
            ->get();

        $count = $anomalousJurnals->count();
        $this->warn("Ditemukan {$count} jurnal anomali (tanpa rincian COA / COA invalid).");

        if ($count > 0) {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
            foreach ($anomalousJurnals as $j) {
                $this->line(" - Menghapus jurnal anomali: {$j->no_transaksi} ({$j->sumber_referensi})");
                JurnalDetail::where('id_jurnal', $j->id_jurnal)->delete();
                $j->delete();
            }
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');

            $this->info('Jurnal anomali berhasil dibersihkan.');
        }

        // 2. Jalankan ulang sync-retro untuk mengisi ulang jurnal yang sempat terlewati
        $this->info('Menjalankan ulang generasi jurnal retroaktif CUGIL...');
        Artisan::call('jurnal:sync-retro');

        // 3. Recalculate saldo_berjalan
        $this->info('Merestrukturisasi saldo berjalan seluruh COA...');
        $akuns = Akun::all();
        foreach ($akuns as $a) {
            $postedDetails = JurnalDetail::where('kode_akun', $a->kode_akun)
                ->whereHas('jurnal', function ($q) {
                    $q->where('is_posted', true);
                })->get();

            $totDebit = $postedDetails->sum('debit');
            $totKredit = $postedDetails->sum('kredit');

            if ($a->saldo_normal === 'Debit') {
                $a->saldo_berjalan = (float)$a->saldo_awal + $totDebit - $totKredit;
            } else {
                $a->saldo_berjalan = (float)$a->saldo_awal + $totKredit - $totDebit;
            }
            $a->save();
        }

        $this->info('Proses perbaikan anomali selesai 100%!');
        return 0;
    }
}
