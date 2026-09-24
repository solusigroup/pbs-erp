@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Pajak Masa & Kepatuhan Fiskal')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-400 mb-1">
                <i class="fas fa-shield-halved"></i>
                <span>Tax Compliance &amp; Fiscal Governance</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Rekapitulasi Pajak Masa &amp; Tahunan</h1>
            <p class="text-slate-400 text-xs mt-1">Pengawasan kewajiban PPN, PPh 21, PPh 22, PPh 23 &amp; validasi NTPN perpajakan PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-2 border border-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('laporan.pajak.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('laporan.pajak.print', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-purple-500/20 transition">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
        <form method="GET" action="{{ route('laporan.pajak') }}" class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Jenis Pajak</label>
                <select name="jenis_pajak" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                    <option value="">-- Semua Jenis Pajak --</option>
                    <option value="PPN Keluaran" {{ request('jenis_pajak') == 'PPN Keluaran' ? 'selected' : '' }}>PPN Keluaran</option>
                    <option value="PPN Masukan" {{ request('jenis_pajak') == 'PPN Masukan' ? 'selected' : '' }}>PPN Masukan</option>
                    <option value="PPh 21" {{ request('jenis_pajak') == 'PPh 21' ? 'selected' : '' }}>PPh 21 (Gaji/Upah)</option>
                    <option value="PPh 22" {{ request('jenis_pajak') == 'PPh 22' ? 'selected' : '' }}>PPh 22 (Limbah/Bahan Baku)</option>
                    <option value="PPh 23" {{ request('jenis_pajak') == 'PPh 23' ? 'selected' : '' }}>PPh 23 (Jasa)</option>
                    <option value="PPh Final" {{ request('jenis_pajak') == 'PPh Final' ? 'selected' : '' }}>PPh Final 4(2)</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Status Penyetoran</label>
                <select name="status_bayar" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                    <option value="">-- Semua Status --</option>
                    <option value="Sudah Disetor" {{ request('status_bayar') == 'Sudah Disetor' ? 'selected' : '' }}>Sudah Disetor (NTPN Valid)</option>
                    <option value="Belum Disetor" {{ request('status_bayar') == 'Belum Disetor' ? 'selected' : '' }}>Belum Disetor (Terutang)</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-purple-600 hover:bg-purple-700 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow transition">
                    <i class="fas fa-filter text-[10px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('laporan.pajak') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold py-2 px-3 rounded-xl text-xs border border-slate-700 transition" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Summary Pajak -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow">
            <span class="text-xs font-semibold uppercase tracking-wider text-slate-400">Total Beban Pajak Terhitung</span>
            <h3 class="text-xl font-black text-white mt-1">Rp {{ number_format($totalPajak, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">{{ $transaksis->count() }} Dokumen transaksi perpajakan</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-emerald-500/30 shadow">
            <span class="text-xs font-semibold uppercase tracking-wider text-emerald-400">Sudah Disetor ke Kas Negara</span>
            <h3 class="text-xl font-black text-emerald-400 mt-1">Rp {{ number_format($pajakSudahSetor, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Status NTPN tervalidasi DJP</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-rose-500/30 shadow">
            <span class="text-xs font-semibold uppercase tracking-wider text-rose-400">Pajak Terutang (Belum Disetor)</span>
            <h3 class="text-xl font-black text-rose-400 mt-1">Rp {{ number_format($pajakBelumSetor, 0, ',', '.') }}</h3>
            <p class="text-[11px] text-slate-400 mt-1">Harap disetor sebelum jatuh tempo</p>
        </div>
        <div class="bg-slate-900/80 p-5 rounded-2xl border border-purple-500/30 shadow">
            <span class="text-xs font-semibold uppercase tracking-wider text-purple-400">Tax Compliance Ratio</span>
            <h3 class="text-xl font-black text-purple-300 mt-1">{{ $totalPajak > 0 ? round(($pajakSudahSetor / $totalPajak) * 100, 1) : 100 }}%</h3>
            <p class="text-[11px] text-slate-400 mt-1">Tingkat kepatuhan fiskal PBS</p>
        </div>
    </div>

    <!-- Rekapitulasi per Jenis Pajak -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-list-check text-purple-500"></i>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Rekapitulasi Kepatuhan per Klasifikasi Pajak</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">Jenis Pajak</th>
                        <th class="px-4 py-3 text-center">Frek</th>
                        <th class="px-4 py-3 text-right">Total Pajak</th>
                        <th class="px-4 py-3 text-right text-emerald-400">Sudah Disetor</th>
                        <th class="px-4 py-3 text-right text-rose-400">Belum Disetor</th>
                        <th class="px-4 py-3 text-center">Tingkat Setor</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($rekapJenis as $item)
                        @php
                            $persenSetor = $item['total'] > 0 ? round(($item['sudah'] / $item['total']) * 100, 1) : 100;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 font-bold text-white">{{ $item['jenis'] }}</td>
                            <td class="px-4 py-3 text-center font-bold text-slate-400">{{ $item['count'] }}x</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-white">Rp {{ number_format($item['total'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-400">Rp {{ number_format($item['sudah'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rose-400">Rp {{ number_format($item['belum'], 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $persenSetor >= 100 ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20' }}">
                                    {{ $persenSetor }}%
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-4 text-center text-slate-400">Tidak ada data perpajakan.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Rincian Semua Transaksi Pajak -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-file-invoice text-purple-500"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Rincian Dokumen &amp; Faktur Perpajakan</h3>
            </div>
            <span class="text-xs text-slate-400">{{ $transaksis->count() }} Dokumen</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">Tanggal / Faktur</th>
                        <th class="px-4 py-3">Jenis Pajak</th>
                        <th class="px-4 py-3">Masa / Tahun</th>
                        <th class="px-4 py-3">Keterangan Transaksi</th>
                        <th class="px-4 py-3 text-right">DPP (Rp)</th>
                        <th class="px-4 py-3 text-center">Tarif</th>
                        <th class="px-4 py-3 text-right text-purple-300 font-bold">Nominal Pajak</th>
                        <th class="px-4 py-3">NTPN / Ref</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transaksis as $t)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-white block">{{ $t->tanggal_faktur_potong ? \Carbon\Carbon::parse($t->tanggal_faktur_potong)->format('d/m/Y') : '-' }}</span>
                                <span class="text-[10px] text-amber-400 font-mono">{{ $t->nomor_dokumen ?? $t->kode_referensi }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-200 block">{{ $t->jenis_pajak }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t->lawan_transaksi ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3 text-slate-400">{{ $t->masa_pajak }} / {{ $t->tahun_pajak }}</td>
                            <td class="px-4 py-3 text-slate-300 max-w-xs truncate">{{ $t->catatan ?? $t->lawan_transaksi ?? '-' }}</td>
                            <td class="px-4 py-3 text-right font-mono">Rp {{ number_format($t->dpp, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center font-mono">{{ $t->tarif_persen }}%</td>
                            <td class="px-4 py-3 text-right font-mono font-black text-purple-300">Rp {{ number_format($t->nominal_pajak, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-400">{{ $t->ntpn ?? '-' }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $t->status_bayar == 'Sudah Disetor' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $t->status_bayar }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada data transaksi pajak yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-800/90 font-bold text-xs text-white border-t-2 border-slate-700">
                    <tr>
                        <td colspan="6" class="px-4 py-3 text-right uppercase">Total Beban Pajak:</td>
                        <td class="px-4 py-3 text-right font-mono text-purple-300">Rp {{ number_format($totalPajak, 0, ',', '.') }}</td>
                        <td colspan="2"></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tanda Tangan / Otorisasi Dokumen Laporan -->
    <div class="mt-8 pt-6 border-t border-slate-800 grid grid-cols-2 gap-8 text-center text-xs">
        <div>
            <p class="text-slate-400">Disiapkan Oleh Petugas Pajak,</p>
            <div class="h-16"></div>
            <p class="text-white font-bold underline">( Tax Specialist PT PBS )</p>
        </div>
        <div>
            <p class="text-slate-400">Disetujui Oleh,</p>
            <p class="text-amber-400 font-semibold mt-1">Direktur Keuangan &amp; Perpajakan (BOD)</p>
            <div class="h-16"></div>
            <p class="text-white font-bold underline">Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.</p>
            <p class="text-[11px] text-slate-400">Board of Director PT Pinastika Bhakti Semesta</p>
        </div>
    </div>
</div>
@endsection
