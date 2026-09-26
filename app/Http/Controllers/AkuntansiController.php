<?php

namespace App\Http\Controllers;

use App\Helpers\TerbilangHelper;
use App\Models\Akun;
use App\Models\JurnalDetail;
use App\Models\JurnalUmum;
use App\Models\Perusahaan;
use App\Services\KasImportService;
use App\Services\LabaRugiPbsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
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
     * Tambah Akun COA Baru (Khusus Superuser / BOD / Admin)
     */
    public function storeAkun(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Akses Ditolak: Hanya Superuser (BOD / Administrator) yang dapat menambah Chart of Accounts (COA).');
        }

        $request->validate([
            'kode_akun' => 'required|string|max:20|unique:akun,kode_akun',
            'nama_akun' => 'required|string|max:150',
            'kategori' => 'required|string|in:Aset Lancar,Aset Tetap,Kewajiban,Ekuitas,Pendapatan,Beban',
            'tipe_akun' => 'required|string|max:50',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'saldo_awal' => 'nullable|numeric',
        ]);

        $saldoAwal = (float) ($request->saldo_awal ?? 0);

        Akun::create([
            'kode_akun' => trim($request->kode_akun),
            'nama_akun' => trim($request->nama_akun),
            'kategori' => $request->kategori,
            'tipe_akun' => $request->tipe_akun,
            'saldo_normal' => $request->saldo_normal,
            'saldo_awal' => $saldoAwal,
            'saldo_berjalan' => $saldoAwal,
            'is_active' => true,
        ]);

        Cache::forget('active_akuns_dropdown');

        return redirect()->route('akuntansi.index')
            ->with('success', "Akun COA [{$request->kode_akun}] {$request->nama_akun} berhasil ditambahkan ke bagan akun resmi.");
    }

    /**
     * Update Akun COA (Khusus Superuser / BOD / Admin)
     */
    public function updateAkun(Request $request, $kode_akun)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Akses Ditolak: Hanya Superuser (BOD / Administrator) yang dapat mengubah data akun COA.');
        }

        $akun = Akun::where('kode_akun', $kode_akun)->firstOrFail();

        $request->validate([
            'nama_akun' => 'required|string|max:150',
            'kategori' => 'required|string|in:Aset Lancar,Aset Tetap,Kewajiban,Ekuitas,Pendapatan,Beban',
            'tipe_akun' => 'required|string|max:50',
            'saldo_normal' => 'required|in:Debit,Kredit',
            'saldo_awal' => 'nullable|numeric',
        ]);

        $saldoAwalLama = (float) $akun->saldo_awal;
        $saldoAwalBaru = (float) ($request->saldo_awal ?? 0);
        $selisihSaldoAwal = $saldoAwalBaru - $saldoAwalLama;

        $akun->nama_akun = trim($request->nama_akun);
        $akun->kategori = $request->kategori;
        $akun->tipe_akun = $request->tipe_akun;
        $akun->saldo_normal = $request->saldo_normal;
        $akun->saldo_awal = $saldoAwalBaru;
        $akun->saldo_berjalan += $selisihSaldoAwal;
        $akun->save();

        Cache::forget('active_akuns_dropdown');

        return redirect()->route('akuntansi.index')
            ->with('success', "Akun COA [{$akun->kode_akun}] {$akun->nama_akun} berhasil diperbarui.");
    }

    /**
     * Hapus Akun COA (Khusus Superuser / BOD / Admin) dengan Validasi Dependensi
     */
    public function destroyAkun($kode_akun)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Akses Ditolak: Hanya Superuser (BOD / Administrator) yang memiliki wewenang menghapus akun COA.');
        }

        $akun = Akun::withCount('detailJurnal')->where('kode_akun', $kode_akun)->firstOrFail();

        // Validasi: Tidak boleh menghapus akun yang telah memiliki riwayat mutasi / jurnal
        if ($akun->detail_jurnal_count > 0) {
            return back()->with('error', "Akun [{$akun->kode_akun}] {$akun->nama_akun} tidak dapat dihapus karena sudah memiliki {$akun->detail_jurnal_count} transaksi jurnal tercatat. Hapus transaksi terkait terlebih dahulu atau gunakan fitur nonaktif.");
        }

        $namaAkun = $akun->nama_akun;
        $akun->delete();

        Cache::forget('active_akuns_dropdown');

        return redirect()->route('akuntansi.index')
            ->with('success', "Akun COA [{$kode_akun}] {$namaAkun} berhasil dihapus dari bagan akun.");
    }

    /**
     * Nolkan Semua Saldo Awal Akun COA (Khusus Superuser / BOD / Admin)
     */
    public function resetSaldoAwal(Request $request)
    {
        if (!auth()->user()->isAdmin()) {
            return back()->with('error', 'Akses Ditolak: Hanya Superuser (BOD / Administrator) yang dapat me-reset saldo awal.');
        }

        DB::transaction(function () {
            // Set saldo_awal = 0 dan sesuaikan saldo_berjalan (Hanya untuk jurnal yang sudah POSTED)
            $akuns = Akun::all();
            foreach ($akuns as $a) {
                $postedDetails = \App\Models\JurnalDetail::where('kode_akun', $a->kode_akun)
                    ->whereHas('jurnal', function($q) {
                        $q->where('is_posted', true);
                    })->get();
                    
                $totalDebit = $postedDetails->sum('debit');
                $totalKredit = $postedDetails->sum('kredit');

                $a->saldo_awal = 0;
                if ($a->saldo_normal === 'Debit') {
                    $a->saldo_berjalan = $totalDebit - $totalKredit;
                } else {
                    $a->saldo_berjalan = $totalKredit - $totalDebit;
                }
                $a->save();
            }
        });

        return redirect()->route('akuntansi.index')
            ->with('success', 'Seluruh Saldo Awal akun COA berhasil di-NOL-kan (0). Saldo berjalan kini murni bersumber dari mutasi transaksi.');
    }

    public function cleanDummyData()
    {
        @set_time_limit(300);
        @ini_set('memory_limit', '512M');

        try {
            \Illuminate\Support\Facades\Artisan::call('migrate', ['--force' => true]);
            \Illuminate\Support\Facades\Artisan::call('db:clean-dummy');
            return redirect()->route('dashboard')->with('success', 'Seluruh data simulasi (Proyek, Anggaran, Pajak & Jurnal Simulasi) berhasil dibersihkan, dan 1.700+ draf jurnal retroaktif berhasil dibuat!');
        } catch (\Throwable $e) {
            return redirect()->route('dashboard')->with('error', 'Gagal memproses pembersihan: ' . $e->getMessage());
        }
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

        // 1. Hitung Saldo Awal sebelum tanggal_dari (Optimized: Direct Join)
        $preMutations = JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->where('jurnal_umum.tanggal', '<', $tanggalDari)
            ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) as total_debit, COALESCE(SUM(jurnal_detail.kredit), 0) as total_kredit')
            ->first();

        $saldoAwalPeriode = $akun->saldo_awal;
        if ($akun->saldo_normal === 'Debit') {
            $saldoAwalPeriode += (($preMutations->total_debit ?? 0) - ($preMutations->total_kredit ?? 0));
        } else {
            $saldoAwalPeriode += (($preMutations->total_kredit ?? 0) - ($preMutations->total_debit ?? 0));
        }

        // 2. Ambil mutasi dalam periode (Optimized: Direct Join & Selective Columns)
        $mutasiDetails = JurnalDetail::with([
                'jurnal:id_jurnal,no_transaksi,tanggal,tipe_jurnal,deskripsi,sumber_referensi',
                'jurnal.details:id_detail,id_jurnal,kode_akun',
                'jurnal.details.akun:kode_akun,nama_akun'
            ])
            ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->whereBetween('jurnal_umum.tanggal', [$tanggalDari, $tanggalSampai])
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
            $lawanAkun = $item->jurnal ? $item->jurnal->details->where('kode_akun', '!=', $selectedKode)->first() : null;
            $item->akun_lawan_nama = $lawanAkun ? ($lawanAkun->kode_akun . ' - ' . ($lawanAkun->akun->nama_akun ?? '')) : 'Serbaguna / Multi-line';
        }

        $saldoAkhirPeriode = $currentBalance;
        $perusahaan = Perusahaan::first();

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
            'saldoAkhirPeriode',
            'perusahaan'
        ));
    }

    /**
     * Cetak Rekening Koran / Buku Kas & Bank (Print View & PDF - Simple, Fast, Low Memory)
     */
    public function cetakBukuKas(Request $request)
    {
        $selectedKode = $request->query('kode_akun', '1-1100');
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $akun = Akun::where('kode_akun', $selectedKode)->firstOrFail();

        // 1. Hitung Saldo Awal sebelum tanggal_dari
        $preMutations = JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->where('jurnal_umum.tanggal', '<', $tanggalDari)
            ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) as total_debit, COALESCE(SUM(jurnal_detail.kredit), 0) as total_kredit')
            ->first();

        $saldoAwalPeriode = $akun->saldo_awal;
        if ($akun->saldo_normal === 'Debit') {
            $saldoAwalPeriode += (($preMutations->total_debit ?? 0) - ($preMutations->total_kredit ?? 0));
        } else {
            $saldoAwalPeriode += (($preMutations->total_kredit ?? 0) - ($preMutations->total_debit ?? 0));
        }

        // 2. Ambil mutasi dalam periode
        $mutasiDetails = JurnalDetail::with([
                'jurnal:id_jurnal,no_transaksi,tanggal,tipe_jurnal,deskripsi,sumber_referensi',
                'jurnal.details:id_detail,id_jurnal,kode_akun',
                'jurnal.details.akun:kode_akun,nama_akun'
            ])
            ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->whereBetween('jurnal_umum.tanggal', [$tanggalDari, $tanggalSampai])
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
            $lawanAkun = $item->jurnal ? $item->jurnal->details->where('kode_akun', '!=', $selectedKode)->first() : null;
            $item->akun_lawan_nama = $lawanAkun ? ($lawanAkun->kode_akun . ' - ' . ($lawanAkun->akun->nama_akun ?? '')) : 'Serbaguna / Multi-line';
        }

        $saldoAkhirPeriode = $currentBalance;
        $perusahaan = Perusahaan::first();

        return view('akuntansi.cetak-buku-kas', compact(
            'selectedKode',
            'akun',
            'tanggalDari',
            'tanggalSampai',
            'saldoAwalPeriode',
            'mutasiDetails',
            'totalMasuk',
            'totalKeluar',
            'saldoAkhirPeriode',
            'perusahaan'
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

        // 1. Saldo Awal sebelum periode (Optimized: Direct Join)
        $preMutations = JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->where('jurnal_umum.tanggal', '<', $tanggalDari)
            ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) as total_debit, COALESCE(SUM(jurnal_detail.kredit), 0) as total_kredit')
            ->first();

        $saldoAwalPeriode = $akun->saldo_awal;
        if ($akun->saldo_normal === 'Debit') {
            $saldoAwalPeriode += (($preMutations->total_debit ?? 0) - ($preMutations->total_kredit ?? 0));
        } else {
            $saldoAwalPeriode += (($preMutations->total_kredit ?? 0) - ($preMutations->total_debit ?? 0));
        }

        // 2. Mutasi transaksi dalam periode (Optimized: Direct Join & Selective Columns, Low Memory)
        $mutasiDetails = JurnalDetail::with(['jurnal:id_jurnal,no_transaksi,tanggal,tipe_jurnal,deskripsi,sumber_referensi'])
            ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->whereBetween('jurnal_umum.tanggal', [$tanggalDari, $tanggalSampai])
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
        $perusahaan = Perusahaan::first();

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
            'saldoAkhirPeriode',
            'perusahaan'
        ));
    }

    /**
     * Cetak Buku Besar (General Ledger Print View & PDF - Ultra Fast & Low Memory)
     */
    public function cetakBukuBesar(Request $request)
    {
        $selectedKode = $request->query('kode_akun', '1-1100');
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $akun = Akun::where('kode_akun', $selectedKode)->firstOrFail();

        // 1. Saldo Awal sebelum periode
        $preMutations = JurnalDetail::join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->where('jurnal_umum.tanggal', '<', $tanggalDari)
            ->selectRaw('COALESCE(SUM(jurnal_detail.debit), 0) as total_debit, COALESCE(SUM(jurnal_detail.kredit), 0) as total_kredit')
            ->first();

        $saldoAwalPeriode = $akun->saldo_awal;
        if ($akun->saldo_normal === 'Debit') {
            $saldoAwalPeriode += (($preMutations->total_debit ?? 0) - ($preMutations->total_kredit ?? 0));
        } else {
            $saldoAwalPeriode += (($preMutations->total_kredit ?? 0) - ($preMutations->total_debit ?? 0));
        }

        // 2. Mutasi transaksi dalam periode
        $mutasiDetails = JurnalDetail::with(['jurnal:id_jurnal,no_transaksi,tanggal,tipe_jurnal,deskripsi,sumber_referensi'])
            ->join('jurnal_umum', 'jurnal_detail.id_jurnal', '=', 'jurnal_umum.id_jurnal')
            ->where('jurnal_detail.kode_akun', $selectedKode)
            ->whereBetween('jurnal_umum.tanggal', [$tanggalDari, $tanggalSampai])
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
        $perusahaan = Perusahaan::first();

        return view('akuntansi.cetak-buku-besar', compact(
            'selectedKode',
            'akun',
            'tanggalDari',
            'tanggalSampai',
            'saldoAwalPeriode',
            'mutasiDetails',
            'totalDebit',
            'totalKredit',
            'saldoAkhirPeriode',
            'perusahaan'
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
        $tipe = $request->query('tipe');
        $status = $request->query('status');
        $perPage = (int) $request->query('per_page', 15);

        if (!in_array($perPage, [15, 30, 50, 100, 500])) {
            $perPage = 15;
        }

        $query = JurnalUmum::with([
            'details' => function ($q) {
                $q->select('id_detail', 'id_jurnal', 'kode_akun', 'keterangan_baris', 'debit', 'kredit');
            },
            'details.akun' => function ($q) {
                $q->select('kode_akun', 'nama_akun', 'kategori');
            }
        ]);

        if ($tanggalDari && $tanggalSampai) {
            $query->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
        } elseif ($tanggalDari) {
            $query->where('tanggal', '>=', $tanggalDari);
        } elseif ($tanggalSampai) {
            $query->where('tanggal', '<=', $tanggalSampai);
        }

        if ($tipe) {
            $query->where('tipe_jurnal', $tipe);
        }

        if ($status === 'draft') {
            $query->where('is_posted', false);
        } elseif ($status === 'posted') {
            $query->where('is_posted', true);
        }

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('sumber_referensi', 'like', "%{$search}%");
            });
        }

        $jurnals = $query->orderBy('tanggal', 'desc')->orderBy('id_jurnal', 'desc')->paginate($perPage)->withQueryString();

        $akuns = Akun::select('kode_akun', 'nama_akun', 'kategori')
            ->where('is_active', true)
            ->orderBy('kode_akun')
            ->get()
            ->values();

        return view('akuntansi.jurnal', compact('jurnals', 'akuns', 'tanggalDari', 'tanggalSampai', 'search', 'perPage', 'tipe', 'status'));
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

        if ($totalDebit <= 0) {
            return back()->withInput()->with('error', 'Total nilai transaksi jurnal harus lebih dari 0.');
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
                        'keterangan_baris' => !empty($item['keterangan_baris']) ? $item['keterangan_baris'] : $request->deskripsi,
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

    public function approveJurnal(Request $request, $id, \App\Services\JurnalAutoService $jurnalService)
    {
        $jurnal = JurnalUmum::findOrFail($id);
        
        if ($jurnal->is_posted) {
            return back()->with('error', "Jurnal {$jurnal->no_transaksi} sudah di-approve sebelumnya.");
        }

        if ($jurnalService->approveJurnal($jurnal)) {
            return back()->with('success', "Jurnal {$jurnal->no_transaksi} berhasil di-approve dan di-posting ke Buku Besar.");
        }

        return back()->with('error', "Gagal melakukan approve pada Jurnal {$jurnal->no_transaksi}.");
    }

    public function updateJurnal(Request $request, $id, \App\Services\JurnalAutoService $jurnalService)
    {
        $jurnal = JurnalUmum::findOrFail($id);
        
        $rules = [
            'tanggal' => 'required|date',
            'deskripsi' => 'required|string',
            'tipe_jurnal' => 'nullable|string',
            'sumber_referensi' => 'nullable|string',
        ];

        if ($request->has('details')) {
            $rules['details'] = 'required|array|min:2';
            $rules['details.*.kode_akun'] = 'required|exists:akun,kode_akun';
            $rules['details.*.debit'] = 'required|numeric|min:0';
            $rules['details.*.kredit'] = 'required|numeric|min:0';
        }

        $request->validate($rules);

        if ($request->has('details')) {
            $activeDetails = collect($request->details)->filter(function ($item) {
                return ((float)($item['debit'] ?? 0) > 0) || ((float)($item['kredit'] ?? 0) > 0);
            })->values();

            if ($activeDetails->count() < 2) {
                return back()->withInput()->with('error', 'Jurnal harus memiliki minimal 2 baris akun dengan nominal (Debit & Kredit).');
            }

            $totalDebit = $activeDetails->sum('debit');
            $totalKredit = $activeDetails->sum('kredit');

            if (abs($totalDebit - $totalKredit) > 0.01) {
                return back()->withInput()->with('error', 'Transaksi tidak seimbang! Total Debit (Rp ' . number_format($totalDebit, 0, ',', '.') . ') harus sama dengan Total Kredit (Rp ' . number_format($totalKredit, 0, ',', '.') . ').');
            }

            if ($totalDebit <= 0) {
                return back()->withInput()->with('error', 'Total nilai transaksi jurnal harus lebih dari 0.');
            }
        }

        try {
            $jurnalService->updateJurnal($jurnal, $request->all());
            return back()->with('success', "Jurnal {$jurnal->no_transaksi} berhasil diperbarui.");
        } catch (\Exception $e) {
            return back()->withInput()->with('error', 'Gagal memperbarui jurnal: ' . $e->getMessage());
        }
    }

    public function bulkApproveJurnal(Request $request, \App\Services\JurnalAutoService $jurnalService)
    {
        $request->validate([
            'jurnal_ids' => 'required|array',
            'jurnal_ids.*' => 'exists:jurnal_umum,id_jurnal'
        ]);

        $result = $jurnalService->bulkApprove($request->jurnal_ids);

        if ($result['approved'] > 0) {
            $msg = "{$result['approved']} Jurnal berhasil di-approve dan di-posting ke Buku Besar.";
            if ($result['failed'] > 0) {
                $msg .= " Namun {$result['failed']} jurnal gagal di-approve (mungkin sudah diposting sebelumnya).";
            }
            return back()->with('success', $msg);
        }

        return back()->with('error', "Gagal melakukan bulk approve. Pastikan jurnal yang dipilih belum diposting.");
    }

    public function adjustHppCugil(Request $request, \App\Services\JurnalAutoService $jurnalService)
    {
        $request->validate([
            'tanggal' => 'nullable|date',
            'deskripsi' => 'nullable|string|max:255'
        ]);

        $res = $jurnalService->createJurnalPenyesuaianHppCugil($request->tanggal, $request->deskripsi);

        if ($res['success']) {
            return back()->with('success', $res['message']);
        }

        return back()->with('error', $res['message']);
    }

    /**
     * Laporan Keuangan (Laba Rugi & Neraca - SAK EP/EMKM Single & Komparatif Periode)
     */
    public function laporan(Request $request)
    {
        $mode = $request->query('mode', 'single'); // 'single' atau 'komparatif'
        $tanggalDari = $request->query('tanggal_dari', date('Y-m-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        // Default Komparatif: Periode Bulan Sebelumnya
        $tanggalDariKomparatif = $request->query('tanggal_dari_komparatif', date('Y-m-01', strtotime('-1 month', strtotime($tanggalDari))));
        $tanggalSampaiKomparatif = $request->query('tanggal_sampai_komparatif', date('Y-m-t', strtotime('-1 month', strtotime($tanggalDari))));

        $dataUtama = $this->hitungDataKeuanganPeriodik($tanggalDari, $tanggalSampai);
        $dataKomparatif = ($mode === 'komparatif') 
            ? $this->hitungDataKeuanganPeriodik($tanggalDariKomparatif, $tanggalSampaiKomparatif) 
            : null;

        // Laba Rugi Format Standar Manufaktur PBS (A - G)
        $labaRugiService = app(LabaRugiPbsService::class);
        $overrides = $request->only([
            'penjualan_jasa', 'penjualan_barang', 'diskon_penjualan',
            'persediaan_awal_bj', 'persediaan_awal_bb', 'pembelian_bb',
            'ongkos_angkut_pembelian', 'diskon_pembelian_bb', 'stock_akhir_bb',
            'foh_btkl', 'foh_listrik', 'foh_maintenance',
            'ongkos_angkut_penjualan', 'persediaan_akhir_bj', 'komisi_sales', 'komisi_lainnya',
            'gaji_manajemen', 'biaya_administrasi_umum'
        ]);

        $labaRugiPbs = $labaRugiService->hitung($tanggalDari, $tanggalSampai, $overrides);
        $labaRugiPbsKomparatif = ($mode === 'komparatif')
            ? $labaRugiService->hitung($tanggalDariKomparatif, $tanggalSampaiKomparatif)
            : null;

        $perusahaan = Perusahaan::first();

        return view('akuntansi.laporan', compact(
            'mode',
            'tanggalDari',
            'tanggalSampai',
            'tanggalDariKomparatif',
            'tanggalSampaiKomparatif',
            'dataUtama',
            'dataKomparatif',
            'labaRugiPbs',
            'labaRugiPbsKomparatif',
            'perusahaan'
        ));
    }

    /**
     * Cetak Laporan Keuangan Resmi / Standar SAK EP & EMKM (Print View & PDF)
     */
    public function cetakLaporan(Request $request)
    {
        $mode = $request->query('mode', 'single'); // 'single' atau 'komparatif'
        $tanggalDari = $request->query('tanggal_dari', date('Y-m-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        // Default Komparatif: Periode Bulan Sebelumnya
        $tanggalDariKomparatif = $request->query('tanggal_dari_komparatif', date('Y-m-01', strtotime('-1 month', strtotime($tanggalDari))));
        $tanggalSampaiKomparatif = $request->query('tanggal_sampai_komparatif', date('Y-m-t', strtotime('-1 month', strtotime($tanggalDari))));

        $dataUtama = $this->hitungDataKeuanganPeriodik($tanggalDari, $tanggalSampai);
        $dataKomparatif = ($mode === 'komparatif') 
            ? $this->hitungDataKeuanganPeriodik($tanggalDariKomparatif, $tanggalSampaiKomparatif) 
            : null;

        // Laba Rugi Format Standar Manufaktur PBS (A - G)
        $labaRugiService = app(LabaRugiPbsService::class);
        $overrides = $request->only([
            'penjualan_jasa', 'penjualan_barang', 'diskon_penjualan',
            'persediaan_awal_bj', 'persediaan_awal_bb', 'pembelian_bb',
            'ongkos_angkut_pembelian', 'diskon_pembelian_bb', 'stock_akhir_bb',
            'foh_btkl', 'foh_listrik', 'foh_maintenance',
            'ongkos_angkut_penjualan', 'persediaan_akhir_bj', 'komisi_sales', 'komisi_lainnya',
            'gaji_manajemen', 'biaya_administrasi_umum'
        ]);

        $labaRugiPbs = $labaRugiService->hitung($tanggalDari, $tanggalSampai, $overrides);
        $labaRugiPbsKomparatif = ($mode === 'komparatif')
            ? $labaRugiService->hitung($tanggalDariKomparatif, $tanggalSampaiKomparatif)
            : null;

        $perusahaan = Perusahaan::first();

        return view('akuntansi.cetak-laporan', compact(
            'mode',
            'tanggalDari',
            'tanggalSampai',
            'tanggalDariKomparatif',
            'tanggalSampaiKomparatif',
            'dataUtama',
            'dataKomparatif',
            'labaRugiPbs',
            'labaRugiPbsKomparatif',
            'perusahaan'
        ));
    }

    /**
     * Export Laporan Laba Rugi PBS ke File CSV / Excel
     */
    public function exportLabaRugiPbs(Request $request): StreamedResponse
    {
        $tanggalDari = $request->query('tanggal_dari', date('Y-m-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $labaRugiService = app(LabaRugiPbsService::class);
        $overrides = $request->only([
            'penjualan_jasa', 'penjualan_barang', 'diskon_penjualan',
            'persediaan_awal_bj', 'persediaan_awal_bb', 'pembelian_bb',
            'ongkos_angkut_pembelian', 'diskon_pembelian_bb', 'stock_akhir_bb',
            'foh_btkl', 'foh_listrik', 'foh_maintenance',
            'ongkos_angkut_penjualan', 'persediaan_akhir_bj', 'komisi_sales', 'komisi_lainnya',
            'gaji_manajemen', 'biaya_administrasi_umum'
        ]);

        $d = $labaRugiService->hitung($tanggalDari, $tanggalSampai, $overrides);
        $perusahaan = Perusahaan::first();
        $fileName = 'laporan_laba_rugi_pbs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($d, $perusahaan, $tanggalDari, $tanggalSampai) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF"); // UTF-8 BOM

            fputcsv($out, ['LAPORAN LABA RUGI - ' . strtoupper($perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA')]);
            fputcsv($out, ['Periode: ' . date('d/m/Y', strtotime($tanggalDari)) . ' s/d ' . date('d/m/Y', strtotime($tanggalSampai))]);
            fputcsv($out, ['Tanggal Cetak: ' . date('d/m/Y H:i:s')]);
            fputcsv($out, []);

            fputcsv($out, ['No', 'Sub', 'Pos Rekening / Deskripsi Transaksi', 'Rincian (Rp)', 'Sub-Total (Rp)', 'Total (Rp)']);

            // A. PENJUALAN
            fputcsv($out, ['A.', '', 'PENJUALAN', '', '', '']);
            fputcsv($out, ['A.', '1.', 'Penjualan Jasa', $d['A1_penjualan_jasa'], '', '']);
            fputcsv($out, ['A.', '2.', 'Penjualan Barang', $d['A2_penjualan_barang'], '', '']);
            fputcsv($out, ['A.', '3.', 'Diskon Penjualan', -$d['A3_diskon_penjualan'], '', '']);
            fputcsv($out, ['', '', 'PENJUALAN BRUTO', '', $d['penjualan_bruto'], '']);
            fputcsv($out, []);
            fputcsv($out, ['A.', '4.', 'Penjualan Barang Bersih', '', $d['A4_penjualan_barang_bersih'], $d['total_penjualan_bersih']]);
            fputcsv($out, []);

            // B. HPP
            fputcsv($out, ['B.', '', 'HARGA POKOK PENJUALAN', '', '', '']);
            fputcsv($out, ['B.', '1.', 'PERSEDIAAN AWAL BRNG JADI', $d['B1_persediaan_awal_bj'], '', '']);
            fputcsv($out, ['B.', '2.', 'HRG POKOK PRODUKSI', '', '', '']);
            fputcsv($out, ['B.', '2. a.', 'PERSEDIAAN AWAL BAHAN BAKU', $d['B2a_persediaan_awal_bb'], '', '']);
            fputcsv($out, ['B.', '2. b.', 'PEMBELIAN BB', $d['B2b_pembelian_bb'], '', '']);
            fputcsv($out, ['B.', '2. c.', 'ONGKOS ANGKUT PEMBELIAN+TIMBANG', $d['B2c_ongkos_angkut_pembelian'], '', '']);
            fputcsv($out, ['B.', '2. d.', 'DISKON PEMBELIAN BB', -$d['B2d_diskon_pembelian_bb'], '', '']);
            fputcsv($out, ['B.', '2. f.', 'TOTAL PEMBELIAN', '', $d['B2f_total_pembelian'], '']);
            fputcsv($out, ['', '', '(-) STOCK AKHIR BB', -$d['stock_akhir_bb'], '', '']);
            fputcsv($out, []);
            fputcsv($out, ['B.', '3.', 'FOH-BIAYA TENAGA KERJA LANGSUNG', $d['B3_foh_btkl'], '', '']);
            fputcsv($out, ['B.', '4.', 'FOH-LISTRIK', $d['B4_foh_listrik'], '', '']);
            fputcsv($out, ['B.', '5.', 'FOH-MAINTENANCE', $d['B5_foh_maintenance'], '', '']);
            fputcsv($out, ['B.', '6.', 'TOTAL OVERHEAD PABRIK', '', $d['B6_total_overhead_pabrik'], '']);
            fputcsv($out, ['B.', '7.', 'TOTAL HRG POKOK PRODUKSI', '', '', $d['B7_total_hrg_pokok_produksi']]);
            fputcsv($out, ['B.', '8.', 'ONGKOS ANGKUT PENJUALAN', $d['B8_ongkos_angkut_penjualan'], '', '']);
            fputcsv($out, ['B.', '9.', 'PERSEDIAAN AKHIR BRNG JADI', -$d['B9_persediaan_akhir_bj'], '', '']);
            fputcsv($out, ['B.', '10.', 'KOMISI SALES (fee marketing)', $d['B10_komisi_sales'], '', '']);
            fputcsv($out, ['B.', '11.', 'KOMISI LAINNYA (ongkos kuli,satpam)', $d['B11_komisi_lainnya'], '', '']);
            fputcsv($out, ['B.', '12.', 'BIAYA PENJUALAN & STOK AKHIR', '', $d['B12_biaya_penjualan_stok_akhir'], '']);
            fputcsv($out, ['C.', '', 'TOTAL HRG POKOK PENJUALAN [COGS]', '', '', $d['C_total_cogs']]);
            fputcsv($out, []);
            fputcsv($out, ['D.', '', 'LABA / RUGI BRUTO', '', '', $d['D_laba_rugi_bruto']]);
            fputcsv($out, []);
            fputcsv($out, ['E.', '', 'GAJI MANAJEMEN', '', $d['E_gaji_manajemen'], '']);
            fputcsv($out, ['F.', '', 'BIAYA ADMINISTRASI DAN UMUM', '', $d['F_biaya_administrasi_umum'], '']);
            fputcsv($out, []);
            fputcsv($out, ['G.', '', 'NET INCOME (DEFISIT / RUGI)', '', '', $d['G_net_income']]);

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Helper perhitungan saldo & mutasi keuangan periodik per SAK EP/EMKM
     */
    private function hitungDataKeuanganPeriodik($tglDari, $tglSampai)
    {
        // 1. Akun Laba Rugi (Pendapatan & Beban) -> Berdasarkan Mutasi Jurnal POSTED dalam Range [$tglDari, $tglSampai]
        $pendapatanAkuns = Akun::where('kategori', 'Pendapatan')->orderBy('kode_akun')->get();
        $bebanAkuns = Akun::where('kategori', 'Beban')->orderBy('kode_akun')->get();

        $pendapatanData = [];
        $totalPendapatan = 0;
        foreach ($pendapatanAkuns as $p) {
            $mutasi = JurnalDetail::where('kode_akun', $p->kode_akun)
                ->whereHas('jurnal', function ($q) use ($tglDari, $tglSampai) {
                    $q->where('is_posted', true)->whereBetween('tanggal', [$tglDari, $tglSampai]);
                });
            $totalMutasi = (float) $mutasi->sum('kredit') - (float) $mutasi->sum('debit');
            $pendapatanData[$p->kode_akun] = [
                'akun' => $p,
                'nominal' => $totalMutasi
            ];
            $totalPendapatan += $totalMutasi;
        }

        $bebanData = [];
        $totalBeban = 0;
        foreach ($bebanAkuns as $b) {
            $mutasi = JurnalDetail::where('kode_akun', $b->kode_akun)
                ->whereHas('jurnal', function ($q) use ($tglDari, $tglSampai) {
                    $q->where('is_posted', true)->whereBetween('tanggal', [$tglDari, $tglSampai]);
                });
            $totalMutasi = (float) $mutasi->sum('debit') - (float) $mutasi->sum('kredit');
            $bebanData[$b->kode_akun] = [
                'akun' => $b,
                'nominal' => $totalMutasi
            ];
            $totalBeban += $totalMutasi;
        }

        $labaBersih = $totalPendapatan - $totalBeban;

        // 2. Akun Neraca (Aset, Kewajiban, Ekuitas) -> Berdasarkan Saldo Akumulasi per $tglSampai
        $asetLancarAkuns = Akun::where('kategori', 'Aset Lancar')->orderBy('kode_akun')->get();
        $asetTetapAkuns = Akun::where('kategori', 'Aset Tetap')->orderBy('kode_akun')->get();
        $kewajibanAkuns = Akun::where('kategori', 'Kewajiban')->orderBy('kode_akun')->get();
        $ekuitasAkuns = Akun::where('kategori', 'Ekuitas')->orderBy('kode_akun')->get();

        $hitungSaldoNeraca = function ($akunList, $isDebit) use ($tglSampai) {
            $list = [];
            $total = 0;
            foreach ($akunList as $a) {
                $mutasi = JurnalDetail::where('kode_akun', $a->kode_akun)
                    ->whereHas('jurnal', function ($q) use ($tglSampai) {
                        $q->where('is_posted', true)->where('tanggal', '<=', $tglSampai);
                    });
                
                $totDebit = (float) $mutasi->sum('debit');
                $totKredit = (float) $mutasi->sum('kredit');

                if ($isDebit) {
                    $saldo = (float) $a->saldo_awal + $totDebit - $totKredit;
                } else {
                    $saldo = (float) $a->saldo_awal + $totKredit - $totDebit;
                }

                $list[$a->kode_akun] = [
                    'akun' => $a,
                    'nominal' => $saldo
                ];
                $total += $saldo;
            }
            return [$list, $total];
        };

        list($asetLancarData, $totalAsetLancar) = $hitungSaldoNeraca($asetLancarAkuns, true);
        list($asetTetapData, $totalAsetTetap) = $hitungSaldoNeraca($asetTetapAkuns, true);
        list($kewajibanData, $totalKewajiban) = $hitungSaldoNeraca($kewajibanAkuns, false);
        list($ekuitasData, $totalEkuitas) = $hitungSaldoNeraca($ekuitasAkuns, false);

        // Laba Bersih Kumulatif per $tglSampai untuk penyeimbang Neraca
        $pendapatanKumulatif = JurnalDetail::whereHas('akun', function($q){ $q->where('kategori', 'Pendapatan'); })
            ->whereHas('jurnal', function($q) use ($tglSampai){ $q->where('is_posted', true)->where('tanggal', '<=', $tglSampai); });
        $totPendK = (float)$pendapatanKumulatif->sum('kredit') - (float)$pendapatanKumulatif->sum('debit');

        $bebanKumulatif = JurnalDetail::whereHas('akun', function($q){ $q->where('kategori', 'Beban'); })
            ->whereHas('jurnal', function($q) use ($tglSampai){ $q->where('is_posted', true)->where('tanggal', '<=', $tglSampai); });
        $totBebD = (float)$bebanKumulatif->sum('debit') - (float)$bebanKumulatif->sum('kredit');

        $labaBersihKumulatif = $totPendK - $totBebD;

        $totalAset = $totalAsetLancar + $totalAsetTetap;
        $totalKewajibanEkuitas = $totalKewajiban + $totalEkuitas + $labaBersihKumulatif;

        return [
            'pendapatanData' => $pendapatanData,
            'totalPendapatan' => $totalPendapatan,
            'bebanData' => $bebanData,
            'totalBeban' => $totalBeban,
            'labaBersih' => $labaBersih,

            'asetLancarData' => $asetLancarData,
            'totalAsetLancar' => $totalAsetLancar,
            'asetTetapData' => $asetTetapData,
            'totalAsetTetap' => $totalAsetTetap,
            'kewajibanData' => $kewajibanData,
            'totalKewajiban' => $totalKewajiban,
            'ekuitasData' => $ekuitasData,
            'totalEkuitas' => $totalEkuitas,

            'totalAset' => $totalAset,
            'labaBersihKumulatif' => $labaBersihKumulatif,
            'totalKewajibanEkuitas' => $totalKewajibanEkuitas
        ];
    }

    /**
     * Hitung kalkulasi Arus Kas Metode Langsung (SAK EP/EMKM)
     */
    private function hitungDataArusKas($tanggalDari, $tanggalSampai)
    {
        $cashAccounts = Akun::where('tipe_akun', 'Kas & Bank')->orderBy('kode_akun')->get();
        $cashCodes = $cashAccounts->pluck('kode_akun')->toArray();

        // 1. Arus Kas dari Aktivitas Operasi
        $penerimaanPelanggan = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('debit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            })
            ->sum('debit');

        $pembayaranHpp = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', 'like', '5-%')->orWhere('kode_akun', '2-1100')->orWhere('kode_akun', '1-1610');
                  });
            })
            ->sum('kredit');

        $pembayaranGaji = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', '6-1100')->orWhere('kode_akun', '2-1200');
                  });
            })
            ->sum('kredit');

        $pembayaranOperasional = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
                  ->whereHas('details', function ($dq) {
                      $dq->whereIn('kode_akun', ['6-1200', '6-1300', '6-1400']);
                  });
            })
            ->sum('kredit');

        $pembayaranPajak = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', 'like', '2-13%')->orWhere('kode_akun', '7-1100');
                  });
            })
            ->sum('kredit');

        $totalPengeluaranOperasi = $pembayaranHpp + $pembayaranGaji + $pembayaranOperasional + $pembayaranPajak;
        $arusKasOperasi = $penerimaanPelanggan - $totalPengeluaranOperasi;

        // 2. Arus Kas dari Aktivitas Investasi
        $perolehanAset = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('kredit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
                  ->whereHas('details', function ($dq) {
                      $dq->whereIn('kode_akun', ['1-2100', '1-2200']);
                  });
            })
            ->sum('kredit');

        $arusKasInvestasi = -$perolehanAset;

        // 3. Arus Kas dari Aktivitas Pendanaan
        $setoranModal = (float) JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->where('debit', '>', 0)
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)
                  ->whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
                  ->whereHas('details', function ($dq) {
                      $dq->where('kode_akun', '3-1100');
                  });
            })
            ->sum('debit');

        $arusKasPendanaan = $setoranModal;

        $kenaikanKasBersih = $arusKasOperasi + $arusKasInvestasi + $arusKasPendanaan;

        // Saldo Kas per tanggal
        $saldoAwalKas = (float) Akun::whereIn('kode_akun', $cashCodes)->sum('saldo_awal');
        
        $mutasiSebelumnya = JurnalDetail::whereIn('kode_akun', $cashCodes)
            ->whereHas('jurnal', function($q) use ($tanggalDari) {
                $q->where('is_posted', true)->where('tanggal', '<', $tanggalDari);
            });
        $totMutasiAwal = (float) $mutasiSebelumnya->sum('debit') - (float) $mutasiSebelumnya->sum('kredit');
        $saldoAwalPeriode = $saldoAwalKas + $totMutasiAwal;

        $saldoAkhirKas = $saldoAwalPeriode + $kenaikanKasBersih;

        // Rincian Akun Kas & Bank pada Akhir Periode
        $rincianKas = [];
        foreach ($cashAccounts as $akun) {
            $mutasiAkun = JurnalDetail::where('kode_akun', $akun->kode_akun)
                ->whereHas('jurnal', function ($q) use ($tanggalSampai) {
                    $q->where('is_posted', true)->where('tanggal', '<=', $tanggalSampai);
                });
            $saldoAkun = (float) $akun->saldo_awal + ((float) $mutasiAkun->sum('debit') - (float) $mutasiAkun->sum('kredit'));
            $rincianKas[] = [
                'kode_akun' => $akun->kode_akun,
                'nama_akun' => $akun->nama_akun,
                'saldo'     => $saldoAkun,
            ];
        }

        return compact(
            'tanggalDari',
            'tanggalSampai',
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
            'saldoAwalPeriode',
            'saldoAkhirKas',
            'rincianKas'
        );
    }

    /**
     * Laporan Arus Kas (Cash Flow Statement - Direct SAK Method with Date Range)
     */
    public function arusKas(Request $request)
    {
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $data = $this->hitungDataArusKas($tanggalDari, $tanggalSampai);

        return view('akuntansi.arus-kas', $data);
    }

    /**
     * Cetak Laporan Arus Kas Resmi / Standar SAK EP & EMKM (Print View & PDF)
     */
    public function cetakArusKas(Request $request)
    {
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $data = $this->hitungDataArusKas($tanggalDari, $tanggalSampai);
        $data['perusahaan'] = Perusahaan::first();

        return view('akuntansi.cetak-arus-kas', $data);
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

            // 1. Rollback Saldo Berjalan pada setiap Akun (COA) hanya jika sudah diposting
            if ($jurnal->is_posted) {
                foreach ($jurnal->details as $detail) {
                    if (!empty($detail->kode_akun)) {
                        $akun = Akun::where('kode_akun', $detail->kode_akun)->lockForUpdate()->first();
                        if ($akun) {
                            if ($akun->saldo_normal === 'Debit') {
                                $akun->saldo_berjalan -= ((float) $detail->debit - (float) $detail->kredit);
                            } else {
                                $akun->saldo_berjalan -= ((float) $detail->kredit - (float) $detail->debit);
                            }
                            $akun->save();
                        }
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

