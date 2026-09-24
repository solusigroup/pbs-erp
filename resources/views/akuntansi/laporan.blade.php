@extends('layouts.admin')

@section('title', 'Laporan Keuangan - Laba Rugi & Neraca')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white">Laporan Keuangan Korporasi</h2>
            <p class="text-xs text-slate-400 mt-0.5">Laba Rugi & Neraca Komprehensif PT Pinastika Bhakti Semesta (Tahun Berjalan 2026)</p>
        </div>
        <button onclick="window.print()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2 self-start">
            <i class="fas fa-print"></i>
            <span>Cetak Laporan</span>
        </button>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <!-- Laporan Laba Rugi (Income Statement) -->
        <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-4">
            <div class="pb-3 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-chart-line text-amber-400"></i>
                        <span>Laporan Laba Rugi</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Periode Berjalan 2026</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    SAK Compliant
                </span>
            </div>

            <!-- Bagian Pendapatan -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-emerald-400 block mb-2">1. Pendapatan Usaha</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($pendapatan as $p)
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-300">{{ $p->kode_akun }} - {{ $p->nama_akun }}</span>
                            <span class="font-mono text-white">Rp {{ number_format($p->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="py-2.5 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1">
                        <span class="text-emerald-400">Total Pendapatan:</span>
                        <span class="text-emerald-400 font-mono text-sm">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Bagian Beban -->
            <div class="pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-rose-400 block mb-2">2. Beban Operasional & Pajak</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($beban as $b)
                        <div class="py-2 flex items-center justify-between">
                            <span class="text-slate-300">{{ $b->kode_akun }} - {{ $b->nama_akun }}</span>
                            <span class="font-mono text-white">Rp {{ number_format($b->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="py-2.5 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1">
                        <span class="text-rose-400">Total Beban:</span>
                        <span class="text-rose-400 font-mono text-sm">Rp {{ number_format($totalBeban, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Laba Bersih -->
            <div class="pt-4 border-t-2 border-slate-700/80">
                <div class="p-4 rounded-2xl bg-gradient-to-r from-amber-500/20 to-orange-500/10 border border-amber-500/30 flex items-center justify-between">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-amber-400">Laba Bersih Tahun Berjalan</span>
                        <p class="text-[10px] text-slate-400 mt-0.5">Pendapatan dikurangi Seluruh Beban Usaha</p>
                    </div>
                    <strong class="text-xl font-black text-amber-400 font-mono">
                        Rp {{ number_format($labaBersih, 0, ',', '.') }}
                    </strong>
                </div>
            </div>
        </div>

        <!-- Laporan Posisi Keuangan (Balance Sheet) -->
        <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-4">
            <div class="pb-3 border-b border-slate-800 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-scale-balanced text-amber-400"></i>
                        <span>Neraca (Posisi Keuangan)</span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Per Tanggal Hari Ini</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                    Balanced
                </span>
            </div>

            <!-- Aset -->
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-sky-400 block mb-2">Aset (Aktiva)</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($asetLancar as $al)
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $al->nama_akun }}</span>
                            <span class="font-mono text-white">Rp {{ number_format($al->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    @foreach($asetTetap as $at)
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $at->nama_akun }}</span>
                            <span class="font-mono text-white">Rp {{ number_format($at->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="py-2 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1">
                        <span class="text-sky-400">Total Aset:</span>
                        <span class="text-sky-400 font-mono text-sm">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Kewajiban & Ekuitas -->
            <div class="pt-2">
                <span class="text-xs font-bold uppercase tracking-wider text-amber-400 block mb-2">Kewajiban & Ekuitas (Pasiva)</span>
                <div class="divide-y divide-slate-800/60 text-xs">
                    @foreach($kewajiban as $k)
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $k->nama_akun }}</span>
                            <span class="font-mono text-white">Rp {{ number_format($k->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    @foreach($ekuitas as $e)
                        <div class="py-1.5 flex items-center justify-between text-slate-300">
                            <span>{{ $e->nama_akun }}</span>
                            <span class="font-mono text-white">Rp {{ number_format($e->saldo_berjalan, 0, ',', '.') }}</span>
                        </div>
                    @endforeach
                    <div class="py-1.5 flex items-center justify-between text-slate-300 font-semibold text-amber-400">
                        <span>Laba Bersih Tahun Berjalan</span>
                        <span class="font-mono">Rp {{ number_format($labaBersih, 0, ',', '.') }}</span>
                    </div>
                    <div class="py-2 flex items-center justify-between font-bold bg-slate-950/40 px-2 rounded-lg mt-1">
                        <span class="text-amber-400">Total Kewajiban & Ekuitas:</span>
                        <span class="text-amber-400 font-mono text-sm">Rp {{ number_format($totalKewajibanEkuitas, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Status Keseimbangan Neraca -->
            <div class="pt-2 text-center text-[11px] text-slate-400">
                <i class="fas fa-check-double text-emerald-400 mr-1"></i>
                Laporan Keuangan diverifikasi dan disupervisi langsung oleh Board of Director (Finance & Tax).
            </div>
        </div>
    </div>
</div>
@endsection
