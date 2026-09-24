@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Pembelian Bahan Baku')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-blue-400 mb-1">
                <i class="fas fa-truck-ramp-box"></i>
                <span>Laporan Pengadaan &amp; Logistik Masuk</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Rekapitulasi Pembelian Bahan Baku</h1>
            <p class="text-slate-400 text-xs mt-1">Penerimaan limbah plastik mentah, potongan rafaksi, biaya logistik truk &amp; status hutang supplier.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-2 border border-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('laporan.pembelian.export', request()->query()) }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('laporan.pembelian.print', request()->query()) }}" target="_blank" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-blue-500/20 transition">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
        <form method="GET" action="{{ route('laporan.pembelian') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Pemasok / Supplier</label>
                <select name="supplier" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">-- Semua Supplier --</option>
                    @foreach($suppliers as $sup)
                        <option value="{{ $sup->kode_supplier }}" {{ request('supplier') == $sup->kode_supplier ? 'selected' : '' }}>
                            {{ $sup->kode_supplier }} - {{ $sup->nama_supplier }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Status Pelunasan</label>
                <select name="status" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">-- Semua Status --</option>
                    <option value="LUNAS" {{ request('status') == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                    <option value="BELUM LUNAS" {{ request('status') == 'BELUM LUNAS' ? 'selected' : '' }}>BELUM LUNAS</option>
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Dari Tanggal</label>
                <input type="date" name="dari" value="{{ request('dari') }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Sampai Tanggal</label>
                <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow transition">
                    <i class="fas fa-filter text-[10px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('laporan.pembelian') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold py-2 px-3 rounded-xl text-xs border border-slate-700 transition" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- KPI Summary Pembelian -->
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-6 gap-3">
        <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-800">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Tonase</span>
            <p class="text-base font-black text-cyan-400 mt-1">{{ number_format($totalQty, 1, ',', '.') }} <span class="text-xs font-normal text-slate-400">Kg</span></p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-800">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Bruto</span>
            <p class="text-sm font-black text-slate-200 mt-1">Rp {{ number_format($totalBruto, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-amber-500/30">
            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Rafaksi Timbangan</span>
            <p class="text-sm font-black text-amber-400 mt-1">-Rp {{ number_format($totalRafaksi, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-purple-500/30">
            <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">Ongkos Angkut</span>
            <p class="text-sm font-black text-purple-300 mt-1">Rp {{ number_format($totalOngkir, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-blue-500/30">
            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Total Tagihan Bersih</span>
            <p class="text-sm font-black text-blue-400 mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-rose-500/30">
            <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Sisa Hutang Dagang</span>
            <p class="text-sm font-black text-rose-400 mt-1">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Rekapitulasi per Supplier -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 p-5 shadow-xl">
        <div class="flex items-center gap-2 mb-4">
            <i class="fas fa-truck text-amber-500"></i>
            <h3 class="text-sm font-bold text-white uppercase tracking-wider">Rekapitulasi Pembelian &amp; Hutang per Supplier</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-700">
                    <tr>
                        <th class="px-3 py-2.5">Supplier</th>
                        <th class="px-3 py-2.5 text-center">Frek</th>
                        <th class="px-3 py-2.5 text-right">Volume (Kg)</th>
                        <th class="px-3 py-2.5 text-right">Total Tagihan</th>
                        <th class="px-3 py-2.5 text-right text-emerald-400">Terbayar</th>
                        <th class="px-3 py-2.5 text-right text-rose-400">Sisa Hutang</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($rekapSupplier as $item)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-3 py-2.5">
                                <span class="font-bold text-white">{{ $item['nama'] }}</span>
                                <span class="text-[10px] text-slate-500 block">{{ $item['kode'] }}</span>
                            </td>
                            <td class="px-3 py-2.5 text-center font-bold text-slate-400">{{ $item['total_transaksi'] }}x</td>
                            <td class="px-3 py-2.5 text-right font-mono">{{ number_format($item['total_qty'], 1, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold text-white">Rp {{ number_format($item['total_tagihan'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right font-mono text-emerald-400">Rp {{ number_format($item['total_payment'], 0, ',', '.') }}</td>
                            <td class="px-3 py-2.5 text-right font-mono font-bold text-rose-400">Rp {{ number_format($item['sisa_hutang'], 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-3 py-4 text-center text-slate-400">Tidak ada data transaksi.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Tabel Rincian Semua Transaksi Pembelian -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-receipt text-blue-500"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Rincian Transaksi Penerimaan Bahan Baku</h3>
            </div>
            <span class="text-xs text-slate-400">{{ $rawMaterials->count() }} Dokumen</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">Tanggal / Ref</th>
                        <th class="px-4 py-3">Pemasok</th>
                        <th class="px-4 py-3">Item Barang (Multi-SKU)</th>
                        <th class="px-4 py-3 text-right">Total Qty (Kg)</th>
                        <th class="px-4 py-3 text-right">Rafaksi / Ongkir</th>
                        <th class="px-4 py-3 text-right text-blue-300">Total Tagihan</th>
                        <th class="px-4 py-3 text-right text-emerald-400">Dibayar</th>
                        <th class="px-4 py-3 text-right text-rose-400">Sisa Hutang</th>
                        <th class="px-4 py-3 text-center">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($rawMaterials as $raw)
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3">
                                <span class="font-bold text-white block">{{ \Carbon\Carbon::parse($raw->tanggal)->format('d/m/Y') }}</span>
                                <span class="text-[10px] text-amber-400 font-mono">{{ $raw->nomor_po ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-200 block">{{ $raw->nama_pemasok ?? $raw->kode_supplier }}</span>
                                <span class="text-[10px] text-slate-400">Truk: {{ $raw->truk ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                @if($raw->items && $raw->items->count() > 0)
                                    <div class="space-y-1">
                                        @foreach($raw->items as $it)
                                            <div class="text-[11px] flex items-center justify-between gap-2 border-b border-slate-800/50 pb-0.5">
                                                <span class="text-slate-300">{{ $it->nama_barang }}</span>
                                                <span class="font-mono text-slate-400">{{ number_format($it->qty, 1, ',', '.') }} Kg @ Rp{{ number_format($it->harga_satuan, 0, ',', '.') }}</span>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <span class="text-slate-300">{{ $raw->nama_barang }}</span>
                                    <span class="text-[10px] text-slate-400 block">{{ number_format($raw->qty, 1, ',', '.') }} Kg @ Rp{{ number_format($raw->harga_satuan, 0, ',', '.') }}</span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-cyan-400">{{ number_format($raw->total_qty, 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right text-[11px]">
                                <span class="text-amber-400 block">-Rp {{ number_format($raw->diskon_rafaksi, 0, ',', '.') }}</span>
                                <span class="text-purple-300 block">+Rp {{ number_format($raw->ongkos_angkut, 0, ',', '.') }}</span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono font-black text-blue-300">Rp {{ number_format($raw->tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-400">Rp {{ number_format($raw->payment, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-rose-400">Rp {{ number_format($raw->sisa_tagihan, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-center">
                                <span class="px-2 py-0.5 rounded-full text-[10px] font-bold {{ $raw->status_lunas == 'LUNAS' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $raw->status_lunas }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="px-4 py-8 text-center text-slate-400">Tidak ada data penerimaan bahan baku yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-800/90 font-bold text-xs text-white border-t-2 border-slate-700">
                    <tr>
                        <td colspan="3" class="px-4 py-3 text-right uppercase">Total:</td>
                        <td class="px-4 py-3 text-right font-mono text-cyan-400">{{ number_format($totalQty, 1, ',', '.') }} Kg</td>
                        <td class="px-4 py-3 text-right text-amber-400">-Rp {{ number_format($totalRafaksi, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-blue-300">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-400">Rp {{ number_format($totalPayment, 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-rose-400">Rp {{ number_format($totalSisa, 0, ',', '.') }}</td>
                        <td></td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tanda Tangan / Otorisasi Dokumen Laporan -->
    <div class="mt-8 pt-6 border-t border-slate-800 grid grid-cols-2 gap-8 text-center text-xs">
        <div>
            <p class="text-slate-400">Diverifikasi Bagian Logistik &amp; Gudang,</p>
            <div class="h-16"></div>
            <p class="text-white font-bold underline">( Tim Timbangan &amp; QC Penerimaan )</p>
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
