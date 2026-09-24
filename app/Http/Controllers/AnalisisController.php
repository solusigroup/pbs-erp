<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\CugilBarang;
use App\Models\CugilCustomer;
use App\Models\CugilPurchaseOrder;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\CugilSupplier;
use App\Models\JurnalUmum;
use App\Models\Perusahaan;
use App\Models\TransaksiPajak;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AnalisisController extends Controller
{
    /**
     * Dashboard Analisis & Business Intelligence (BI) Eksekutif BOD
     * Dilengkapi Analisis Komparatif Penjualan vs Pembelian Tahun ke Tahun (YoY)
     */
    public function index(Request $request)
    {
        $perusahaan = Perusahaan::first();

        // ── 0. DAFTAR TAHUN TERSEDIA & PARAMETER TAHUN KOMPARATIF ───────────────
        $salesYears = CugilSale::selectRaw('YEAR(tanggal) as y')->pluck('y');
        $rawYears = CugilRawMaterial::selectRaw('YEAR(tanggal) as y')->pluck('y');
        
        $availableYears = collect([$salesYears, $rawYears])
            ->flatten()
            ->filter()
            ->unique()
            ->sortDesc()
            ->values()
            ->toArray();

        if (empty($availableYears)) {
            $availableYears = [(int)date('Y'), (int)date('Y') - 1];
        }

        // Tahun Utama ($tahun) & Tahun Pembanding ($tahunBanding)
        $defaultTahun = in_array((int)date('Y'), $availableYears) ? (int)date('Y') : (int)($availableYears[0] ?? 2026);
        $tahun = (int)$request->input('tahun', $defaultTahun);

        // Cari default tahun pembanding (tahun sebelumnya yang tersedia)
        $defaultTahunBanding = $tahun - 1;
        if (!in_array($defaultTahunBanding, $availableYears)) {
            $filteredYears = array_values(array_filter($availableYears, fn($y) => $y != $tahun));
            $defaultTahunBanding = $filteredYears[0] ?? $tahun;
        }
        $tahunBanding = (int)$request->input('tahun_banding', $defaultTahunBanding);

        // ── 1. RINGKASAN METRIK UTAMA (KPIs) ──────────────────────────────────
        $totalBahanMasuk = CugilBarang::where('kategori', 'BAHAN BAKU')->sum('barang_masuk');
        $totalHasilCugil = CugilBarang::where('kategori', 'CUCI GILING')->sum('barang_masuk');
        
        // Rendemen Cuci Giling (% output cacahan bersih terhadap input bahan mentah)
        $rendemenPersen = $totalBahanMasuk > 0 ? round(($totalHasilCugil / $totalBahanMasuk) * 100, 1) : 82.5;
        $susutPersen = max(0, 100 - $rendemenPersen);

        // Omset & Pengeluaran (All-time / Total Akumulatif)
        $totalPenjualan = CugilSale::sum('tagihan');
        $totalPembelian = CugilRawMaterial::sum('tagihan');
        $totalLabaKotor = max(0, $totalPenjualan - $totalPembelian);
        $grossMarginPersen = $totalPenjualan > 0 ? round(($totalLabaKotor / $totalPenjualan) * 100, 1) : 0;

        // Piutang (AR) & Hutang (AP)
        $totalPiutang = CugilSale::sum(DB::raw('tagihan - payment'));
        $totalHutang = CugilRawMaterial::sum(DB::raw('tagihan - payment'));
        $piutangKritis = CugilSale::where('status_pelunasan', '!=', 'LUNAS')
            ->whereDate('tanggal', '<', Carbon::now()->subDays(30))
            ->sum(DB::raw('tagihan - payment'));

        // ── 2. METRIK KOMPARATIF TAHUN UTAMA VS TAHUN PEMBANDING (YoY) ─────────
        $salesTahun1 = CugilSale::whereYear('tanggal', $tahun)
            ->selectRaw('SUM(tagihan) as total_rp, SUM(total_qty) as total_qty, COUNT(*) as total_trx')
            ->first();
        $salesTahun2 = CugilSale::whereYear('tanggal', $tahunBanding)
            ->selectRaw('SUM(tagihan) as total_rp, SUM(total_qty) as total_qty, COUNT(*) as total_trx')
            ->first();

        $rawTahun1 = CugilRawMaterial::whereYear('tanggal', $tahun)
            ->selectRaw('SUM(tagihan) as total_rp, SUM(total_qty) as total_qty, COUNT(*) as total_trx')
            ->first();
        $rawTahun2 = CugilRawMaterial::whereYear('tanggal', $tahunBanding)
            ->selectRaw('SUM(tagihan) as total_rp, SUM(total_qty) as total_qty, COUNT(*) as total_trx')
            ->first();

        $jual1 = (float)($salesTahun1->total_rp ?? 0);
        $jualQty1 = (float)($salesTahun1->total_qty ?? 0);
        $jualTrx1 = (int)($salesTahun1->total_trx ?? 0);

        $jual2 = (float)($salesTahun2->total_rp ?? 0);
        $jualQty2 = (float)($salesTahun2->total_qty ?? 0);
        $jualTrx2 = (int)($salesTahun2->total_trx ?? 0);

        $beli1 = (float)($rawTahun1->total_rp ?? 0);
        $beliQty1 = (float)($rawTahun1->total_qty ?? 0);
        $beliTrx1 = (int)($rawTahun1->total_trx ?? 0);

        $beli2 = (float)($rawTahun2->total_rp ?? 0);
        $beliQty2 = (float)($rawTahun2->total_qty ?? 0);
        $beliTrx2 = (int)($rawTahun2->total_trx ?? 0);

        $margin1 = $jual1 - $beli1;
        $margin2 = $jual2 - $beli2;

        $marginPct1 = $jual1 > 0 ? round(($margin1 / $jual1) * 100, 1) : 0;
        $marginPct2 = $jual2 > 0 ? round(($margin2 / $jual2) * 100, 1) : 0;

        // Delta & Persentase Pertumbuhan YoY
        $jualDeltaRp = $jual1 - $jual2;
        $jualDeltaPct = $jual2 > 0 ? round(($jualDeltaRp / $jual2) * 100, 1) : ($jual1 > 0 ? 100 : 0);

        $jualDeltaQty = $jualQty1 - $jualQty2;
        $jualDeltaQtyPct = $jualQty2 > 0 ? round(($jualDeltaQty / $jualQty2) * 100, 1) : ($jualQty1 > 0 ? 100 : 0);

        $beliDeltaRp = $beli1 - $beli2;
        $beliDeltaPct = $beli2 > 0 ? round(($beliDeltaRp / $beli2) * 100, 1) : ($beli1 > 0 ? 100 : 0);

        $beliDeltaQty = $beliQty1 - $beliQty2;
        $beliDeltaQtyPct = $beliQty2 > 0 ? round(($beliDeltaQty / $beliQty2) * 100, 1) : ($beliQty1 > 0 ? 100 : 0);

        $marginDeltaRp = $margin1 - $margin2;
        $marginDeltaPct = $margin2 != 0 ? round(($marginDeltaRp / abs($margin2)) * 100, 1) : ($margin1 != 0 ? 100 : 0);

        $yoyMetrics = [
            'tahun1' => $tahun,
            'tahun2' => $tahunBanding,
            'sales1' => $jual1,
            'sales2' => $jual2,
            'sales_qty1' => $jualQty1,
            'sales_qty2' => $jualQty2,
            'sales_trx1' => $jualTrx1,
            'sales_trx2' => $jualTrx2,
            'sales_delta_rp' => $jualDeltaRp,
            'sales_delta_pct' => $jualDeltaPct,
            'sales_delta_qty' => $jualDeltaQty,
            'sales_delta_qty_pct' => $jualDeltaQtyPct,

            'raw1' => $beli1,
            'raw2' => $beli2,
            'raw_qty1' => $beliQty1,
            'raw_qty2' => $beliQty2,
            'raw_trx1' => $beliTrx1,
            'raw_trx2' => $beliTrx2,
            'raw_delta_rp' => $beliDeltaRp,
            'raw_delta_pct' => $beliDeltaPct,
            'raw_delta_qty' => $beliDeltaQty,
            'raw_delta_qty_pct' => $beliDeltaQtyPct,

            'margin1' => $margin1,
            'margin2' => $margin2,
            'margin_pct1' => $marginPct1,
            'margin_pct2' => $marginPct2,
            'margin_delta_rp' => $marginDeltaRp,
            'margin_delta_pct' => $marginDeltaPct,
        ];

        // ── 3. MATRIKS KOMPARATIF 12 BULAN (MONTH-BY-MONTH YoY) ────────────────
        $monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'Mei', 'Jun', 'Jul', 'Agu', 'Sep', 'Okt', 'Nov', 'Des'];
        $bulanLongNames = ['Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember'];

        $salesM1 = CugilSale::selectRaw('MONTH(tanggal) as bln, SUM(tagihan) as total')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bln')
            ->pluck('total', 'bln');

        $salesM2 = CugilSale::selectRaw('MONTH(tanggal) as bln, SUM(tagihan) as total')
            ->whereYear('tanggal', $tahunBanding)
            ->groupBy('bln')
            ->pluck('total', 'bln');

        $rawM1 = CugilRawMaterial::selectRaw('MONTH(tanggal) as bln, SUM(tagihan) as total')
            ->whereYear('tanggal', $tahun)
            ->groupBy('bln')
            ->pluck('total', 'bln');

        $rawM2 = CugilRawMaterial::selectRaw('MONTH(tanggal) as bln, SUM(tagihan) as total')
            ->whereYear('tanggal', $tahunBanding)
            ->groupBy('bln')
            ->pluck('total', 'bln');

        $monthlyPenjualan1 = [];
        $monthlyPenjualan2 = [];
        $monthlyPembelian1 = [];
        $monthlyPembelian2 = [];
        $monthlyMargin1 = [];
        $monthlyMargin2 = [];
        $monthlyYoYMatrix = [];

        for ($m = 1; $m <= 12; $m++) {
            $s1 = (float)($salesM1[$m] ?? 0);
            $s2 = (float)($salesM2[$m] ?? 0);
            $sDelta = $s1 - $s2;
            $sGrow = $s2 > 0 ? round(($sDelta / $s2) * 100, 1) : ($s1 > 0 ? 100 : 0);

            $r1 = (float)($rawM1[$m] ?? 0);
            $r2 = (float)($rawM2[$m] ?? 0);
            $rDelta = $r1 - $r2;
            $rGrow = $r2 > 0 ? round(($rDelta / $r2) * 100, 1) : ($r1 > 0 ? 100 : 0);

            $m1 = $s1 - $r1;
            $m2 = $s2 - $r2;
            $mDelta = $m1 - $m2;
            $mGrow = $m2 != 0 ? round(($mDelta / abs($m2)) * 100, 1) : ($m1 != 0 ? 100 : 0);

            $monthlyPenjualan1[] = $s1;
            $monthlyPenjualan2[] = $s2;
            $monthlyPembelian1[] = $r1;
            $monthlyPembelian2[] = $r2;
            $monthlyMargin1[] = $m1;
            $monthlyMargin2[] = $m2;

            $monthlyYoYMatrix[] = [
                'bulan_num' => $m,
                'bulan_singkat' => $monthlyLabels[$m - 1],
                'bulan_nama' => $bulanLongNames[$m - 1],
                'sales_1' => $s1,
                'sales_2' => $s2,
                'sales_delta' => $sDelta,
                'sales_grow' => $sGrow,
                'raw_1' => $r1,
                'raw_2' => $r2,
                'raw_delta' => $rDelta,
                'raw_grow' => $rGrow,
                'margin_1' => $m1,
                'margin_2' => $m2,
                'margin_delta' => $mDelta,
                'margin_grow' => $mGrow,
            ];
        }

        // ── 4. HISTORI MULTI-TAHUN KESELURUHAN (ALL YEARS SUMMARY) ─────────────
        $multiYearSummary = [];
        $sortedYearsAsc = collect($availableYears)->sort()->values();
        $prevSales = null;
        $prevRaw = null;

        $multiYearLabels = [];
        $multiYearSales = [];
        $multiYearRaw = [];
        $multiYearProfit = [];

        foreach ($sortedYearsAsc as $y) {
            $s = (float)CugilSale::whereYear('tanggal', $y)->sum('tagihan');
            $sQty = (float)CugilSale::whereYear('tanggal', $y)->sum('total_qty');
            $r = (float)CugilRawMaterial::whereYear('tanggal', $y)->sum('tagihan');
            $rQty = (float)CugilRawMaterial::whereYear('tanggal', $y)->sum('total_qty');
            $profit = $s - $r;
            $marginPct = $s > 0 ? round(($profit / $s) * 100, 1) : 0;

            $salesGrowth = ($prevSales !== null && $prevSales > 0) ? round((($s - $prevSales) / $prevSales) * 100, 1) : null;
            $rawGrowth = ($prevRaw !== null && $prevRaw > 0) ? round((($r - $prevRaw) / $prevRaw) * 100, 1) : null;

            $multiYearSummary[] = [
                'tahun' => $y,
                'sales_rp' => $s,
                'sales_qty' => $sQty,
                'sales_growth' => $salesGrowth,
                'raw_rp' => $r,
                'raw_qty' => $rQty,
                'raw_growth' => $rawGrowth,
                'profit_rp' => $profit,
                'margin_pct' => $marginPct,
            ];

            $multiYearLabels[] = (string)$y;
            $multiYearSales[] = $s;
            $multiYearRaw[] = $r;
            $multiYearProfit[] = $profit;

            $prevSales = $s;
            $prevRaw = $r;
        }

        // ── 5. ANALISIS UMUR PIUTANG (AR AGING) ────────────────────────────────
        $allUnpaidSales = CugilSale::where('status_pelunasan', '!=', 'LUNAS')->get();
        $now = Carbon::now();

        $agingAR = [
            'current'   => 0, // 0 - 15 hari
            'day16_30'  => 0, // 16 - 30 hari
            'day31_60'  => 0, // 31 - 60 hari
            'over60'    => 0, // > 60 hari
        ];

        foreach ($allUnpaidSales as $sale) {
            $sisa = max(0, $sale->tagihan - $sale->payment);
            $selisihHari = $sale->tanggal ? $now->diffInDays(Carbon::parse($sale->tanggal)) : 0;

            if ($selisihHari <= 15) {
                $agingAR['current'] += $sisa;
            } elseif ($selisihHari <= 30) {
                $agingAR['day16_30'] += $sisa;
            } elseif ($selisihHari <= 60) {
                $agingAR['day31_60'] += $sisa;
            } else {
                $agingAR['over60'] += $sisa;
            }
        }

        // ── 6. KOMPOSISI VOLUME & REVENUE PER KATEGORI PRODUK ──────────────────
        $kategoriStats = CugilBarang::select('kategori', 
                DB::raw('SUM(barang_keluar) as total_keluar'),
                DB::raw('SUM(barang_keluar * harga_jual) as total_nilai_jual'),
                DB::raw('SUM(stok_akhir) as total_stok')
            )
            ->groupBy('kategori')
            ->get();

        $kategoriLabels = $kategoriStats->pluck('kategori')->toArray();
        $kategoriVolumes = $kategoriStats->pluck('total_keluar')->toArray();

        // ── 7. TOP 5 BUYERS & SUPPLIERS (PARETO MATRIX) ────────────────────────
        $topBuyers = CugilSale::select('kode_customer', 'nama_buyer',
                DB::raw('SUM(tagihan) as total_omset'),
                DB::raw('SUM(payment) as total_bayar'),
                DB::raw('SUM(tagihan - payment) as sisa_piutang'),
                DB::raw('COUNT(*) as frekuensi_beli')
            )
            ->groupBy('kode_customer', 'nama_buyer')
            ->orderByDesc('total_omset')
            ->limit(5)
            ->get();

        $topSuppliers = CugilRawMaterial::select('kode_supplier', 'nama_pemasok',
                DB::raw('SUM(tagihan) as total_pasokan'),
                DB::raw('SUM(payment) as total_dibayar'),
                DB::raw('SUM(tagihan - payment) as sisa_hutang'),
                DB::raw('COUNT(*) as frekuensi_pasok')
            )
            ->groupBy('kode_supplier', 'nama_pemasok')
            ->orderByDesc('total_pasokan')
            ->limit(5)
            ->get();

        // ── 8. MARGIN ANALISIS PER SKU BARANG ─────────────────────────────────
        $barangMargin = CugilBarang::where('harga_beli', '>', 0)
            ->select('kode_barang', 'nama_barang', 'kategori', 'harga_beli', 'harga_jual', 'stok_akhir', 'barang_keluar')
            ->get()
            ->map(function ($b) {
                $marginRp = $b->harga_jual - $b->harga_beli;
                $marginPct = $b->harga_jual > 0 ? round(($marginRp / $b->harga_jual) * 100, 1) : 0;
                $potensiLabaStok = max(0, $b->stok_akhir) * $marginRp;
                return [
                    'kode' => $b->kode_barang,
                    'nama' => $b->nama_barang,
                    'kategori' => $b->kategori,
                    'harga_beli' => $b->harga_beli,
                    'harga_jual' => $b->harga_jual,
                    'margin_rp' => $marginRp,
                    'margin_pct' => $marginPct,
                    'stok' => $b->stok_akhir,
                    'potensi_laba' => $potensiLabaStok,
                ];
            })
            ->sortByDesc('margin_pct')
            ->values();

        return view('analisis.index', compact(
            'perusahaan', 'tahun', 'tahunBanding', 'availableYears',
            'totalBahanMasuk', 'totalHasilCugil', 'rendemenPersen', 'susutPersen',
            'totalPenjualan', 'totalPembelian', 'totalLabaKotor', 'grossMarginPersen',
            'totalPiutang', 'totalHutang', 'piutangKritis',
            'yoyMetrics', 'monthlyYoYMatrix', 'multiYearSummary',
            'monthlyLabels', 'monthlyPenjualan1', 'monthlyPenjualan2', 'monthlyPembelian1', 'monthlyPembelian2', 'monthlyMargin1', 'monthlyMargin2',
            'multiYearLabels', 'multiYearSales', 'multiYearRaw', 'multiYearProfit',
            'agingAR', 'kategoriLabels', 'kategoriVolumes', 'topBuyers', 'topSuppliers', 'barangMargin'
        ));
    }
}
