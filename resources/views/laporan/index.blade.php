@extends('layouts.admin')

@section('title', 'Pusat Laporan & Rekapitulasi')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-500 mb-1">
                <i class="fas fa-file-contract"></i>
                <span>Executive Reporting Hub</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Pusat Laporan &amp; Rekapitulasi</h1>
            <p class="text-slate-400 text-sm mt-1">Konsolidasi data operasional Cuci Giling, logistik, penjualan, posisi keuangan &amp; kepatuhan pajak PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex items-center gap-3">
            <button onclick="window.print()" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold flex items-center gap-2 shadow transition">
                <i class="fas fa-print text-amber-400"></i>
                <span>Cetak / PDF</span>
            </button>
            <a href="{{ route('analisis.index') }}" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-orange-500/20 transition">
                <i class="fas fa-chart-line"></i>
                <span>Buka Business Analytics</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Finansial & Operasional Global -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Pembelian Bahan</p>
                    <h3 class="text-xl font-black text-amber-400 mt-1">Rp {{ number_format($totalPembelian, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Bahan mentah dari {{ $totalSupplier }} supplier</p>
                </div>
                <div class="h-12 w-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Omset Penjualan</p>
                    <h3 class="text-xl font-black text-emerald-400 mt-1">Rp {{ number_format($totalPenjualan, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">Hasil gilingan ke {{ $totalCustomer }} buyer</p>
                </div>
                <div class="h-12 w-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                    <i class="fas fa-hand-holding-dollar"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Katalog SKU Aktif</p>
                    <h3 class="text-xl font-black text-cyan-400 mt-1">{{ number_format($totalBarang, 0, ',', '.') }} <span class="text-sm font-normal text-slate-400">SKU</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">Bahan baku, olahan &amp; brokering</p>
                </div>
                <div class="h-12 w-12 rounded-xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl">
                    <i class="fas fa-boxes-stacked"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-xs font-semibold uppercase tracking-wider text-slate-400">Kewajiban Pajak Terdata</p>
                    <h3 class="text-xl font-black text-purple-400 mt-1">Rp {{ number_format($totalPajak, 0, ',', '.') }}</h3>
                    <p class="text-[11px] text-slate-400 mt-1">PPN &amp; PPh Masa Berjalan</p>
                </div>
                <div class="h-12 w-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl">
                    <i class="fas fa-shield-halved"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Direktori Modul Laporan PBS -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        
        <!-- 1. Laporan CUGIL Rekapitulasi Stok & Rendemen -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-amber-500/50 p-6 flex flex-col justify-between transition-all duration-300 group hover:shadow-xl hover:shadow-orange-500/5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="h-12 w-12 rounded-xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-recycle"></i>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/20">
                        CUGIL REKAP
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white group-hover:text-amber-400 transition-colors">Rekapitulasi Stok &amp; Rendemen</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Laporan mutasi persediaan plastik, pergerakan stok awal, penerimaan bahan mentah, pengeluaran gilingan, dan valuasi aset gudang per kategori.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.cugil-rekap.export') }}" title="Export Excel" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <a href="{{ route('laporan.cugil-rekap.print') }}" target="_blank" title="Cetak / PDF" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-amber-500 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <a href="{{ route('laporan.cugil-rekap') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-amber-400 hover:text-amber-300 group-hover:translate-x-1 transition">
                    <span>Buka Laporan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- 2. Laporan Pembelian & Penerimaan Bahan -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-blue-500/50 p-6 flex flex-col justify-between transition-all duration-300 group hover:shadow-xl hover:shadow-blue-500/5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="h-12 w-12 rounded-xl bg-blue-500/10 border border-blue-500/20 flex items-center justify-center text-blue-400 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-truck-ramp-box"></i>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/20">
                        CUGIL RAW
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white group-hover:text-blue-400 transition-colors">Rekapitulasi Pembelian Bahan</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Rincian penerimaan bahan mentah (kresek HD/PE/PP), potongan rafaksi timbangan, ongkos angkut armada, serta posisi pelunasan hutang per supplier.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.pembelian.export') }}" title="Export Excel" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <a href="{{ route('laporan.pembelian.print') }}" target="_blank" title="Cetak / PDF" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-blue-500 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <a href="{{ route('laporan.pembelian') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-blue-400 hover:text-blue-300 group-hover:translate-x-1 transition">
                    <span>Buka Laporan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- 3. Laporan Penjualan & Pengiriman -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-emerald-500/50 p-6 flex flex-col justify-between transition-all duration-300 group hover:shadow-xl hover:shadow-emerald-500/5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="h-12 w-12 rounded-xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                        CUGIL SALES
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white group-hover:text-emerald-400 transition-colors">Rekapitulasi Penjualan Produk</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Catatan pengiriman cacahan/gilingan plastik, tonase kuintal/sak, fee makelar per transaksi, ongkos kuli &amp; angkut, serta status piutang buyer.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.penjualan.export') }}" title="Export Excel" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <a href="{{ route('laporan.penjualan.print') }}" target="_blank" title="Cetak / PDF" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-emerald-500 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <a href="{{ route('laporan.penjualan') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-emerald-400 hover:text-emerald-300 group-hover:translate-x-1 transition">
                    <span>Buka Laporan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- 4. Laporan Keuangan Komprehensif -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-yellow-500/50 p-6 flex flex-col justify-between transition-all duration-300 group hover:shadow-xl hover:shadow-yellow-500/5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="h-12 w-12 rounded-xl bg-yellow-500/10 border border-yellow-500/20 flex items-center justify-center text-yellow-400 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-scale-balanced"></i>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-yellow-500/10 text-yellow-400 border border-yellow-500/20">
                        STANDAR SAK
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white group-hover:text-yellow-400 transition-colors">Laba Rugi &amp; Posisi Keuangan</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Laporan Laba Rugi Komprehensif (Pendapatan, HPP, Laba Kotor, Beban Operasional) dan Posisi Keuangan (Neraca Saldo Aset, Hutang, Modal).
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.keuangan.export') }}" title="Export Excel" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <a href="{{ route('laporan.keuangan.print') }}" target="_blank" title="Cetak / PDF" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-yellow-500 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <a href="{{ route('laporan.keuangan') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-yellow-400 hover:text-yellow-300 group-hover:translate-x-1 transition">
                    <span>Buka Laporan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- 5. Laporan Kepatuhan Pajak (BOD Tax) -->
        <div class="bg-slate-900/80 rounded-2xl border border-slate-800 hover:border-purple-500/50 p-6 flex flex-col justify-between transition-all duration-300 group hover:shadow-xl hover:shadow-purple-500/5">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="h-12 w-12 rounded-xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-file-invoice-dollar"></i>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-purple-500/10 text-purple-400 border border-purple-500/20">
                        TAX COMPLIANCE
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white group-hover:text-purple-400 transition-colors">Rekapitulasi Pajak Masa &amp; SPT</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Rekapitulasi PPN Masukan &amp; Keluaran, PPh 21, PPh 22 (Pembelian Limbah Plastik), PPh 23, bukti setor NTPN, dan status kepatuhan perpajakan.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <a href="{{ route('laporan.pajak.export') }}" title="Export Excel" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-emerald-600 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-file-excel"></i>
                    </a>
                    <a href="{{ route('laporan.pajak.print') }}" target="_blank" title="Cetak / PDF" class="h-7 w-7 rounded-lg bg-slate-800 hover:bg-purple-500 text-slate-400 hover:text-white flex items-center justify-center text-xs transition">
                        <i class="fas fa-print"></i>
                    </a>
                </div>
                <a href="{{ route('laporan.pajak') }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-purple-400 hover:text-purple-300 group-hover:translate-x-1 transition">
                    <span>Buka Laporan</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

        <!-- 6. Executive Business Intelligence -->
        <div class="bg-gradient-to-br from-amber-500/10 via-slate-900 to-orange-500/10 rounded-2xl border border-amber-500/30 p-6 flex flex-col justify-between transition-all duration-300 group hover:shadow-xl hover:shadow-orange-500/10">
            <div>
                <div class="flex items-center justify-between mb-4">
                    <span class="h-12 w-12 rounded-xl bg-amber-500/20 border border-amber-500/40 flex items-center justify-center text-amber-400 text-xl group-hover:scale-110 transition-transform">
                        <i class="fas fa-chart-pie"></i>
                    </span>
                    <span class="text-[10px] uppercase font-bold tracking-wider px-2.5 py-1 rounded-full bg-amber-500/20 text-amber-300 border border-amber-500/30">
                        EXECUTIVE BI
                    </span>
                </div>
                <h3 class="text-lg font-bold text-white group-hover:text-amber-400 transition-colors">Analisis &amp; Business Intelligence</h3>
                <p class="text-xs text-slate-400 mt-2 leading-relaxed">
                    Visualisasi grafik interaktif, analisis rasio rendemen &amp; susut produksi, margin kontribusi per produk, umur piutang (AR Aging), dan analisis Pareto partner.
                </p>
            </div>
            <div class="mt-6 pt-4 border-t border-slate-800/80 flex items-center justify-between">
                <span class="text-xs text-amber-400 font-medium">DSS &amp; Visual BI</span>
                <a href="{{ route('analisis.index') }}" class="inline-flex items-center gap-2 text-xs font-bold text-amber-400 hover:text-amber-300 group-hover:translate-x-1 transition">
                    <span>Buka Analisis</span>
                    <i class="fas fa-arrow-right text-[10px]"></i>
                </a>
            </div>
        </div>

    </div>
</div>
@endsection
