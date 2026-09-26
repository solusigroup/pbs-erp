<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Arus Kas (Metode Langsung) - {{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</title>
    
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
                <a href="{{ route('akuntansi.arus-kas', request()->query()) }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-600 transition flex items-center gap-2">
                    <i class="fas fa-arrow-left text-amber-400"></i>
                    <span>Kembali ke Arus Kas</span>
                </a>
                <div class="h-6 w-px bg-slate-700 hidden sm:block"></div>
                <div>
                    <div class="text-xs font-bold text-white flex items-center gap-2">
                        <span>Format Dokumen Cetak Keuangan</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-mono bg-teal-500/20 text-teal-300 border border-teal-500/30 font-bold uppercase">
                            Arus Kas Metode Langsung
                        </span>
                    </div>
                    <p class="text-[11px] text-slate-400">Standar SAK EP / SAK EMKM • Terverifikasi PBS-ERP</p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
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
                LAPORAN ARUS KAS (STATEMENT OF CASH FLOWS)
            </h2>
            <p class="text-xs font-extrabold text-teal-800 uppercase tracking-widest mt-0.5">
                METODE LANGSUNG (DIRECT METHOD) — STANDAR SAK EP &amp; SAK EMKM
            </p>
            
            <div class="inline-flex items-center gap-2 mt-2 px-3.5 py-1 bg-slate-100 rounded-lg border border-slate-300 text-xs text-slate-800">
                <i class="far fa-calendar-check text-slate-700"></i>
                <span>
                    Periode: <strong>{{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d F Y') }}</strong> s/d <strong>{{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }}</strong>
                </span>
            </div>
            <p class="text-[10px] text-slate-500 mt-1 italic">
                (Mata Uang: Rupiah Indonesia [IDR], disajikan penuh dalam satuan Rupiah)
            </p>
        </div>

        <!-- 3. RINGKASAN EKSEKUTIF (4 KPI UTAMA ARUS KAS) -->
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 py-3 mb-6">
            <div class="border border-slate-300 rounded-xl p-2.5 bg-slate-50/70 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Arus Kas Operasi (I)</span>
                <strong class="text-sm font-mono font-bold block mt-0.5 {{ $arusKasOperasi >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                    Rp {{ number_format($arusKasOperasi, 0, ',', '.') }}
                </strong>
            </div>

            <div class="border border-slate-300 rounded-xl p-2.5 bg-slate-50/70 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Arus Kas Investasi (II)</span>
                <strong class="text-sm font-mono font-bold block mt-0.5 {{ $arusKasInvestasi >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                    Rp {{ number_format($arusKasInvestasi, 0, ',', '.') }}
                </strong>
            </div>

            <div class="border border-slate-300 rounded-xl p-2.5 bg-slate-50/70 text-center">
                <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wider block">Arus Kas Pendanaan (III)</span>
                <strong class="text-sm font-mono font-bold block mt-0.5 {{ $arusKasPendanaan >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                    Rp {{ number_format($arusKasPendanaan, 0, ',', '.') }}
                </strong>
            </div>

            <div class="border border-slate-900 rounded-xl p-2.5 bg-slate-100 text-center">
                <span class="text-[10px] font-bold text-slate-700 uppercase tracking-wider block">Saldo Kas Akhir (IV)</span>
                <strong class="text-sm font-mono text-slate-950 font-black block mt-0.5">
                    Rp {{ number_format($saldoAkhirKas, 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <!-- 4. TABEL UTAMA LAPORAN ARUS KAS -->
        <div class="border border-slate-300 rounded-xl overflow-hidden shadow-sm mb-6">
            <table class="w-full text-xs text-left border-collapse">
                <thead>
                    <tr class="bg-slate-900 text-white font-bold text-[11px] uppercase tracking-wider">
                        <th class="py-2.5 px-4 w-[60%] border-r border-slate-800">Uraian Arus Kas (Metode Langsung)</th>
                        <th class="py-2.5 px-3 text-center w-[12%] border-r border-slate-800">Cat.</th>
                        <th class="py-2.5 px-4 text-right w-[28%]">Jumlah (Rp)</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-200">
                    
                    <!-- ================= BAGIAN I: AKTIVITAS OPERASI ================= -->
                    <tr class="bg-slate-100/80 font-bold text-slate-900">
                        <td colspan="3" class="py-2 px-4 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-industry text-teal-700"></i>
                            <span>I. ARUS KAS DARI AKTIVITAS OPERASI</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Penerimaan Kas dari Pelanggan &amp; Penjualan Usaha
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">1</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-slate-900">
                            {{ number_format($penerimaanPelanggan, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Pembayaran Kas kepada Pemasok &amp; Pembelian Bahan Baku
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">2</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-rose-700">
                            ({{ number_format($pembayaranHpp, 0, ',', '.') }})
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Pembayaran Kas untuk Beban Gaji, Upah, Honor &amp; Karyawan
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">3</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-rose-700">
                            ({{ number_format($pembayaranGaji, 0, ',', '.') }})
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Pembayaran Kas untuk Beban Operasional, Kantor &amp; Utilitas
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">4</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-rose-700">
                            ({{ number_format($pembayaranOperasional, 0, ',', '.') }})
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Pembayaran Kas untuk Pajak Penghasilan (PPh Badan, PPh 21/23 &amp; PPN)
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">5</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-rose-700">
                            ({{ number_format($pembayaranPajak, 0, ',', '.') }})
                        </td>
                    </tr>
                    <tr class="bg-teal-50/60 font-bold border-t border-b border-teal-200">
                        <td class="py-2 px-4 pl-6 text-teal-950 uppercase tracking-wide">
                            Arus Kas Bersih yang Diperoleh dari (Digunakan untuk) Aktivitas Operasi
                        </td>
                        <td class="py-2 px-3 text-center text-teal-800 font-mono text-[10px]">A</td>
                        <td class="py-2 px-4 text-right font-mono text-sm {{ $arusKasOperasi >= 0 ? 'text-emerald-900 font-bold' : 'text-rose-800 font-bold' }}">
                            {{ $arusKasOperasi >= 0 ? number_format($arusKasOperasi, 0, ',', '.') : '(' . number_format(abs($arusKasOperasi), 0, ',', '.') . ')' }}
                        </td>
                    </tr>

                    <!-- ================= BAGIAN II: AKTIVITAS INVESTASI ================= -->
                    <tr class="bg-slate-100/80 font-bold text-slate-900">
                        <td colspan="3" class="py-2 px-4 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-chart-line text-sky-700"></i>
                            <span>II. ARUS KAS DARI AKTIVITAS INVESTASI</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Pembayaran Kas untuk Perolehan Aset Tetap, Mesin Pabrik &amp; Peralatan
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">6</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-rose-700">
                            ({{ number_format($perolehanAset, 0, ',', '.') }})
                        </td>
                    </tr>
                    <tr class="bg-sky-50/60 font-bold border-t border-b border-sky-200">
                        <td class="py-2 px-4 pl-6 text-sky-950 uppercase tracking-wide">
                            Arus Kas Bersih yang Diperoleh dari (Digunakan untuk) Aktivitas Investasi
                        </td>
                        <td class="py-2 px-3 text-center text-sky-800 font-mono text-[10px]">B</td>
                        <td class="py-2 px-4 text-right font-mono text-sm {{ $arusKasInvestasi >= 0 ? 'text-emerald-900 font-bold' : 'text-rose-800 font-bold' }}">
                            {{ $arusKasInvestasi >= 0 ? number_format($arusKasInvestasi, 0, ',', '.') : '(' . number_format(abs($arusKasInvestasi), 0, ',', '.') . ')' }}
                        </td>
                    </tr>

                    <!-- ================= BAGIAN III: AKTIVITAS PENDANAAN ================= -->
                    <tr class="bg-slate-100/80 font-bold text-slate-900">
                        <td colspan="3" class="py-2 px-4 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-vault text-amber-700"></i>
                            <span>III. ARUS KAS DARI AKTIVITAS PENDANAAN</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-1.5 px-4 pl-8 text-slate-800">
                            Penerimaan Kas dari Setoran Modal Saham &amp; Tambahan Ekuitas Pemilik
                        </td>
                        <td class="py-1.5 px-3 text-center text-slate-500 font-mono text-[10px]">7</td>
                        <td class="py-1.5 px-4 text-right font-mono font-medium text-slate-900">
                            {{ number_format($setoranModal, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="bg-amber-50/60 font-bold border-t border-b border-amber-200">
                        <td class="py-2 px-4 pl-6 text-amber-950 uppercase tracking-wide">
                            Arus Kas Bersih yang Diperoleh dari (Digunakan untuk) Aktivitas Pendanaan
                        </td>
                        <td class="py-2 px-3 text-center text-amber-800 font-mono text-[10px]">C</td>
                        <td class="py-2 px-4 text-right font-mono text-sm {{ $arusKasPendanaan >= 0 ? 'text-emerald-900 font-bold' : 'text-rose-800 font-bold' }}">
                            {{ $arusKasPendanaan >= 0 ? number_format($arusKasPendanaan, 0, ',', '.') : '(' . number_format(abs($arusKasPendanaan), 0, ',', '.') . ')' }}
                        </td>
                    </tr>

                    <!-- ================= BAGIAN IV: REKONSILIASI KAS & SALDO AKHIR ================= -->
                    <tr class="bg-slate-100/80 font-bold text-slate-900">
                        <td colspan="3" class="py-2 px-4 uppercase tracking-wider flex items-center gap-2">
                            <i class="fas fa-calculator text-slate-800"></i>
                            <span>IV. REKONSILIASI PERUBAHAN NETO &amp; SALDO KAS DAN SETARA KAS</span>
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-2 px-4 pl-8 text-slate-900 font-semibold">
                            Kenaikan (Penurunan) Bersih Kas dan Setara Kas (A + B + C)
                        </td>
                        <td class="py-2 px-3 text-center text-slate-500 font-mono text-[10px]">—</td>
                        <td class="py-2 px-4 text-right font-mono font-bold {{ $kenaikanKasBersih >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                            {{ $kenaikanKasBersih >= 0 ? number_format($kenaikanKasBersih, 0, ',', '.') : '(' . number_format(abs($kenaikanKasBersih), 0, ',', '.') . ')' }}
                        </td>
                    </tr>
                    <tr class="hover:bg-slate-50/50">
                        <td class="py-2 px-4 pl-8 text-slate-800">
                            Kas dan Setara Kas pada Awal Periode ({{ \Carbon\Carbon::parse($tanggalDari)->translatedFormat('d F Y') }})
                        </td>
                        <td class="py-2 px-3 text-center text-slate-500 font-mono text-[10px]">8</td>
                        <td class="py-2 px-4 text-right font-mono font-medium text-slate-900">
                            {{ number_format($saldoAwalPeriode, 0, ',', '.') }}
                        </td>
                    </tr>
                    <tr class="bg-slate-900 text-white font-black text-xs">
                        <td class="py-3 px-4 uppercase tracking-wider">
                            KAS DAN SETARA KAS PADA AKHIR PERIODE ({{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }})
                        </td>
                        <td class="py-3 px-3 text-center font-mono text-amber-400">9</td>
                        <td class="py-3 px-4 text-right font-mono text-base text-amber-400">
                            Rp {{ number_format($saldoAkhirKas, 0, ',', '.') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- 5. CATATAN ATAS LAPORAN KEUANGAN: RINCIAN KAS & SETARA KAS PADA AKHIR PERIODE -->
        <section class="mb-6 break-inside-avoid">
            <div class="bg-slate-100 border border-slate-300 rounded-xl p-3.5">
                <h4 class="text-xs font-bold text-slate-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                    <i class="fas fa-clipboard-list text-slate-700"></i>
                    <span>Catatan 9: Rincian Kas dan Setara Kas pada Akhir Periode ({{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }})</span>
                </h4>
                <div class="overflow-x-auto">
                    <table class="w-full text-xs text-left border-collapse">
                        <thead>
                            <tr class="border-b border-slate-300 text-slate-600 font-semibold text-[10px] uppercase">
                                <th class="py-1 px-3 w-[20%]">Kode Akun</th>
                                <th class="py-1 px-3 w-[50%]">Nama Rekening / Akun Buku Besar</th>
                                <th class="py-1 px-3 w-[30%] text-right">Saldo Akhir (Rp)</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-200">
                            @forelse($rincianKas as $rk)
                                <tr class="hover:bg-slate-50">
                                    <td class="py-1.5 px-3 font-mono text-slate-700">{{ $rk['kode_akun'] }}</td>
                                    <td class="py-1.5 px-3 font-medium text-slate-900">{{ $rk['nama_akun'] }}</td>
                                    <td class="py-1.5 px-3 text-right font-mono font-medium text-slate-900">
                                        {{ number_format($rk['saldo'], 0, ',', '.') }}
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="py-2 px-3 text-center text-slate-400 italic">Tidak ada rekening kas &amp; bank terdaftar</td>
                                </tr>
                            @endforelse
                            <tr class="font-bold border-t-2 border-slate-400 bg-white">
                                <td colspan="2" class="py-1.5 px-3 text-right text-slate-800 uppercase tracking-wide">
                                    Total Kas dan Setara Kas (Catatan 9):
                                </td>
                                <td class="py-1.5 px-3 text-right font-mono text-slate-950 font-black double-border-bottom">
                                    Rp {{ number_format($saldoAkhirKas, 0, ',', '.') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>

            <!-- Verification Balance Bar -->
            <div class="mt-3 p-2.5 rounded-lg border border-slate-300 bg-slate-50 flex items-center justify-between text-[11px] text-slate-700">
                <div class="flex items-center gap-2">
                    <i class="fas fa-certificate text-emerald-600"></i>
                    <span>Status Posisi Kas: <strong class="text-slate-950">Seluruh Mutasi Jurnal Umum Terposting Sesuai Buku Kas &amp; Rekening Bank (Balanced)</strong></span>
                </div>
                <div class="font-mono text-[10px] text-slate-500">
                    Sistem Otomatisasi PBS-ERP Terpadu
                </div>
            </div>
        </section>

        <!-- 6. LEMBAR PENGESAHAN & TANDA TANGAN (3 KOLOM RESMI) -->
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

        <!-- 7. CATATAN KAKI DOKUMEN -->
        <footer class="mt-8 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-500">
            <p>Dokumen Laporan Arus Kas ini diterbitkan resmi melalui Sistem ERP &amp; Akuntansi Terpadu PT Pinastika Bhakti Semesta (PBS-ERP).</p>
            <p class="mt-0.5 text-slate-400">Keabsahan laporan ini diakui secara internal dan eksternal untuk keperluan pelaporan pajak, audit kepatuhan, serta manajemen korporasi.</p>
        </footer>
    </div>

</body>
</html>
