<?php

namespace App\Http\Controllers;

use App\Helpers\TerbilangHelper;
use App\Models\Akun;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Perusahaan;
use App\Services\KasImportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AkuntansiController extends Controller
{
    /**
     * Display Chart of Accounts (COA) / Daftar Akun
     */
    public function index(Request $request)
    {
        $kategori = $request->query('kategori');
        $search = $request->query('search');

        $query = Akun::query();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('kode_akun', 'like', "%{$search}%")
                  ->orWhere('nama_akun', 'like', "%{$search}%")
                  ->orWhere('tipe_akun', 'like', "%{$search}%");
            });
        }

        $akuns = $query->orderBy('kode_akun')->get();

        $totalAset = Akun::whereIn('kategori', ['Aset Lancar', 'Aset Tetap'])->sum('saldo_berjalan');
        $totalKewajiban = Akun::where('kategori', 'Kewajiban')->sum('saldo_berjalan');
        $totalEkuitas = Akun::where('kategori', 'Ekuitas')->sum('saldo_berjalan');
        $totalPendapatan = Akun::where('kategori', 'Pendapatan')->sum('saldo_berjalan');
        $totalBeban = Akun::where('kategori', 'Beban')->sum('saldo_berjalan');

        return view('akuntansi.index', compact(
            'akuns',
            'totalAset',
            'totalKewajiban',
            'totalEkuitas',
            'totalPendapatan',
            'totalBeban',
            'kategori',
            'search'
        ));
    }

    /**
     * Jurnal Kas & Bank (BKM - Kas Masuk, BKK - Kas Keluar, Mutasi Transfer)
     * Signature simpleakunting 3-6 architecture.
     */
    public function jurnalKas(Request $request)
    {
        $tipe = $request->query('tipe'); // 'Kas Masuk', 'Kas Keluar', 'Transfer'
        $kodeKas = $request->query('kode_akun_kas');
        $tanggalDari = $request->query('tanggal_dari', date('Y-m-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));
        $search = $request->query('search');

        $query = JurnalUmum::with(['details.akun'])
            ->whereIn('tipe_jurnal', ['Kas Masuk', 'Kas Keluar', 'Transfer'])
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);

        if ($tipe) {
            $query->where('tipe_jurnal', $tipe);
        }

        if ($kodeKas) {
            $query->whereHas('details', function ($q) use ($kodeKas) {
                $q->where('kode_akun', $kodeKas);
            });
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('sumber_referensi', 'like', "%{$search}%");
            });
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('id_jurnal', 'desc')->paginate(20)->withQueryString();

        // Cash Accounts
        $cashAccounts = Akun::where('tipe_akun', 'Kas & Bank')->where('is_active', true)->orderBy('kode_akun')->get();

        // Non-Cash Counterpart Accounts (for dropdowns)
        $counterpartAccounts = Akun::where('tipe_akun', '!=', 'Kas & Bank')->where('is_active', true)->orderBy('kode_akun')->get();

        // Statistics in period
        $statsKasMasuk = JurnalUmum::where('tipe_jurnal', 'Kas Masuk')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('total_debit');

        $statsKasKeluar = JurnalUmum::where('tipe_jurnal', 'Kas Keluar')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('total_kredit');

        $statsTransfer = JurnalUmum::where('tipe_jurnal', 'Transfer')
            ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('total_debit');

        $netCashFlow = $statsKasMasuk - $statsKasKeluar;

        return view('akuntansi.jurnal-kas', compact(
            'jurnals',
            'cashAccounts',
            'counterpartAccounts',
            'tipe',
            'kodeKas',
            'tanggalDari',
            'tanggalSampai',
            'search',
            'statsKasMasuk',
            'statsKasKeluar',
            'statsTransfer',
            'netCashFlow'
        ));
    }

    /**
     * Store Kas Masuk (BKM - Bukti Kas Masuk)
     */
    public function storeKasMasuk(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|string|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'kode_akun_kas' => 'required|exists:akun,kode_akun',
            'diterima_dari' => 'required|string|max:150',
            'deskripsi' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.kode_akun_lawan' => 'required|exists:akun,kode_akun',
            'items.*.nominal' => 'required|numeric|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $totalNominal = collect($request->items)->sum('nominal');

        if ($totalNominal <= 0) {
            return back()->withInput()->with('error', 'Total nominal penerimaan kas harus lebih besar dari 0.');
        }

        DB::transaction(function () use ($request, $totalNominal) {
            // Lock Kas account
            $kasAkun = Akun::where('kode_akun', $request->kode_akun_kas)->lockForUpdate()->firstOrFail();

            $jurnal = JurnalUmum::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'tipe_jurnal' => 'Kas Masuk',
                'deskripsi' => $request->deskripsi,
                'sumber_referensi' => 'BKM / Dari: ' . $request->diterima_dari,
                'total_debit' => $totalNominal,
                'total_kredit' => $totalNominal,
                'created_by' => auth()->user()->name ?? 'Kurniawan, S.E. (BOD)',
                'is_posted' => true,
            ]);

            // 1. Debit Kas / Bank Penerima
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $kasAkun->kode_akun,
                'keterangan_baris' => 'Penerimaan Kas/Bank dari: ' . $request->diterima_dari,
                'debit' => $totalNominal,
                'kredit' => 0,
            ]);

            // Update Saldo Kas
            if ($kasAkun->saldo_normal === 'Debit') {
                $kasAkun->saldo_berjalan += $totalNominal;
            } else {
                $kasAkun->saldo_berjalan -= $totalNominal;
            }
            $kasAkun->save();

            // 2. Kredit Akun Lawan (Pendapatan / Piutang / Modal / Lainnya)
            foreach ($request->items as $item) {
                $nominal = floatval($item['nominal']);
                if ($nominal <= 0) continue;

                $akunLawan = Akun::where('kode_akun', $item['kode_akun_lawan'])->lockForUpdate()->firstOrFail();

                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunLawan->kode_akun,
                    'keterangan_baris' => !empty($item['keterangan']) ? $item['keterangan'] : $request->deskripsi,
                    'debit' => 0,
                    'kredit' => $nominal,
                ]);

                // Update Saldo Akun Lawan
                if ($akunLawan->saldo_normal === 'Kredit') {
                    $akunLawan->saldo_berjalan += $nominal;
                } else {
                    $akunLawan->saldo_berjalan -= $nominal;
                }
                $akunLawan->save();
            }
        });

        return redirect()->route('akuntansi.jurnal-kas', ['tipe' => 'Kas Masuk'])
            ->with('success', 'Bukti Kas Masuk (BKM) ' . $request->no_transaksi . ' sebesar Rp ' . number_format($totalNominal, 0, ',', '.') . ' berhasil dibukukan.');
    }

    /**
     * Store Kas Keluar (BKK - Bukti Kas Keluar)
     */
    public function storeKasKeluar(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|string|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'kode_akun_kas' => 'required|exists:akun,kode_akun',
            'dibayar_kepada' => 'required|string|max:150',
            'deskripsi' => 'required|string|max:255',
            'items' => 'required|array|min:1',
            'items.*.kode_akun_lawan' => 'required|exists:akun,kode_akun',
            'items.*.nominal' => 'required|numeric|min:1',
            'items.*.keterangan' => 'nullable|string|max:255',
        ]);

        $totalNominal = collect($request->items)->sum('nominal');

        if ($totalNominal <= 0) {
            return back()->withInput()->with('error', 'Total nominal pengeluaran kas harus lebih besar dari 0.');
        }

        DB::transaction(function () use ($request, $totalNominal) {
            // Lock Kas account
            $kasAkun = Akun::where('kode_akun', $request->kode_akun_kas)->lockForUpdate()->firstOrFail();

            $jurnal = JurnalUmum::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'tipe_jurnal' => 'Kas Keluar',
                'deskripsi' => $request->deskripsi,
                'sumber_referensi' => 'BKK / Kepada: ' . $request->dibayar_kepada,
                'total_debit' => $totalNominal,
                'total_kredit' => $totalNominal,
                'created_by' => auth()->user()->name ?? 'Kurniawan, S.E. (BOD)',
                'is_posted' => true,
            ]);

            // 1. Debit Akun Lawan (Beban / Hutang / Aset / Lainnya)
            foreach ($request->items as $item) {
                $nominal = floatval($item['nominal']);
                if ($nominal <= 0) continue;

                $akunLawan = Akun::where('kode_akun', $item['kode_akun_lawan'])->lockForUpdate()->firstOrFail();

                JurnalDetail::create([
                    'id_jurnal' => $jurnal->id_jurnal,
                    'kode_akun' => $akunLawan->kode_akun,
                    'keterangan_baris' => !empty($item['keterangan']) ? $item['keterangan'] : $request->deskripsi,
                    'debit' => $nominal,
                    'kredit' => 0,
                ]);

                // Update Saldo Akun Lawan
                if ($akunLawan->saldo_normal === 'Debit') {
                    $akunLawan->saldo_berjalan += $nominal;
                } else {
                    $akunLawan->saldo_berjalan -= $nominal;
                }
                $akunLawan->save();
            }

            // 2. Kredit Kas / Bank Sumber
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $kasAkun->kode_akun,
                'keterangan_baris' => 'Pengeluaran Kas/Bank kepada: ' . $request->dibayar_kepada,
                'debit' => 0,
                'kredit' => $totalNominal,
            ]);

            // Update Saldo Kas
            if ($kasAkun->saldo_normal === 'Debit') {
                $kasAkun->saldo_berjalan -= $totalNominal;
            } else {
                $kasAkun->saldo_berjalan += $totalNominal;
            }
            $kasAkun->save();
        });

        return redirect()->route('akuntansi.jurnal-kas', ['tipe' => 'Kas Keluar'])
            ->with('success', 'Bukti Kas Keluar (BKK) ' . $request->no_transaksi . ' sebesar Rp ' . number_format($totalNominal, 0, ',', '.') . ' berhasil dibukukan.');
    }

    /**
     * Store Mutasi / Transfer Antar Kas & Bank
     */
    public function storeTransferKas(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|string|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'kas_asal' => 'required|exists:akun,kode_akun',
            'kas_tujuan' => 'required|exists:akun,kode_akun|different:kas_asal',
            'nominal' => 'required|numeric|min:1',
            'biaya_admin' => 'nullable|numeric|min:0',
            'deskripsi' => 'required|string|max:255',
        ]);

        $nominal = floatval($request->nominal);
        $biayaAdmin = floatval($request->biaya_admin ?? 0);
        $totalKeluar = $nominal + $biayaAdmin;

        DB::transaction(function () use ($request, $nominal, $biayaAdmin, $totalKeluar) {
            $kasAsal = Akun::where('kode_akun', $request->kas_asal)->lockForUpdate()->firstOrFail();
            $kasTujuan = Akun::where('kode_akun', $request->kas_tujuan)->lockForUpdate()->firstOrFail();

            $jurnal = JurnalUmum::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'tipe_jurnal' => 'Transfer',
                'deskripsi' => $request->deskripsi,
                'sumber_referensi' => 'Mutasi: ' . $kasAsal->nama_akun . ' -> ' . $kasTujuan->nama_akun,
                'total_debit' => $totalKeluar,
                'total_kredit' => $totalKeluar,
                'created_by' => auth()->user()->name ?? 'Kurniawan, S.E. (BOD)',
                'is_posted' => true,
            ]);

            // 1. Debit Kas Tujuan (Masuk)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $kasTujuan->kode_akun,
                'keterangan_baris' => 'Penerimaan transfer dari ' . $kasAsal->nama_akun,
                'debit' => $nominal,
                'kredit' => 0,
            ]);
            $kasTujuan->saldo_berjalan += $nominal;
            $kasTujuan->save();

            // 2. Debit Biaya Admin Bank jika ada
            if ($biayaAdmin > 0) {
                $akunAdmin = Akun::where('kode_akun', '6-1200')->lockForUpdate()->first();
                if (!$akunAdmin) {
                    $akunAdmin = Akun::where('kategori', 'Beban')->first();
                }

                if ($akunAdmin) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $akunAdmin->kode_akun,
                        'keterangan_baris' => 'Biaya administrasi transfer bank',
                        'debit' => $biayaAdmin,
                        'kredit' => 0,
                    ]);
                    $akunAdmin->saldo_berjalan += $biayaAdmin;
                    $akunAdmin->save();
                }
            }

            // 3. Kredit Kas Asal (Keluar)
            JurnalDetail::create([
                'id_jurnal' => $jurnal->id_jurnal,
                'kode_akun' => $kasAsal->kode_akun,
                'keterangan_baris' => 'Transfer keluar ke ' . $kasTujuan->nama_akun,
                'debit' => 0,
                'kredit' => $totalKeluar,
            ]);
            $kasAsal->saldo_berjalan -= $totalKeluar;
            $kasAsal->save();
        });

        return redirect()->route('akuntansi.jurnal-kas', ['tipe' => 'Transfer'])
            ->with('success', 'Mutasi Transfer ' . $request->no_transaksi . ' sebesar Rp ' . number_format($nominal, 0, ',', '.') . ' berhasil dicatat.');
    }

    /**
     * Buku Kas & Bank (Cash & Bank Ledger with Running Balance)
     */
    public function bukuKas(Request $request)
    {
        $cashAccounts = Akun::where('tipe_akun', 'Kas & Bank')->where('is_active', true)->orderBy('kode_akun')->get();

        $selectedKode = $request->query('kode_akun', $cashAccounts->first()->kode_akun ?? '1-1100');
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $akun = Akun::where('kode_akun', $selectedKode)->firstOrFail();

        // 1. Hitung Saldo Awal sebelum tanggal_dari
        $preMutations = JurnalDetail::where('kode_akun', $selectedKode)
            ->whereHas('jurnal', function ($q) use ($tanggalDari) {
                $q->where('tanggal', '<', $tanggalDari);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')
            ->first();

        $saldoAwalPeriode = $akun->saldo_awal;
        if ($akun->saldo_normal === 'Debit') {
            $saldoAwalPeriode += (($preMutations->total_debit ?? 0) - ($preMutations->total_kredit ?? 0));
        } else {
            $saldoAwalPeriode += (($preMutations->total_kredit ?? 0) - ($preMutations->total_debit ?? 0));
        }

        // 2. Ambil mutasi dalam periode
        $mutasiDetails = JurnalDetail::with(['jurnal.details.akun'])
            ->where('kode_akun', $selectedKode)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            })
            ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->orderBy('jurnal_umum.tanggal', 'asc')
            ->orderBy('jurnal_detail.id_detail', 'asc')
            ->select('jurnal_detail.*')
            ->get();

        // 3. Kalkulasi Saldo Berjalan Row-by-Row
        $currentBalance = $saldoAwalPeriode;
        $totalMasuk = 0;
        $totalKeluar = 0;

        foreach ($mutasiDetails as $item) {
            $totalMasuk += $item->debit;
            $totalKeluar += $item->kredit;

            if ($akun->saldo_normal === 'Debit') {
                $currentBalance += ($item->debit - $item->kredit);
            } else {
                $currentBalance += ($item->kredit - $item->debit);
            }
            $item->saldo_berjalan = $currentBalance;

            // Cari nama akun lawan
            $lawanAkun = $item->jurnal->details->where('kode_akun', '!=', $selectedKode)->first();
            $item->akun_lawan_nama = $lawanAkun ? ($lawanAkun->kode_akun . ' - ' . ($lawanAkun->akun->nama_akun ?? '')) : 'Serbaguna / Multi-line';
        }

        $saldoAkhirPeriode = $currentBalance;

        return view('akuntansi.buku-kas', compact(
            'cashAccounts',
            'selectedKode',
            'akun',
            'tanggalDari',
            'tanggalSampai',
            'saldoAwalPeriode',
            'mutasiDetails',
            'totalMasuk',
            'totalKeluar',
            'saldoAkhirPeriode'
        ));
    }

    /**
     * Buku Besar (General Ledger per COA)
     */
    public function bukuBesar(Request $request)
    {
        $allAccounts = Akun::where('is_active', true)->orderBy('kode_akun')->get();

        $selectedKode = $request->query('kode_akun', $allAccounts->first()->kode_akun ?? '1-1100');
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $akun = Akun::where('kode_akun', $selectedKode)->firstOrFail();

        // 1. Saldo Awal sebelum periode
        $preMutations = JurnalDetail::where('kode_akun', $selectedKode)
            ->whereHas('jurnal', function ($q) use ($tanggalDari) {
                $q->where('tanggal', '<', $tanggalDari);
            })
            ->selectRaw('COALESCE(SUM(debit), 0) as total_debit, COALESCE(SUM(kredit), 0) as total_kredit')
            ->first();

        $saldoAwalPeriode = $akun->saldo_awal;
        if ($akun->saldo_normal === 'Debit') {
            $saldoAwalPeriode += (($preMutations->total_debit ?? 0) - ($preMutations->total_kredit ?? 0));
        } else {
            $saldoAwalPeriode += (($preMutations->total_kredit ?? 0) - ($preMutations->total_debit ?? 0));
        }

        // 2. Mutasi transaksi dalam periode
        $mutasiDetails = JurnalDetail::with(['jurnal.details.akun'])
            ->where('kode_akun', $selectedKode)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            })
            ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->orderBy('jurnal_umum.tanggal', 'asc')
            ->orderBy('jurnal_detail.id_detail', 'asc')
            ->select('jurnal_detail.*')
            ->get();

        // 3. Hitung running balance
        $currentBalance = $saldoAwalPeriode;
        $totalDebit = 0;
        $totalKredit = 0;

        foreach ($mutasiDetails as $item) {
            $totalDebit += $item->debit;
            $totalKredit += $item->kredit;

            if ($akun->saldo_normal === 'Debit') {
                $currentBalance += ($item->debit - $item->kredit);
            } else {
                $currentBalance += ($item->kredit - $item->debit);
            }
            $item->saldo_berjalan = $currentBalance;
        }

        $saldoAkhirPeriode = $currentBalance;

        return view('akuntansi.buku-besar', compact(
            'allAccounts',
            'selectedKode',
            'akun',
            'tanggalDari',
            'tanggalSampai',
            'saldoAwalPeriode',
            'mutasiDetails',
            'totalDebit',
            'totalKredit',
            'saldoAkhirPeriode'
        ));
    }

    /**
     * Buku Jurnal Umum Memorial (Double Entry Manual)
     */
    public function jurnal(Request $request)
    {
        $tanggalDari = $request->query('tanggal_dari');
        $tanggalSampai = $request->query('tanggal_sampai');
        $search = $request->query('search');

        $query = JurnalUmum::with('details.akun');

        if ($tanggalDari && $tanggalSampai) {
            $query->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('sumber_referensi', 'like', "%{$search}%");
            });
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('id_jurnal', 'desc')->paginate(15)->withQueryString();
        $akuns = Akun::where('is_active', true)->orderBy('kode_akun')->get();

        return view('akuntansi.jurnal', compact('jurnals', 'akuns', 'tanggalDari', 'tanggalSampai', 'search'));
    }

    /**
     * Store Manual Memorial Journal Entry
     */
    public function storeJurnal(Request $request)
    {
        $request->validate([
            'no_transaksi' => 'required|string|unique:jurnal_umum,no_transaksi',
            'tanggal' => 'required|date',
            'tipe_jurnal' => 'required|string',
            'deskripsi' => 'required|string',
            'details' => 'required|array|min:2',
            'details.*.kode_akun' => 'required|exists:akun,kode_akun',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.kredit' => 'required|numeric|min:0',
        ]);

        $totalDebit = collect($request->details)->sum('debit');
        $totalKredit = collect($request->details)->sum('kredit');

        if (abs($totalDebit - $totalKredit) > 0.01) {
            return back()->withInput()->with('error', 'Transaksi tidak seimbang! Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') harus sama dengan Total Kredit (Rp ' . number_format($totalKredit, 0, ',', '.') . ').');
        }

        DB::transaction(function () use ($request, $totalDebit, $totalKredit) {
            $jurnal = JurnalUmum::create([
                'no_transaksi' => $request->no_transaksi,
                'tanggal' => $request->tanggal,
                'tipe_jurnal' => $request->tipe_jurnal,
                'deskripsi' => $request->deskripsi,
                'sumber_referensi' => $request->sumber_referensi,
                'total_debit' => $totalDebit,
                'total_kredit' => $totalKredit,
                'created_by' => auth()->user()->name ?? 'Kurniawan, S.E. (BOD)',
                'is_posted' => true,
            ]);

            foreach ($request->details as $item) {
                if ($item['debit'] > 0 || $item['kredit'] > 0) {
                    JurnalDetail::create([
                        'id_jurnal' => $jurnal->id_jurnal,
                        'kode_akun' => $item['kode_akun'],
                        'keterangan_baris' => $item['keterangan_baris'] ?? $request->deskripsi,
                        'debit' => $item['debit'],
                        'kredit' => $item['kredit'],
                    ]);

                    // Update Saldo Berjalan Akun with Lock
                    $akun = Akun::where('kode_akun', $item['kode_akun'])->lockForUpdate()->first();
                    if ($akun) {
                        if ($akun->saldo_normal === 'Debit') {
                            $akun->saldo_berjalan += ($item['debit'] - $item['kredit']);
                        } else {
                            $akun->saldo_berjalan += ($item['kredit'] - $item['debit']);
                        }
                        $akun->save();
                    }
                }
            }
        });

        return redirect()->route('akuntansi.jurnal')->with('success', 'Jurnal memorial ' . $request->no_transaksi . ' berhasil dibukukan.');
    }

    /**
     * Laporan Keuangan (Laba Rugi & Neraca)
     */
    public function laporan()
    {
        $pendapatan = Akun::where('kategori', 'Pendapatan')->orderBy('kode_akun')->get();
        $beban = Akun::where('kategori', 'Beban')->orderBy('kode_akun')->get();

        $totalPendapatan = $pendapatan->sum('saldo_berjalan');
        $totalBeban = $beban->sum('saldo_berjalan');
        $labaBersih = $totalPendapatan - $totalBeban;

        $asetLancar = Akun::where('kategori', 'Aset Lancar')->orderBy('kode_akun')->get();
        $asetTetap = Akun::where('kategori', 'Aset Tetap')->orderBy('kode_akun')->get();
        $kewajiban = Akun::where('kategori', 'Kewajiban')->orderBy('kode_akun')->get();
        $ekuitas = Akun::where('kategori', 'Ekuitas')->orderBy('kode_akun')->get();

        $totalAset = $asetLancar->sum('saldo_berjalan') + $asetTetap->sum('saldo_berjalan');
        $totalKewajibanEkuitas = $kewajiban->sum('saldo_berjalan') + $ekuitas->sum('saldo_berjalan') + $labaBersih;

        return view('akuntansi.laporan', compact(
            'pendapatan',
            'beban',
            'totalPendapatan',
            'totalBeban',
            'labaBersih',
            'asetLancar',
            'asetTetap',
            'kewajiban',
            'ekuitas',
            'totalAset',
            'totalKewajibanEkuitas'
        ));
    }

    /**
     * Laporan Arus Kas (Cash Flow Statement - Direct SAK Method)
     */
    public function arusKas(Request $request)
    {
        $tahun = $request->query('tahun', date('Y'));
        $cashCodes = Akun::where('tipe_akun', 'Kas & Bank')->pluck('kode_akun')->toArray();

        // 1. Arus Kas dari Aktivitas Operasi
        // Penerimaan dari Pelanggan & Penjualan (Kas Masuk dengan akun lawan Pendapatan 4-xxx atau Piutang 1-1300)
        $penerimaanPelanggan = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('debit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Masuk')
                  ->whereYear('tanggal', $tahun);
            })
            ->sum('debit');

        // Pembayaran Bahan Baku / HPP (Kas Keluar akun lawan 5-xxx atau Hutang 2-1100)
        $pembayaranHpp = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Keluar')
                  ->whereYear('tanggal', $tahun)
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', 'like', '5-%')->orWhere('kode_akun', '2-1100');
                  });
            })
            ->sum('kredit');

        // Pembayaran Gaji & Karyawan (Kas Keluar lawan 6-1100 atau 2-1200)
        $pembayaranGaji = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Keluar')
                  ->whereYear('tanggal', $tahun)
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', '6-1100')->orWhere('kode_akun', '2-1200');
                  });
            })
            ->sum('kredit');

        // Pembayaran Operasional Kantor, Utilitas & Transport (Kas Keluar lawan 6-1200, 6-1300, 6-1400)
        $pembayaranOperasional = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Keluar')
                  ->whereYear('tanggal', $tahun)
                  ->whereHas('details', function ($dq) {
                      $dq->whereIn('kode_akun', ['6-1200', '6-1300', '6-1400']);
                  });
            })
            ->sum('kredit');

        // Pembayaran Pajak (Kas Keluar lawan 2-13xx atau 7-1100)
        $pembayaranPajak = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Keluar')
                  ->whereYear('tanggal', $tahun)
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', 'like', '2-13%')->orWhere('kode_akun', '7-1100');
                  });
            })
            ->sum('kredit');

        // Total Kas Bersih dari Aktivitas Operasi
        $totalPengeluaranOperasi = $pembayaranHpp + $pembayaranGaji + $pembayaranOperasional + $pembayaranPajak;
        $arusKasOperasi = $penerimaanPelanggan - $totalPengeluaranOperasi;

        // 2. Arus Kas dari Aktivitas Investasi
        // Perolehan Aset Tetap (Kendaraan & Peralatan)
        $perolehanAset = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Keluar')
                  ->whereYear('tanggal', $tahun)
                  ->whereHas('details', function ($dq) {
                      $dq->whereIn('kode_akun', ['1-2100', '1-2200']);
                  });
            })
            ->sum('kredit');

        $arusKasInvestasi = -$perolehanAset;

        // 3. Arus Kas dari Aktivitas Pendanaan
        // Setoran Modal Saham
        $setoranModal = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('debit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tahun) {
                $q->where('tipe_jurnal', 'Kas Masuk')
                  ->whereYear('tanggal', $tahun)
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', '3-1100');
                  });
            })
            ->sum('debit');

        $arusKasPendanaan = $setoranModal;

        // 4. Rekapitulasi Kas & Setara Kas
        $kenaikanKasBersih = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;
        
        $saldoAwalKas = Akun::whereIn('kode_akun', $cashCodes)->sum('saldo_awal');
        $saldoAkhirKas = Akun::whereIn('kode_akun', $cashCodes)->sum('saldo_berjalan');

        return view('akuntansi.arus-kas', compact(
            'tahun',
            'penerimaanPelanggan',
            'pembayaranHpp',
            'pembayaranGaji',
            'pembayaranOperasional',
            'pembayaranPajak',
            'totalPengeluaranOperasi',
            'arusKasOperasi',
            'perolehanAset',
            'arusKasInvestasi',
            'setoranModal',
            'arusKasPendanaan',
            'kenaikanKasBersih',
            'saldoAwalKas',
            'saldoAkhirKas'
        ));
    }

    /**
     * Print Bukti Kas / Jurnal Voucher (BKM / BKK / Transfer / Memorial)
     */
    public function printVoucher($id)
    {
        $jurnal = JurnalUmum::with(['details.akun'])->findOrFail($id);
        $perusahaan = Perusahaan::first();
        $terbilang = TerbilangHelper::make($jurnal->total_debit);

        return view('akuntansi.voucher', compact('jurnal', 'perusahaan', 'terbilang'));
    }

    /**
     * Download Excel / CSV Template for Kas Transactions
     */
    public function downloadTemplateKas(KasImportService $importer): StreamedResponse
    {
        $csvData = $importer->generateTemplateCsv();
        $fileName = 'template_impor_transaksi_kas_pbs_' . date('Ymd') . '.csv';

        return response()->streamDownload(function () use ($csvData) {
            echo $csvData;
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Import Kas Transactions from Excel / CSV
     */
    public function importExcelKas(Request $request, KasImportService $importer)
    {
        $request->validate([
            'file_transaksi' => 'required|file|max:10240', // max 10MB
        ]);

        $file = $request->file('file_transaksi');
        $ext = $file->getClientOriginalExtension();

        if (!in_array(strtolower($ext), ['xlsx', 'xls', 'csv', 'txt'])) {
            return back()->with('error', 'Format file tidak didukung. Harap unggah file Excel (.xlsx / .xls) atau CSV (.csv).');
        }

        try {
            $parsedRows = $importer->parseFile($file->getRealPath(), $ext);

            if (empty($parsedRows)) {
                return back()->with('error', 'File yang diunggah kosong atau format data tidak dapat dibaca.');
            }

            $importedBy = auth()->user()->name ?? 'Impor Excel Kas';
            $result = $importer->importTransactions($parsedRows, $importedBy);

            if ($result['imported_count'] > 0) {
                $msg = 'Berhasil mengimpor ' . $result['imported_count'] . ' transaksi kas dengan total nominal Rp ' . number_format($result['total_nominal'], 0, ',', '.') . '.';
                
                if (!empty($result['errors'])) {
                    $msg .= ' Namun terdapat ' . count($result['errors']) . ' baris yang dilewati karena tidak valid.';
                    return redirect()->route('akuntansi.jurnal-kas')
                        ->with('success', $msg)
                        ->with('import_warnings', $result['errors']);
                }

                return redirect()->route('akuntansi.jurnal-kas')->with('success', $msg);
            } else {
                $errMsg = 'Gagal mengimpor transaksi. ';
                if (!empty($result['errors'])) {
                    $errMsg .= implode(' | ', array_slice($result['errors'], 0, 3));
                }
                return back()->with('error', $errMsg)->with('import_errors', $result['errors']);
            }
        } catch (\Throwable $e) {
            return back()->with('error', 'Terjadi kesalahan sistem saat memproses impor: ' . $e->getMessage());
        }
    }

    /**
     * Hapus Jurnal (Kas/Bank/Memorial) dengan Otomatis Rollback Saldo Buku Besar COA
     */
    public function destroyJurnal($id)
    {
        return DB::transaction(function () use ($id) {
            $jurnal = JurnalUmum::with('details')->where('id_jurnal', $id)->lockForUpdate()->firstOrFail();
            $noTransaksi = $jurnal->no_transaksi;
            $tipeJurnal = $jurnal->tipe_jurnal;
            $detailsCount = $jurnal->details->count();

            // 1. Rollback Saldo Berjalan pada setiap Akun (COA)
            foreach ($jurnal->details as $detail) {
                if (!empty($detail->kode_akun)) {
                    $akun = Akun::where('kode_akun', $detail->kode_akun)->lockForUpdate()->first();
                    if ($akun) {
                        // Kebalikan dari saat posting:
                        // Jika Saldo Normal Debit: Debit menambah (+), Kredit mengurangi (-)
                        // Maka rollback: Debit dikurangkan (-), Kredit ditambahkan (+)
                        if ($akun->saldo_normal === 'Debit') {
                            $akun->saldo_berjalan -= ((float) $detail->debit - (float) $detail->kredit);
                        } else {
                            // Saldo Normal Kredit: Kredit menambah (+), Debit mengurangi (-)
                            // Maka rollback: Kredit dikurangkan (-), Debit ditambahkan (+)
                            $akun->saldo_berjalan -= ((float) $detail->kredit - (float) $detail->debit);
                        }
                        $akun->save();
                    }
                }
            }

            // 2. Hapus baris detail debit/kredit
            $jurnal->details()->delete();

            // 3. Hapus header jurnal
            $jurnal->delete();

            $redirectRoute = str_contains($tipeJurnal, 'Kas') || $tipeJurnal === 'Transfer'
                ? route('akuntansi.jurnal-kas')
                : route('akuntansi.jurnal');

            return redirect($redirectRoute)->with(
                'success',
                "Jurnal [{$noTransaksi}] ({$tipeJurnal}) berhasil dihapus. Saldo berjalan pada {$detailsCount} akun perkiraan (COA) telah otomatis di-rollback."
            );
        });
    }
}

