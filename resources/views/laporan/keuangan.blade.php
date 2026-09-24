@extends('layouts.admin')

@section('title', 'Laporan Keuangan Komprehensif (Laba Rugi & Neraca)')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-yellow-400 mb-1">
                <i class="fas fa-scale-balanced"></i>
                <span>Standar Akuntansi Keuangan (SAK)</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Laporan Keuangan Komprehensif</h1>
            <p class="text-slate-400 text-xs mt-1">Laba Rugi Operasional &amp; Laporan Posisi Keuangan (Neraca) PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-2 border border-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('laporan.keuangan.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('laporan.keuangan.print', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-yellow-500 hover:bg-yellow-600 text-slate-950 text-xs font-bold flex items-center gap-2 shadow-lg shadow-yellow-500/20 transition">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Executive Highlights -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800">
            <span class="text-xs font-semibold uppercase text-slate-400">Total Pendapatan Usaha</span>
            <h3 class="text-xl font-black text-emerald-400 mt-1">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Penjualan produk &amp; jasa olahan</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800">
            <span class="text-xs font-semibold uppercase text-slate-400">Laba Kotor Usaha</span>
            <h3 class="text-xl font-black text-yellow-400 mt-1">Rp {{ number_format($labaKotor, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Margin Kotor: {{ $totalPendapatan > 0 ? round(($labaKotor / $totalPendapatan) * 100, 1) : 0 }}%</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800">
            <span class="text-xs font-semibold uppercase text-slate-400">Laba Bersih Berjalan</span>
            <h3 class="text-xl font-black text-cyan-400 mt-1">Rp {{ number_format($labaBersih, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Net Margin: {{ $totalPendapatan > 0 ? round(($labaBersih / $totalPendapatan) * 100, 1) : 0 }}%</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800">
            <span class="text-xs font-semibold uppercase text-slate-400">Total Aset Terdaftar</span>
            <h3 class="text-xl font-black text-purple-300 mt-1">Rp {{ number_format($totalAset, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-emerald-400 mt-1 font-semibold flex items-center gap-1">
                <i class="fas fa-check-circle"></i> Neraca Saldo Seimbang
            </p>
        </div>
    </div>

    <!-- Dua Kolom: Laba Rugi (Kiri) & Posisi Keuangan / Neraca (Kanan) -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- 1. LAPORAN LABA RUGI -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-receipt text-emerald-400"></i>
                        <span>Laporan Laba Rugi</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Periode Tahun Berjalan (Year to Date)</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    SAK EMKM
                </span>
            </div>

            <!-- I. PENDAPATAN OPERASIONAL -->
            <div>
                <h4 class="text-xs font-bold text-emerald-400 uppercase tracking-wider mb-2">I. Pendapatan Operasional</h4>
                <div class="space-y-1.5 text-xs">
                    @forelse($akunPendapatan as $akun)
                        <div class="flex items-center justify-between py-1.5 px-3 rounded-lg bg-slate-800/40 hover:bg-slate-800/80">
                            <div>
                                <span class="font-semibold text-slate-200">{{ $akun->nama_akun }}</span>
                                <span class="text-[10px] text-slate-500 block font-mono">{{ $akun->kode_akun }}</span>
                            </div>
                            <span class="font-mono font-bold text-white">Rp {{ number_format($akun->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-2 text-slate-500 text-center">Belum ada pos akun pendapatan terdaftar.</div>
                    @endforelse
                    <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-emerald-500/10 border border-emerald-500/30 font-bold text-emerald-300 mt-2">
                        <span>Total Pendapatan Operasional</span>
                        <span class="font-mono">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- II. HARGA POKOK PENJUALAN (HPP) -->
            <div>
                <h4 class="text-xs font-bold text-rose-400 uppercase tracking-wider mb-2">II. Harga Pokok Penjualan (HPP)</h4>
                <div class="space-y-1.5 text-xs">
                    @forelse($akunHPP as $akun)
                        <div class="flex items-center justify-between py-1.5 px-3 rounded-lg bg-slate-800/40 hover:bg-slate-800/80">
                            <div>
                                <span class="font-semibold text-slate-200">{{ $akun->nama_akun }}</span>
                                <span class="text-[10px] text-slate-500 block font-mono">{{ $akun->kode_akun }}</span>
                            </div>
                            <span class="font-mono font-bold text-rose-300">Rp {{ number_format($akun->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-2 text-slate-500 text-center">Belum ada pos akun HPP.</div>
                    @endforelse
                    <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-rose-500/10 border border-rose-500/30 font-bold text-rose-300 mt-2">
                        <span>Total Beban Pokok Penjualan (HPP)</span>
                        <span class="font-mono">Rp {{ number_format($totalHPP, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- LABA KOTOR SUB-TOTAL -->
            <div class="flex items-center justify-between p-3.5 rounded-xl bg-amber-500/10 border border-amber-500/30 font-black text-sm text-amber-300">
                <span class="uppercase tracking-wider">Laba Kotor (Gross Profit)</span>
                <span class="font-mono text-base">Rp {{ number_format($labaKotor, 0, ',', '.') }}</span>
            </div>

            <!-- III. BEBAN OPERASIONAL & ADMINISTRASI -->
            <div>
                <h4 class="text-xs font-bold text-purple-400 uppercase tracking-wider mb-2">III. Beban Operasional &amp; Administrasi</h4>
                <div class="space-y-1.5 text-xs max-h-48 overflow-y-auto custom-scrollbar pr-1">
                    @forelse($akunBeban as $akun)
                        <div class="flex items-center justify-between py-1.5 px-3 rounded-lg bg-slate-800/40 hover:bg-slate-800/80">
                            <div>
                                <span class="font-semibold text-slate-200">{{ $akun->nama_akun }}</span>
                                <span class="text-[10px] text-slate-500 block font-mono">{{ $akun->kode_akun }}</span>
                            </div>
                            <span class="font-mono font-bold text-slate-300">Rp {{ number_format($akun->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @empty
                        <div class="py-2 text-slate-500 text-center">Belum ada pos beban usaha terdaftar.</div>
                    @endforelse
                </div>
                <div class="flex items-center justify-between py-2 px-3 rounded-lg bg-purple-500/10 border border-purple-500/30 font-bold text-purple-300 mt-2 text-xs">
                    <span>Total Beban Operasional</span>
                    <span class="font-mono">Rp {{ number_format($totalBeban, 0, ',', '.') }}</span>
                </div>
            </div>

            <!-- NET INCOME (LABA BERSIH) -->
            <div class="flex items-center justify-between p-4 rounded-xl bg-gradient-to-r from-cyan-600/30 via-slate-800 to-cyan-600/30 border-2 border-cyan-500/50 font-black text-sm text-white">
                <span class="uppercase tracking-wider">Laba Bersih Tahun Berjalan (Net Profit)</span>
                <span class="font-mono text-lg text-cyan-300">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
            </div>
        </div>

        <!-- 2. LAPORAN POSISI KEUANGAN (NERACA) -->
        <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-6 shadow-xl space-y-6">
            <div class="flex items-center justify-between border-b border-slate-800 pb-4">
                <div>
                    <h3 class="text-base font-black text-white uppercase tracking-wider flex items-center gap-2">
                        <i class="fas fa-building-columns text-cyan-400"></i>
                        <span>Posisi Keuangan (Neraca)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">Struktur Aset, Kewajiban &amp; Ekuitas Modal</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                    BALANCE SHEET
                </span>
            </div>

            <!-- AKTIVA / ASET -->
            <div class="space-y-4">
                <h4 class="text-xs font-bold text-cyan-400 uppercase tracking-wider">A. Aset &amp; Harta Perusahaan</h4>
                
                <!-- Kas & Setara Kas -->
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300">
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
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between text-xs font-bold text-slate-300">
                    <span>2. Piutang Dagang (Kastamer)</span>
                    <span class="font-mono text-white">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</span>
                </div>

                <!-- Persediaan Barang CUGIL -->
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 flex items-center justify-between text-xs font-bold text-slate-300">
                    <span>3. Persediaan Bahan &amp; Gilingan Plastik</span>
                    <span class="font-mono text-white">Rp {{ number_format($totalPersediaan, 0, ',', '.') }}</span>
                </div>

                <!-- Aset Tetap Pabrik -->
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300">
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
            <div class="space-y-4 pt-2 border-t border-slate-800">
                <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider">B. Kewajiban &amp; Ekuitas Modal</h4>
                
                <!-- Hutang Usaha -->
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300">
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
                <div class="p-3 rounded-xl bg-slate-800/40 border border-slate-800 space-y-1.5 text-xs">
                    <div class="flex items-center justify-between font-bold text-slate-300">
                        <span>2. Ekuitas &amp; Modal Disetor</span>
                        <span class="font-mono text-white">Rp {{ number_format($totalModal, 0, ',', '.') }}</span>
                    </div>
                    <div class="flex items-center justify-between text-[11px] text-emerald-400 pl-3">
                        <span>Laba Bersih Tahun Berjalan</span>
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
@endsection
