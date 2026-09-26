<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Keuangan Korporasi - PT Pinastika Bhakti Semesta</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        @media print {
            .no-print { display: none !important; }
            body { background: white !important; color: black !important; padding: 0 !important; font-size: 10pt; }
            @page { size: A4 portrait; margin: 8mm 10mm; }
            .page-break { page-break-before: always; }
            tr { page-break-inside: avoid; }
        }
    </style>
</head>
<body class="bg-slate-900 text-slate-100 min-h-screen p-4 sm:p-8">

    <!-- Action Toolbar (Hidden in Print) -->
    <div class="max-w-4xl mx-auto mb-6 flex items-center justify-between no-print">
        <a href="{{ route('laporan.keuangan', request()->query()) }}" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold border border-slate-700 flex items-center gap-2">
            <i class="fas fa-arrow-left"></i>
            <span>Kembali ke Laporan</span>
        </a>
        <div class="flex items-center gap-2">
            <a href="{{ route('laporan.keuangan.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel (.csv)</span>
            </a>
            <button onclick="window.print()" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-orange-500/20 flex items-center gap-2">
                <i class="fas fa-print"></i>
                <span>Cetak / Simpan ke PDF</span>
            </button>
        </div>
    </div>

    <!-- Paper Canvas -->
    <div class="max-w-4xl mx-auto bg-white text-slate-900 rounded-2xl shadow-2xl p-8 border border-slate-200">
        
        <!-- Corporate Header -->
        <div class="flex items-start justify-between pb-4 border-b-2 border-slate-950">
            <div class="flex items-center gap-4">
                <div class="h-16 w-16 shrink-0 flex items-center justify-center p-1 border border-slate-300 rounded-lg">
                    <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PT Pinastika Bhakti Semesta" class="h-full w-full object-contain">
                </div>
                <div>
                    <h1 class="text-xl font-black tracking-tight text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                    <p class="text-[11px] font-bold text-amber-700 uppercase">Pengolahan Limbah Industri, Bahan Bakar Alternatif RDF &amp; Pengelolaan Lingkungan</p>
                    <p class="text-[10px] text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto, Jawa Timur' }}</p>
                    <p class="text-[10px] text-slate-500">NPWP: {{ $perusahaan->npwp ?? '43.688.232.8-602.000' }} | Telp: {{ $perusahaan->telepon ?? '+62 821 4164 3495' }} | Email: {{ $perusahaan->email ?? 'kurniawan@pinastika.co.id' }}</p>
                </div>
            </div>
            <div class="text-right">
                <span class="inline-block px-3 py-1 rounded bg-slate-950 text-white font-black text-[10px] uppercase tracking-wider">
                    LAPORAN RESMI SAK
                </span>
                <div class="mt-1 text-[10px] font-bold text-slate-700">
                    Standar SAK EP / EMKM
                </div>
                <div class="text-[9px] text-slate-500 font-mono">
                    Dicetak: {{ date('d/m/Y H:i') }} WIB
                </div>
            </div>
        </div>

        <!-- Document Title -->
        <div class="text-center py-3 border-b border-slate-200">
            <h2 class="text-base font-black text-slate-950 uppercase tracking-wide">LAPORAN LABA RUGI &amp; POSISI KEUANGAN (NERACA)</h2>
            <p class="text-xs text-slate-600 mt-0.5 font-medium">
                Periode: <strong class="text-slate-900 font-mono">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</strong> s/d <strong class="text-slate-900 font-mono">{{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</strong>
            </p>
        </div>

        <!-- KPI Summary Cards (Standar PBS) -->
        <div class="grid grid-cols-4 gap-2.5 py-3 text-xs">
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[9px] text-slate-500 block uppercase font-semibold">Penjualan Bersih (A.4)</span>
                <strong class="text-xs text-emerald-800 font-mono">Rp {{ number_format($labaRugiPbs['total_penjualan_bersih'], 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[9px] text-slate-500 block uppercase font-semibold">HPP / COGS (C)</span>
                <strong class="text-xs text-amber-800 font-mono">Rp {{ number_format($labaRugiPbs['C_total_cogs'], 0, ',', '.') }}</strong>
            </div>
            <div class="border border-slate-300 rounded-lg p-2 text-center bg-slate-50">
                <span class="text-[9px] text-slate-500 block uppercase font-semibold">Laba / Rugi Bruto (D)</span>
                <strong class="text-xs font-mono {{ $labaRugiPbs['D_laba_rugi_bruto'] >= 0 ? 'text-slate-900' : 'text-rose-700' }}">
                    Rp {{ number_format($labaRugiPbs['D_laba_rugi_bruto'], 0, ',', '.') }}
                </strong>
            </div>
            <div class="border border-slate-900 rounded-lg p-2 text-center bg-slate-100">
                <span class="text-[9px] text-slate-600 block uppercase font-bold">Net Income (G)</span>
                <strong class="text-xs font-mono {{ $labaRugiPbs['G_net_income'] >= 0 ? 'text-emerald-800 font-black' : 'text-rose-700 font-black' }}">
                    Rp {{ number_format($labaRugiPbs['G_net_income'], 0, ',', '.') }}
                </strong>
            </div>
        </div>

        <!-- ================= SECTION 1: LABA RUGI FORMAT STANDAR PBS ================= -->
        <div class="pt-2 pb-2">
            <div class="bg-slate-900 text-white font-bold text-xs uppercase px-3 py-1.5 rounded flex items-center justify-between mb-2">
                <span>I. Laporan Laba Rugi Operasional Cuci Giling (Format Standar PBS)</span>
                <span class="text-[10px] font-normal text-slate-300">Struktur Pos A s/d G</span>
            </div>

            <!-- Embed PBS Print Table Partial -->
            @include('akuntansi.partials.laba_rugi_pbs_print_table', ['labaRugiPbs' => $labaRugiPbs])
        </div>

        <!-- Page break for cleaner printing on A4 -->
        <div class="page-break my-6"></div>

        <!-- ================= SECTION 2: NERACA ================= -->
        <div class="pt-4 pb-2">
            <div class="bg-slate-900 text-white font-bold text-xs uppercase px-3 py-1.5 rounded flex items-center justify-between">
                <span>II. Laporan Posisi Keuangan (Neraca Saldo)</span>
                <span class="text-[10px] font-normal text-slate-300">Posisi Per {{ \Carbon\Carbon::parse($tanggalSampai)->format('d F Y') }}</span>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-2">
                <!-- SISI KIRI: ASET / AKTIVA -->
                <div class="border border-slate-300 rounded p-3 bg-white">
                    <h3 class="font-bold text-xs text-slate-900 uppercase border-b border-slate-300 pb-1 mb-2">ASET (AKTIVA)</h3>
                    
                    <!-- Kas & Bank -->
                    <div class="text-xs font-semibold text-slate-800 mt-2 mb-1">1. Kas &amp; Setara Kas</div>
                    <table class="w-full text-xs mb-2">
                        @foreach($akunKasBank as $kb)
                            <tr>
                                <td class="text-slate-600 font-mono text-[11px] w-20">{{ $kb->kode_akun }}</td>
                                <td class="text-slate-800">{{ $kb->nama_akun }}</td>
                                <td class="text-right font-mono">{{ number_format($kb->saldo_berjalan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold text-slate-700">
                            <td colspan="2" class="pt-1">Total Kas &amp; Bank:</td>
                            <td class="text-right font-mono pt-1">Rp {{ number_format($totalKasBank, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <!-- Piutang -->
                    <div class="text-xs font-semibold text-slate-800 mt-2 mb-1">2. Piutang Usaha</div>
                    <table class="w-full text-xs mb-2">
                        @foreach($akunPiutang as $pt)
                            <tr>
                                <td class="text-slate-600 font-mono text-[11px] w-20">{{ $pt->kode_akun }}</td>
                                <td class="text-slate-800">{{ $pt->nama_akun }}</td>
                                <td class="text-right font-mono">{{ number_format($pt->saldo_berjalan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold text-slate-700">
                            <td colspan="2" class="pt-1">Total Piutang:</td>
                            <td class="text-right font-mono pt-1">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <!-- Persediaan -->
                    <div class="text-xs font-semibold text-slate-800 mt-2 mb-1">3. Persediaan Barang Dagang &amp; Bahan</div>
                    <table class="w-full text-xs mb-2">
                        @foreach($akunPersediaan as $ps)
                            <tr>
                                <td class="text-slate-600 font-mono text-[11px] w-20">{{ $ps->kode_akun }}</td>
                                <td class="text-slate-800">{{ $ps->nama_akun }}</td>
                                <td class="text-right font-mono">{{ number_format($ps->saldo_berjalan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold text-slate-700">
                            <td colspan="2" class="pt-1">Total Persediaan:</td>
                            <td class="text-right font-mono pt-1">Rp {{ number_format($totalPersediaan, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <!-- Aset Tetap -->
                    <div class="text-xs font-semibold text-slate-800 mt-2 mb-1">4. Aset Tetap &amp; Mesin Pabrik</div>
                    <table class="w-full text-xs mb-3">
                        @foreach($akunAsetTetap as $at)
                            <tr>
                                <td class="text-slate-600 font-mono text-[11px] w-20">{{ $at->kode_akun }}</td>
                                <td class="text-slate-800">{{ $at->nama_akun }}</td>
                                <td class="text-right font-mono">{{ number_format($at->saldo_berjalan, 0, ',', '.') }}</td>
                            </tr>
                        @endforeach
                        <tr class="font-semibold text-slate-700">
                            <td colspan="2" class="pt-1">Total Aset Tetap:</td>
                            <td class="text-right font-mono pt-1">Rp {{ number_format($totalAsetTetap, 0, ',', '.') }}</td>
                        </tr>
                    </table>

                    <div class="border-t-2 border-slate-900 pt-2 flex justify-between font-bold text-xs bg-slate-50 p-2 rounded">
                        <span>TOTAL AKTIVA / ASET:</span>
                        <span class="font-mono text-slate-950">Rp {{ number_format($totalAset, 0, ',', '.') }}</span>
                    </div>
                </div>

                <!-- SISI KANAN: PASIVA / KEWAJIBAN & EKUITAS -->
                <div class="border border-slate-300 rounded p-3 bg-white flex flex-col justify-between">
                    <div>
                        <h3 class="font-bold text-xs text-slate-900 uppercase border-b border-slate-300 pb-1 mb-2">KEWAJIBAN &amp; EKUITAS (PASIVA)</h3>
                        
                        <!-- Kewajiban / Hutang -->
                        <div class="text-xs font-semibold text-slate-800 mt-2 mb-1">1. Kewajiban Jangka Pendek (Hutang)</div>
                        <table class="w-full text-xs mb-2">
                            @foreach($akunHutang as $ht)
                                <tr>
                                    <td class="text-slate-600 font-mono text-[11px] w-20">{{ $ht->kode_akun }}</td>
                                    <td class="text-slate-800">{{ $ht->nama_akun }}</td>
                                    <td class="text-right font-mono">{{ number_format($ht->saldo_berjalan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr class="font-semibold text-slate-700">
                                <td colspan="2" class="pt-1">Total Kewajiban:</td>
                                <td class="text-right font-mono pt-1">Rp {{ number_format($totalHutang, 0, ',', '.') }}</td>
                            </tr>
                        </table>

                        <!-- Ekuitas -->
                        <div class="text-xs font-semibold text-slate-800 mt-4 mb-1">2. Ekuitas / Modal</div>
                        <table class="w-full text-xs mb-3">
                            @foreach($akunModal as $md)
                                <tr>
                                    <td class="text-slate-600 font-mono text-[11px] w-20">{{ $md->kode_akun }}</td>
                                    <td class="text-slate-800">{{ $md->nama_akun }}</td>
                                    <td class="text-right font-mono">{{ number_format($md->saldo_berjalan, 0, ',', '.') }}</td>
                                </tr>
                            @endforeach
                            <tr>
                                <td class="text-slate-600 font-mono text-[11px] w-20">3999</td>
                                <td class="text-slate-800 font-semibold">Laba Bersih Tahun Berjalan</td>
                                <td class="text-right font-mono font-semibold">{{ number_format($labaBersih, 0, ',', '.') }}</td>
                            </tr>
                            <tr class="font-semibold text-slate-700">
                                <td colspan="2" class="pt-1">Total Ekuitas:</td>
                                <td class="text-right font-mono pt-1">Rp {{ number_format($totalModal + $labaBersih, 0, ',', '.') }}</td>
                            </tr>
                        </table>
                    </div>

                    <div class="border-t-2 border-slate-900 pt-2 flex justify-between font-bold text-xs bg-slate-50 p-2 rounded">
                        <span>TOTAL PASIVA:</span>
                        <span class="font-mono text-slate-950">Rp {{ number_format($totalKewajibanEkuitas, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- 3-Tier Signature Block -->
        <div class="grid grid-cols-3 gap-6 pt-8 text-center text-xs">
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Dibuat Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Senior Accounting &amp; Tax Staff</strong>
                <span class="text-[10px] text-slate-500">Divisi Finance &amp; Accounting</span>
            </div>
            <div class="border border-slate-300 rounded-xl p-3">
                <span class="text-slate-600 block mb-14 font-medium">Diperiksa Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900">Manajer Keuangan Korporasi</strong>
                <span class="text-[10px] text-slate-500">Divisi Keuangan &amp; Akuntansi</span>
            </div>
            <div class="border border-slate-900 rounded-xl p-3 bg-slate-50">
                <span class="text-slate-700 block mb-14 font-bold">Disetujui &amp; Disahkan Oleh,</span>
                <strong class="block border-t border-slate-900 pt-1 text-slate-950">Kurniawan, S.E., Ak., CA., M.Ak.</strong>
                <span class="text-[10px] text-slate-600 font-semibold">Board of Director (Finance &amp; Tax)</span>
            </div>
        </div>

        <div class="mt-6 pt-3 border-t border-slate-200 text-center text-[10px] text-slate-400">
            Laporan Keuangan Resmi PT Pinastika Bhakti Semesta • Sistem PBS-ERP Enterprise • Standar SAK EP &amp; PBS Manufaktur
        </div>
    </div>
</body>
</html>
