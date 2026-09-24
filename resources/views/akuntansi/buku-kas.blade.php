@extends('layouts.admin')

@section('title', 'Buku Kas & Bank (Mutasi Rekening) - Akuntansi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
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
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-print text-slate-400"></i>
                <span>Cetak Rekening</span>
            </button>
        </div>
    </div>

    <!-- Filter Account & Date Range -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
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

    <!-- 4 Summary KPI Cards for Selected Account -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
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

    <!-- Running Ledger Table -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
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

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">No Transaksi</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Uraian / Deskripsi</th>
                        <th class="py-3 px-4">Lawan Akun</th>
                        <th class="py-3 px-4 text-right">Penerimaan (Dr)</th>
                        <th class="py-3 px-4 text-right">Pengeluaran (Cr)</th>
                        <th class="py-3 px-4 text-right bg-slate-950/40">Saldo Berjalan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <!-- Row Saldo Awal -->
                    <tr class="bg-slate-950/30 font-semibold italic text-slate-400">
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 font-mono">-</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300">SALDO AWAL</span></td>
                        <td class="py-3 px-4" colspan="2">Saldo awal sebelum tanggal {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 text-right font-mono">-</td>
                        <td class="py-3 px-4 text-right font-mono">-</td>
                        <td class="py-3 px-4 text-right font-mono font-bold text-amber-400 bg-slate-950/40">
                            Rp {{ number_format($saldoAwalPeriode, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">-</td>
                    </tr>

                    @forelse($mutasiDetails as $m)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 whitespace-nowrap text-slate-300">
                                {{ $m->jurnal->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4 font-bold text-white font-mono whitespace-nowrap">
                                {{ $m->jurnal->no_transaksi }}
                            </td>
                            <td class="py-3 px-4 whitespace-nowrap">
                                @if($m->jurnal->tipe_jurnal === 'Kas Masuk')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">BKM</span>
                                @elseif($m->jurnal->tipe_jurnal === 'Kas Keluar')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">BKK</span>
                                @elseif($m->jurnal->tipe_jurnal === 'Transfer')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">TRF</span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-amber-500/10 text-amber-400 border border-amber-500/20">JU</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-200">
                                {{ $m->keterangan_baris ?: $m->jurnal->deskripsi }}
                                @if($m->jurnal->sumber_referensi)
                                    <span class="text-[10px] text-slate-500 block">{{ $m->jurnal->sumber_referensi }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-400 text-[11px]">
                                {{ $m->akun_lawan_nama }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-medium text-emerald-400 whitespace-nowrap">
                                {{ $m->debit > 0 ? 'Rp ' . number_format($m->debit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-medium text-rose-400 whitespace-nowrap">
                                {{ $m->kredit > 0 ? 'Rp ' . number_format($m->kredit, 0, ',', '.') : '-' }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-white bg-slate-950/40 whitespace-nowrap">
                                Rp {{ number_format($m->saldo_berjalan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <a href="{{ route('akuntansi.voucher', $m->jurnal->id_jurnal) }}" target="_blank" class="px-2 py-0.5 rounded bg-slate-800 hover:bg-slate-700 text-slate-300 text-[10px] border border-slate-700 inline-flex items-center gap-1">
                                    <i class="fas fa-print text-amber-400"></i>
                                    <span>Bukti</span>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="py-8 text-center text-slate-500">
                                Tidak ada mutasi transaksi untuk rekening ini pada rentang tanggal yang dipilih.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Row Total & Saldo Akhir -->
                    <tr class="bg-slate-950/80 font-bold border-t-2 border-slate-700 text-xs">
                        <td colspan="5" class="py-3 px-4 text-right text-slate-300 uppercase">
                            Total Mutasi &amp; Saldo Akhir:
                        </td>
                        <td class="py-3 px-4 text-right text-emerald-400 font-mono">
                            Rp {{ number_format($totalMasuk, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right text-rose-400 font-mono">
                            Rp {{ number_format($totalKeluar, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right text-emerald-300 font-mono text-sm bg-slate-950/90">
                            Rp {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-center">-</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
