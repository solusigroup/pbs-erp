<?php

namespace App\Http\Controllers;

use App\Models\Akun;
use App\Models\CugilCustomer;
use App\Models\CugilPurchaseOrder;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\CugilSupplier;
use App\Models\JurnalUmum;
use App\Models\PengajuanDana;
use App\Models\Perusahaan;
use App\Models\Proyek;
use App\Models\TransaksiPajak;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $perusahaan = Perusahaan::first();

        // 1. Kas & Bank
        $kasBankTotal = Akun::where('tipe_akun', 'Kas & Bank')->sum('saldo_berjalan');

        // 2. Piutang Usaha
        $piutangTotal = Akun::where('tipe_akun', 'Piutang')->sum('saldo_berjalan');

        // 3. Hutang Usaha & Kewajiban
        $hutangTotal = Akun::where('tipe_akun', 'Hutang')->sum('saldo_berjalan');

        // 4. Pendapatan Berjalan (YTD)
        $pendapatanTotal = Akun::where('tipe_akun', 'Pendapatan')->sum('saldo_berjalan');

        // 5. Beban Berjalan (YTD)
        $bebanTotal = Akun::where('tipe_akun', 'Beban')->sum('saldo_berjalan');

        // 6. Laba Rugi Operasional Berjalan
        $labaBerjalan = $pendapatanTotal - $bebanTotal;

        // 7. Pajak: Total Pajak Masa Berjalan & Pajak Belum Disetor
        $pajakBelumSetor = TransaksiPajak::where('status_bayar', 'Belum Disetor')->sum('nominal_pajak');
        $pajakSudahSetor = TransaksiPajak::where('status_bayar', 'Sudah Disetor')->sum('nominal_pajak');
        $transaksiPajakTerbaru = TransaksiPajak::latest()->take(5)->get();

        // 8. Pengajuan Dana Menunggu Approval BOD
        $pengajuanMenunggu = PengajuanDana::where('status', 'Menunggu Approval')->get();
        $pengajuanTerbaru = PengajuanDana::latest()->take(5)->get();

        // 9. Jurnal Transaksi Terkini
        $jurnalTerbaru = JurnalUmum::with('details.akun')->latest()->take(5)->get();

        // 10. Proyek Berjalan
        $proyekAktif = Proyek::where('status_proyek', 'Berjalan')->latest()->get();

        // 11. CUGIL Operasional Metrics
        $cugilTotalSales = CugilSale::sum('tagihan');
        $cugilTotalPaymentSales = CugilSale::sum('payment');
        $cugilTotalRawTagihan = CugilRawMaterial::sum('tagihan');
        $cugilTotalRawPayment = CugilRawMaterial::sum('payment');
        $cugilTotalSupplier = CugilSupplier::count();
        $cugilTotalCustomer = CugilCustomer::count();
        $cugilRecentSales = CugilSale::latest('tanggal')->take(5)->get();
        $cugilRecentRaw = CugilRawMaterial::latest('tanggal')->take(5)->get();

        return view('dashboard', compact(
            'user',
            'perusahaan',
            'kasBankTotal',
            'piutangTotal',
            'hutangTotal',
            'pendapatanTotal',
            'bebanTotal',
            'labaBerjalan',
            'pajakBelumSetor',
            'pajakSudahSetor',
            'transaksiPajakTerbaru',
            'pengajuanMenunggu',
            'pengajuanTerbaru',
            'jurnalTerbaru',
            'proyekAktif',
            'cugilTotalSales',
            'cugilTotalPaymentSales',
            'cugilTotalRawTagihan',
            'cugilTotalRawPayment',
            'cugilTotalSupplier',
            'cugilTotalCustomer',
            'cugilRecentSales',
            'cugilRecentRaw'
        ));
    }
}
