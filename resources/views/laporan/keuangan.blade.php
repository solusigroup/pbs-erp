@extends('layouts.admin')

@section('title', 'Laporan Keuangan Komprehensif (Laba Rugi PBS & Neraca)')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-400 mb-1">
                <i class="fas fa-industry"></i>
                <span>Standar PBS Manufaktur &amp; SAK EP</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Laporan Keuangan Komprehensif</h1>
            <p class="text-slate-400 text-xs mt-1">Laba Rugi Operasional Cuci Giling (PBS) &amp; Laporan Posisi Keuangan (Neraca) PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-2 border border-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('laporan.keuangan.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel / CSV</span>
            </a>
            <a href="{{ route('laporan.keuangan.print', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold flex items-center gap-2 shadow-lg shadow-amber-500/20 transition">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Periode Tanggal & Penyesuaian Angka -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg space-y-4">
        <form method="GET" action="{{ route('laporan.keuangan') }}" id="filterFormKeuangan" class="space-y-4 text-xs">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
                <div>
                    <label class="block text-[11px] text-slate-400 font-semibold mb-1">Periode Transaksi (Dari Tanggal)</label>
                    <input type="date" name="tanggal_dari" id="inputTanggalDari" value="{{ $tanggalDari }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono focus:border-amber-500 focus:outline-none">
                </div>

                <div>
                    <label class="block text-[11px] text-slate-400 font-semibold mb-1">Periode Transaksi (Sampai Tanggal)</label>
                    <input type="date" name="tanggal_sampai" id="inputTanggalSampai" value="{{ $tanggalSampai }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono focus:border-amber-500 focus:outline-none">
                </div>

                <div class="flex items-center gap-1.5 pb-0.5">
                    <button type="button" onclick="setPresetKeuangan('bulan_ini')" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-semibold border border-slate-700 transition">
                        Bulan Ini
                    </button>
                    <button type="button" onclick="setPresetKeuangan('tahun_ini')" class="px-2.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-semibold border border-slate-700 transition">
                        Tahun Ini
                    </button>
                    <button type="submit" class="flex-1 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition flex items-center justify-center gap-1.5 shadow-lg shadow-emerald-600/20">
                        <i class="fas fa-filter"></i>
                        <span>Terapkan</span>
                    </button>
                </div>

                <div class="flex items-center justify-end pb-0.5">
                    <button type="button" onclick="document.getElementById('manualAdjustmentPanelKeuangan').classList.toggle('hidden')" class="w-full px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-300 text-xs font-semibold border border-amber-500/30 transition flex items-center justify-center gap-2">
                        <i class="fas fa-sliders text-amber-400"></i>
                        <span>Simulasi / Penyesuaian Angka</span>
                    </button>
                </div>
            </div>

            <!-- Collapsible Manual Adjustments Panel -->
            <div id="manualAdjustmentPanelKeuangan" class="hidden pt-4 border-t border-slate-800 space-y-4">
                <div class="bg-slate-950/60 p-4 rounded-xl border border-slate-800">
                    <div class="flex items-center justify-between mb-3">
                        <span class="font-bold text-amber-300 text-xs flex items-center gap-1.5">
                            <i class="fas fa-info-circle"></i> Parameter Penyesuaian Manual (Stock Opname Fisik &amp; FOH Pabrik)
                        </span>
                        <a href="{{ route('laporan.keuangan', ['tanggal_dari' => $tanggalDari, 'tanggal_sampai' => $tanggalSampai]) }}" class="text-[11px] text-rose-400 hover:underline">
                            Reset ke Nilai Database Riil
                        </a>
                    </div>
                    <p class="text-[11px] text-slate-400 mb-3">
                        Kosongkan field di bawah jika ingin menggunakan kalkulasi riil dari buku besar dan modul timbangan/surat jalan.
                    </p>
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-3 text-xs">
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">B.1 Persediaan Awal BJ (Rp)</label>
                            <input type="number" step="any" name="persediaan_awal_bj" value="{{ request('persediaan_awal_bj') }}" placeholder="Otomatis (0)" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">B.2.a Persediaan Awal BB (Rp)</label>
                            <input type="number" step="any" name="persediaan_awal_bb" value="{{ request('persediaan_awal_bb') }}" placeholder="Otomatis (0)" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">(-) Stock Akhir BB (Rp)</label>
                            <input type="number" step="any" name="stock_akhir_bb" value="{{ request('stock_akhir_bb') }}" placeholder="Otomatis (0)" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">B.9 Persediaan Akhir BJ (Rp)</label>
                            <input type="number" step="any" name="persediaan_akhir_bj" value="{{ request('persediaan_akhir_bj') }}" placeholder="Otomatis (0)" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>

                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">B.3 FOH BTKL (Rp)</label>
                            <input type="number" step="any" name="foh_btkl" value="{{ request('foh_btkl') }}" placeholder="Otomatis Jurnal" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">B.4 FOH Listrik (Rp)</label>
                            <input type="number" step="any" name="foh_listrik" value="{{ request('foh_listrik') }}" placeholder="Otomatis Jurnal" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">B.5 FOH Maintenance (Rp)</label>
                            <input type="number" step="any" name="foh_maintenance" value="{{ request('foh_maintenance') }}" placeholder="Otomatis Jurnal" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-400 font-semibold mb-1">E. Gaji Manajemen (Rp)</label>
                            <input type="number" step="any" name="gaji_manajemen" value="{{ request('gaji_manajemen') }}" placeholder="Otomatis Jurnal" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs">
                        </div>
                    </div>
                    <div class="mt-3 flex justify-end">
                        <button type="submit" class="px-4 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-400 text-slate-950 font-bold text-xs transition">
                            Terapkan Parameter Penyesuaian
                        </button>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <!-- Executive Highlights (Standar PBS) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
            <span class="text-xs font-semibold uppercase text-slate-400">Total Penjualan Bersih (A.4)</span>
            <h3 class="text-xl font-black text-emerald-400 mt-1">Rp {{ number_format($labaRugiPbs['total_penjualan_bersih'], 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Penjualan Jasa + Barang Bersih</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
            <span class="text-xs font-semibold uppercase text-slate-400">Total HPP / COGS (C)</span>
            <h3 class="text-xl font-black text-amber-400 mt-1">Rp {{ number_format($labaRugiPbs['C_total_cogs'], 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Bahan Baku + FOH + Beban Penjualan</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
            <span class="text-xs font-semibold uppercase text-slate-400">Laba / Rugi Bruto (D)</span>
            <h3 class="text-xl font-black {{ $labaRugiPbs['D_laba_rugi_bruto'] >= 0 ? 'text-cyan-400' : 'text-rose-400' }} mt-1">
                Rp {{ number_format($labaRugiPbs['D_laba_rugi_bruto'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-400 mt-1">
                Margin Kotor: {{ $labaRugiPbs['total_penjualan_bersih'] > 0 ? round(($labaRugiPbs['D_laba_rugi_bruto'] / $labaRugiPbs['total_penjualan_bersih']) * 100, 1) : 0 }}%
            </p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
            <span class="text-xs font-semibold uppercase text-slate-400">Net Income (G)</span>
            <h3 class="text-xl font-black {{ $labaRugiPbs['G_net_income'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }} mt-1">
                Rp {{ number_format($labaRugiPbs['G_net_income'], 0, ',', '.') }}
            </h3>
            <p class="text-[11px] text-slate-400 mt-1">
                Net Margin: {{ $labaRugiPbs['total_penjualan_bersih'] > 0 ? round(($labaRugiPbs['G_net_income'] / $labaRugiPbs['total_penjualan_bersih']) * 100, 1) : 0 }}%
            </p>
        </div>
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 1: LAPORAN LABA RUGI OPERASIONAL PABRIK (FORMAT STANDAR PBS)      -->
    <!-- ========================================================================= -->
    <div class="bg-slate-900/90 rounded-3xl border-2 border-amber-500/40 p-6 shadow-2xl space-y-6">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 border-b border-slate-800 pb-4">
            <div>
                <div class="flex items-center gap-2.5">
                    <span class="px-2.5 py-1 rounded-lg text-xs font-black uppercase bg-amber-500/20 text-amber-300 border border-amber-500/40">
                        Format Standar PBS
                    </span>
                    <h2 class="text-lg font-black text-white tracking-wide flex items-center gap-2">
                        <i class="fas fa-file-invoice-dollar text-amber-400"></i>
                        <span>Laporan Laba Rugi Operasional Cuci Giling (PBS)</span>
                    </h2>
                </div>
                <p class="text-xs text-slate-400 mt-1">
                    Struktur Resmi PBS: Penjualan Bersih, HPP Produksi, FOH Pabrik, Biaya Penjualan, Laba Bruto, Operasional, Net Income (A s/d G)
                    &bull; Periode: <span class="font-mono text-amber-300 font-semibold">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</span>
                </p>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('laporan.keuangan.export', request()->query()) }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600/30 hover:bg-emerald-600/50 text-emerald-300 text-xs font-bold border border-emerald-500/40 transition flex items-center gap-1.5">
                    <i class="fas fa-file-excel"></i>
                    <span>Export CSV</span>
                </a>
                <a href="{{ route('laporan.keuangan.print', request()->query()) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-amber-500/20">
                    <i class="fas fa-print"></i>
                    <span>Cetak A4</span>
                </a>
            </div>
        </div>

        <!-- Render Table Partial Laba Rugi PBS -->
        @include('akuntansi.partials.laba_rugi_pbs_table', ['labaRugiPbs' => $labaRugiPbs])
    </div>

    <!-- ========================================================================= -->
    <!-- BAGIAN 2: LAPORAN POSISI KEUANGAN (NERACA SAK EP)                         -->
    <!-- ========================================================================= -->
    <div class="bg-slate-900/90 rounded-3xl border border-slate-800 p-6 shadow-2xl space-y-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4">
            <div>
                <h3 class="text-lg font-black text-white uppercase tracking-wider flex items-center gap-2">
                    <i class="fas fa-building-columns text-cyan-400"></i>
                    <span>Laporan Posisi Keuangan (Neraca)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Struktur Aset, Kewajiban &amp; Ekuitas Modal Sesuai Standar Akuntansi Keuangan</p>
            </div>
            <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                BALANCE SHEET SAK EP
            </span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- AKTIVA / ASET -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-cyan-400 uppercase tracking-wider">A. Aset &amp; Harta Perusahaan</h4>
                
                <!-- Kas & Setara Kas -->
                <div class="p-3.5 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300 pb-1 border-b border-slate-800">
                        <span>1. Kas &amp; Bank</span>
                        <span class="font-mono text-white">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</span>
                    </div>
                    @foreach($akunKasBank as $ak)
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pl-3">
                            <span>{{ $ak->nama_akun }}</span>
                            <span class="font-mono">Rp {{ number_format($ak->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Piutang Usaha -->
                <div class="p-3.5 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between text-xs font-bold text-slate-300">
                    <span>2. Piutang Dagang (Kastamer)</span>
                    <span class="font-mono text-white">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
                </div>

                <!-- Persediaan Barang CUGIL -->
                <div class="p-3.5 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between text-xs font-bold text-slate-300">
                    <span>3. Persediaan Bahan &amp; Gilingan Plastik</span>
                    <span class="font-mono text-white">Rp {{ number_format($totalPersediaan, 0, ',', '.') }}</span>
                </div>

                <!-- Aset Tetap Pabrik -->
                <div class="p-3.5 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300 pb-1 border-b border-slate-800">
                        <span>4. Aset Tetap &amp; Mesin Pabrik</span>
                        <span class="font-mono text-white">Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}</span>
                    </div>
                    @foreach($akunAsetTetap as $ak)
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pl-3">
                            <span>{{ $ak->nama_akun }}</span>
                            <span class="font-mono">Rp {{ number_format($ak->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-cyan-500/10 border border-cyan-500/30 font-black text-sm text-cyan-300">
                    <span class="uppercase">Total Aktiva / Aset</span>
                    <span class="font-mono text-base">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- PASIVA / KEWAJIBAN & EKUITAS -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">B. Kewajiban &amp; Ekuitas Modal</h4>
                
                <!-- Hutang Usaha -->
                <div class="p-3.5 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300 pb-1 border-b border-slate-800">
                        <span>1. Kewajiban Jangka Pendek (Hutang Dagang)</span>
                        <span class="font-mono text-rose-400">Rp {{ number_format($totalHutang, 0, ',', '.') }}</span>
                    </div>
                    @foreach($akunHutang as $ak)
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pl-3">
                            <span>{{ $ak->nama_akun }}</span>
                            <span class="font-mono">Rp {{ number_format($ak->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                </div>

                <!-- Modal Disetor & Laba Ditahan -->
                <div class="p-3.5 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300 pb-1 border-b border-slate-800">
                        <span>2. Ekuitas &amp; Modal Disetor</span>
                        <span class="font-mono text-white">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
                    </div>
                    @foreach($akunModal as $ak)
                        <div class="flex items-center justify-between text-[11px] text-slate-400 pl-3">
                            <span>{{ $ak->nama_akun }}</span>
                            <span class="font-mono">Rp {{ number_format($ak->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="flex items-center justify-between text-[11px] text-emerald-400 pl-3 pt-1 border-t border-slate-800">
                        <span class="font-semibold">Laba Bersih Tahun Berjalan</span>
                        <span class="font-mono font-bold">+Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
                    </div>
                </div>

                <div class="flex items-center justify-between p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 font-black text-sm text-amber-300">
                    <span class="uppercase">Total Kewajiban &amp; Ekuitas</span>
                    <span class="font-mono text-base">Rp {{ number_format($totalKewajibanEkuitas, 0, ',', '.') }}</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Tanda Tangan / Otorisasi Dokumen Laporan -->
    <div class="mt-8 pt-6 border-t border-slate-800 grid grid-cols-2 gap-8 text-center text-xs">
        <div>
            <p class="text-slate-400">Disiapkan Oleh Accounting &amp; Finance,</p>
            <div class="h-16"></div>
            <p class="text-white font-bold underline">( Senior Accountant PBS )</p>
        </div>
        <div>
            <p class="text-slate-400">Disetujui &amp; Disahkan Oleh,</p>
            <p class="text-amber-400 font-semibold mt-1">Direktur Keuangan &amp; Perpajakan (BOD)</p>
            <div class="h-16"></div>
            <p class="text-white font-bold underline">Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.</p>
            <p class="text-[11px] text-slate-400">Board of Director PT Pinastika Bhakti Semesta</p>
        </div>
    </div>
</div>

<script>
function setPresetKeuangan(type) {
    const today = new Date();
    const yyyy = today.getFullYear();
    const mm = String(today.getMonth() + 1).padStart(2, '0');
    const dd = String(today.getDate()).padStart(2, '0');

    let dari = '';
    let sampai = `${yyyy}-${mm}-${dd}`;

    if (type === 'bulan_ini') {
        dari = `${yyyy}-${mm}-01`;
    } else if (type === 'tahun_ini') {
        dari = `${yyyy}-01-01`;
    }

    document.getElementById('inputTanggalDari').value = dari;
    document.getElementById('inputTanggalSampai').value = sampai;
    document.getElementById('filterFormKeuangan').submit();
}
</script>
@endsection
