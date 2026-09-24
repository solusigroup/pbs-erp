@extends('layouts.admin')

@section('title', 'Laporan Arus Kas (Metode Langsung) - Akuntansi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-teal-500/20 to-emerald-500/20 border border-teal-500/30 text-teal-400 shadow-lg shadow-teal-500/10">
                    <i class="fas fa-money-bill-wave text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                        Laporan Arus Kas (Cash Flow Statement)
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-teal-500/10 text-teal-400 border border-teal-500/30 font-semibold font-mono">
                            Metode Langsung (SAK)
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Laporan pergerakan arus kas riil PT Pinastika Bhakti Semesta (Tahun {{ $tahun }})</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <form method="GET" action="{{ route('akuntansi.arus-kas') }}" class="flex items-center gap-2">
                <select name="tahun" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-semibold">
                    @for($y = date('Y'); $y >= 2024; $y--)
                        <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>Tahun {{ $y }}</option>
                    @endfor
                </select>
            </form>
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-print text-teal-400"></i>
                <span>Cetak Laporan</span>
            </button>
        </div>
    </div>

    <!-- 3 Summary Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Arus Kas Bersih Aktivitas Operasi</span>
            <strong class="text-lg font-black {{ $arusKasOperasi >= 0 ? 'text-emerald-400' : 'text-rose-400' }} block mt-2 font-mono">
                Rp {{ number_format($arusKasOperasi, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Operasional bisnis utama</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Arus Kas Bersih Aktivitas Investasi</span>
            <strong class="text-lg font-black {{ $arusKasInvestasi >= 0 ? 'text-emerald-400' : 'text-rose-400' }} block mt-2 font-mono">
                Rp {{ number_format($arusKasInvestasi, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Perolehan/pelepasan aset</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Arus Kas Bersih Aktivitas Pendanaan</span>
            <strong class="text-lg font-black {{ $arusKasPendanaan >= 0 ? 'text-emerald-400' : 'text-rose-400' }} block mt-2 font-mono">
                Rp {{ number_format($arusKasPendanaan, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Modal &amp; pembiayaan</span>
        </div>
    </div>

    <!-- Main Cash Flow Statement Document Card -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-6 max-w-4xl mx-auto">
        <div class="text-center pb-4 border-b border-slate-800">
            <h3 class="text-base font-black text-white uppercase tracking-wider">PT PINASTIKA BHAKTI SEMESTA</h3>
            <h4 class="text-sm font-bold text-amber-400 mt-0.5">LAPORAN ARUS KAS (METODE LANGSUNG)</h4>
            <p class="text-xs text-slate-400 mt-0.5">Untuk Tahun yang Berakhir pada 31 Desember {{ $tahun }}</p>
        </div>

        <div class="space-y-6 text-xs text-slate-200">
            <!-- 1. AKTIVITAS OPERASI -->
            <div>
                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider flex items-center gap-2 mb-3">
                    <i class="fas fa-industry"></i>
                    <span>I. ARUS KAS DARI AKTIVITAS OPERASI</span>
                </h4>
                <div class="space-y-2 pl-4">
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Penerimaan Kas dari Pelanggan &amp; Penjualan Usaha</span>
                        <span class="font-mono text-emerald-400 font-medium">Rp {{ number_format($penerimaanPelanggan, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Pembayaran Kas kepada Pemasok &amp; Pembelian Bahan Baku</span>
                        <span class="font-mono text-rose-400 font-medium">(Rp {{ number_format($pembayaranHpp, 0, ',', '.') }})</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Pembayaran Kas untuk Beban Gaji, Honor &amp; Karyawan</span>
                        <span class="font-mono text-rose-400 font-medium">(Rp {{ number_format($pembayaranGaji, 0, ',', '.') }})</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Pembayaran Kas untuk Beban Operasional, Kantor &amp; Utilitas</span>
                        <span class="font-mono text-rose-400 font-medium">(Rp {{ number_format($pembayaranOperasional, 0, ',', '.') }})</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Pembayaran Kas untuk Pajak (PPh Badan, PPh 21/23 &amp; PPN)</span>
                        <span class="font-mono text-rose-400 font-medium">(Rp {{ number_format($pembayaranPajak, 0, ',', '.') }})</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-slate-950/60 font-bold mt-2">
                        <span class="text-slate-200">Arus Kas Bersih yang Diperoleh dari (Digunakan untuk) Aktivitas Operasi:</span>
                        <span class="font-mono text-sm {{ $arusKasOperasi >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            Rp {{ number_format($arusKasOperasi, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 2. AKTIVITAS INVESTASI -->
            <div>
                <h4 class="text-xs font-bold text-sky-400 uppercase tracking-wider flex items-center gap-2 mb-3">
                    <i class="fas fa-building-columns"></i>
                    <span>II. ARUS KAS DARI AKTIVITAS INVESTASI</span>
                </h4>
                <div class="space-y-2 pl-4">
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Pengeluaran Kas untuk Perolehan Aset Tetap (Kendaraan, IT &amp; Mesin)</span>
                        <span class="font-mono text-rose-400 font-medium">(Rp {{ number_format($perolehanAset, 0, ',', '.') }})</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Penerimaan Kas dari Pelepasan / Penjualan Aset Tetap</span>
                        <span class="font-mono text-slate-400 font-medium">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-slate-950/60 font-bold mt-2">
                        <span class="text-slate-200">Arus Kas Bersih yang Digunakan untuk Aktivitas Investasi:</span>
                        <span class="font-mono text-sm {{ $arusKasInvestasi >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            Rp {{ number_format($arusKasInvestasi, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- 3. AKTIVITAS PENDANAAN -->
            <div>
                <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider flex items-center gap-2 mb-3">
                    <i class="fas fa-hand-holding-dollar"></i>
                    <span>III. ARUS KAS DARI AKTIVITAS PENDANAAN</span>
                </h4>
                <div class="space-y-2 pl-4">
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Penerimaan dari Penyetoran Modal Saham</span>
                        <span class="font-mono text-emerald-400 font-medium">Rp {{ number_format($setoranModal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between py-1 border-b border-slate-800/40">
                        <span class="text-slate-300">Penarikan Modal / Pembagian Dividen Pemegang Saham</span>
                        <span class="font-mono text-slate-400 font-medium">Rp 0</span>
                    </div>
                    <div class="flex items-center justify-between py-2 px-3 rounded-xl bg-slate-950/60 font-bold mt-2">
                        <span class="text-slate-200">Arus Kas Bersih yang Diperoleh dari Aktivitas Pendanaan:</span>
                        <span class="font-mono text-sm {{ $arusKasPendanaan >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                            Rp {{ number_format($arusKasPendanaan, 0, ',', '.') }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- REKAPITULASI TOTAL & SALDO KAS -->
            <div class="pt-4 border-t-2 border-slate-700 space-y-3">
                <div class="p-3.5 rounded-2xl bg-gradient-to-r from-amber-500/10 to-orange-500/10 border border-amber-500/30 flex items-center justify-between font-bold">
                    <span class="text-slate-200">KENAIKAN (PENURUNAN) BERSIH KAS &amp; SETARA KAS:</span>
                    <span class="font-mono text-base {{ $kenaikanKasBersih >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                        Rp {{ number_format($kenaikanKasBersih, 0, ',', '.') }}
                    </span>
                </div>

                <div class="flex items-center justify-between py-1.5 px-3 border-b border-slate-800">
                    <span class="text-slate-400">Saldo Kas &amp; Setara Kas pada Awal Periode (1 Januari {{ $tahun }}):</span>
                    <span class="font-mono font-semibold text-white">Rp {{ number_format($saldoAwalKas, 0, ',', '.') }}</span>
                </div>

                <div class="p-3.5 rounded-2xl bg-emerald-950/30 border border-emerald-500/40 flex items-center justify-between font-bold">
                    <span class="text-emerald-300">SALDO KAS &amp; SETARA KAS PADA AKHIR PERIODE (31 Desember {{ $tahun }}):</span>
                    <span class="font-mono text-lg text-emerald-300">
                        Rp {{ number_format($saldoAkhirKas, 0, ',', '.') }}
                    </span>
                </div>
            </div>

            <!-- Tanda Tangan Resmi Direksi -->
            <div class="pt-8 grid grid-cols-2 gap-8 text-center">
                <div>
                    <span class="text-[11px] text-slate-400 block mb-12">Disiapkan Oleh:</span>
                    <strong class="text-slate-200 block border-b border-slate-700 pb-1 max-w-[200px] mx-auto">Finance &amp; Accounting Team</strong>
                    <span class="text-[10px] text-slate-500 mt-1 block">Staff Akuntansi PBS</span>
                </div>
                <div>
                    <span class="text-[11px] text-slate-400 block mb-12">Disetujui Oleh:</span>
                    <strong class="text-amber-400 block border-b border-slate-700 pb-1 max-w-[280px] mx-auto">Kurniawan, S.E., Ak., CA., M.Ak., CMA.</strong>
                    <span class="text-[10px] text-slate-400 mt-1 block">Board of Director (Finance &amp; Tax)</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
