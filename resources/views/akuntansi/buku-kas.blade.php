@extends('layouts.admin')

@section('title', 'Buku Kas & Bank (Mutasi Rekening) - Akuntansi')

@section('content')
<style>
    @media print {
        /* 1. Zero graphics overhead & eliminate browser memory bloat */
        *, *::before, *::after {
            box-shadow: none !important;
            text-shadow: none !important;
            filter: none !important;
            transition: none !important;
        }

        /* 2. Kill all scroll/overflow contexts so Chromium doesn't paginate scroll slices */
        html, body, main, div, .overflow-x-auto, .overflow-hidden {
            overflow: visible !important;
            height: auto !important;
            background: #ffffff !important;
            color: #000000 !important;
        }

        /* 3. Hide non-printable screen UI */
        .no-print, header, footer, nav, aside, #mainSidebar, form, .action-column {
            display: none !important;
        }

        /* 4. Force 1-pass fast layout & compact accounting rows */
        table {
            table-layout: fixed !important;
            width: 100% !important;
            border-collapse: collapse !important;
            font-size: 8pt !important;
            color: #000000 !important;
        }

        thead {
            display: table-header-group !important;
        }

        tr {
            page-break-inside: avoid !important;
            break-inside: avoid !important;
        }

        th, td {
            padding: 3px 5px !important;
            border: 1px solid #94a3b8 !important;
            color: #000000 !important;
            line-height: 1.25 !important;
            white-space: normal !important;
        }

        th {
            background-color: #f1f5f9 !important;
            font-weight: 700 !important;
            text-transform: uppercase !important;
        }

        @page {
            size: A4 portrait;
            margin: 8mm 10mm 10mm 10mm;
        }
    }
</style>

<!-- Print-Only Kop Surat Resmi Korporasi -->
<div class="hidden print:block pb-3 mb-3 border-b-2 border-slate-900">
    <div class="flex items-start justify-between pb-2 border-b border-slate-300">
        <div class="flex items-center gap-3">
            <div class="h-14 w-14 shrink-0 flex items-center justify-center p-1 border border-slate-300 rounded">
                <img src="{{ asset('images/logo-pbs.png') }}" alt="Logo PBS" class="h-full w-full object-contain">
            </div>
            <div>
                <h1 class="text-base font-black text-slate-950 uppercase">{{ $perusahaan->nama_perusahaan ?? 'PT PINASTIKA BHAKTI SEMESTA' }}</h1>
                <p class="text-[10px] font-bold text-emerald-800 uppercase">Buku Rekening Kas &amp; Bank &bull; Pengelolaan Keuangan &amp; Akuntansi</p>
                <p class="text-[9px] text-slate-600 mt-0.5">{{ $perusahaan->alamat ?? 'Jl. Suromulang Barat VI/20, Mojokerto' }} | NPWP: {{ $perusahaan->npwp ?? '43.688.232.8-602.000' }}</p>
            </div>
        </div>
        <div class="text-right">
            <span class="inline-block border border-slate-900 bg-slate-950 text-white font-black text-[9px] uppercase tracking-wider px-2 py-0.5 rounded">
                REKENING KORAN
            </span>
            <div class="text-[9px] text-slate-600 font-mono mt-1">SAK EP / EMKM</div>
            <div class="text-[9px] text-slate-500 font-mono">Dicetak: {{ date('d/m/Y H:i') }} WIB</div>
        </div>
    </div>
    <div class="text-center py-2">
        <h2 class="text-sm font-black text-slate-950 uppercase">BUKU MUTASI KAS &amp; BANK (REKENING KORAN)</h2>
        <p class="text-[11px] font-bold text-emerald-800">Rekening: {{ $akun->kode_akun }} - {{ $akun->nama_akun }}</p>
        <p class="text-[10px] text-slate-600 font-medium">Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }} &bull; Saldo Normal: {{ $akun->saldo_normal }}</p>
    </div>
</div>

<!-- Print-Only Compact Summary Header -->
<div class="hidden print:grid grid-cols-4 gap-2 mb-3 text-xs">
    <div class="border border-slate-400 p-1.5 text-center">
        <span class="block text-[8px] uppercase font-bold text-slate-600">Saldo Awal</span>
        <strong class="font-mono text-[9pt] text-slate-950">Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}</strong>
    </div>
    <div class="border border-slate-400 p-1.5 text-center">
        <span class="block text-[8px] uppercase font-bold text-slate-600">Total Masuk (Dr)</span>
        <strong class="font-mono text-[9pt] text-slate-950">Rp {{ number_format($totalMasuk, 0, ',', '.') }}</strong>
    </div>
    <div class="border border-slate-400 p-1.5 text-center">
        <span class="block text-[8px] uppercase font-bold text-slate-600">Total Keluar (Cr)</span>
        <strong class="font-mono text-[9pt] text-slate-950">Rp {{ number_format($totalKeluar, 0, ',', '.') }}</strong>
    </div>
    <div class="border border-slate-900 p-1.5 text-center bg-slate-100">
        <span class="block text-[8px] uppercase font-black text-slate-950">Saldo Akhir</span>
        <strong class="font-mono text-[9pt] text-slate-950">Rp {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}</strong>
    </div>
</div>

<div class="space-y-6">
    <!-- Header Section (Screen Only) -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 no-print">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-emerald-500/20 to-teal-500/20 border border-emerald-500/30 text-emerald-400 shadow-lg shadow-emerald-500/10">
                    <i class="fas fa-wallet text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                        Buku Kas &amp; Bank
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 font-semibold font-mono">
                            Running Balance
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Buku mutasi rekening kas &amp; bank dengan kalkulasi saldo berjalan otomatis</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('akuntansi.jurnal-kas') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition flex items-center gap-2">
                <i class="fas fa-money-bill-transfer"></i>
                <span>Jurnal Kas &amp; Bank</span>
            </a>
            <a href="{{ route('akuntansi.buku-kas.cetak', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-500 to-emerald-600 hover:from-emerald-400 hover:to-emerald-500 text-slate-950 text-xs font-black transition flex items-center gap-2 shadow-lg shadow-emerald-500/25" title="Buka Cetak Rekening Kas & Bank (Cepat & Ringan)">
                <i class="fas fa-print"></i>
                <span>Cetak Rekening / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Account & Date Range (Screen Only) -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg no-print">
        <form method="GET" action="{{ route('akuntansi.buku-kas') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Pilih Rekening Kas / Bank</label>
                <select name="kode_akun" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-amber-500/40 text-white font-semibold focus:border-amber-400">
                    @foreach($cashAccounts as $ca)
                        <option value="{{ $ca->kode_akun }}" {{ $selectedKode === $ca->kode_akun ? 'selected' : '' }}>
                            {{ $ca->kode_akun }} - {{ $ca->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Mulai</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Selesai</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div class="flex items-end">
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold transition flex items-center justify-center gap-2 h-[38px]">
                    <i class="fas fa-arrows-rotate"></i>
                    <span>Tampilkan Mutasi</span>
                </button>
            </div>
        </form>
    </div>

    <!-- 4 Summary KPI Cards for Selected Account (Screen Only) -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 no-print">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Saldo Awal (Sebelum {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }})</span>
            <strong class="text-lg font-black text-amber-400 block mt-2 font-mono">
                Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Posisi saldo awal periode</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Total Penerimaan (Masuk / Debit)</span>
            <strong class="text-lg font-black text-emerald-400 block mt-2 font-mono">
                Rp {{ number_format($totalMasuk, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Dana masuk rekening</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Total Pengeluaran (Keluar / Kredit)</span>
            <strong class="text-lg font-black text-rose-400 block mt-2 font-mono">
                Rp {{ number_format($totalKeluar, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Dana keluar rekening</span>
        </div>

        <div class="p-4 rounded-2xl border border-emerald-500/30 bg-emerald-950/20 shadow-lg">
            <span class="text-xs text-slate-300 font-medium block">Saldo Akhir (Per {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }})</span>
            <strong class="text-lg font-black text-emerald-300 block mt-2 font-mono">
                Rp {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-emerald-400/70 mt-1 block">Saldo riil di pembukuan</span>
        </div>
    </div>

    <!-- Running Ledger Table Container -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl print:border-none print:shadow-none print:bg-white print:p-0 print:rounded-none">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2 no-print">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fas fa-list-ol text-amber-400"></i>
                    <span>Mutasi Rekening: {{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                </h3>
                <p class="text-[11px] text-slate-400">Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d F Y') }}</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-xl bg-slate-800 text-slate-300 border border-slate-700">
                Saldo Normal: <strong class="text-emerald-400">{{ $akun->saldo_normal }}</strong>
            </span>
        </div>

        <div class="overflow-x-auto print:overflow-visible">
            <table class="w-full text-left text-xs text-slate-300 print:text-black">
                <colgroup>
                    <col style="width: 10%;">
                    <col style="width: 14%;">
                    <col style="width: 8%;">
                    <col style="width: 30%;">
                    <col style="width: 14%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col style="width: 8%;">
                    <col class="action-column" style="width: 60px;">
                </colgroup>
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800 print:bg-slate-100 print:text-slate-900 print:border-slate-400">
                    <tr>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt] text-center">Tanggal</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt]">No Transaksi</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt] text-center">Tipe</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt]">Uraian / Deskripsi</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt]">Lawan Akun</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt] text-right">Penerimaan (Dr)</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt] text-right">Pengeluaran (Cr)</th>
                        <th class="py-3 px-4 print:py-1 print:px-1.5 print:text-[8pt] text-right bg-slate-950/40 print:bg-slate-100">Saldo Berjalan</th>
                        <th class="py-3 px-4 text-center action-column print:hidden">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60 print:divide-slate-300">
                    <!-- Row Saldo Awal -->
                    <tr class="bg-slate-950/30 font-semibold italic text-slate-400 print:bg-slate-50 print:text-slate-800">
                        <td class="py-3 px-4 print:py-1 print:px-1.5 text-center font-mono">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 print:py-1 print:px-1.5 font-mono">-</td>
                        <td class="py-3 px-4 print:py-1 print:px-1.5 text-center"><span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300 print:bg-transparent print:p-0 print:text-black">SALDO</span></td>
                        <td class="py-3 px-4 print:py-1 print:px-1.5" colspan="2">Saldo awal sebelum tanggal {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 print:py-1 print:px-1.5 text-right font-mono">-</td>
                        <td class="py-3 px-4 print:py-1 print:px-1.5 text-right font-mono">-</td>
                        <td class="py-3 px-4 print:py-1 print:px-1.5 text-right font-mono font-bold text-amber-400 bg-slate-950/40 print:text-black print:bg-slate-50">
                            Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center action-column print:hidden">-</td>
                    </tr>

                    @forelse($mutasiDetails as $m)
                        <tr class="hover:bg-slate-800/30 transition print:hover:bg-transparent">
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-center font-mono text-slate-300 print:text-black">
                                {{ $m->jurnal ? $m->jurnal->tanggal->format('d/m/Y') : '-' }}
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 font-bold text-white font-mono print:text-black">
                                {{ $m->jurnal ? $m->jurnal->no_transaksi : '-' }}
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-center whitespace-nowrap">
                                @if($m->jurnal)
                                    @if($m->jurnal->tipe_jurnal === 'Kas Masuk')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20 print:border-none print:bg-transparent print:p-0 print:text-black">BKM</span>
                                    @elseif($m->jurnal->tipe_jurnal === 'Kas Keluar')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20 print:border-none print:bg-transparent print:p-0 print:text-black">BKK</span>
                                    @elseif($m->jurnal->tipe_jurnal === 'Transfer')
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20 print:border-none print:bg-transparent print:p-0 print:text-black">TRF</span>
                                    @else
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20 print:border-none print:bg-transparent print:p-0 print:text-black">JU</span>
                                    @endif
                                @else
                                    -
                                @endif
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-slate-200 print:text-black">
                                {{ $m->keterangan_baris ?: ($m->jurnal ? $m->jurnal->deskripsi : '-') }}
                                @if($m->jurnal && $m->jurnal->sumber_referensi)
                                    <span class="text-[10px] text-slate-500 block print:text-slate-600 print:text-[7pt]">Ref: {{ $m->jurnal->sumber_referensi }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-slate-400 print:text-slate-700 text-[11px] print:text-[7.5pt]">
                                {{ $m->akun_lawan_nama }}
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-right font-mono font-medium text-emerald-400 print:text-black">
                                {{ $m->debit > 0 ? number_format($m->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-right font-mono font-medium text-rose-400 print:text-black">
                                {{ $m->kredit > 0 ? number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 print:py-1 print:px-1.5 text-right font-mono font-bold text-white bg-slate-950/40 print:text-black print:bg-transparent">
                                {{ number_format($m->saldo_berjalan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center action-column print:hidden">
                                <a href="{{ route('akuntansi.voucher', $m->jurnal ? $m->jurnal->id_jurnal : 0) }}" target="_blank" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 inline-flex items-center gap-1">
                                    <i class="fas fa-print text-amber-400"></i>
                                    <span>Bukti</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500 print:text-slate-700">
                                Tidak ada mutasi transaksi untuk rekening ini pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Row Total & Saldo Akhir -->
                    <tr class="bg-slate-950/80 font-bold border-t-2 border-slate-700 text-xs print:bg-slate-100 print:text-black print:border-slate-900">
                        <td colspan="5" class="py-3 px-4 print:py-1.5 print:px-1.5 text-right text-slate-300 print:text-black uppercase">
                            Total Mutasi &amp; Saldo Akhir:
                        </td>
                        <td class="py-3 px-4 print:py-1.5 print:px-1.5 text-right text-emerald-400 font-mono print:text-black">
                            {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 print:py-1.5 print:px-1.5 text-right text-rose-400 font-mono print:text-black">
                            {{ number_format($totalKeluar, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 print:py-1.5 print:px-1.5 text-right text-emerald-300 font-mono text-sm bg-slate-950/90 print:text-black print:bg-transparent">
                            {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center action-column print:hidden">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Print-Only Lembar Pengesahan Resmi -->
    <div class="hidden print:block pt-4 border-t border-slate-400 mt-4 break-inside-avoid">
        <div class="text-right text-xs text-slate-800 mb-2 font-semibold">
            {{ $perusahaan->kota ?? 'Mojokerto' }}, {{ \Carbon\Carbon::parse($tanggalSampai)->translatedFormat('d F Y') }}
        </div>
        <div class="grid grid-cols-3 gap-3 text-center text-xs">
            <div class="border border-slate-400 rounded p-2 bg-white">
                <span class="text-slate-600 block mb-10 text-[9px]">Dibuat &amp; Disusun Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900 text-[10px]">Kasir / Staff Keuangan</strong>
                <span class="text-[8px] text-slate-500">Divisi Keuangan</span>
            </div>
            <div class="border border-slate-400 rounded p-2 bg-white">
                <span class="text-slate-600 block mb-10 text-[9px]">Diperiksa &amp; Diverifikasi Oleh,</span>
                <strong class="block border-t border-slate-400 pt-1 text-slate-900 text-[10px]">Manager Keuangan</strong>
                <span class="text-[8px] text-slate-500">Divisi Keuangan</span>
            </div>
            <div class="border border-slate-900 rounded p-2 bg-slate-50">
                <span class="text-slate-900 block mb-10 text-[9px] font-bold">Disetujui &amp; Disahkan Oleh,</span>
                <strong class="block border-t border-slate-900 pt-1 text-slate-950 font-black text-[10px]">
                    {{ $perusahaan->bod_finance_tax ?? 'Kurniawan, S.E., Ak., CA., M.Ak.' }}
                </strong>
                <span class="text-[8px] text-slate-600 font-semibold">Board of Director (Finance &amp; Tax)</span>
            </div>
        </div>
        <div class="mt-3 text-center text-[8px] text-slate-500 italic">
            Buku Kas &amp; Bank PBS-ERP &bull; PT Pinastika Bhakti Semesta &bull; SAK EP / EMKM
        </div>
    </div>
</div>
@endsection
