@extends('layouts.admin')

@section('title', 'Laporan Rekapitulasi Stok & Rendemen CUGIL')

@section('content')
<div class="space-y-6">
    <!-- Header Page (Printable) -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-slate-900/90 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-500 mb-1">
                <i class="fas fa-recycle"></i>
                <span>Laporan Operasional Produksi &amp; Persediaan</span>
            </div>
            <h1 class="text-2xl font-black text-white tracking-tight">Rekapitulasi Mutasi Stok &amp; Olahan CUGIL</h1>
            <p class="text-slate-400 text-xs mt-1">Pergerakan bahan mentah, proses cuci giling, dan persediaan siap jual PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('laporan.index') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold flex items-center gap-2 border border-slate-700 transition">
                <i class="fas fa-arrow-left"></i>
                <span>Kembali</span>
            </a>
            <a href="{{ route('laporan.cugil-rekap.export') }}" class="px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-emerald-500/20 transition">
                <i class="fas fa-file-excel"></i>
                <span>Export Excel</span>
            </a>
            <a href="{{ route('laporan.cugil-rekap.print') }}" target="_blank" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-orange-500/20 transition">
                <i class="fas fa-print"></i>
                <span>Cetak / PDF</span>
            </a>
        </div>
    </div>

    <!-- Ringkasan Agregat Mutasi Stok -->
    <div class="grid grid-cols-2 lg:grid-cols-6 gap-3">
        <div class="bg-slate-900/80 p-4 rounded-xl border border-slate-800 shadow">
            <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Stok Awal</span>
            <p class="text-lg font-black text-slate-200 mt-1">{{ number_format($totalStokAwal, 1, ',', '.') }} <span class="text-xs font-normal text-slate-400">Kg</span></p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-blue-500/30 shadow">
            <span class="text-[10px] font-bold text-blue-400 uppercase tracking-wider">Barang Masuk</span>
            <p class="text-lg font-black text-blue-400 mt-1">+{{ number_format($totalMasuk, 1, ',', '.') }} <span class="text-xs font-normal text-slate-400">Kg</span></p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-rose-500/30 shadow">
            <span class="text-[10px] font-bold text-rose-400 uppercase tracking-wider">Barang Keluar</span>
            <p class="text-lg font-black text-rose-400 mt-1">-{{ number_format($totalKeluar, 1, ',', '.') }} <span class="text-xs font-normal text-slate-400">Kg</span></p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-emerald-500/30 shadow">
            <span class="text-[10px] font-bold text-emerald-400 uppercase tracking-wider">Stok Akhir Fisik</span>
            <p class="text-lg font-black text-emerald-400 mt-1">{{ number_format($totalStokAkhir, 1, ',', '.') }} <span class="text-xs font-normal text-slate-400">Kg</span></p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-amber-500/30 shadow">
            <span class="text-[10px] font-bold text-amber-400 uppercase tracking-wider">Valuasi (Hrg Beli)</span>
            <p class="text-sm font-black text-amber-300 mt-1">Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }}</p>
        </div>
        <div class="bg-slate-900/80 p-4 rounded-xl border border-purple-500/30 shadow">
            <span class="text-[10px] font-bold text-purple-400 uppercase tracking-wider">Estimasi Omset Jual</span>
            <p class="text-sm font-black text-purple-300 mt-1">Rp {{ number_format($totalNilaiJual, 0, ',', '.') }}</p>
        </div>
    </div>

    <!-- Ringkasan Per Kategori Produk -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        @foreach($rekapPerKategori as $kat => $stat)
            <div class="bg-slate-900/70 p-4 rounded-xl border border-slate-800 flex items-center justify-between">
                <div>
                    <span class="text-xs font-bold text-slate-300">{{ $kat }}</span>
                    <p class="text-[11px] text-slate-400 mt-0.5">{{ $stat['count'] }} Jenis SKU</p>
                    <div class="mt-2 text-xs">
                        <span class="text-emerald-400 font-bold">Stok: {{ number_format($stat['stok'], 1, ',', '.') }} Kg</span>
                    </div>
                </div>
                <div class="text-right text-[11px] space-y-1">
                    <p class="text-blue-400 font-medium">In: {{ number_format($stat['masuk'], 0, ',', '.') }} Kg</p>
                    <p class="text-rose-400 font-medium">Out: {{ number_format($stat['keluar'], 0, ',', '.') }} Kg</p>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Tabel Rincian Rekapitulasi Seluruh SKU Barang -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <i class="fas fa-list text-amber-500"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar SKU &amp; Valuasi Posisi Persediaan</h3>
            </div>
            <div class="text-xs text-slate-400">
                Total terdaftar: <span class="text-white font-bold">{{ $barangs->count() }} SKU</span>
            </div>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">#</th>
                        <th class="px-4 py-3">Kode</th>
                        <th class="px-4 py-3">Nama Barang</th>
                        <th class="px-4 py-3">Kategori</th>
                        <th class="px-4 py-3 text-right">Stok Awal</th>
                        <th class="px-4 py-3 text-right text-blue-400">Masuk (In)</th>
                        <th class="px-4 py-3 text-right text-rose-400">Keluar (Out)</th>
                        <th class="px-4 py-3 text-right text-emerald-400 font-bold">Stok Akhir</th>
                        <th class="px-4 py-3 text-right">Hrg Beli (Rp)</th>
                        <th class="px-4 py-3 text-right">Hrg Jual (Rp)</th>
                        <th class="px-4 py-3 text-right text-amber-300">Nilai Persediaan</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($barangs as $index => $b)
                        @php
                            $nilaiAset = max(0, $b->stok_akhir) * $b->harga_beli;
                            $margin = $b->harga_jual - $b->harga_beli;
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3 text-slate-400">{{ $index + 1 }}</td>
                            <td class="px-4 py-3 font-mono font-bold text-amber-400">{{ $b->kode_barang }}</td>
                            <td class="px-4 py-3 font-semibold text-white">{{ $b->nama_barang }}</td>
                            <td class="px-4 py-3">
                                <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-slate-800 text-slate-300 border border-slate-700">
                                    {{ $b->kategori }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($b->stok_awal, 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-blue-400 font-semibold">+{{ number_format($b->barang_masuk, 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-rose-400 font-semibold">-{{ number_format($b->barang_keluar, 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-emerald-400 font-black">{{ number_format($b->stok_akhir, 1, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono">{{ number_format($b->harga_beli, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono text-slate-200">{{ number_format($b->harga_jual, 0, ',', '.') }}</td>
                            <td class="px-4 py-3 text-right font-mono font-bold text-amber-300">Rp {{ number_format($nilaiAset, 0, ',', '.') }}</td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="11" class="px-4 py-8 text-center text-slate-400">Tidak ada data persediaan barang CUGIL.</td>
                        </tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-800/90 font-bold text-xs text-white border-t-2 border-slate-700">
                    <tr>
                        <td colspan="4" class="px-4 py-3 text-right uppercase tracking-wider">Total Agregat:</td>
                        <td class="px-4 py-3 text-right font-mono">{{ number_format($totalStokAwal, 1, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-blue-400">+{{ number_format($totalMasuk, 1, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-rose-400">-{{ number_format($totalKeluar, 1, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-mono text-emerald-400">{{ number_format($totalStokAkhir, 1, ',', '.') }}</td>
                        <td colspan="2" class="px-4 py-3 text-right">Total Valuasi:</td>
                        <td class="px-4 py-3 text-right font-mono text-amber-400">Rp {{ number_format($totalNilaiBeli, 0, ',', '.') }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Tanda Tangan / Otorisasi Dokumen Laporan -->
    <div class="mt-8 pt-6 border-t border-slate-800 grid grid-cols-2 gap-8 text-center text-xs">
        <div>
            <p class="text-slate-400">Dibuat &amp; Diverifikasi Oleh,</p>
            <p class="text-slate-400 font-semibold mt-1">Kepala Operasional &amp; Produksi Pabrik</p>
            <div class="h-16"></div>
            <p class="text-white font-bold underline">( Tim Produksi &amp; Gudang CUGIL )</p>
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
