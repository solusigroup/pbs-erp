@extends('layouts.admin')

@section('title', 'Buku Besar (General Ledger) - Akuntansi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-sky-500/20 to-blue-500/20 border border-sky-500/30 text-sky-400 shadow-lg shadow-sky-500/10">
                    <i class="fas fa-book-journal-whills text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                        Buku Besar (General Ledger)
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-sky-500/10 text-sky-400 border border-sky-500/30 font-semibold font-mono">
                            SAK Standard
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Rincian mutasi akun buku besar dan kalkulasi saldo akhir per Chart of Accounts</p>
                </div>
            </div>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('akuntansi.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-book-bookmark text-amber-400"></i>
                <span>Daftar COA</span>
            </a>
            <button onclick="window.print()" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-print text-slate-400"></i>
                <span>Cetak Buku Besar</span>
            </button>
        </div>
    </div>

    <!-- Filter Account & Date Range -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
        <form method="GET" action="{{ route('akuntansi.buku-besar') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Pilih Akun Buku Besar</label>
                <select name="kode_akun" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-sky-500/40 text-white font-semibold focus:border-sky-400">
                    @foreach($allAccounts as $a)
                        <option value="{{ $a->kode_akun }}" {{ $selectedKode === $a->kode_akun ? 'selected' : '' }}>
                            {{ $a->kode_akun }} - {{ $a->nama_akun }} ({{ $a->kategori }})
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
                <button type="submit" class="w-full px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold transition flex items-center justify-center gap-2 h-[38px]">
                    <i class="fas fa-search"></i>
                    <span>Tampilkan Buku Besar</span>
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
            <span class="text-[10px] text-slate-500 mt-1 block">Saldo awal akun</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Total Mutasi Debit</span>
            <strong class="text-lg font-black text-emerald-400 block mt-2 font-mono">
                Rp {{ number_format($totalDebit, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Sisi penambahan/pengurangan</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-xs text-slate-400 font-medium block">Total Mutasi Kredit</span>
            <strong class="text-lg font-black text-rose-400 block mt-2 font-mono">
                Rp {{ number_format($totalKredit, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Sisi penambahan/pengurangan</span>
        </div>

        <div class="p-4 rounded-2xl border border-sky-500/30 bg-sky-950/20 shadow-lg">
            <span class="text-xs text-slate-300 font-medium block">Saldo Akhir (Per {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }})</span>
            <strong class="text-lg font-black text-sky-300 block mt-2 font-mono">
                Rp {{ number_format($saldoAkhirPeriode, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-sky-400/70 mt-1 block">Saldo akhir buku besar</span>
        </div>
    </div>

    <!-- General Ledger Table -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-2">
            <div>
                <h3 class="text-sm font-bold text-white flex items-center gap-2">
                    <i class="fas fa-book text-sky-400"></i>
                    <span>Akun: {{ $akun->kode_akun }} - {{ $akun->nama_akun }}</span>
                </h3>
                <p class="text-[11px] text-slate-400">Kategori: <strong class="text-slate-200">{{ $akun->kategori }}</strong> ({{ $akun->tipe_akun }}) &bull; Periode: {{ \Carbon\Carbon::parse($tanggalDari)->format('d F Y') }} s/d {{ \Carbon\Carbon::parse($tanggalSampai)->format('d F Y') }}</p>
            </div>
            <span class="text-xs px-3 py-1 rounded-xl bg-slate-800 text-slate-300 border border-slate-700">
                Saldo Normal: <strong class="{{ $akun->saldo_normal === 'Debit' ? 'text-emerald-400' : 'text-amber-400' }}">{{ $akun->saldo_normal }}</strong>
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">No Transaksi</th>
                        <th class="py-3 px-4">Tipe</th>
                        <th class="py-3 px-4">Keterangan / Deskripsi</th>
                        <th class="py-3 px-4 text-right">Debit (Rp)</th>
                        <th class="py-3 px-4 text-right">Kredit (Rp)</th>
                        <th class="py-3 px-4 text-right bg-slate-950/40">Saldo Berjalan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    <!-- Saldo Awal Row -->
                    <tr class="bg-slate-950/30 font-semibold italic text-slate-400">
                        <td class="py-3 px-4">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
                        <td class="py-3 px-4 font-mono">-</td>
                        <td class="py-3 px-4"><span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 text-slate-300">SALDO AWAL</span></td>
                        <td class="py-3 px-4">Saldo akun sebelum tanggal {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</td>
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
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $m->jurnal->tipe_jurnal }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-200">
                                {{ $m->keterangan_baris ?: $m->jurnal->deskripsi }}
                                @if($m->jurnal->sumber_referensi)
                                    <span class="text-[10px] text-slate-500 block">{{ $m->jurnal->sumber_referensi }}</span>
                                @endif
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
                            <td colspan="8" class="py-8 text-center text-slate-500">
                                Tidak ada transaksi buku besar untuk akun ini pada periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse

                    <!-- Total Row -->
                    <tr class="bg-slate-950/80 font-bold border-t-2 border-slate-700 text-xs">
                        <td colspan="4" class="py-3 px-4 text-right text-slate-300 uppercase">
                            Total Mutasi &amp; Saldo Akhir:
                        </td>
                        <td class="py-3 px-4 text-right text-emerald-400 font-mono">
                            Rp {{ number_format($totalDebit, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right text-rose-400 font-mono">
                            Rp {{ number_format($totalKredit, 0, ',', '.') }}
                        </td>
                        <td class="py-3 px-4 text-right text-sky-300 font-mono text-sm bg-slate-950/90">
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
