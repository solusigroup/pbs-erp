<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\CugilBarang;
use App\Models\CugilCustomer;
use App\Models\CugilPurchaseOrder;
use App\Models\CugilRawItem;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\CugilSaleItem;
use App\Models\CugilSupplier;
use App\Models\JurnalUmum;
use App\Models\Perusahaan;
use App\Models\TransaksiPajak;
use App\Services\LabaRugiPbsService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class LaporanController extends Controller
{
    /**
     * Hub / Portal Utama Laporan PBS-ERP
     */
    public function index()
    {
        $perusahaan = Perusahaan::first();
        $totalSupplier = CugilSupplier::count();
        $totalCustomer = CugilCustomer::count();
        $totalBarang = CugilBarang::count();
        $totalPembelian = CugilRawMaterial::sum('tagihan');
        $totalPenjualan = CugilSale::sum('tagihan');
        $totalPajak = TransaksiPajak::sum('nominal_pajak');

        return view('laporan.index', compact(
            'perusahaan', 'totalSupplier', 'totalCustomer', 'totalBarang',
            'totalPembelian', 'totalPenjualan', 'totalPajak'
        ));
    }

    /**
     * Laporan Rekapitulasi Stok, Olah & Rendemen CUGIL
     */
    public function cugilRekap(Request $request)
    {
        $data = $this->getCugilRekapData($request);
        return view('laporan.cugil-rekap', $data);
    }

    public function printCugilRekap(Request $request)
    {
        $data = $this->getCugilRekapData($request);
        return view('laporan.print.cugil-rekap', $data);
    }

    private function getCugilRekapData(Request $request): array
    {
        $barangs = CugilBarang::orderBy('kategori')->orderBy('nama_barang')->get();
        $perusahaan = Perusahaan::first();

        $totalStokAwal = $barangs->sum('stok_awal');
        $totalMasuk = $barangs->sum('barang_masuk');
        $totalKeluar = $barangs->sum('barang_keluar');
        $totalStokAkhir = $barangs->sum('stok_akhir');

        $totalNilaiBeli = (float) (CugilBarang::selectRaw('SUM(CASE WHEN stok_akhir > 0 THEN stok_akhir * harga_beli ELSE 0 END) as total')->value('total') ?? 0);
        $totalNilaiJual = (float) (CugilBarang::selectRaw('SUM(CASE WHEN stok_akhir > 0 THEN stok_akhir * harga_jual ELSE 0 END) as total')->value('total') ?? 0);

        $rekapPerKategori = $barangs->groupBy('kategori')->map(function ($items, $kat) {
            return [
                'kategori' => $kat,
                'count' => $items->count(),
                'masuk' => $items->sum('barang_masuk'),
                'keluar' => $items->sum('barang_keluar'),
                'stok' => $items->sum('stok_akhir'),
            ];
        });

        return compact(
            'barangs', 'perusahaan', 'totalStokAwal', 'totalMasuk', 'totalKeluar',
            'totalStokAkhir', 'totalNilaiBeli', 'totalNilaiJual', 'rekapPerKategori'
        );
    }

    public function exportCugilRekap(Request $request): StreamedResponse
    {
        $data = $this->getCugilRekapData($request);
        $fileName = 'rekap_stok_cugil_pbs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            // Write UTF-8 BOM for automatic Excel recognition
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, ['REKAPITULASI MUTASI STOK & OLAHAN CUGIL - PT PINASTIKA BHAKTI SEMESTA']);
            fputcsv($out, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
            fputcsv($out, []);

            // Header
            fputcsv($out, [
                'No',
                'Kode Barang',
                'Nama Barang / SKU',
                'Kategori',
                'Satuan',
                'Stok Awal (Kg)',
                'Barang Masuk (Kg)',
                'Barang Keluar (Kg)',
                'Stok Akhir (Kg)',
                'Harga Beli (Rp)',
                'Harga Jual (Rp)',
                'Total Nilai Persediaan (Rp)'
            ]);

            foreach ($data['barangs'] as $idx => $b) {
                $nilaiPersediaan = max(0, $b->stok_akhir) * $b->harga_beli;
                fputcsv($out, [
                    $idx + 1,
                    $b->kode_barang,
                    $b->nama_barang,
                    $b->kategori,
                    $b->satuan ?? 'Kg',
                    $b->stok_awal,
                    $b->barang_masuk,
                    $b->barang_keluar,
                    $b->stok_akhir,
                    $b->harga_beli,
                    $b->harga_jual,
                    $nilaiPersediaan
                ]);
            }

            // Summary row
            fputcsv($out, []);
            fputcsv($out, [
                'TOTAL',
                '',
                '',
                '',
                '',
                $data['totalStokAwal'],
                $data['totalMasuk'],
                $data['totalKeluar'],
                $data['totalStokAkhir'],
                '',
                '',
                $data['totalNilaiBeli']
            ]);

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Laporan Pembelian & Penerimaan Bahan Baku CUGIL
     */
    public function pembelian(Request $request)
    {
        $data = $this->getPembelianData($request);
        return view('laporan.pembelian', $data);
    }

    public function printPembelian(Request $request)
    {
        $data = $this->getPembelianData($request);
        return view('laporan.print.pembelian', $data);
    }

    private function getPembelianData(Request $request): array
    {
        $perusahaan = Perusahaan::first();
        $query = CugilRawMaterial::with(['supplier', 'items.barang']);

        if ($request->filled('supplier')) {
            $query->where('kode_supplier', $request->supplier);
        }
        if ($request->filled('status')) {
            $query->where('status_lunas', $request->status);
        }
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $rawMaterials = $query->orderBy('tanggal', 'desc')->get();
        $suppliers = CugilSupplier::orderBy('nama_supplier')->get();

        $totalQty = $rawMaterials->sum('total_qty');
        $totalBruto = $rawMaterials->sum('total_bruto');
        $totalRafaksi = $rawMaterials->sum('total_rafaksi');
        $totalOngkir = $rawMaterials->sum('ongkos_angkut');
        $totalTagihan = $rawMaterials->sum('tagihan');
        $totalPayment = $rawMaterials->sum('payment');
        $totalSisa = $rawMaterials->sum('sisa_tagihan');

        $rekapSupplier = $rawMaterials->groupBy('kode_supplier')->map(function ($group) {
            $first = $group->first();
            return [
                'kode' => $first->kode_supplier,
                'nama' => $first->nama_pemasok ?? $first->kode_supplier,
                'total_transaksi' => $group->count(),
                'total_qty' => $group->sum('total_qty'),
                'total_tagihan' => $group->sum('tagihan'),
                'total_payment' => $group->sum('payment'),
                'sisa_hutang' => $group->sum('sisa_tagihan'),
            ];
        })->sortByDesc('total_tagihan');

        return compact(
            'rawMaterials', 'suppliers', 'perusahaan', 'totalQty', 'totalBruto',
            'totalRafaksi', 'totalOngkir', 'totalTagihan', 'totalPayment', 'totalSisa', 'rekapSupplier'
        );
    }

    public function exportPembelian(Request $request): StreamedResponse
    {
        $data = $this->getPembelianData($request);
        $fileName = 'rekap_pembelian_bahan_pbs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, ['REKAPITULASI PEMBELIAN & PENERIMAAN BAHAN BAKU CUGIL - PT PINASTIKA BHAKTI SEMESTA']);
            fputcsv($out, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
            fputcsv($out, []);

            fputcsv($out, [
                'No',
                'Tanggal',
                'No PO / Bukti',
                'Kode Supplier',
                'Nama Pemasok',
                'Armada Truk',
                'Rincian Item (SKU - Qty - Harga)',
                'Total Qty (Kg)',
                'Total Bruto (Rp)',
                'Rafaksi Timbangan (Rp)',
                'Ongkos Angkut (Rp)',
                'Total Tagihan (Rp)',
                'Total Pembayaran (Rp)',
                'Sisa Hutang (Rp)',
                'Status Pelunasan'
            ]);

            foreach ($data['rawMaterials'] as $idx => $raw) {
                $itemDetails = [];
                if ($raw->items && $raw->items->count() > 0) {
                    foreach ($raw->items as $it) {
                        $itemDetails[] = $it->nama_barang . ' (' . $it->qty . ' Kg @ Rp' . $it->harga_satuan . ')';
                    }
                } else {
                    $itemDetails[] = $raw->nama_barang . ' (' . $raw->qty . ' Kg @ Rp' . $raw->harga_satuan . ')';
                }

                fputcsv($out, [
                    $idx + 1,
                    $raw->tanggal->format('Y-m-d'),
                    $raw->nomor_po ?? '-',
                    $raw->kode_supplier,
                    $raw->nama_pemasok ?? ($raw->supplier->nama_supplier ?? $raw->kode_supplier),
                    $raw->truk ?? '-',
                    implode('; ', $itemDetails),
                    $raw->total_qty,
                    $raw->total_bruto,
                    $raw->diskon_rafaksi,
                    $raw->ongkos_angkut,
                    $raw->tagihan,
                    $raw->payment,
                    $raw->sisa_tagihan,
                    $raw->status_lunas
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                $data['totalQty'],
                $data['totalBruto'],
                $data['totalRafaksi'],
                $data['totalOngkir'],
                $data['totalTagihan'],
                $data['totalPayment'],
                $data['totalSisa'],
                ''
            ]);

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Laporan Penjualan & Pengiriman Hasil Cuci Giling
     */
    public function penjualan(Request $request)
    {
        $data = $this->getPenjualanData($request);
        return view('laporan.penjualan', $data);
    }

    public function printPenjualan(Request $request)
    {
        $data = $this->getPenjualanData($request);
        return view('laporan.print.penjualan', $data);
    }

    private function getPenjualanData(Request $request): array
    {
        $perusahaan = Perusahaan::first();
        $query = CugilSale::with(['customer', 'items.barang']);

        if ($request->filled('customer')) {
            $query->where('kode_customer', $request->customer);
        }
        if ($request->filled('status')) {
            $query->where('status_pelunasan', $request->status);
        }
        if ($request->filled('dari') && $request->filled('sampai')) {
            $query->whereBetween('tanggal', [$request->dari, $request->sampai]);
        }

        $sales = $query->orderBy('tanggal', 'desc')->get();
        $customers = CugilCustomer::orderBy('nama_customer')->get();

        $totalQty = $sales->sum('total_qty');
        $totalSak = $sales->sum('total_sak');
        $totalBruto = $sales->sum('total_bruto');
        $totalDiskon = $sales->sum('total_diskon');
        $totalOngkir = $sales->sum('ongkos_angkut');
        $totalFeeMakelar = $sales->sum('fee_makelar');
        $totalTagihan = $sales->sum('tagihan');
        $totalPayment = $sales->sum('payment');
        $totalSisa = $sales->sum('sisa_piutang');

        $rekapCustomer = $sales->groupBy('kode_customer')->map(function ($group) {
            $first = $group->first();
            return [
                'kode' => $first->kode_customer,
                'nama' => $first->nama_buyer ?? $first->kode_customer,
                'total_transaksi' => $group->count(),
                'total_qty' => $group->sum('total_qty'),
                'total_sak' => $group->sum('total_sak'),
                'total_tagihan' => $group->sum('tagihan'),
                'total_payment' => $group->sum('payment'),
                'sisa_piutang' => $group->sum('sisa_piutang'),
            ];
        })->sortByDesc('total_tagihan');

        return compact(
            'sales', 'customers', 'perusahaan', 'totalQty', 'totalSak', 'totalBruto',
            'totalDiskon', 'totalOngkir', 'totalFeeMakelar', 'totalTagihan', 'totalPayment',
            'totalSisa', 'rekapCustomer'
        );
    }

    public function exportPenjualan(Request $request): StreamedResponse
    {
        $data = $this->getPenjualanData($request);
        $fileName = 'rekap_penjualan_cugil_pbs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, ['REKAPITULASI PENJUALAN PRODUK CUCI GILING - PT PINASTIKA BHAKTI SEMESTA']);
            fputcsv($out, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
            fputcsv($out, []);

            fputcsv($out, [
                'No',
                'Tanggal',
                'ID Penjualan',
                'Kode Customer',
                'Nama Pembeli / Buyer',
                'Armada Truk',
                'Makelar / Broker',
                'Rincian Produk (SKU - Qty - Harga)',
                'Total Qty (Kg)',
                'Jumlah Sak',
                'Total Bruto (Rp)',
                'Diskon Penjualan (Rp)',
                'Ongkos Angkut (Rp)',
                'Fee Makelar (Rp)',
                'Total Tagihan (Rp)',
                'Total Pembayaran (Rp)',
                'Sisa Piutang (Rp)',
                'Status Pelunasan'
            ]);

            foreach ($data['sales'] as $idx => $s) {
                $itemDetails = [];
                if ($s->items && $s->items->count() > 0) {
                    foreach ($s->items as $it) {
                        $itemDetails[] = $it->nama_barang . ' (' . $it->qty_terjual . ' Kg @ Rp' . $it->harga_satuan . ')';
                    }
                } else {
                    $itemDetails[] = $s->nama_barang . ' (' . $s->qty_terjual . ' Kg @ Rp' . $s->harga_satuan . ')';
                }

                fputcsv($out, [
                    $idx + 1,
                    $s->tanggal->format('Y-m-d'),
                    $s->id_penjualan,
                    $s->kode_customer,
                    $s->nama_buyer ?? ($s->customer->nama_customer ?? $s->kode_customer),
                    $s->truk ?? '-',
                    $s->broker ?? '-',
                    implode('; ', $itemDetails),
                    $s->total_qty,
                    $s->total_sak,
                    $s->total_bruto,
                    $s->diskon_rupiah,
                    $s->ongkos_angkut,
                    $s->fee_makelar,
                    $s->tagihan,
                    $s->payment,
                    $s->sisa_piutang,
                    $s->status_pelunasan
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, [
                'TOTAL',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $data['totalQty'],
                $data['totalSak'],
                $data['totalBruto'],
                $data['totalDiskon'],
                $data['totalOngkir'],
                $data['totalFeeMakelar'],
                $data['totalTagihan'],
                $data['totalPayment'],
                $data['totalSisa'],
                ''
            ]);

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Laporan Laba Rugi Komprehensif & Posisi Keuangan (Neraca)
     */
    public function keuangan(Request $request)
    {
        $data = $this->getKeuanganData($request);
        return view('laporan.keuangan', $data);
    }

    public function printKeuangan(Request $request)
    {
        $data = $this->getKeuanganData($request);
        return view('laporan.print.keuangan', $data);
    }

    private function getKeuanganData(Request $request): array
    {
        $perusahaan = Perusahaan::first();
        $tanggalDari = $request->query('tanggal_dari', date('Y-01-01'));
        $tanggalSampai = $request->query('tanggal_sampai', date('Y-m-d'));

        $akuns = Akun::orderBy('kode_akun')->get();

        $akunPendapatan = $akuns->where('tipe_akun', 'Pendapatan');
        $totalPendapatan = $akunPendapatan->sum('saldo_berjalan');

        $akunHPP = $akuns->where('tipe_akun', 'HPP');
        $totalHPP = $akunHPP->sum('saldo_berjalan');
        $labaKotor = $totalPendapatan - $totalHPP;

        $akunBeban = $akuns->where('tipe_akun', 'Beban');
        $totalBeban = $akunBeban->sum('saldo_berjalan');
        $labaBersih = $labaKotor - $totalBeban;

        $akunKasBank = $akuns->where('tipe_akun', 'Kas & Bank');
        $totalKasBank = $akunKasBank->sum('saldo_berjalan');

        $akunPiutang = $akuns->where('tipe_akun', 'Piutang');
        $totalPiutang = $akunPiutang->sum('saldo_berjalan');

        $akunPersediaan = $akuns->where('tipe_akun', 'Persediaan');
        $totalPersediaan = $akunPersediaan->sum('saldo_berjalan');

        $akunAsetTetap = $akuns->where('tipe_akun', 'Aset Tetap');
        $totalAsetTetap = $akunAsetTetap->sum('saldo_berjalan');
        $totalAset = $totalKasBank + $totalPiutang + $totalPersediaan + $totalAsetTetap;

        $akunHutang = $akuns->where('tipe_akun', 'Hutang');
        $totalHutang = $akunHutang->sum('saldo_berjalan');

        $akunModal = $akuns->where('tipe_akun', 'Modal');
        $totalModal = $akunModal->sum('saldo_berjalan');
        $totalKewajibanEkuitas = $totalHutang + $totalModal + $labaBersih;

        // Laporan Laba Rugi Spesifik PBS (Manufaktur / Cuci Giling)
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

        return compact(
            'perusahaan', 'tanggalDari', 'tanggalSampai', 'labaRugiPbs',
            'akunPendapatan', 'totalPendapatan', 'akunHPP', 'totalHPP',
            'labaKotor', 'akunBeban', 'totalBeban', 'labaBersih', 'akunKasBank', 'totalKasBank',
            'akunPiutang', 'totalPiutang', 'akunPersediaan', 'totalPersediaan', 'akunAsetTetap',
            'totalAsetTetap', 'totalAset', 'akunHutang', 'totalHutang', 'akunModal', 'totalModal',
            'totalKewajibanEkuitas'
        );
    }

    public function exportKeuangan(Request $request): StreamedResponse
    {
        $data = $this->getKeuanganData($request);
        $fileName = 'laporan_keuangan_pbs_' . date('Ymd_His') . '.csv';
        $d = $data['labaRugiPbs'];

        return response()->streamDownload(function () use ($data, $d) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, ['LAPORAN KEUANGAN KORPORASI - PT PINASTIKA BHAKTI SEMESTA']);
            fputcsv($out, ['Periode: ' . date('d/m/Y', strtotime($data['tanggalDari'])) . ' s/d ' . date('d/m/Y', strtotime($data['tanggalSampai']))]);
            fputcsv($out, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
            fputcsv($out, []);

            // 1. Laba Rugi Standar PBS
            fputcsv($out, ['=== I. LAPORAN LABA RUGI PBS ===']);
            fputcsv($out, ['No', 'Sub', 'Pos Rekening / Deskripsi Transaksi', 'Rincian (Rp)', 'Sub-Total (Rp)', 'Total (Rp)']);
            fputcsv($out, ['A.', '', 'PENJUALAN', '', '', '']);
            fputcsv($out, ['A.', '1.', 'Penjualan Jasa', $d['A1_penjualan_jasa'], '', '']);
            fputcsv($out, ['A.', '2.', 'Penjualan Barang', $d['A2_penjualan_barang'], '', '']);
            fputcsv($out, ['A.', '3.', 'Diskon Penjualan', -$d['A3_diskon_penjualan'], '', '']);
            fputcsv($out, ['', '', 'PENJUALAN BRUTO', '', $d['penjualan_bruto'], '']);
            fputcsv($out, ['A.', '4.', 'Penjualan Barang Bersih', '', $d['A4_penjualan_barang_bersih'], $d['total_penjualan_bersih']]);
            fputcsv($out, []);

            fputcsv($out, ['B.', '', 'HARGA POKOK PENJUALAN', '', '', '']);
            fputcsv($out, ['B.', '1.', 'PERSEDIAAN AWAL BRNG JADI', $d['B1_persediaan_awal_bj'], '', '']);
            fputcsv($out, ['B.', '2.', 'HRG POKOK PRODUKSI', '', '', '']);
            fputcsv($out, ['B.', '2. a.', 'PERSEDIAAN AWAL BAHAN BAKU', $d['B2a_persediaan_awal_bb'], '', '']);
            fputcsv($out, ['B.', '2. b.', 'PEMBELIAN BB', $d['B2b_pembelian_bb'], '', '']);
            fputcsv($out, ['B.', '2. c.', 'ONGKOS ANGKUT PEMBELIAN+TIMBANG', $d['B2c_ongkos_angkut_pembelian'], '', '']);
            fputcsv($out, ['B.', '2. d.', 'DISKON PEMBELIAN BB', -$d['B2d_diskon_pembelian_bb'], '', '']);
            fputcsv($out, ['B.', '2. f.', 'TOTAL PEMBELIAN', '', $d['B2f_total_pembelian'], '']);
            fputcsv($out, ['', '', '(-) STOCK AKHIR BB', -$d['stock_akhir_bb'], '', '']);
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
            fputcsv($out, []);

            // 2. Neraca
            fputcsv($out, ['=== II. LAPORAN POSISI KEUANGAN (NERACA) ===']);
            fputcsv($out, ['Kode Akun', 'Nama Akun / Pos Rekening', 'Kategori Akun', 'Saldo Berjalan (Rp)']);

            fputcsv($out, ['--- ASET (AKTIVA) ---']);
            foreach ($data['akunKasBank'] as $kb) {
                fputcsv($out, [$kb->kode_akun, $kb->nama_akun, $kb->tipe_akun, $kb->saldo_berjalan]);
            }
            foreach ($data['akunPiutang'] as $pi) {
                fputcsv($out, [$pi->kode_akun, $pi->nama_akun, $pi->tipe_akun, $pi->saldo_berjalan]);
            }
            foreach ($data['akunAsetTetap'] as $at) {
                fputcsv($out, [$at->kode_akun, $at->nama_akun, $at->tipe_akun, $at->saldo_berjalan]);
            }
            fputcsv($out, ['', 'TOTAL ASET (AKTIVA)', '', $data['totalAset']]);

            fputcsv($out, ['--- KEWAJIBAN & EKUITAS (PASIVA) ---']);
            foreach ($data['akunHutang'] as $ht) {
                fputcsv($out, [$ht->kode_akun, $ht->nama_akun, $ht->tipe_akun, $ht->saldo_berjalan]);
            }
            foreach ($data['akunModal'] as $md) {
                fputcsv($out, [$md->kode_akun, $md->nama_akun, $md->tipe_akun, $md->saldo_berjalan]);
            }
            fputcsv($out, ['', 'Laba Bersih Tahun Berjalan', 'Laba', $data['labaBersih']]);
            fputcsv($out, ['', 'TOTAL KEWAJIBAN & EKUITAS (PASIVA)', '', $data['totalKewajibanEkuitas']]);

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }

    /**
     * Laporan Rekapitulasi Pajak Masa & Tahunan
     */
    public function pajak(Request $request)
    {
        $data = $this->getPajakData($request);
        return view('laporan.pajak', $data);
    }

    public function printPajak(Request $request)
    {
        $data = $this->getPajakData($request);
        return view('laporan.print.pajak', $data);
    }

    private function getPajakData(Request $request): array
    {
        $perusahaan = Perusahaan::first();
        $query = TransaksiPajak::query();

        if ($request->filled('jenis_pajak')) {
            $query->where('jenis_pajak', $request->jenis_pajak);
        }
        if ($request->filled('status_bayar')) {
            $query->where('status_bayar', $request->status_bayar);
        }

        $transaksis = $query->orderBy('tanggal_faktur_potong', 'desc')->get();

        $totalPajak = $transaksis->sum('nominal_pajak');
        $pajakSudahSetor = $transaksis->where('status_bayar', 'Sudah Disetor')->sum('nominal_pajak');
        $pajakBelumSetor = $transaksis->where('status_bayar', 'Belum Disetor')->sum('nominal_pajak');

        $rekapJenis = $transaksis->groupBy('jenis_pajak')->map(function ($group, $jenis) {
            return [
                'jenis' => $jenis,
                'count' => $group->count(),
                'total' => $group->sum('nominal_pajak'),
                'sudah' => $group->where('status_bayar', 'Sudah Disetor')->sum('nominal_pajak'),
                'belum' => $group->where('status_bayar', 'Belum Disetor')->sum('nominal_pajak'),
            ];
        });

        return compact(
            'perusahaan', 'transaksis', 'totalPajak', 'pajakSudahSetor', 'pajakBelumSetor', 'rekapJenis'
        );
    }

    public function exportPajak(Request $request): StreamedResponse
    {
        $data = $this->getPajakData($request);
        $fileName = 'rekap_pajak_pbs_' . date('Ymd_His') . '.csv';

        return response()->streamDownload(function () use ($data) {
            $out = fopen('php://output', 'w');
            fputs($out, "\xEF\xBB\xBF");

            fputcsv($out, ['REKAPITULASI KEWAJIBAN PERPAJAKAN (PPN & PPH) - PT PINASTIKA BHAKTI SEMESTA']);
            fputcsv($out, ['Tanggal Cetak: ' . date('d F Y H:i:s')]);
            fputcsv($out, []);

            fputcsv($out, [
                'No',
                'Kode Referensi',
                'Jenis Pajak',
                'Masa Pajak',
                'Tahun Pajak',
                'Tanggal Faktur / Bukti Potong',
                'Nomor Dokumen Faktur/BPU',
                'Lawan Transaksi',
                'NPWP Lawan Transaksi',
                'DPP / Nilai Transaksi (Rp)',
                'Tarif (%)',
                'Nominal Pajak (Rp)',
                'Status Pembayaran',
                'Kode NTPN Setor',
                'Tanggal Setor',
                'Status Pelaporan SPT',
                'Nomor BPE Bukti Lapor',
                'Catatan Tax Review'
            ]);

            foreach ($data['transaksis'] as $idx => $t) {
                fputcsv($out, [
                    $idx + 1,
                    $t->kode_referensi,
                    $t->jenis_pajak,
                    $t->masa_pajak,
                    $t->tahun_pajak,
                    $t->tanggal_faktur_potong ? $t->tanggal_faktur_potong->format('Y-m-d') : '-',
                    $t->nomor_dokumen ?? '-',
                    $t->lawan_transaksi,
                    $t->npwp_lawan_transaksi ?? '-',
                    $t->dpp,
                    $t->tarif_persen,
                    $t->nominal_pajak,
                    $t->status_bayar,
                    $t->ntpn ?? '-',
                    $t->tanggal_setor ? $t->tanggal_setor->format('Y-m-d') : '-',
                    $t->status_lapor,
                    $t->bpe_spt ?? '-',
                    $t->catatan ?? '-'
                ]);
            }

            fputcsv($out, []);
            fputcsv($out, [
                'TOTAL PAJAK',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                '',
                $data['totalPajak'],
                '',
                '',
                '',
                '',
                '',
                ''
            ]);

            fclose($out);
        }, $fileName, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => "attachment; filename=\"{$fileName}\"",
        ]);
    }
}
