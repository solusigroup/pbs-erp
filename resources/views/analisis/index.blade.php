@extends('layouts.admin')

@section('title', 'Executive Business Intelligence & Analisis Komparatif YoY - PBS-ERP')

@section('content')
<style>
    @media print {
        /* Zero graphic bloat, turn dark mode off on print */
        *, *::before, *::after {
            box-shadow: none !important;
            text-shadow: none !important;
            filter: none !important;
            transition: none !important;
        }

        html, body, main, div, .overflow-x-auto, .overflow-hidden {
            overflow: visible !important;
            height: auto !important;
            background: #ffffff !important;
            color: #0f172a !important;
            border-color: #cbd5e1 !important;
        }

        .no-print, header, footer, nav, aside, #mainSidebar, form, button, canvas, #formFilterTahun {
            display: none !important;
        }

        table {
            table-layout: fixed !important;
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 8pt !important;
            color: #000000 !important;
        }

        th, td {
            border: 1px solid #94a3b8 !important;
            padding: 4px 6px !important;
            color: #000000 !important;
        }

        th {
            background-color: #f1f5f9 !important;
        }

        @page {
            size: A4 portrait;
            margin: 8mm 10mm 10mm 10mm;
        }
    }
</style>
<div class="space-y-6">
    <!-- Header Page & Multi-Year Selection Toolbar -->
    <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-6">
            <div>
                <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-500 mb-1">
                    <i class="fas fa-chart-line"></i>
                    <span>Decision Support System (DSS) &bull; Executive BI</span>
                </div>
                <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Analisis &amp; Business Intelligence</h1>
                <p class="text-slate-400 text-sm mt-1">Evaluasi komparatif Penjualan vs Pembelian tahun ke tahun (YoY), efisiensi rendemen pabrik, &amp; profit margin PBS-ERP.</p>
            </div>

            <!-- Interactive Year Comparison Selector -->
            <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 bg-slate-950/80 p-3 rounded-2xl border border-slate-800 shadow-inner">
                <form method="GET" action="{{ route('analisis.index') }}" id="formFilterTahun" class="flex flex-wrap items-center gap-3">
                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-bold text-emerald-400 uppercase tracking-wider">Tahun Basis:</label>
                        <select name="tahun" onchange="document.getElementById('formFilterTahun').submit()" class="bg-slate-800 border border-slate-700 rounded-xl px-3 py-1.5 text-xs font-bold text-white focus:outline-none focus:border-emerald-500">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $tahun == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="text-slate-600 font-bold text-xs">VS</div>

                    <div class="flex items-center gap-2">
                        <label class="text-[11px] font-bold text-amber-400 uppercase tracking-wider">Pembanding:</label>
                        <select name="tahun_banding" onchange="document.getElementById('formFilterTahun').submit()" class="bg-slate-800 border border-slate-700 rounded-xl px-3 py-1.5 text-xs font-bold text-white focus:outline-none focus:border-amber-500">
                            @foreach($availableYears as $y)
                                <option value="{{ $y }}" {{ $tahunBanding == $y ? 'selected' : '' }}>{{ $y }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="px-3.5 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-400 text-slate-950 font-black text-xs transition flex items-center gap-1.5 shadow">
                        <i class="fas fa-arrows-rotate"></i>
                        <span>Bandingkan</span>
                    </button>
                </form>

                <div class="h-6 w-px bg-slate-800 hidden sm:block"></div>

                <a href="{{ route('analisis.cetak', ['tahun' => $tahun, 'tahun_banding' => $tahunBanding]) }}" target="_blank" class="px-3.5 py-1.5 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-950 font-black text-xs transition flex items-center justify-center gap-2 shadow-lg shadow-emerald-500/25" title="Buka Format Cetak Ringan & Rapi (Siap Cetak / PDF)">
                    <i class="fas fa-print"></i>
                    <span>Cetak Laporan / PDF</span>
                </a>
            </div>
        </div>

        <!-- Quick Comparison Presets -->
        <div class="flex flex-wrap items-center gap-2 mt-4 pt-4 border-t border-slate-800/80 text-xs">
            <span class="text-slate-400 text-[11px] font-medium"><i class="fas fa-bolt text-amber-400 mr-1"></i> Quick Presets:</span>
            @foreach($availableYears as $index => $y)
                @if(isset($availableYears[$index + 1]))
                    @php $prev = $availableYears[$index + 1]; @endphp
                    <a href="{{ route('analisis.index', ['tahun' => $y, 'tahun_banding' => $prev]) }}" 
                       class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ ($tahun == $y && $tahunBanding == $prev) ? 'bg-amber-500 text-slate-950 font-black shadow' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 border border-slate-700' }}">
                        {{ $y }} vs {{ $prev }}
                    </a>
                @endif
            @endforeach
            <a href="{{ route('analisis.index', ['tahun' => 2026, 'tahun_banding' => 2023]) }}" 
               class="px-2.5 py-1 rounded-lg text-[11px] font-bold transition {{ ($tahun == 2026 && $tahunBanding == 2023) ? 'bg-amber-500 text-slate-950 font-black shadow' : 'bg-slate-800/80 text-slate-300 hover:bg-slate-700 border border-slate-700' }}">
                2026 vs 2023 (Multi-Year)
            </a>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards: Komparatif Tahun Utama vs Tahun Pembanding -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        
        <!-- KPI 1: Perbandingan Omset Penjualan (Sales Revenue) -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden group hover:border-emerald-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Penjualan YoY</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $yoyMetrics['sales_delta_pct'] >= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                            {{ $yoyMetrics['sales_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['sales_delta_pct'] }}%
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-emerald-400 mt-1">Rp {{ number_format($yoyMetrics['sales1'], 0, ',', '.') }}</h3>
                    <div class="text-[11px] text-slate-400 mt-1 flex items-center justify-between gap-2">
                        <span>Thn {{ $tahunBanding }}: <span class="text-slate-300 font-mono">Rp {{ number_format($yoyMetrics['sales2'] / 1000000, 1, ',', '.') }}M</span></span>
                        <span class="{{ $yoyMetrics['sales_delta_rp'] >= 0 ? 'text-emerald-400 font-semibold' : 'text-rose-400 font-semibold' }}">
                            {{ $yoyMetrics['sales_delta_rp'] >= 0 ? '+' : '' }}Rp {{ number_format($yoyMetrics['sales_delta_rp'] / 1000000, 1, ',', '.') }}M
                        </span>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl group-hover:scale-110 transition">
                    <i class="fas fa-arrow-trend-{{ $yoyMetrics['sales_delta_pct'] >= 0 ? 'up' : 'down' }}"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                <span>Volume Terjual:</span>
                <span class="text-slate-200 font-bold">{{ number_format($yoyMetrics['sales_qty1'], 0, ',', '.') }} Kg <span class="text-[10px] text-slate-400">({{ $yoyMetrics['sales_delta_qty_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['sales_delta_qty_pct'] }}%)</span></span>
            </div>
        </div>

        <!-- KPI 2: Perbandingan Pembelian Bahan Baku (Raw Material Procurement) -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden group hover:border-amber-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pembelian Bahan YoY</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $yoyMetrics['raw_delta_pct'] <= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                            {{ $yoyMetrics['raw_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['raw_delta_pct'] }}%
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-amber-400 mt-1">Rp {{ number_format($yoyMetrics['raw1'], 0, ',', '.') }}</h3>
                    <div class="text-[11px] text-slate-400 mt-1 flex items-center justify-between gap-2">
                        <span>Thn {{ $tahunBanding }}: <span class="text-slate-300 font-mono">Rp {{ number_format($yoyMetrics['raw2'] / 1000000, 1, ',', '.') }}M</span></span>
                        <span class="{{ $yoyMetrics['raw_delta_rp'] <= 0 ? 'text-emerald-400 font-semibold' : 'text-amber-400 font-semibold' }}">
                            {{ $yoyMetrics['raw_delta_rp'] >= 0 ? '+' : '' }}Rp {{ number_format($yoyMetrics['raw_delta_rp'] / 1000000, 1, ',', '.') }}M
                        </span>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl group-hover:scale-110 transition">
                    <i class="fas fa-truck-fast"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                <span>Pasokan Diterima:</span>
                <span class="text-slate-200 font-bold">{{ number_format($yoyMetrics['raw_qty1'], 0, ',', '.') }} Kg <span class="text-[10px] text-slate-400">({{ $yoyMetrics['raw_delta_qty_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['raw_delta_qty_pct'] }}%)</span></span>
            </div>
        </div>

        <!-- KPI 3: Perbandingan Laba Kotor & Gross Profit Margin -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden group hover:border-cyan-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <div class="flex items-center gap-1.5">
                        <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Gross Margin Thn {{ $tahun }}</span>
                        <span class="px-1.5 py-0.5 rounded text-[10px] font-black {{ $yoyMetrics['margin_pct1'] >= 50 ? 'bg-cyan-500/20 text-cyan-400' : 'bg-emerald-500/20 text-emerald-400' }}">
                            {{ $yoyMetrics['margin_pct1'] }}%
                        </span>
                    </div>
                    <h3 class="text-2xl font-black text-cyan-400 mt-1">Rp {{ number_format($yoyMetrics['margin1'], 0, ',', '.') }}</h3>
                    <div class="text-[11px] text-slate-400 mt-1 flex items-center justify-between gap-2">
                        <span>Thn {{ $tahunBanding }}: <span class="text-slate-300 font-mono">{{ $yoyMetrics['margin_pct2'] }}%</span></span>
                        <span class="{{ $yoyMetrics['margin_delta_rp'] >= 0 ? 'text-emerald-400 font-semibold' : 'text-rose-400 font-semibold' }}">
                            {{ $yoyMetrics['margin_delta_rp'] >= 0 ? '+' : '' }}Rp {{ number_format($yoyMetrics['margin_delta_rp'] / 1000000, 1, ',', '.') }}M
                        </span>
                    </div>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-cyan-500/10 border border-cyan-500/20 flex items-center justify-center text-cyan-400 text-xl group-hover:scale-110 transition">
                    <i class="fas fa-sack-dollar"></i>
                </div>
            </div>
            <div class="w-full bg-slate-800 h-1.5 rounded-full mt-3 overflow-hidden">
                <div class="bg-cyan-500 h-full rounded-full" style="width: {{ min(100, max(0, $yoyMetrics['margin_pct1'])) }}%"></div>
            </div>
        </div>

        <!-- KPI 4: Rendemen Cuci Giling & Piutang / Hutang Position -->
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden group hover:border-purple-500/40 transition">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Rendemen &amp; Piutang</span>
                    <h3 class="text-2xl font-black text-purple-400 mt-1">{{ $rendemenPersen }}% <span class="text-xs text-slate-400 font-normal">Yield</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">AR Aktif: <span class="text-cyan-400 font-semibold">Rp {{ number_format($totalPiutang / 1000000, 1, ',', '.') }}M</span></p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl group-hover:scale-110 transition">
                    <i class="fas fa-recycle"></i>
                </div>
            </div>
            <div class="mt-3 pt-2.5 border-t border-slate-800/80 flex items-center justify-between text-[11px] text-slate-400">
                <span>Hutang Supplier:</span>
                <span class="text-rose-400 font-bold">Rp {{ number_format($totalHutang / 1000000, 1, ',', '.') }}M</span>
            </div>
        </div>

    </div>

    <!-- Grafik Baris 1: Komparatif Bulanan 12 Bulan (YoY) & Histori Multi-Tahun (5 Tahun) -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Tren Komparatif Bulanan: Penjualan vs Pembelian (2 Cols) -->
        <div class="lg:col-span-2 bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-chart-column text-amber-500"></i>
                        <span>Komparatif Finansial Bulanan: {{ $tahun }} vs {{ $tahunBanding }}</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Visualisasi perbandingan omset penjualan &amp; biaya pembelian bahan baku per bulan.</p>
                </div>
                <div class="flex flex-wrap items-center gap-3 text-xs">
                    <span class="flex items-center gap-1.5 text-emerald-400 font-semibold">
                        <span class="h-3 w-3 rounded-full bg-emerald-500 inline-block"></span> Jual {{ $tahun }}
                    </span>
                    <span class="flex items-center gap-1.5 text-emerald-300/60 font-semibold">
                        <span class="h-3 w-3 rounded-full bg-emerald-400/40 border border-dashed border-emerald-400 inline-block"></span> Jual {{ $tahunBanding }}
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-400 font-semibold">
                        <span class="h-3 w-3 rounded-full bg-amber-500 inline-block"></span> Beli {{ $tahun }}
                    </span>
                    <span class="flex items-center gap-1.5 text-amber-300/60 font-semibold">
                        <span class="h-3 w-3 rounded-full bg-amber-400/40 border border-dashed border-amber-400 inline-block"></span> Beli {{ $tahunBanding }}
                    </span>
                </div>
            </div>
            <div class="h-80">
                <canvas id="yoyMonthlyComparisonChart"></canvas>
            </div>
        </div>

        <!-- Histori Kinerja Multi-Tahun (1 Col) -->
        <div class="bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2 mb-1">
                    <i class="fas fa-chart-line-up text-cyan-400"></i>
                    <span>Trayektori Multi-Tahun</span>
                </h3>
                <p class="text-xs text-slate-400">Performa tahunan PBS-ERP (2022 - 2026)</p>
            </div>
            <div class="h-64 my-2">
                <canvas id="multiYearBarChart"></canvas>
            </div>
            <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-800 flex items-center justify-between">
                <span>Total Omset 5 Tahun:</span>
                <span class="text-emerald-400 font-bold font-mono">Rp {{ number_format(array_sum($multiYearSales) / 1000000000, 2, ',', '.') }} Miliar</span>
            </div>
        </div>

    </div>

    <!-- Tabel Komparatif Bulanan Detail (Januari - Desember) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-table-columns text-emerald-400"></i>
                    <span>Tabel Rincian Komparatif Penjualan &amp; Pembelian Bulanan (YoY Matrix)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Analisis delta nominal (Rp) &amp; persentase kenaikan/penurunan per bulan antara Tahun {{ $tahun }} vs {{ $tahunBanding }}.</p>
            </div>
            <div class="flex items-center gap-2 text-xs font-semibold">
                <span class="px-2.5 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Basis: {{ $tahun }}</span>
                <span class="px-2.5 py-1 rounded-lg bg-amber-500/10 text-amber-400 border border-amber-500/20">Banding: {{ $tahunBanding }}</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-black tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th rowspan="2" class="px-4 py-3 text-center border-r border-slate-800/80">Bulan</th>
                        <th colspan="3" class="px-4 py-2 text-center text-emerald-400 border-r border-slate-800/80 bg-emerald-950/20">PENJUALAN (OMSET)</th>
                        <th colspan="3" class="px-4 py-2 text-center text-amber-400 border-r border-slate-800/80 bg-amber-950/20">PEMBELIAN (BAHAN BAKU)</th>
                        <th colspan="3" class="px-4 py-2 text-center text-cyan-400 bg-cyan-950/20">LABA KOTOR (GROSS PROFIT)</th>
                    </tr>
                    <tr>
                        <!-- Penjualan -->
                        <th class="px-3 py-2 text-right text-emerald-300 font-mono">{{ $tahun }}</th>
                        <th class="px-3 py-2 text-right text-slate-400 font-mono">{{ $tahunBanding }}</th>
                        <th class="px-3 py-2 text-center text-emerald-400 border-r border-slate-800/80">Delta YoY</th>
                        <!-- Pembelian -->
                        <th class="px-3 py-2 text-right text-amber-300 font-mono">{{ $tahun }}</th>
                        <th class="px-3 py-2 text-right text-slate-400 font-mono">{{ $tahunBanding }}</th>
                        <th class="px-3 py-2 text-center text-amber-400 border-r border-slate-800/80">Delta YoY</th>
                        <!-- Laba Kotor -->
                        <th class="px-3 py-2 text-right text-cyan-300 font-mono">{{ $tahun }}</th>
                        <th class="px-3 py-2 text-right text-slate-400 font-mono">{{ $tahunBanding }}</th>
                        <th class="px-3 py-2 text-center text-cyan-400">Pertumbuhan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @foreach($monthlyYoYMatrix as $m)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-2.5 font-bold text-white border-r border-slate-800/80 text-center">
                                <span class="hidden sm:inline">{{ $m['bulan_nama'] }}</span>
                                <span class="sm:hidden">{{ $m['bulan_singkat'] }}</span>
                            </td>

                            <!-- Penjualan Tahun 1 & 2 & Delta -->
                            <td class="px-3 py-2.5 text-right font-mono {{ $m['sales_1'] > 0 ? 'text-emerald-300 font-semibold' : 'text-slate-500' }}">
                                {{ $m['sales_1'] > 0 ? 'Rp ' . number_format($m['sales_1'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-slate-400">
                                {{ $m['sales_2'] > 0 ? 'Rp ' . number_format($m['sales_2'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-center border-r border-slate-800/80">
                                @if($m['sales_1'] == 0 && $m['sales_2'] == 0)
                                    <span class="text-slate-600">-</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold text-[10px] {{ $m['sales_delta'] >= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        <i class="fas fa-caret-{{ $m['sales_delta'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $m['sales_grow'] >= 0 ? '+' : '' }}{{ $m['sales_grow'] }}%
                                    </span>
                                @endif
                            </td>

                            <!-- Pembelian Tahun 1 & 2 & Delta -->
                            <td class="px-3 py-2.5 text-right font-mono {{ $m['raw_1'] > 0 ? 'text-amber-300 font-semibold' : 'text-slate-500' }}">
                                {{ $m['raw_1'] > 0 ? 'Rp ' . number_format($m['raw_1'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-slate-400">
                                {{ $m['raw_2'] > 0 ? 'Rp ' . number_format($m['raw_2'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-center border-r border-slate-800/80">
                                @if($m['raw_1'] == 0 && $m['raw_2'] == 0)
                                    <span class="text-slate-600">-</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold text-[10px] {{ $m['raw_delta'] <= 0 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                        <i class="fas fa-caret-{{ $m['raw_delta'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $m['raw_grow'] >= 0 ? '+' : '' }}{{ $m['raw_grow'] }}%
                                    </span>
                                @endif
                            </td>

                            <!-- Laba Kotor Tahun 1 & 2 & Delta -->
                            <td class="px-3 py-2.5 text-right font-mono {{ $m['margin_1'] >= 0 ? 'text-cyan-300 font-semibold' : 'text-rose-400 font-semibold' }}">
                                {{ $m['margin_1'] != 0 ? 'Rp ' . number_format($m['margin_1'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-right font-mono text-slate-400">
                                {{ $m['margin_2'] != 0 ? 'Rp ' . number_format($m['margin_2'], 0, ',', '.') : '-' }}
                            </td>
                            <td class="px-3 py-2.5 text-center">
                                @if($m['margin_1'] == 0 && $m['margin_2'] == 0)
                                    <span class="text-slate-600">-</span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md font-bold text-[10px] {{ $m['margin_delta'] >= 0 ? 'bg-cyan-500/10 text-cyan-400 border border-cyan-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                        <i class="fas fa-caret-{{ $m['margin_delta'] >= 0 ? 'up' : 'down' }}"></i>
                                        {{ $m['margin_grow'] >= 0 ? '+' : '' }}{{ $m['margin_grow'] }}%
                                    </span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="bg-slate-950 font-black text-slate-200 border-t-2 border-slate-700">
                    <tr>
                        <td class="px-4 py-3 text-center uppercase tracking-wider text-amber-400 border-r border-slate-800">TOTAL TAHUNAN</td>
                        <!-- Total Penjualan -->
                        <td class="px-3 py-3 text-right font-mono text-emerald-400">Rp {{ number_format($yoyMetrics['sales1'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-mono text-slate-400">Rp {{ number_format($yoyMetrics['sales2'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-center border-r border-slate-800">
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $yoyMetrics['sales_delta_pct'] >= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-rose-500/20 text-rose-400' }}">
                                {{ $yoyMetrics['sales_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['sales_delta_pct'] }}%
                            </span>
                        </td>
                        <!-- Total Pembelian -->
                        <td class="px-3 py-3 text-right font-mono text-amber-400">Rp {{ number_format($yoyMetrics['raw1'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-mono text-slate-400">Rp {{ number_format($yoyMetrics['raw2'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-center border-r border-slate-800">
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $yoyMetrics['raw_delta_pct'] <= 0 ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }}">
                                {{ $yoyMetrics['raw_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['raw_delta_pct'] }}%
                            </span>
                        </td>
                        <!-- Total Laba Kotor -->
                        <td class="px-3 py-3 text-right font-mono text-cyan-400">Rp {{ number_format($yoyMetrics['margin1'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-right font-mono text-slate-400">Rp {{ number_format($yoyMetrics['margin2'], 0, ',', '.') }}</td>
                        <td class="px-3 py-3 text-center">
                            <span class="px-2 py-0.5 rounded-md font-black text-xs {{ $yoyMetrics['margin_delta_pct'] >= 0 ? 'bg-cyan-500/20 text-cyan-400' : 'bg-rose-500/20 text-rose-400' }}">
                                {{ $yoyMetrics['margin_delta_pct'] >= 0 ? '+' : '' }}{{ $yoyMetrics['margin_delta_pct'] }}%
                            </span>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tabel Rekapitulasi Multi-Tahun (All Recorded Years) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-timeline text-cyan-400"></i>
                    <span>Tabel Histori Pertumbuhan &amp; Kinerja Multi-Tahun PBS-ERP</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Rekapitulasi volume, revenue, pembelian bahan baku, dan profitabilitas per tahun buku.</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-black bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">
                5 YEARS AUDIT TRAIL
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-black tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3 text-center">Tahun</th>
                        <th class="px-4 py-3 text-right">Omset Penjualan (Rp)</th>
                        <th class="px-4 py-3 text-right">Volume Jual (Kg)</th>
                        <th class="px-4 py-3 text-center">YoY Sales Growth</th>
                        <th class="px-4 py-3 text-right">Pembelian Bahan (Rp)</th>
                        <th class="px-4 py-3 text-right">Volume Pasok (Kg)</th>
                        <th class="px-4 py-3 text-center">YoY Cost Growth</th>
                        <th class="px-4 py-3 text-right">Gross Profit (Rp)</th>
                        <th class="px-4 py-3 text-center">Gross Margin %</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800 font-mono">
                    @foreach($multiYearSummary as $my)
                        <tr class="hover:bg-slate-800/40 transition {{ $my['tahun'] == $tahun ? 'bg-amber-500/5 font-bold' : '' }}">
                            <td class="px-4 py-3 text-center font-bold text-white">
                                <span class="px-2.5 py-1 rounded-lg {{ $my['tahun'] == $tahun ? 'bg-amber-500 text-slate-950 font-black' : 'bg-slate-800 text-slate-300' }}">
                                    {{ $my['tahun'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right text-emerald-400 font-semibold">Rp {{ number_format($my['sales_rp'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-slate-300">{{ number_format($my['sales_qty'], 0, ',', '.') }} Kg</td>
                            <td class="px-4 py-3 text-center">
                                @if($my['sales_growth'] !== null)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $my['sales_growth'] >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                        {{ $my['sales_growth'] >= 0 ? '+' : '' }}{{ $my['sales_growth'] }}%
                                    </span>
                                @else
                                    <span class="text-slate-600 font-sans text-[11px]">Baseline</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right text-amber-400 font-semibold">Rp {{ number_format($my['raw_rp'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-slate-300">{{ number_format($my['raw_qty'], 0, ',', '.') }} Kg</td>
                            <td class="px-4 py-3 text-center">
                                @if($my['raw_growth'] !== null)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $my['raw_growth'] <= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-amber-500/10 text-amber-400' }}">
                                        {{ $my['raw_growth'] >= 0 ? '+' : '' }}{{ $my['raw_growth'] }}%
                                    </span>
                                @else
                                    <span class="text-slate-600 font-sans text-[11px]">Baseline</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-black {{ $my['profit_rp'] >= 0 ? 'text-cyan-300' : 'text-rose-400' }}">
                                Rp {{ number_format($my['profit_rp'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded font-black text-[10px] {{ $my['margin_pct'] >= 40 ? 'bg-emerald-500/20 text-emerald-400' : ($my['margin_pct'] >= 20 ? 'bg-amber-500/20 text-amber-400' : 'bg-rose-500/20 text-rose-400') }}">
                                    {{ $my['margin_pct'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-center font-sans">
                                @if($my['profit_rp'] > 0 && $my['margin_pct'] >= 50)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">Highly Profitable</span>
                                @elseif($my['profit_rp'] > 0)
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-cyan-500/10 text-cyan-400 border border-cyan-500/20">Profitable</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-400">Setup / Trial</span>
                                @endif
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Grafik Baris 2: Analisis Umur Piutang (AR Aging) & Efisiensi Cuci Giling -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        
        <!-- AR Aging Analysis Bar Chart -->
        <div class="bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-clock-rotate-left text-cyan-400"></i>
                        <span>Analisis Umur Piutang (AR Aging Matrix)</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Struktur penagihan faktur penjualan berdasarkan hari jatuh tempo</p>
                </div>
            </div>
            <div class="h-64">
                <canvas id="arAgingChart"></canvas>
            </div>
            <div class="grid grid-cols-4 gap-2 mt-4 pt-4 border-t border-slate-800 text-center text-xs">
                <div class="bg-slate-800/50 p-2 rounded-xl border border-emerald-500/20">
                    <span class="text-[10px] text-slate-400 block font-semibold">Lancar (0-15 hr)</span>
                    <span class="font-bold text-emerald-400 font-mono text-[11px]">Rp {{ number_format($agingAR['current'] / 1000000, 1, ',', '.') }}M</span>
                </div>
                <div class="bg-slate-800/50 p-2 rounded-xl border border-blue-500/20">
                    <span class="text-[10px] text-slate-400 block font-semibold">16 - 30 Hari</span>
                    <span class="font-bold text-blue-400 font-mono text-[11px]">Rp {{ number_format($agingAR['day16_30'] / 1000000, 1, ',', '.') }}M</span>
                </div>
                <div class="bg-slate-800/50 p-2 rounded-xl border border-amber-500/20">
                    <span class="text-[10px] text-slate-400 block font-semibold">31 - 60 Hari</span>
                    <span class="font-bold text-amber-400 font-mono text-[11px]">Rp {{ number_format($agingAR['day31_60'] / 1000000, 1, ',', '.') }}M</span>
                </div>
                <div class="bg-slate-800/50 p-2 rounded-xl border border-rose-500/20">
                    <span class="text-[10px] text-slate-400 block font-semibold">> 60 Hari (Macet)</span>
                    <span class="font-bold text-rose-400 font-mono text-[11px]">Rp {{ number_format($agingAR['over60'] / 1000000, 1, ',', '.') }}M</span>
                </div>
            </div>
        </div>

        <!-- Rendemen & Efisiensi Cuci Giling -->
        <div class="bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-gauge-high text-amber-400"></i>
                        <span>Efisiensi Pabrik &amp; Rendemen Cuci Giling</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Keseimbangan input bahan mentah vs output cacahan bersih</p>
                </div>
                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">
                    YIELD ANALYSIS
                </span>
            </div>

            <div class="space-y-4">
                <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/60">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-slate-300 font-medium">Bahan Baku Masuk (Input)</span>
                        <span class="text-sm font-bold text-amber-400 font-mono">{{ number_format($totalBahanMasuk, 1, ',', '.') }} Kg</span>
                    </div>
                    <div class="w-full bg-slate-950 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-amber-500 h-full rounded-full" style="width: 100%"></div>
                    </div>
                </div>

                <div class="bg-slate-800/60 p-4 rounded-xl border border-slate-700/60">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs text-slate-300 font-medium">Hasil Cacahan Bersih (Output Cuci Giling)</span>
                        <span class="text-sm font-bold text-emerald-400 font-mono">{{ number_format($totalHasilCugil, 1, ',', '.') }} Kg</span>
                    </div>
                    <div class="w-full bg-slate-950 h-2.5 rounded-full overflow-hidden">
                        <div class="bg-emerald-500 h-full rounded-full" style="width: {{ min(100, $rendemenPersen) }}%"></div>
                    </div>
                </div>

                <div class="grid grid-cols-2 gap-3 pt-2">
                    <div class="bg-emerald-500/10 border border-emerald-500/20 p-3 rounded-xl text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Tingkat Rendemen</span>
                        <span class="text-xl font-black text-emerald-400 mt-1 block">{{ $rendemenPersen }}%</span>
                        <span class="text-[10px] text-emerald-400/80">Benchmark Pabrik: &gt; 80%</span>
                    </div>
                    <div class="bg-rose-500/10 border border-rose-500/20 p-3 rounded-xl text-center">
                        <span class="text-[10px] text-slate-400 uppercase font-bold block">Tingkat Susut (Waste)</span>
                        <span class="text-xl font-black text-rose-400 mt-1 block">{{ $susutPersen }}%</span>
                        <span class="text-[10px] text-rose-400/80">Kotoran, Lumpur &amp; Air</span>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Baris 3: Komposisi Volume Produk & Top 5 Buyers / Suppliers -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        
        <!-- Komposisi Volume Penjualan per Kategori (1 Col) -->
        <div class="bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl flex flex-col justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2 mb-1">
                    <i class="fas fa-chart-pie text-cyan-400"></i>
                    <span>Komposisi Volume Produk</span>
                </h3>
                <p class="text-xs text-slate-400">Distribusi tonase per kategori olahan</p>
            </div>
            <div class="h-56 relative flex items-center justify-center my-2">
                <canvas id="categoryDoughnutChart"></canvas>
            </div>
            <div class="text-center text-xs text-slate-400 pt-2 border-t border-slate-800">
                <span>Total Output Terdistribusi: </span>
                <span class="text-white font-bold">{{ number_format(array_sum($kategoriVolumes), 0, ',', '.') }} Kg</span>
            </div>
        </div>

        <!-- Top 5 Buyers (Pareto Matrix) -->
        <div class="bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-crown text-amber-400"></i>
                        <span>Top 5 Pembeli Terbesar</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Kontribusi omset &amp; status piutang buyer</p>
                </div>
                <span class="text-[10px] font-bold text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">PARETO</span>
            </div>
            <div class="space-y-3">
                @forelse($topBuyers as $b)
                    <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-800 hover:border-slate-700 transition">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-white">{{ $b->nama_buyer ?: $b->kode_customer }}</span>
                            <span class="font-bold text-emerald-400 font-mono">Rp {{ number_format($b->total_omset, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                            <span>Trx: {{ $b->frekuensi_beli }}x order</span>
                            <span class="{{ $b->sisa_piutang > 0 ? 'text-rose-400 font-semibold' : 'text-emerald-400 font-semibold' }}">
                                Piutang: Rp {{ number_format($b->sisa_piutang, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada data penjualan.</p>
                @endforelse
            </div>
        </div>

        <!-- Top 5 Suppliers (Pareto Matrix) -->
        <div class="bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
            <div class="flex items-center justify-between mb-4">
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <i class="fas fa-boxes-packing text-emerald-400"></i>
                        <span>Top 5 Pemasok Bahan Baku</span>
                    </h3>
                    <p class="text-xs text-slate-400 mt-0.5">Nilai pengadaan bahan &amp; kewajiban hutang</p>
                </div>
                <span class="text-[10px] font-bold text-emerald-400 bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">SUPPLY</span>
            </div>
            <div class="space-y-3">
                @forelse($topSuppliers as $s)
                    <div class="bg-slate-800/40 p-3 rounded-xl border border-slate-800 hover:border-slate-700 transition">
                        <div class="flex items-center justify-between text-xs">
                            <span class="font-bold text-white">{{ $s->nama_pemasok ?: $s->kode_supplier }}</span>
                            <span class="font-bold text-amber-400 font-mono">Rp {{ number_format($s->total_pasokan, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex items-center justify-between text-[11px] text-slate-400 mt-1">
                            <span>Pasok: {{ $s->frekuensi_pasok }}x trip</span>
                            <span class="{{ $s->sisa_hutang > 0 ? 'text-rose-400 font-semibold' : 'text-emerald-400 font-semibold' }}">
                                Hutang: Rp {{ number_format($s->sisa_hutang, 0, ',', '.') }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-400 py-4 text-center">Belum ada data pembelian bahan baku.</p>
                @endforelse
            </div>
        </div>

    </div>

    <!-- Tabel Profit Margin per SKU Barang -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-6 border-b border-slate-800 flex items-center justify-between">
            <div>
                <h3 class="text-base font-bold text-white flex items-center gap-2">
                    <i class="fas fa-tags text-emerald-400"></i>
                    <span>Analisis Margin Keuntungan per SKU Barang (Unit Economics)</span>
                </h3>
                <p class="text-xs text-slate-400 mt-0.5">Selisih harga jual terhadap harga pokok beli (HPP) dan potensi laba stok aktif</p>
            </div>
            <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                PROFITABILITY MATRIX
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/80 text-slate-400 uppercase font-bold tracking-wider text-[10px] border-b border-slate-800">
                    <tr>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Produk / Barang</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Harga Beli</th>
                        <th class="px-4 py-3 text-right">Harga Jual</th>
                        <th class="px-4 py-3 text-right">Margin / Kg</th>
                        <th class="px-4 py-3 text-center">Margin %</th>
                        <th class="px-4 py-3 text-right">Stok Aktif</th>
                        <th class="px-4 py-3 text-right">Potensi Laba Stok</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800">
                    @forelse($barangMargin as $bm)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 font-mono text-amber-400 font-bold">{{ $bm['kode'] }}</td>
                            <td class="px-4 py-3 font-bold text-white">{{ $bm['nama'] }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $bm['kategori'] }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono text-slate-400">Rp {{ number_format($bm['harga_beli'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-400 font-semibold">Rp {{ number_format($bm['harga_jual'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold {{ $bm['margin_rp'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }}">
                                Rp {{ number_format($bm['margin_rp'], 0, ',', '.') }}
                            </td>
                            <td class="px-4 py-3 text-center font-bold">
                                <span class="px-2 py-0.5 rounded-full text-[10px] {{ $bm['margin_pct'] >= 30 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($bm['margin_pct'] >= 15 ? 'bg-amber-500/10 text-amber-400 border border-amber-500/20' : 'bg-slate-800 text-slate-400') }}">
                                    {{ $bm['margin_pct'] }}%
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($bm['stok'], 1, ',', '.') }} Kg</td>
                            <td class="px-4 py-3 text-right font-mono font-black text-cyan-300">Rp {{ number_format($bm['potensi_laba'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">Belum ada data margin barang.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Chart.js CDN & Analytics Visualizations Initialization -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Common Dark Theme Options for Chart.js
        Chart.defaults.color = '#94a3b8';
        Chart.defaults.borderColor = '#1e293b';

        // 1. Multi-Dataset Monthly Comparison YoY Chart (Penjualan vs Pembelian Tahun 1 vs Tahun 2)
        const ctxYoY = document.getElementById('yoyMonthlyComparisonChart').getContext('2d');
        new Chart(ctxYoY, {
            type: 'bar',
            data: {
                labels: @json($monthlyLabels),
                datasets: [
                    {
                        label: 'Penjualan {{ $tahun }} (Rp)',
                        data: @json($monthlyPenjualan1),
                        backgroundColor: 'rgba(16, 185, 129, 0.85)',
                        borderColor: '#10b981',
                        borderWidth: 1.5,
                        borderRadius: 5,
                        order: 2
                    },
                    {
                        label: 'Penjualan {{ $tahunBanding }} (Rp)',
                        data: @json($monthlyPenjualan2),
                        backgroundColor: 'rgba(52, 211, 153, 0.35)',
                        borderColor: '#34d399',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        borderRadius: 5,
                        order: 3
                    },
                    {
                        label: 'Pembelian {{ $tahun }} (Rp)',
                        data: @json($monthlyPembelian1),
                        backgroundColor: 'rgba(245, 158, 11, 0.85)',
                        borderColor: '#f59e0b',
                        borderWidth: 1.5,
                        borderRadius: 5,
                        order: 4
                    },
                    {
                        label: 'Pembelian {{ $tahunBanding }} (Rp)',
                        data: @json($monthlyPembelian2),
                        backgroundColor: 'rgba(251, 191, 36, 0.35)',
                        borderColor: '#fbbf24',
                        borderWidth: 1.5,
                        borderDash: [4, 4],
                        borderRadius: 5,
                        order: 5
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            boxWidth: 12,
                            font: { size: 11, weight: '600' },
                            color: '#cbd5e1'
                        }
                    },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        grid: { display: false }
                    },
                    y: {
                        ticks: {
                            callback: function(val) {
                                return 'Rp ' + (val / 1000000) + ' Jt';
                            }
                        }
                    }
                }
            }
        });

        // 2. Multi-Year Historical Bar Chart (All Recorded Years)
        const ctxMultiYear = document.getElementById('multiYearBarChart').getContext('2d');
        new Chart(ctxMultiYear, {
            type: 'bar',
            data: {
                labels: @json($multiYearLabels),
                datasets: [
                    {
                        label: 'Penjualan (Rp)',
                        data: @json($multiYearSales),
                        backgroundColor: 'rgba(16, 185, 129, 0.8)',
                        borderColor: '#10b981',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Pembelian (Rp)',
                        data: @json($multiYearRaw),
                        backgroundColor: 'rgba(245, 158, 11, 0.8)',
                        borderColor: '#f59e0b',
                        borderWidth: 1,
                        borderRadius: 4
                    },
                    {
                        label: 'Laba Kotor (Rp)',
                        data: @json($multiYearProfit),
                        backgroundColor: 'rgba(6, 182, 212, 0.8)',
                        borderColor: '#06b6d4',
                        borderWidth: 1,
                        borderRadius: 4
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return context.dataset.label + ': Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(val) {
                                return 'Rp ' + (val / 1000000) + ' Jt';
                            }
                        }
                    }
                }
            }
        });

        // 3. Category Doughnut Chart
        const ctxCategory = document.getElementById('categoryDoughnutChart').getContext('2d');
        new Chart(ctxCategory, {
            type: 'doughnut',
            data: {
                labels: @json($kategoriLabels),
                datasets: [{
                    data: @json($kategoriVolumes),
                    backgroundColor: [
                        '#ff8c00',
                        '#3b82f6',
                        '#10b981',
                        '#8b5cf6',
                        '#06b6d4',
                        '#ec4899'
                    ],
                    borderWidth: 2,
                    borderColor: '#0a1628'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: { boxWidth: 10, font: { size: 10 } }
                    }
                }
            }
        });

        // 4. AR Aging Bar Chart
        const ctxAging = document.getElementById('arAgingChart').getContext('2d');
        new Chart(ctxAging, {
            type: 'bar',
            data: {
                labels: ['Lancar (0-15 hr)', '16-30 Hari', '31-60 Hari', '> 60 Hari'],
                datasets: [{
                    label: 'Nominal Piutang (Rp)',
                    data: [
                        {{ $agingAR['current'] }},
                        {{ $agingAR['day16_30'] }},
                        {{ $agingAR['day31_60'] }},
                        {{ $agingAR['over60'] }}
                    ],
                    backgroundColor: [
                        'rgba(16, 185, 129, 0.8)',
                        'rgba(59, 130, 246, 0.8)',
                        'rgba(245, 158, 11, 0.8)',
                        'rgba(244, 63, 94, 0.8)'
                    ],
                    borderRadius: 8
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Piutang: Rp ' + new Intl.NumberFormat('id-ID').format(context.raw);
                            }
                        }
                    }
                },
                scales: {
                    x: { grid: { display: false } },
                    y: {
                        ticks: {
                            callback: function(val) {
                                return 'Rp ' + (val / 1000000) + ' Jt';
                            }
                        }
                    }
                }
            }
        });
    });
</script>
@endsection
