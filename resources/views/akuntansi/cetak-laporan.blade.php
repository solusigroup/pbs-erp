<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Korporasi (SAK EP/EMKM) - {{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</title>
    
    <!-- Tailwind CSS CDN & Font Awesome -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800;900&family=JetBrains+Mono:wght@400;500;600;700&display=swap');
        
        body {
            font-family: 'Plus Jakarta Sans', system-ui, -apple-system, sans-serif;
            background-color: #0b1329;
            color: #0f172a;
        }

        .font-mono {
            font-family: 'JetBrains Mono', monospace;
        }

        .double-border-bottom {
            border-bottom: 3.5px double #0f172a !important;
        }

        .double-border-top-bottom {
            border-top: 1.5px solid #0f172a !important;
            border-bottom: 3.5px double #0f172a !important;
        }

        @media print {
            .no-print {
                display: none !important;
            }
            body {
                background: #ffffff !important;
                color: #0f172a !important;
                padding: 0 !important;
                margin: 0 !important;
                font-size: 9.5pt;
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
            @page {
                size: A4 portrait;
                margin: 8mm 12mm 8mm 12mm;
            }
            .page-break {
                page-break-before: always;
            }
            .break-inside-avoid {
                break-inside: avoid;
                page-break-inside: avoid;
            }
            tr {
                page-break-inside: avoid;
            }
            .print-shadow-none {
                box-shadow: none !important;
            }
            .print-border-none {
                border: none !important;
            }
            .print-p-0 {
                padding: 0 !important;
            }
            .print-m-0 {
                margin: 0 !important;
            }
        }
    </style>
</head>
<body class="min-h-screen p-4 sm:p-8 antialiased">

    <!-- Top Action Floating Toolbar (Hidden when Printed) -->
    <div class="max-w-5xl mx-auto mb-6 no-print">
        <div class="bg-slate-900/90 backdrop-blur border border-slate-700/80 rounded-2xl p-4 shadow-2xl flex flex-col md:flex-row md:items-center md:justify-between gap-4">
            <div class="flex items-center gap-3">
                <a href="{{ route('akuntansi.laporan', request()->query()) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-600 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left text-amber-400"></i>
                    <span>Kembali ke Laporan</span>
                </a>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <div>
                    <div class="text-xs font-bold text-white flex items-center gap-2">
                        <span>Format Dokumen Cetak Keuangan</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 font-bold uppercase">
                            {{ $mode === 'komparatif' ? 'Mode Komparatif' : 'Mode Single' }}
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400">Standar SAK EP / SAK EMKM • Terverifikasi PBS-ERP</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <!-- Mode Switcher Quick Link -->
                @if($mode === 'single')
                    <a href="{{ route('akuntansi.laporan.cetak', array_merge(request()->query(), ['mode' => 'komparatif'])) }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium border border-slate-700 transition flex items-center gap-1.5" title="Ganti ke tampilan pembanding dua periode">
                        <i class="fas fa-columns text-cyan-400"></i>
                        <span>Lihat Komparatif</span>
                    </a>
                @else
                    <a href="{{ route('akuntansi.laporan.cetak', array_merge(request()->query(), ['mode' => 'single'])) }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-medium border border-slate-700 transition flex items-center gap-1.5" title="Ganti ke tampilan satu periode">
                        <i class="fas fa-file-lines text-cyan-400"></i>
                        <span>Lihat Single Periode</span>
                    </a>
                @endif

                <!-- Print & PDF Button -->
                <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-gradient-to-r from-amber-500 to-amber-600 hover:from-amber-400 hover:to-amber-500 text-slate-950 font-black text-xs shadow-lg shadow-amber-500/25 transition flex items-center gap-2">
                    <i class="fas fa-print text-sm"></i>
                    <span>Cetak Dokumen / Simpan PDF</span>
                </button>
            </div>
        </div>
    </div>

    <!-- MAIN CORPORATE PAPER SHEET CANVAS -->
    <div class="max-w-5xl mx-auto bg-white text-slate-900 rounded-3xl shadow-2xl p-8 sm:p-12 border border-slate-200 print:shadow-none print:border-none print:p-0 print:m-0 print:rounded-none">

        <!-- 1. KOP SURAT RESMI KORPORASI -->
        <header class="pb-4">
            <div class="flex items-start justify-between gap-4">
                <div class="flex items-center gap-4 sm:gap-5">
                    <div class="h-20 w-20 shrink-0 flex items-center justify-center p-1 border border-slate-200 rounded-xl bg-white shadow-sm">
                        <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PBS" class="h-full w-full object-contain">
                    </div>
                    <div>
                        <h1 class="text-xl sm:text-2xl font-black tracking-tight text-slate-950 uppercase leading-none">
                            {{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}
                        </h1>
                        <p class="text-xs font-bold text-amber-700 mt-1 uppercase tracking-wider">
                            Pengolahan Limbah Industri, Bahan Bakar Alternatif (RDF) &amp; Pengelolaan Lingkungan
                        </p>
                        <p class="text-[11px] text-slate-600 mt-0.5 leading-snug">
                            {{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mergelo, Sooko' }}, {{ $perusahaan->kota ?? 'Mojokerto' }}, {{ $perusahaan->provinsi ?? 'Jawa Timur' }}
                        </p>
                        <p class="text-[10.5px] text-slate-500 mt-0.5">
                            <span class="font-semibold text-slate-700">NPWP:</span> {{ $perusahaan->npwp ?? '43.688.232.8-602.000' }} &bull;
                            <span class="font-semibold text-slate-700">Telp:</span> {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} &bull;
                            <span class="font-semibold text-slate-700">Email:</span> {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}
                        </p>
                    </div>
                </div>

                <div class="text-right shrink-0">
                    <div class="inline-block border-2 border-slate-900 bg-slate-950 text-white font-black text-[10px] uppercase tracking-widest px-3 py-1 rounded">
                        DOKUMEN RESMI
                    </div>
                    <div class="mt-2 text-[10px] font-semibold text-slate-600 uppercase tracking-wider">
                        Standar: SAK EP / EMKM
                    </div>
                    <div class="text-[10px] text-slate-500 font-mono mt-0.5">
                        Dicetak: {{ \Carbon\Carbon::now()->translatedFormat('d/m/Y H:i') }} WIB
                    </div>
                </div>
            </div>

            <!-- Double Horizontal Rule Divider (Garis Kop Formal) -->
            <div class="mt-4 border-b-2 border-slate-950"></div>
            <div class="border-b border-slate-400 mt-0.5"></div>
        </header>

        <!-- 2. JUDUL DOKUMEN & PERIODE PELAPORAN -->
        <div class="text-center py-4 my-2 border-b border-slate-200">
            <h2 class="text-base sm:text-lg font-black text-slate-950 tracking-wider uppercase leading-snug">
                LAPORAN KEUANGAN KORPORASI
            </h2>
            <p class="text-xs font-extrabold text-slate-700 uppercase tracking-widest mt-0.5">
                LAPORAN LABA RUGI KOMPREHENSIF &amp; POSISI KEUANGAN (NERACA)
            </p>
            
            <div class="inline-flex items-center gap-2 mt-2 px-3.5 py-1 bg-slate-100 rounded-lg border border-slate-300 text-xs text-slate-800">
                <i class="far fa-calendar-check text-slate-700"></i>
                <span>
                    Periode: <strong>{{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }}</strong>
                </span>
                @if($mode === 'komparatif')
                    <span class="text-slate-400 font-bold mx-1">|</span>
                    <span class="text-amber-800 font-semibold">
                        Pembanding: <strong>{{ \Carbon\Carbon::parse($tanggalDariKomparatif)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampaiKomparatif)->translatedFormat('d F Y') }}</strong>
                    </span>
                @endif
            </div>
            <p class="text-[10px] text-slate-500 mt-1 italic">
                (Mata Uang: Rupiah Indonesia [IDR], disajikan penuh dalam satuan Rupiah)
            </p>
        </div>

        <!-- 3. RINGKASAN EKSEKUTIF (4 KPI UTAMA) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-3 mb-4">
            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50/70 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Pendapatan</span>
                <strong class="text-sm font-mono text-emerald-700 font-bold block mt-0.5">
                    Rp {{ number_format($dataUtama['totalPendapatan'], 0, ',', '.') }}
                </strong>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50/70 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Total Beban Usaha</span>
                <strong class="text-sm font-mono text-rose-700 font-bold block mt-0.5">
                    Rp {{ number_format($dataUtama['totalBeban'], 0, ',', '.') }}
                </strong>
            </div>

            <div class="border border-slate-300 rounded-xl p-3 bg-slate-50/70 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Laba (Rugi) Berjalan</span>
                <strong class="text-sm font-mono font-bold block mt-0.5 {{ $dataUtama['labaBersih'] >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                    @if($dataUtama['labaBersih'] < 0)
                        (Rp {{ number_format(abs($dataUtama['labaBersih']), 0, ',', '.') }})
                    @else
                        Rp {{ number_format($dataUtama['labaBersih'], 0, ',', '.') }}
                    @endif
                </strong>
            </div>

            <div class="border border-slate-900 rounded-xl p-3 bg-slate-100 text-center">
                <span class="text-[10px] font-bold text-slate-700 uppercase tracking-wider block">Total Aset (Aktiva)</span>
                <strong class="text-sm font-mono text-slate-950 font-black block mt-0.5">
                    Rp {{ number_format($dataUtama['totalAset'], 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <!-- ================= BAGIAN I: LAPORAN LABA RUGI ================= -->
        <section class="mb-8 break-inside-avoid">
            <div class="bg-slate-900 text-white px-3.5 py-1.5 rounded-t-lg flex items-center justify-between font-bold text-xs uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <i class="fas fa-chart-line text-amber-400"></i>
                    <span>I. LAPORAN LABA RUGI KOMPREHENSIF</span>
                </div>
                <span class="text-[10px] font-normal text-slate-300 normal-case font-mono">
                    {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}
                </span>
            </div>

            @if($mode === 'komparatif')
                <!-- TABEL LABA RUGI KOMPARATIF -->
                <table class="w-full text-xs border-collapse border border-slate-300">
                    <thead>
                        <tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-300 text-[11px]">
                            <th class="py-2 px-3 border border-slate-300 text-left w-20">Kode</th>
                            <th class="py-2 px-3 border border-slate-300 text-left">Nama Akun / Pos Rekening</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-36">Periode Utama</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-36">Pembanding</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-32">Selisih (Rp)</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-16">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Pendapatan Usaha -->
                        <tr class="bg-slate-100/70 font-bold text-slate-900">
                            <td colspan="6" class="py-1.5 px-3 border border-slate-300 uppercase tracking-wide">
                                1. PENDAPATAN USAHA (REVENUE)
                            </td>
                        </tr>
                        @forelse($dataUtama['pendapatanData'] as $kode => $item)
                            @php
                                $nomUtama = (float)$item['nominal'];
                                $nomKomp = (float)($dataKomparatif['pendapatanData'][$kode]['nominal'] ?? 0);
                                $selisih = $nomUtama - $nomKomp;
                                $persen = $nomKomp != 0 ? ($selisih / abs($nomKomp)) * 100 : ($nomUtama != 0 ? 100 : 0);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-1.5 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-900 font-medium">
                                    {{ number_format($nomUtama, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-600">
                                    {{ number_format($nomKomp, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono {{ $selisih >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-[10px] {{ $persen >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $persen >= 0 ? '+' : '' }}{{ number_format($persen, 1, ',', '.') }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-2 px-3 border border-slate-300 text-slate-400 italic text-center">Tidak ada pos pendapatan usaha.</td>
                            </tr>
                        @endforelse
                        @php
                            $totPendUtama = (float)$dataUtama['totalPendapatan'];
                            $totPendKomp = (float)$dataKomparatif['totalPendapatan'];
                            $selisihPend = $totPendUtama - $totPendKomp;
                            $persenPend = $totPendKomp != 0 ? ($selisihPend / abs($totPendKomp)) * 100 : 0;
                        @endphp
                        <tr class="font-bold bg-slate-100 border-t border-b-2 border-slate-400 text-slate-900">
                            <td colspan="2" class="py-2 px-3 border border-slate-300 text-right uppercase">TOTAL PENDAPATAN USAHA:</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-emerald-800">Rp {{ number_format($totPendUtama, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-slate-700">Rp {{ number_format($totPendKomp, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono {{ $selisihPend >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $selisihPend >= 0 ? '+' : '' }}{{ number_format($selisihPend, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-[10px] {{ $persenPend >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $persenPend >= 0 ? '+' : '' }}{{ number_format($persenPend, 1, ',', '.') }}%
                            </td>
                        </tr>

                        <!-- 2. Beban Usaha & Pokok -->
                        <tr class="bg-slate-100/70 font-bold text-slate-900">
                            <td colspan="6" class="py-1.5 px-3 border border-slate-300 uppercase tracking-wide">
                                2. BEBAN POKOK &amp; OPERASIONAL (EXPENSES)
                            </td>
                        </tr>
                        @forelse($dataUtama['bebanData'] as $kode => $item)
                            @php
                                $nomUtama = (float)$item['nominal'];
                                $nomKomp = (float)($dataKomparatif['bebanData'][$kode]['nominal'] ?? 0);
                                $selisih = $nomUtama - $nomKomp;
                                $persen = $nomKomp != 0 ? ($selisih / abs($nomKomp)) * 100 : ($nomUtama != 0 ? 100 : 0);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-1.5 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-900 font-medium">
                                    {{ number_format($nomUtama, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-600">
                                    {{ number_format($nomKomp, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono {{ $selisih <= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                                </td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-[10px] {{ $persen <= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $persen >= 0 ? '+' : '' }}{{ number_format($persen, 1, ',', '.') }}%
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-2 px-3 border border-slate-300 text-slate-400 italic text-center">Tidak ada pos beban usaha.</td>
                            </tr>
                        @endforelse
                        @php
                            $totBebUtama = (float)$dataUtama['totalBeban'];
                            $totBebKomp = (float)$dataKomparatif['totalBeban'];
                            $selisihBeb = $totBebUtama - $totBebKomp;
                            $persenBeb = $totBebKomp != 0 ? ($selisihBeb / abs($totBebKomp)) * 100 : 0;
                        @endphp
                        <tr class="font-bold bg-slate-100 border-t border-b-2 border-slate-400 text-slate-900">
                            <td colspan="2" class="py-2 px-3 border border-slate-300 text-right uppercase">TOTAL BEBAN USAHA:</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-rose-800">Rp {{ number_format($totBebUtama, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-slate-700">Rp {{ number_format($totBebKomp, 0, ',', '.') }}</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono {{ $selisihBeb <= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $selisihBeb >= 0 ? '+' : '' }}{{ number_format($selisihBeb, 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-[10px] {{ $persenBeb <= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                {{ $persenBeb >= 0 ? '+' : '' }}{{ number_format($persenBeb, 1, ',', '.') }}%
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        @php
                            $labaUtama = (float)$dataUtama['labaBersih'];
                            $labaKomp = (float)$dataKomparatif['labaBersih'];
                            $selisihLaba = $labaUtama - $labaKomp;
                            $persenLaba = $labaKomp != 0 ? ($selisihLaba / abs($labaKomp)) * 100 : 0;
                        @endphp
                        <tr class="bg-slate-900 text-white font-black text-xs double-border-top-bottom">
                            <td colspan="2" class="py-2.5 px-3 uppercase tracking-wider text-right">
                                LABA (RUGI) BERSIH PERIODE BERJALAN:
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-sm text-amber-300">
                                {{ $labaUtama < 0 ? '(Rp ' . number_format(abs($labaUtama), 0, ',', '.') . ')' : 'Rp ' . number_format($labaUtama, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-sm text-slate-300">
                                {{ $labaKomp < 0 ? '(Rp ' . number_format(abs($labaKomp), 0, ',', '.') . ')' : 'Rp ' . number_format($labaKomp, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-xs {{ $selisihLaba >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                {{ $selisihLaba >= 0 ? '+' : '' }}{{ number_format($selisihLaba, 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-[10px] {{ $persenLaba >= 0 ? 'text-emerald-300' : 'text-rose-300' }}">
                                {{ $persenLaba >= 0 ? '+' : '' }}{{ number_format($persenLaba, 1, ',', '.') }}%
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @else
                <!-- TABEL LABA RUGI SINGLE PERIODE -->
                <table class="w-full text-xs border-collapse border border-slate-300">
                    <thead>
                        <tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-300 text-[11px]">
                            <th class="py-2 px-3 border border-slate-300 text-left w-24">Kode Akun</th>
                            <th class="py-2 px-3 border border-slate-300 text-left">Pos Rekening / Nama Akun</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-48">Nominal (Rp)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- 1. Pendapatan Usaha -->
                        <tr class="bg-slate-100/70 font-bold text-slate-900">
                            <td colspan="3" class="py-1.5 px-3 border border-slate-300 uppercase tracking-wide">
                                1. PENDAPATAN USAHA (REVENUE)
                            </td>
                        </tr>
                        @forelse($dataUtama['pendapatanData'] as $kode => $item)
                            <tr class="hover:bg-slate-50">
                                <td class="py-1.5 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-900 font-medium">
                                    {{ number_format($item['nominal'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-2 px-3 border border-slate-300 text-slate-400 italic text-center">Tidak ada pos pendapatan usaha.</td>
                            </tr>
                        @endforelse
                        <tr class="font-bold bg-slate-100 border-t border-b-2 border-slate-400 text-slate-900">
                            <td colspan="2" class="py-2 px-3 border border-slate-300 text-right uppercase">TOTAL PENDAPATAN USAHA:</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-emerald-800 text-sm">
                                Rp {{ number_format($dataUtama['totalPendapatan'], 0, ',', '.') }}
                            </td>
                        </tr>

                        <!-- 2. Beban Pokok & Operasional -->
                        <tr class="bg-slate-100/70 font-bold text-slate-900">
                            <td colspan="3" class="py-1.5 px-3 border border-slate-300 uppercase tracking-wide">
                                2. BEBAN POKOK &amp; OPERASIONAL (EXPENSES)
                            </td>
                        </tr>
                        @forelse($dataUtama['bebanData'] as $kode => $item)
                            <tr class="hover:bg-slate-50">
                                <td class="py-1.5 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-900 font-medium">
                                    {{ number_format($item['nominal'], 0, ',', '.') }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="py-2 px-3 border border-slate-300 text-slate-400 italic text-center">Tidak ada pos beban usaha.</td>
                            </tr>
                        @endforelse
                        <tr class="font-bold bg-slate-100 border-t border-b-2 border-slate-400 text-slate-900">
                            <td colspan="2" class="py-2 px-3 border border-slate-300 text-right uppercase">TOTAL BEBAN USAHA &amp; POKOK:</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-rose-800 text-sm">
                                Rp {{ number_format($dataUtama['totalBeban'], 0, ',', '.') }}
                            </td>
                        </tr>
                    </tbody>
                    <tfoot>
                        <tr class="bg-slate-900 text-white font-black text-xs double-border-top-bottom">
                            <td colspan="2" class="py-2.5 px-3 uppercase tracking-wider text-right">
                                LABA (RUGI) BERSIH PERIODE BERJALAN:
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono text-sm text-amber-300">
                                @if($dataUtama['labaBersih'] < 0)
                                    (Rp {{ number_format(abs($dataUtama['labaBersih']), 0, ',', '.') }})
                                @else
                                    Rp {{ number_format($dataUtama['labaBersih'], 0, ',', '.') }}
                                @endif
                            </td>
                        </tr>
                    </tfoot>
                </table>
            @endif
        </section>

        <!-- ================= BAGIAN II: LAPORAN POSISI KEUANGAN (NERACA) ================= -->
        <section class="mb-8 break-inside-avoid">
            <div class="bg-slate-900 text-white px-3.5 py-1.5 rounded-t-lg flex items-center justify-between font-bold text-xs uppercase tracking-wider">
                <div class="flex items-center gap-2">
                    <i class="fas fa-scale-balanced text-amber-400"></i>
                    <span>II. LAPORAN POSISI KEUANGAN (NERACA)</span>
                </div>
                <span class="text-[10px] font-normal text-slate-300 normal-case font-mono">
                    Posisi Per: {{ \Carbon\Carbon::parse($tanggalSampai)->format('d F Y') }}
                </span>
            </div>

            @if($mode === 'komparatif')
                <!-- TABEL NERACA KOMPARATIF -->
                <table class="w-full text-xs border-collapse border border-slate-300">
                    <thead>
                        <tr class="bg-slate-100 text-slate-900 font-bold border-b border-slate-300 text-[11px]">
                            <th class="py-2 px-3 border border-slate-300 text-left w-20">Kode</th>
                            <th class="py-2 px-3 border border-slate-300 text-left">Pos Akun / Elemen Neraca</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-36">Per {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-36">Per {{ \Carbon\Carbon::parse($tanggalSampaiKomparatif)->format('d/m/Y') }}</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-32">Selisih (Rp)</th>
                            <th class="py-2 px-3 border border-slate-300 text-right w-16">%</th>
                        </tr>
                    </thead>
                    <tbody>
                        <!-- ASET (AKTIVA) -->
                        <tr class="bg-slate-800 text-white font-bold uppercase tracking-wider text-[11px]">
                            <td colspan="6" class="py-1.5 px-3">A. ASET (AKTIVA)</td>
                        </tr>

                        <!-- 1. Aset Lancar -->
                        <tr class="bg-slate-100 font-semibold text-slate-800">
                            <td colspan="6" class="py-1 px-3 border border-slate-300 italic">1. Aset Lancar</td>
                        </tr>
                        @foreach($dataUtama['asetLancarData'] as $kode => $item)
                            @php
                                $nomUtama = (float)$item['nominal'];
                                $nomKomp = (float)($dataKomparatif['asetLancarData'][$kode]['nominal'] ?? 0);
                                $selisih = $nomUtama - $nomKomp;
                                $persen = $nomKomp != 0 ? ($selisih / abs($nomKomp)) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-1 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono">{{ number_format($nomUtama, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($nomKomp, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono {{ $selisih >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-[10px]">
                                    {{ $persen >= 0 ? '+' : '' }}{{ number_format($persen, 1, ',', '.') }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr class="font-bold bg-slate-50 border-b border-slate-300 text-slate-800">
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300 text-right">Subtotal Aset Lancar:</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono">{{ number_format($dataUtama['totalAsetLancar'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($dataKomparatif['totalAsetLancar'], 0, ',', '.') }}</td>
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300"></td>
                        </tr>

                        <!-- 2. Aset Tetap -->
                        <tr class="bg-slate-100 font-semibold text-slate-800">
                            <td colspan="6" class="py-1 px-3 border border-slate-300 italic">2. Aset Tetap &amp; Lain-lain</td>
                        </tr>
                        @foreach($dataUtama['asetTetapData'] as $kode => $item)
                            @php
                                $nomUtama = (float)$item['nominal'];
                                $nomKomp = (float)($dataKomparatif['asetTetapData'][$kode]['nominal'] ?? 0);
                                $selisih = $nomUtama - $nomKomp;
                                $persen = $nomKomp != 0 ? ($selisih / abs($nomKomp)) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-1 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono">{{ number_format($nomUtama, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($nomKomp, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono {{ $selisih >= 0 ? 'text-emerald-700' : 'text-rose-700' }}">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-[10px]">
                                    {{ $persen >= 0 ? '+' : '' }}{{ number_format($persen, 1, ',', '.') }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr class="font-bold bg-slate-50 border-b border-slate-300 text-slate-800">
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300 text-right">Subtotal Aset Tetap:</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono">{{ number_format($dataUtama['totalAsetTetap'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($dataKomparatif['totalAsetTetap'], 0, ',', '.') }}</td>
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300"></td>
                        </tr>

                        <!-- Total Aset -->
                        <tr class="font-bold bg-slate-200 border-t-2 border-b-2 border-slate-900 text-slate-950 text-xs">
                            <td colspan="2" class="py-2 px-3 border border-slate-300 text-right uppercase">TOTAL ASET (AKTIVA):</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-slate-950 font-black">
                                Rp {{ number_format($dataUtama['totalAset'], 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-slate-800">
                                Rp {{ number_format($dataKomparatif['totalAset'], 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="py-2 px-3 border border-slate-300"></td>
                        </tr>

                        <!-- KEWAJIBAN & EKUITAS (PASIVA) -->
                        <tr class="bg-slate-800 text-white font-bold uppercase tracking-wider text-[11px]">
                            <td colspan="6" class="py-1.5 px-3">B. KEWAJIBAN &amp; EKUITAS (PASIVA)</td>
                        </tr>

                        <!-- 1. Kewajiban -->
                        <tr class="bg-slate-100 font-semibold text-slate-800">
                            <td colspan="6" class="py-1 px-3 border border-slate-300 italic">1. Kewajiban (Liabilitas)</td>
                        </tr>
                        @foreach($dataUtama['kewajibanData'] as $kode => $item)
                            @php
                                $nomUtama = (float)$item['nominal'];
                                $nomKomp = (float)($dataKomparatif['kewajibanData'][$kode]['nominal'] ?? 0);
                                $selisih = $nomUtama - $nomKomp;
                                $persen = $nomKomp != 0 ? ($selisih / abs($nomKomp)) * 100 : 0;
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-1 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono">{{ number_format($nomUtama, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($nomKomp, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono {{ $selisih >= 0 ? 'text-rose-700' : 'text-emerald-700' }}">
                                    {{ $selisih >= 0 ? '+' : '' }}{{ number_format($selisih, 0, ',', '.') }}
                                </td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-[10px]">
                                    {{ $persen >= 0 ? '+' : '' }}{{ number_format($persen, 1, ',', '.') }}%
                                </td>
                            </tr>
                        @endforeach
                        <tr class="font-bold bg-slate-50 border-b border-slate-300 text-slate-800">
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300 text-right">Total Kewajiban:</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono">{{ number_format($dataUtama['totalKewajiban'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($dataKomparatif['totalKewajiban'], 0, ',', '.') }}</td>
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300"></td>
                        </tr>

                        <!-- 2. Ekuitas -->
                        <tr class="bg-slate-100 font-semibold text-slate-800">
                            <td colspan="6" class="py-1 px-3 border border-slate-300 italic">2. Ekuitas / Modal</td>
                        </tr>
                        @foreach($dataUtama['ekuitasData'] as $kode => $item)
                            @php
                                $nomUtama = (float)$item['nominal'];
                                $nomKomp = (float)($dataKomparatif['ekuitasData'][$kode]['nominal'] ?? 0);
                            @endphp
                            <tr class="hover:bg-slate-50">
                                <td class="py-1 px-3 border border-slate-300 font-mono text-slate-600">{{ $item['akun']->kode_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 pl-6 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono">{{ number_format($nomUtama, 0, ',', '.') }}</td>
                                <td class="py-1 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($nomKomp, 0, ',', '.') }}</td>
                                <td colspan="2" class="py-1 px-3 border border-slate-300"></td>
                            </tr>
                        @endforeach
                        <tr class="hover:bg-slate-50">
                            <td class="py-1 px-3 border border-slate-300 font-mono text-slate-600">3-9999</td>
                            <td class="py-1 px-3 border border-slate-300 pl-6 text-slate-800 font-semibold">Laba Bersih Kumulatif Berjalan</td>
                            <td class="py-1 px-3 border border-slate-300 text-right font-mono font-semibold">{{ number_format($dataUtama['labaBersihKumulatif'], 0, ',', '.') }}</td>
                            <td class="py-1 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($dataKomparatif['labaBersihKumulatif'], 0, ',', '.') }}</td>
                            <td colspan="2" class="py-1 px-3 border border-slate-300"></td>
                        </tr>
                        <tr class="font-bold bg-slate-50 border-b border-slate-300 text-slate-800">
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300 text-right">Total Ekuitas &amp; Laba:</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono">{{ number_format($dataUtama['totalEkuitas'] + $dataUtama['labaBersihKumulatif'], 0, ',', '.') }}</td>
                            <td class="py-1.5 px-3 border border-slate-300 text-right font-mono text-slate-600">{{ number_format($dataKomparatif['totalEkuitas'] + $dataKomparatif['labaBersihKumulatif'], 0, ',', '.') }}</td>
                            <td colspan="2" class="py-1.5 px-3 border border-slate-300"></td>
                        </tr>

                        <!-- Total Pasiva -->
                        <tr class="font-bold bg-slate-200 border-t-2 border-b-2 border-slate-900 text-slate-950 text-xs">
                            <td colspan="2" class="py-2 px-3 border border-slate-300 text-right uppercase">TOTAL KEWAJIBAN &amp; EKUITAS:</td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-slate-950 font-black">
                                Rp {{ number_format($dataUtama['totalKewajibanEkuitas'], 0, ',', '.') }}
                            </td>
                            <td class="py-2 px-3 border border-slate-300 text-right font-mono text-slate-800">
                                Rp {{ number_format($dataKomparatif['totalKewajibanEkuitas'], 0, ',', '.') }}
                            </td>
                            <td colspan="2" class="py-2 px-3 border border-slate-300"></td>
                        </tr>
                    </tbody>
                </table>
            @else
                <!-- TABEL NERACA SINGLE PERIODE (2-KOLOM SEIMBANG FORMAL) -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <!-- KOLOM KIRI: ASET (AKTIVA) -->
                    <div class="border border-slate-300 rounded-lg p-3 bg-white flex flex-col justify-between">
                        <div>
                            <div class="border-b-2 border-slate-900 pb-1 mb-2 font-black text-xs text-slate-950 uppercase tracking-wide flex items-center justify-between">
                                <span>ASET (AKTIVA)</span>
                                <span class="text-[10px] font-mono text-slate-500 font-semibold">DEBET</span>
                            </div>

                            <!-- 1. Aset Lancar -->
                            <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mt-2 mb-1">
                                1. Aset Lancar
                            </div>
                            <table class="w-full text-xs">
                                <tbody>
                                    @foreach($dataUtama['asetLancarData'] as $kode => $item)
                                        <tr class="border-b border-slate-100">
                                            <td class="py-1 font-mono text-slate-500 text-[10.5px] w-16">{{ $item['akun']->kode_akun }}</td>
                                            <td class="py-1 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                            <td class="py-1 text-right font-mono text-slate-900 font-medium">
                                                {{ number_format($item['nominal'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold text-slate-900 bg-slate-50/80">
                                        <td colspan="2" class="py-1.5 text-right uppercase text-[10.5px]">Subtotal Aset Lancar:</td>
                                        <td class="py-1.5 text-right font-mono">Rp {{ number_format($dataUtama['totalAsetLancar'], 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- 2. Aset Tetap -->
                            <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mt-3 mb-1">
                                2. Aset Tetap &amp; Lainnya
                            </div>
                            <table class="w-full text-xs">
                                <tbody>
                                    @foreach($dataUtama['asetTetapData'] as $kode => $item)
                                        <tr class="border-b border-slate-100">
                                            <td class="py-1 font-mono text-slate-500 text-[10.5px] w-16">{{ $item['akun']->kode_akun }}</td>
                                            <td class="py-1 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                            <td class="py-1 text-right font-mono text-slate-900 font-medium">
                                                {{ number_format($item['nominal'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold text-slate-900 bg-slate-50/80">
                                        <td colspan="2" class="py-1.5 text-right uppercase text-[10.5px]">Subtotal Aset Tetap:</td>
                                        <td class="py-1.5 text-right font-mono">Rp {{ number_format($dataUtama['totalAsetTetap'], 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Aset -->
                        <div class="mt-4 pt-2 border-t-2 border-slate-900 double-border-bottom bg-slate-100/90 p-2 rounded">
                            <div class="flex items-center justify-between font-black text-xs text-slate-950">
                                <span class="uppercase tracking-wider">TOTAL ASET (AKTIVA):</span>
                                <span class="font-mono text-sm text-slate-950">
                                    Rp {{ number_format($dataUtama['totalAset'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>

                    <!-- KOLOM KANAN: KEWAJIBAN & EKUITAS (PASIVA) -->
                    <div class="border border-slate-300 rounded-lg p-3 bg-white flex flex-col justify-between">
                        <div>
                            <div class="border-b-2 border-slate-900 pb-1 mb-2 font-black text-xs text-slate-950 uppercase tracking-wide flex items-center justify-between">
                                <span>KEWAJIBAN &amp; EKUITAS (PASIVA)</span>
                                <span class="text-[10px] font-mono text-slate-500 font-semibold">KREDIT</span>
                            </div>

                            <!-- 1. Kewajiban -->
                            <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mt-2 mb-1">
                                1. Kewajiban (Liabilitas)
                            </div>
                            <table class="w-full text-xs">
                                <tbody>
                                    @foreach($dataUtama['kewajibanData'] as $kode => $item)
                                        <tr class="border-b border-slate-100">
                                            <td class="py-1 font-mono text-slate-500 text-[10.5px] w-16">{{ $item['akun']->kode_akun }}</td>
                                            <td class="py-1 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                            <td class="py-1 text-right font-mono text-slate-900 font-medium">
                                                {{ number_format($item['nominal'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="font-bold text-slate-900 bg-slate-50/80">
                                        <td colspan="2" class="py-1.5 text-right uppercase text-[10.5px]">Total Kewajiban:</td>
                                        <td class="py-1.5 text-right font-mono">Rp {{ number_format($dataUtama['totalKewajiban'], 0, ',', '.') }}</td>
                                    </tr>
                                </tbody>
                            </table>

                            <!-- 2. Ekuitas -->
                            <div class="text-[11px] font-bold text-slate-800 uppercase tracking-wider mt-3 mb-1">
                                2. Ekuitas / Modal
                            </div>
                            <table class="w-full text-xs">
                                <tbody>
                                    @foreach($dataUtama['ekuitasData'] as $kode => $item)
                                        <tr class="border-b border-slate-100">
                                            <td class="py-1 font-mono text-slate-500 text-[10.5px] w-16">{{ $item['akun']->kode_akun }}</td>
                                            <td class="py-1 text-slate-800">{{ $item['akun']->nama_akun }}</td>
                                            <td class="py-1 text-right font-mono text-slate-900 font-medium">
                                                {{ number_format($item['nominal'], 0, ',', '.') }}
                                            </td>
                                        </tr>
                                    @endforeach
                                    <tr class="border-b border-slate-100 font-semibold text-slate-900">
                                        <td class="py-1 font-mono text-slate-500 text-[10.5px] w-16">3-9999</td>
                                        <td class="py-1 text-slate-800">Laba Bersih Kumulatif Berjalan</td>
                                        <td class="py-1 text-right font-mono text-slate-900">
                                            {{ number_format($dataUtama['labaBersihKumulatif'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                    <tr class="font-bold text-slate-900 bg-slate-50/80">
                                        <td colspan="2" class="py-1.5 text-right uppercase text-[10.5px]">Total Ekuitas &amp; Laba:</td>
                                        <td class="py-1.5 text-right font-mono">
                                            Rp {{ number_format($dataUtama['totalEkuitas'] + $dataUtama['labaBersihKumulatif'], 0, ',', '.') }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>

                        <!-- Total Pasiva -->
                        <div class="mt-4 pt-2 border-t-2 border-slate-900 double-border-bottom bg-slate-100/90 p-2 rounded">
                            <div class="flex items-center justify-between font-black text-xs text-slate-950">
                                <span class="uppercase tracking-wider">TOTAL KEWAJIBAN &amp; EKUITAS:</span>
                                <span class="font-mono text-sm text-slate-950">
                                    Rp {{ number_format($dataUtama['totalKewajibanEkuitas'], 0, ',', '.') }}
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Verification Balance Bar -->
            <div class="mt-3 p-2.5 rounded-lg border border-slate-300 bg-slate-50 flex items-center justify-between text-[11px] text-slate-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-certificate text-emerald-600"></i>
                    <span>Status Posisi Keuangan: <strong class="text-slate-950">Standar Akuntansi Keuangan (SAK EP/EMKM) Terpenuhi</strong></span>
                </div>
                <div class="font-mono text-[10px] text-slate-500">
                    Sistem Otomatisasi PBS-ERP Terpadu
                </div>
            </div>
        </section>

        <!-- ================= BAGIAN III: LEMBAR PENGESAHAN & TANDA TANGAN ================= -->
        <section class="pt-4 border-t border-slate-200 break-inside-avoid">
            <div class="text-right text-xs text-slate-700 mb-4 font-medium">
                {{ $perusahaan->kota ?? 'Mojokerto' }}, {{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }}
            </div>

            <div class="grid grid-cols-3 gap-4 text-center text-xs">
                <!-- Kolom 1 -->
                <div class="border border-slate-300 rounded-xl p-3 bg-white">
                    <span class="text-slate-600 block mb-14 font-medium text-[11px]">Dibuat &amp; Disusun Oleh,</span>
                    <strong class="block border-t border-slate-400 pt-1.5 text-slate-950 font-bold">
                        Staff Akuntansi &amp; Perpajakan
                    </strong>
                    <span class="text-[10px] text-slate-500 block">Departemen Keuangan &amp; Akuntansi</span>
                </div>

                <!-- Kolom 2 -->
                <div class="border border-slate-300 rounded-xl p-3 bg-white">
                    <span class="text-slate-600 block mb-14 font-medium text-[11px]">Diperiksa &amp; Diverifikasi Oleh,</span>
                    <strong class="block border-t border-slate-400 pt-1.5 text-slate-950 font-bold">
                        Manager Keuangan Korporasi
                    </strong>
                    <span class="text-[10px] text-slate-500 block">Departemen Keuangan &amp; Akuntansi</span>
                </div>

                <!-- Kolom 3 -->
                <div class="border-2 border-slate-900 rounded-xl p-3 bg-slate-50/80 shadow-sm">
                    <span class="text-slate-950 block mb-14 font-bold text-[11px]">Disetujui &amp; Disahkan Oleh,</span>
                    <strong class="block border-t-2 border-slate-950 pt-1.5 text-slate-950 font-black">
                        {{ $perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.' }}
                    </strong>
                    <span class="text-[10px] text-slate-700 font-semibold block">Board of Director (Finance &amp; Tax)</span>
                </div>
            </div>
        </section>

        <!-- ================= BAGIAN IV: CATATAN KAKI DOKUMEN ================= -->
        <footer class="mt-8 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-500">
            <p>Dokumen Laporan Keuangan ini diterbitkan resmi melalui Sistem ERP &amp; Akuntansi Terpadu PT Pinastika Bhakti Semesta (PBS-ERP).</p>
            <p class="mt-0.5 text-slate-400">Keabsahan laporan ini diakui secara internal dan eksternal untuk keperluan pelaporan pajak, audit kepatuhan, serta manajemen korporasi.</p>
        </footer>
    </div>

</body>
</html>
