<?php

use App\Models\Akun;
use App\Models\JurnalDetail;
use Illuminate\Support\Facades\DB;

DB::transaction(function() {
    $akuns = Akun::all();
    foreach($akuns as $a) {
        $postedDetails = JurnalDetail::where('kode_akun', $a->kode_akun)
            ->whereHas('jurnal', function($q){ 
                $q->where('is_posted', true); 
            })->get();
        
        $totDebit = $postedDetails->sum('debit');
        $totKredit = $postedDetails->sum('kredit');
        
        $a->saldo_awal = 0;
        if ($a->saldo_normal === 'Debit') {
            $a->saldo_berjalan = $a->saldo_awal + $totDebit - $totKredit;
        } else {
            $a->saldo_berjalan = $a->saldo_awal + $totKredit - $totDebit;
        }
        $a->save();
    }
});
