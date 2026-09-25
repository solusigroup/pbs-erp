<?php

namespace App\Console\Commands;

use App\Models\Akun;
use App\Models\InvoiceProyek;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\PengajuanDana;
use App\Models\Proyek;
use App\Models\TransaksiPajak;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CleanDummyDataCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'db:clean-dummy';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Bersihkan semua data simulasi (Proyek, Anggaran, Pajak, Jurnal Simulasi) dan hitung ulang saldo COA';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Memulai pembersihan data simulasi...');

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // 1. Bersihkan tabel modul
        Proyek::truncate();
        InvoiceProyek::truncate();
        PengajuanDana::truncate();
        TransaksiPajak::truncate();

        // 2. Hapus jurnal simulasi awal jika ada
        $dummyJurnals = JurnalUmum::where('no_transaksi', 'JU-2026-0001')
            ->orWhere('deskripsi', 'like', '%PT Mitra Sejahtera%')
            ->get();

        foreach ($dummyJurnals as $j) {
            JurnalDetail::where('id_jurnal', $j->id_jurnal)->delete();
            $j->delete();
        }

        // 3. Recalculate saldo_berjalan pada seluruh COA berdasarkan jurnal POSTED saja
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

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');

        $this->info('Pembersihan data simulasi selesai dan saldo COA berhasil di-recalculating!');
        return 0;
    }
}
