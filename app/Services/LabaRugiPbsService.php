<?php

namespace App\Services;

use App\Models\Akun;
use App\Models\CugilBarang;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\JurnalDetail;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * LabaRugiPbsService
 * 
 * Menghitung Laporan Laba Rugi Spesifik PT Pinastika Bhakti Semesta (PBS)
 * sesuai struktur manufaktur / pengolahan cuci giling (CUGIL):
 * 
 * A. PENJUALAN
 *    A. 1. Penjualan Jasa
 *    A. 2. Penjualan Barang
 *    A. 3. Diskon Penjualan
 *          PENJUALAN BRUTO
 *    A. 4. Penjualan Barang Bersih
 * 
 * B. HARGA POKOK PENJUALAN
 *    B. 1. PERSEDIAAN AWAL BRNG JADI
 *    B. 2. HRG POKOK PRODUKSI
 *       B. 2. a. PERSEDIAAN AWAL BAHAN BAKU
 *       B. 2. b. PEMBELIAN BB
 *       B. 2. c. ONGKOS ANGKUT PEMBELIAN+TIMBANG
 *       B. 2. d. DISKON PEMBELIAN BB
 *       B. 2. f. TOTAL PEMBELIAN
 *          (-) STOCK AKHIR BB
 *       B. 3. FOH-BIAYA TENAGA KERJA LANGSUNG
 *       B. 4. FOH-LISTRIK
 *       B. 5. FOH-MAINTENANCE
 *       B. 6. TOTAL OVERHEAD PABRIK
 *       B. 7. TOTAL HRG POKOK PRODUKSI
 *    B. 8. ONGKOS ANGKUT PENJUALAN
 *    B. 9. PERSEDIAAN AKHIR BRNG JADI
 *    B. 10. KOMISI SALES (fee marketing)
 *    B. 11. KOMISI LAINNYA (ongkos kuli,satpam)
 *    B. 12. BIAYA PENJUALAN & STOK AKHIR
 * 
 * C. TOTAL HRG POKOK PENJUALAN [COGS]
 * D. LABA / RUGI BRUTO
 * E. GAJI MANAJEMEN
 * F. BIAYA ADMINISTRASI DAN UMUM
 * G. NET INCOME (DEFISIT / RUGI)
 */
class LabaRugiPbsService
{
    /**
     * Hitung laporan Laba Rugi PBS untuk periode tertentu.
     *
     * @param string $tanggalDari Format Y-m-d
     * @param string $tanggalSampai Format Y-m-d
     * @param array $overrides Nilai override manual jika ada
     * @return array
     */
    public function hitung(string $tanggalDari, string $tanggalSampai, array $overrides = []): array
    {
        // ─── A. PENJUALAN ───────────────────────────────────────────────────────────
        
        // Identifikasi transaksi penjualan JASA di cugil_sales (nama_barang/kode_barang JASA, ID *.jasa*, atau remark jasa)
        $jasaSaleIds = DB::table('cugil_sales')
            ->leftJoin('cugil_sale_items', 'cugil_sales.id', '=', 'cugil_sale_items.cugil_sale_id')
            ->where(function($q) {
                $q->where('cugil_sale_items.nama_barang', 'like', '%JASA%')
                  ->orWhere('cugil_sale_items.kode_barang', 'like', '%JASA%')
                  ->orWhere('cugil_sales.id_penjualan', 'like', '%.jasa%')
                  ->orWhere('cugil_sales.remark', 'like', '%jasa cuci%')
                  ->orWhere('cugil_sales.remark', 'like', '%jasa giling%');
            })
            ->pluck('cugil_sales.id')
            ->unique()
            ->toArray();

        // A.1 Penjualan Jasa (Dari cugil_sales berunsur JASA + Mutasi Kredit - Debit Akun 4-1200)
        $calcPenjualanJasaCugil = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->whereIn('id', $jasaSaleIds)
            ->sum('total_bruto');

        $jasaMutasi = JurnalDetail::where('kode_akun', '4-1200')
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            });
        $calcPenjualanJasaGL = (float) $jasaMutasi->sum('kredit') - (float) $jasaMutasi->sum('debit');
        $calcPenjualanJasa = $calcPenjualanJasaCugil + max(0.0, $calcPenjualanJasaGL);

        $penjualanJasa = isset($overrides['penjualan_jasa']) && is_numeric($overrides['penjualan_jasa'])
            ? (float) $overrides['penjualan_jasa']
            : max(0.0, $calcPenjualanJasa);

        // A.2 Penjualan Barang (Barang Jadi non-jasa dari cugil_sales)
        $calcPenjualanBarang = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->whereNotIn('id', $jasaSaleIds)
            ->sum('total_bruto');
        $penjualanBarang = isset($overrides['penjualan_barang']) && is_numeric($overrides['penjualan_barang'])
            ? (float) $overrides['penjualan_barang']
            : $calcPenjualanBarang;

        // A.3 Diskon Penjualan (cugil_sales.total_diskon + mutasi Debit Akun 4-1110)
        $calcDiskonJual = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('total_diskon');
        $diskonAkun = JurnalDetail::where('kode_akun', '4-1110')
            ->whereHas('jurnal', function ($q) use ($tanggalDari, $tanggalSampai) {
                $q->where('is_posted', true)->whereBetween('tanggal', [$tanggalDari, $tanggalSampai]);
            });
        $calcDiskonJual += ((float) $diskonAkun->sum('debit') - (float) $diskonAkun->sum('kredit'));
        $diskonPenjualan = isset($overrides['diskon_penjualan']) && is_numeric($overrides['diskon_penjualan'])
            ? (float) $overrides['diskon_penjualan']
            : max(0.0, $calcDiskonJual);

        // Diskon Penjualan Barang dan Jasa
        $diskonBarangOnly = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->whereNotIn('id', $jasaSaleIds)
            ->sum('total_diskon');
        $diskonJasaOnly = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->whereIn('id', $jasaSaleIds)
            ->sum('total_diskon');

        // PENJUALAN BRUTO (Penjualan Jasa + Penjualan Barang)
        $penjualanBruto = $penjualanJasa + $penjualanBarang;

        // A.4 Penjualan Barang Bersih (Penjualan Barang - Diskon Barang)
        $penjualanBarangBersih = $penjualanBarang - $diskonBarangOnly;

        // TOTAL PENJUALAN BERSIH (Penjualan Jasa Bersih + Penjualan Barang Bersih)
        $penjualanJasaBersih = $penjualanJasa - $diskonJasaOnly;
        $totalPenjualanBersih = $penjualanJasaBersih + $penjualanBarangBersih;


        // ─── B. HARGA POKOK PENJUALAN ───────────────────────────────────────────────

        // B.1 Persediaan Awal Barang Jadi
        $calcAwalBJ = $this->getSaldoAkunPerTanggal('1-1620', $tanggalDari, true);
        if ($calcAwalBJ <= 0) {
            $calcAwalBJ = (float) CugilBarang::where('kategori', 'CUCI GILING')
                ->where('is_active', true)
                ->sum(DB::raw('stok_awal * harga_beli'));
        }
        $persediaanAwalBJ = isset($overrides['persediaan_awal_bj']) && is_numeric($overrides['persediaan_awal_bj'])
            ? (float) $overrides['persediaan_awal_bj']
            : max(0.0, $calcAwalBJ);

        // B.2.a Persediaan Awal Bahan Baku
        $calcAwalBB = $this->getSaldoAkunPerTanggal('1-1610', $tanggalDari, true);
        if ($calcAwalBB <= 0) {
            $calcAwalBB = (float) CugilBarang::where('kategori', 'BAHAN BAKU')
                ->where('is_active', true)
                ->sum(DB::raw('stok_awal * harga_beli'));
        }
        $persediaanAwalBB = isset($overrides['persediaan_awal_bb']) && is_numeric($overrides['persediaan_awal_bb'])
            ? (float) $overrides['persediaan_awal_bb']
            : max(0.0, $calcAwalBB);

        // B.2.b Pembelian BB (cugil_raw_materials.total_bruto)
        $calcPembelianBB = (float) CugilRawMaterial::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('total_bruto');
        $pembelianBB = isset($overrides['pembelian_bb']) && is_numeric($overrides['pembelian_bb'])
            ? (float) $overrides['pembelian_bb']
            : $calcPembelianBB;

        // B.2.c Ongkos Angkut Pembelian + Timbang (cugil_raw_materials.ongkos_angkut)
        $calcOngkosBeli = (float) CugilRawMaterial::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('ongkos_angkut');
        $ongkosAngkutPembelian = isset($overrides['ongkos_angkut_pembelian']) && is_numeric($overrides['ongkos_angkut_pembelian'])
            ? (float) $overrides['ongkos_angkut_pembelian']
            : $calcOngkosBeli;

        // B.2.d Diskon Pembelian BB / Rafaksi (cugil_raw_materials.total_rafaksi)
        $calcDiskonBeli = (float) CugilRawMaterial::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('total_rafaksi');
        $diskonPembelianBB = isset($overrides['diskon_pembelian_bb']) && is_numeric($overrides['diskon_pembelian_bb'])
            ? (float) $overrides['diskon_pembelian_bb']
            : $calcDiskonBeli;

        // B.2.f TOTAL PEMBELIAN = Pembelian BB + Ongkos Angkut - Diskon Pembelian
        $totalPembelian = $pembelianBB + $ongkosAngkutPembelian - $diskonPembelianBB;

        // (-) STOCK AKHIR BB
        $calcAkhirBB = $this->getSaldoAkunPerTanggal('1-1610', $tanggalSampai, true);
        if ($calcAkhirBB <= 0) {
            $calcAkhirBB = (float) CugilBarang::where('kategori', 'BAHAN BAKU')
                ->where('is_active', true)
                ->sum(DB::raw('stok_akhir * harga_beli'));
        }
        $stockAkhirBB = isset($overrides['stock_akhir_bb']) && is_numeric($overrides['stock_akhir_bb'])
            ? (float) $overrides['stock_akhir_bb']
            : max(0.0, $calcAkhirBB);

        // Bahan Baku yang Digunakan
        $bahanBakuDigunakan = max(0.0, $persediaanAwalBB + $totalPembelian - $stockAkhirBB);

        // B.3 FOH - Biaya Tenaga Kerja Langsung (Akun 5-1200)
        $calcFohBTKL = $this->getMutasiDebitAkun('5-1200', $tanggalDari, $tanggalSampai);
        $fohBTKL = isset($overrides['foh_btkl']) && is_numeric($overrides['foh_btkl'])
            ? (float) $overrides['foh_btkl']
            : $calcFohBTKL;

        // B.4 FOH - Listrik (Akun 5-1300)
        $calcFohListrik = $this->getMutasiDebitAkun('5-1300', $tanggalDari, $tanggalSampai);
        $fohListrik = isset($overrides['foh_listrik']) && is_numeric($overrides['foh_listrik'])
            ? (float) $overrides['foh_listrik']
            : $calcFohListrik;

        // B.5 FOH - Maintenance (Akun 5-1400)
        $calcFohMaintenance = $this->getMutasiDebitAkun('5-1400', $tanggalDari, $tanggalSampai);
        $fohMaintenance = isset($overrides['foh_maintenance']) && is_numeric($overrides['foh_maintenance'])
            ? (float) $overrides['foh_maintenance']
            : $calcFohMaintenance;

        // B.6 TOTAL OVERHEAD PABRIK
        $totalOverheadPabrik = $fohBTKL + $fohListrik + $fohMaintenance;

        // B.7 TOTAL HRG POKOK PRODUKSI
        $totalHrgPokokProduksi = $bahanBakuDigunakan + $totalOverheadPabrik;

        // B.8 ONGKOS ANGKUT PENJUALAN (cugil_sales.ongkos_angkut + Akun 5-2100)
        $calcOngkosJual = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('ongkos_angkut');
        $calcOngkosJual += $this->getMutasiDebitAkun('5-2100', $tanggalDari, $tanggalSampai);
        $ongkosAngkutPenjualan = isset($overrides['ongkos_angkut_penjualan']) && is_numeric($overrides['ongkos_angkut_penjualan'])
            ? (float) $overrides['ongkos_angkut_penjualan']
            : $calcOngkosJual;

        // B.9 PERSEDIAAN AKHIR BRNG JADI
        $calcAkhirBJ = $this->getSaldoAkunPerTanggal('1-1620', $tanggalSampai, true);
        if ($calcAkhirBJ <= 0) {
            $calcAkhirBJ = (float) CugilBarang::where('kategori', 'CUCI GILING')
                ->where('is_active', true)
                ->sum(DB::raw('stok_akhir * harga_beli'));
        }
        $persediaanAkhirBJ = isset($overrides['persediaan_akhir_bj']) && is_numeric($overrides['persediaan_akhir_bj'])
            ? (float) $overrides['persediaan_akhir_bj']
            : max(0.0, $calcAkhirBJ);

        // B.10 KOMISI SALES (fee marketing - cugil_sales.fee_makelar + Akun 5-2200)
        $calcKomisiSales = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('fee_makelar');
        $calcKomisiSales += $this->getMutasiDebitAkun('5-2200', $tanggalDari, $tanggalSampai);
        $komisiSales = isset($overrides['komisi_sales']) && is_numeric($overrides['komisi_sales'])
            ? (float) $overrides['komisi_sales']
            : $calcKomisiSales;

        // B.11 KOMISI LAINNYA (ongkos kuli,satpam - cugil_sales.ongkos_kuli + Akun 5-2300)
        $calcKomisiLainnya = (float) CugilSale::whereBetween('tanggal', [$tanggalDari, $tanggalSampai])
            ->sum('ongkos_kuli');
        $calcKomisiLainnya += $this->getMutasiDebitAkun('5-2300', $tanggalDari, $tanggalSampai);
        $komisiLainnya = isset($overrides['komisi_lainnya']) && is_numeric($overrides['komisi_lainnya'])
            ? (float) $overrides['komisi_lainnya']
            : $calcKomisiLainnya;

        // B.12 BIAYA PENJUALAN & STOK AKHIR:
        // Komponen penjualan (Ongkos Angkut + Komisi Sales + Komisi Lainnya) dikurangi Persediaan Akhir BJ
        $totalBiayaPenjualan = $ongkosAngkutPenjualan + $komisiSales + $komisiLainnya;
        $biayaPenjualanStokAkhir = $totalBiayaPenjualan - $persediaanAkhirBJ;

        // ─── C. TOTAL HRG POKOK PENJUALAN [COGS] ────────────────────────────────────
        // Formula Manufaktur: Persediaan Awal BJ + Total HPP Produksi - Persediaan Akhir BJ + Biaya Penjualan
        $totalHppCogs = $persediaanAwalBJ + $totalHrgPokokProduksi + $biayaPenjualanStokAkhir;

        // ─── D. LABA / RUGI BRUTO ───────────────────────────────────────────────────
        $labaRugiBruto = $totalPenjualanBersih - $totalHppCogs;

        // ─── E. GAJI MANAJEMEN (Akun 6-1150 atau mutasi beban gaji kantor) ──────────
        $calcGajiManajemen = $this->getMutasiDebitAkun('6-1150', $tanggalDari, $tanggalSampai);
        if ($calcGajiManajemen <= 0) {
            $calcGajiManajemen = $this->getMutasiDebitAkun('6-1100', $tanggalDari, $tanggalSampai);
        }
        $gajiManajemen = isset($overrides['gaji_manajemen']) && is_numeric($overrides['gaji_manajemen'])
            ? (float) $overrides['gaji_manajemen']
            : $calcGajiManajemen;

        // ─── F. BIAYA ADMINISTRASI DAN UMUM (Akun 6-1200, 6-1300, 6-1400, 7-1100) ───
        $calcAdminUmum = $this->getMutasiDebitAkun('6-1200', $tanggalDari, $tanggalSampai)
            + $this->getMutasiDebitAkun('6-1300', $tanggalDari, $tanggalSampai)
            + $this->getMutasiDebitAkun('6-1400', $tanggalDari, $tanggalSampai)
            + $this->getMutasiDebitAkun('7-1100', $tanggalDari, $tanggalSampai);
        $biayaAdministrasiUmum = isset($overrides['biaya_administrasi_umum']) && is_numeric($overrides['biaya_administrasi_umum'])
            ? (float) $overrides['biaya_administrasi_umum']
            : $calcAdminUmum;

        // ─── G. NET INCOME (DEFISIT / RUGI) ─────────────────────────────────────────
        $netIncome = $labaRugiBruto - $gajiManajemen - $biayaAdministrasiUmum;

        return [
            // Periode
            'tanggal_dari' => $tanggalDari,
            'tanggal_sampai' => $tanggalSampai,

            // A. Penjualan
            'A1_penjualan_jasa' => $penjualanJasa,
            'A2_penjualan_barang' => $penjualanBarang,
            'A3_diskon_penjualan' => $diskonPenjualan,
            'penjualan_bruto' => $penjualanBruto,
            'A4_penjualan_barang_bersih' => $penjualanBarangBersih,
            'total_penjualan_bersih' => $totalPenjualanBersih,

            // B. HPP
            'B1_persediaan_awal_bj' => $persediaanAwalBJ,
            'B2a_persediaan_awal_bb' => $persediaanAwalBB,
            'B2b_pembelian_bb' => $pembelianBB,
            'B2c_ongkos_angkut_pembelian' => $ongkosAngkutPembelian,
            'B2d_diskon_pembelian_bb' => $diskonPembelianBB,
            'B2f_total_pembelian' => $totalPembelian,
            'stock_akhir_bb' => $stockAkhirBB,
            'bahan_baku_digunakan' => $bahanBakuDigunakan,

            // FOH
            'B3_foh_btkl' => $fohBTKL,
            'B4_foh_listrik' => $fohListrik,
            'B5_foh_maintenance' => $fohMaintenance,
            'B6_total_overhead_pabrik' => $totalOverheadPabrik,
            'B7_total_hrg_pokok_produksi' => $totalHrgPokokProduksi,

            // Biaya Penjualan & Barang Jadi Akhir
            'B8_ongkos_angkut_penjualan' => $ongkosAngkutPenjualan,
            'B9_persediaan_akhir_bj' => $persediaanAkhirBJ,
            'B10_komisi_sales' => $komisiSales,
            'B11_komisi_lainnya' => $komisiLainnya,
            'total_biaya_penjualan' => $totalBiayaPenjualan,
            'B12_biaya_penjualan_stok_akhir' => $biayaPenjualanStokAkhir,

            // C. Total HPP [COGS]
            'C_total_cogs' => $totalHppCogs,

            // D. Laba / Rugi Bruto
            'D_laba_rugi_bruto' => $labaRugiBruto,
            'is_laba_bruto' => $labaRugiBruto >= 0,

            // E & F. Operasional Kantor
            'E_gaji_manajemen' => $gajiManajemen,
            'F_biaya_administrasi_umum' => $biayaAdministrasiUmum,

            // G. Net Income
            'G_net_income' => $netIncome,
            'is_net_laba' => $netIncome >= 0,
        ];
    }

    /**
     * Hitung perbandingan komparatif antara dua periode (Utama vs Komparatif)
     */
    public function hitungKomparatif(string $tglDari1, string $tglSampai1, string $tglDari2, string $tglSampai2, array $overrides = []): array
    {
        $utama = $this->hitung($tglDari1, $tglSampai1, $overrides);
        $komparatif = $this->hitung($tglDari2, $tglSampai2, $overrides['komparatif'] ?? []);

        // Hitung selisih dan persentase pertumbuhan
        $diff = [];
        foreach ($utama as $k => $v) {
            if (is_numeric($v)) {
                $vKomp = $komparatif[$k] ?? 0;
                $selisih = $v - $vKomp;
                $persen = ($vKomp != 0) ? ($selisih / abs($vKomp)) * 100 : 0;
                $diff[$k] = [
                    'selisih' => $selisih,
                    'persen' => round($persen, 1),
                ];
            }
        }

        return [
            'utama' => $utama,
            'komparatif' => $komparatif,
            'diff' => $diff,
        ];
    }

    /**
     * Helper: Mutasi debit akun dalam range tanggal
     */
    private function getMutasiDebitAkun(string $kodeAkun, string $tglDari, string $tglSampai): float
    {
        $mutasi = JurnalDetail::where('kode_akun', $kodeAkun)
            ->whereHas('jurnal', function ($q) use ($tglDari, $tglSampai) {
                $q->where('is_posted', true)->whereBetween('tanggal', [$tglDari, $tglSampai]);
            });

        $totDebit = (float) $mutasi->sum('debit');
        $totKredit = (float) $mutasi->sum('kredit');

        return max(0.0, $totDebit - $totKredit);
    }

    /**
     * Helper: Saldo akun per cutoff tanggal
     */
    private function getSaldoAkunPerTanggal(string $kodeAkun, string $tanggalCutoff, bool $isDebit = true): float
    {
        $akun = Akun::where('kode_akun', $kodeAkun)->first();
        if (!$akun) {
            return 0.0;
        }

        $mutasi = JurnalDetail::where('kode_akun', $kodeAkun)
            ->whereHas('jurnal', function ($q) use ($tanggalCutoff) {
                $q->where('is_posted', true)->where('tanggal', '<=', $tanggalCutoff);
            });

        $totDebit = (float) $mutasi->sum('debit');
        $totKredit = (float) $mutasi->sum('kredit');

        if ($isDebit) {
            return max(0.0, (float) $akun->saldo_awal + $totDebit - $totKredit);
        } else {
            return max(0.0, (float) $akun->saldo_awal + $totKredit - $totDebit);
        }
    }
}
