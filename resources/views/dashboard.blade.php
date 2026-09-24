@extends('layouts.admin')

@section('title', 'Executive Dashboard - BOD Finance & Tax')

@section('content')
<div class="space-y-6">
    <!-- Executive Welcome Banner -->
    <div class="relative overflow-hidden rounded-3xl border border-amber-500/30 bg-gradient-to-r from-slate-900 via-[#101d33] to-slate-900 p-8 shadow-2xl backdrop-blur-xl">
        <div class="absolute -right-16 -top-16 h-60 w-60 rounded-full bg-amber-500/15 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative z-10">
            <div class="flex items-center gap-5">
                <div class="h-20 w-20 rounded-2xl border-2 border-amber-500/50 bg-slate-900 overflow-hidden shadow-xl shrink-0">
                    <img src="{{ asset('images/ayahrompi.png') }}" alt="Kurniawan" class="h-full w-full object-cover object-top" onerror="this.style.display='none'">
                    <div class="h-full w-full flex items-center justify-center font-black text-2xl text-amber-400">K</div>
                </div>
                <div>
                    <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/30 mb-2">
                        <i class="fas fa-crown text-[10px]"></i>
                        <span>Executive Workspace &bull; Board of Director</span>
                    </div>
                    <h2 class="text-xl sm:text-2xl font-black text-white">Selamat Datang, {{ auth()->user()->name ?? 'Kurniawan, S.E.' }}</h2>
                    <p class="text-xs sm:text-sm text-slate-300 mt-0.5">
                        Jabatan: <span class="text-amber-400 font-semibold">{{ auth()->user()->position ?? 'Board of Director (Finance & Tax)' }}</span> &bull; PT Pinastika Bhakti Semesta
                    </p>
                </div>
            </div>

            <!-- Quick Action Buttons -->
            <div class="flex flex-wrap items-center gap-3">
                <a href="{{ route('akuntansi.jurnal') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition">
                    <i class="fas fa-plus"></i>
                    <span>Catat Jurnal Baru</span>
                </a>
                <a href="{{ route('anggaran.index') }}" class="inline-flex items-center gap-2 px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold transition">
                    <i class="fas fa-hand-holding-dollar text-amber-400"></i>
                    <span>Review Pengajuan Dana</span>
                </a>
            </div>
        </div>
    </div>

    <!-- 4 KPI Stat Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Kas & Bank -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 backdrop-blur-md">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Total Kas & Bank</span>
                <div class="h-9 w-9 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                    <i class="fas fa-wallet"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">
                Rp {{ number_format($kasBankTotal, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-emerald-400 mt-2 flex items-center gap-1">
                <i class="fas fa-circle-check text-[9px]"></i>
                <span>Likuiditas kas operasional siap pakai</span>
            </p>
        </div>

        <!-- Piutang Usaha Proyek -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 backdrop-blur-md">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Piutang Proyek (AR)</span>
                <div class="h-9 w-9 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm">
                    <i class="fas fa-file-invoice"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">
                Rp {{ number_format($piutangTotal, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-sky-400 mt-2 flex items-center gap-1">
                <i class="fas fa-clock text-[9px]"></i>
                <span>Tagihan termin & invoice klien</span>
            </p>
        </div>

        <!-- Hutang & Kewajiban -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 backdrop-blur-md">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Hutang Usaha (AP)</span>
                <div class="h-9 w-9 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm">
                    <i class="fas fa-receipt"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-white tracking-tight">
                Rp {{ number_format($hutangTotal, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
                <i class="fas fa-info-circle text-[9px]"></i>
                <span>Kewajiban vendor & operasional</span>
            </p>
        </div>

        <!-- Laba Rugi Berjalan (YTD) -->
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 backdrop-blur-md">
            <div class="flex items-center justify-between text-slate-400 mb-3">
                <span class="text-xs font-bold uppercase tracking-wider">Laba Bersih Berjalan</span>
                <div class="h-9 w-9 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm">
                    <i class="fas fa-chart-line"></i>
                </div>
            </div>
            <div class="text-2xl font-black text-amber-400 tracking-tight">
                Rp {{ number_format($labaBerjalan, 0, ',', '.') }}
            </div>
            <p class="text-[11px] text-slate-400 mt-2 flex items-center gap-1">
                <span>Pendapatan: Rp {{ number_format($pendapatanTotal, 0, ',', '.') }}</span>
            </p>
        </div>
    </div>

    <!-- 2 Column Section: Pajak & Pengajuan Dana Approval -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Kolom Kiri: Status Pajak (Finance & Tax BOD focus) -->
        <div class="lg:col-span-6 rounded-3xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur-md">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-amber-500/10 text-amber-400 flex items-center justify-center text-sm">
                        <i class="fas fa-shield-halved"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Monitoring Pajak Korporasi</h3>
                        <p class="text-[11px] text-slate-400">Pengawasan langsung BOD Finance & Tax</p>
                    </div>
                </div>
                <a href="{{ route('pajak.index') }}" class="text-xs text-amber-400 hover:underline">Kelola &rarr;</a>
            </div>

            <div class="grid grid-cols-2 gap-3 mb-4">
                <div class="p-3.5 rounded-xl border border-slate-800 bg-slate-950/50">
                    <span class="text-[11px] text-slate-400 block">Pajak Belum Disetor</span>
                    <strong class="text-base font-black text-rose-400 block mt-1">Rp {{ number_format($pajakBelumSetor, 0, ',', '.') }}</strong>
                </div>
                <div class="p-3.5 rounded-xl border border-slate-800 bg-slate-950/50">
                    <span class="text-[11px] text-slate-400 block">Pajak Sudah Disetor (NTPN)</span>
                    <strong class="text-base font-black text-emerald-400 block mt-1">Rp {{ number_format($pajakSudahSetor, 0, ',', '.') }}</strong>
                </div>
            </div>

            <div class="space-y-2.5">
                <div class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Transaksi Bukti Potong / Faktur Terbaru</div>
                @forelse($transaksiPajakTerbaru as $tax)
                    <div class="p-3 rounded-xl border border-slate-800/80 bg-slate-950/40 flex items-center justify-between text-xs">
                        <div>
                            <span class="font-bold text-white block">{{ $tax->lawan_transaksi }}</span>
                            <span class="text-[10px] text-slate-400">{{ $tax->jenis_pajak }} &bull; Masa: {{ $tax->masa_pajak }}</span>
                        </div>
                        <div class="text-right">
                            <span class="font-bold text-amber-400 block">Rp {{ number_format($tax->nominal_pajak, 0, ',', '.') }}</span>
                            <span class="text-[10px] {{ $tax->status_bayar === 'Sudah Disetor' ? 'text-emerald-400' : 'text-rose-400' }} font-medium">
                                {{ $tax->status_bayar }}
                            </span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic">Belum ada transaksi pajak tercatat.</p>
                @endforelse
            </div>
        </div>

        <!-- Kolom Kanan: Pengajuan Dana Menunggu Approval BOD -->
        <div class="lg:col-span-6 rounded-3xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur-md">
            <div class="flex items-center justify-between mb-5">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-orange-500/10 text-orange-400 flex items-center justify-center text-sm">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Antrean Approval Anggaran BOD</h3>
                        <p class="text-[11px] text-slate-400">Verifikasi pengeluaran kas & anggaran operasional</p>
                    </div>
                </div>
                <a href="{{ route('anggaran.index') }}" class="text-xs text-amber-400 hover:underline">Semua &rarr;</a>
            </div>

            @if($pengajuanMenunggu->count() > 0)
                <div class="space-y-3">
                    @foreach($pengajuanMenunggu as $req)
                        <div class="p-4 rounded-2xl border border-amber-500/30 bg-amber-500/5 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
                            <div>
                                <div class="flex items-center gap-2">
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-amber-400 bg-amber-500/10 px-2 py-0.5 rounded">
                                        {{ $req->departemen }}
                                    </span>
                                    <span class="text-xs font-semibold text-slate-300">{{ $req->pemohon }}</span>
                                </div>
                                <h4 class="text-sm font-bold text-white mt-1">{{ $req->keperluan }}</h4>
                                <span class="text-xs text-amber-400 font-extrabold mt-0.5 block">
                                    Nominal: Rp {{ number_format($req->nominal_diajukan, 0, ',', '.') }}
                                </span>
                            </div>

                            <!-- Fast Approve Modal or Button -->
                            <form method="POST" action="{{ route('anggaran.approve', $req->id) }}" class="flex items-center gap-2">
                                @csrf
                                <input type="hidden" name="nominal_disetujui" value="{{ $req->nominal_diajukan }}">
                                <input type="hidden" name="metode_pencairan" value="Transfer Mandiri">
                                <button type="submit" class="px-3 py-1.5 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold shadow transition">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                                <a href="{{ route('anggaran.index') }}" class="px-2.5 py-1.5 rounded-lg bg-slate-800 text-slate-300 text-xs hover:bg-slate-700">
                                    Detail
                                </a>
                            </form>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="p-8 text-center border border-dashed border-slate-800 rounded-2xl">
                    <i class="fas fa-circle-check text-emerald-400 text-2xl mb-2"></i>
                    <p class="text-xs text-slate-400 font-medium">Tidak ada antrean pengajuan dana menunggu approval.</p>
                </div>
            @endif

            <!-- Riwayat Pengajuan Lainnya -->
            <div class="mt-4 pt-4 border-t border-slate-800 space-y-2">
                <span class="text-[11px] font-bold text-slate-400 uppercase tracking-wider">Riwayat Pengajuan Terkini</span>
                @foreach($pengajuanTerbaru->take(3) as $row)
                    <div class="flex items-center justify-between text-xs text-slate-300 py-1">
                        <span class="truncate max-w-[200px]">{{ $row->keperluan }}</span>
                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $row->status === 'Disetujui BOD' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-slate-800 text-slate-400' }}">
                            {{ $row->status }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <!-- Jurnal Transaksi & Proyek Berjalan -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- Jurnal Terkini -->
        <div class="lg:col-span-7 rounded-3xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur-md">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-blue-500/10 text-blue-400 flex items-center justify-center text-sm">
                        <i class="fas fa-receipt"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Jurnal Transaksi Terkini</h3>
                        <p class="text-[11px] text-slate-400">Pencatatan pembukuan akuntansi double-entry</p>
                    </div>
                </div>
                <a href="{{ route('akuntansi.jurnal') }}" class="text-xs text-amber-400 hover:underline">Buku Jurnal &rarr;</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-300">
                    <thead class="text-[10px] uppercase font-bold text-slate-400 border-b border-slate-800">
                        <tr>
                            <th class="py-2.5 px-3">No Transaksi</th>
                            <th class="py-2.5 px-3">Tanggal</th>
                            <th class="py-2.5 px-3">Deskripsi</th>
                            <th class="py-2.5 px-3 text-right">Debit / Kredit</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/60">
                        @forelse($jurnalTerbaru as $j)
                            <tr>
                                <td class="py-3 px-3 font-bold text-amber-400">{{ $j->no_transaksi }}</td>
                                <td class="py-3 px-3">{{ $j->tanggal->format('d/m/Y') }}</td>
                                <td class="py-3 px-3 truncate max-w-[220px]">{{ $j->deskripsi }}</td>
                                <td class="py-3 px-3 text-right font-bold text-white">Rp {{ number_format($j->total_debit, 0, ',', '.') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="py-4 text-center text-slate-500 italic">Belum ada jurnal transaksi.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Proyek Berjalan -->
        <div class="lg:col-span-5 rounded-3xl border border-slate-800 bg-slate-900/60 p-6 backdrop-blur-md">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm">
                        <i class="fas fa-diagram-project"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Proyek Klien Berjalan</h3>
                        <p class="text-[11px] text-slate-400">Kontrak & Progress Penagihan</p>
                    </div>
                </div>
                <a href="{{ route('proyek.index') }}" class="text-xs text-amber-400 hover:underline">Kelola &rarr;</a>
            </div>

            <div class="space-y-3.5">
                @forelse($proyekAktif as $p)
                    <div class="p-3.5 rounded-2xl border border-slate-800 bg-slate-950/50">
                        <div class="flex items-center justify-between">
                            <h4 class="text-xs font-bold text-white truncate max-w-[200px]">{{ $p->nama_proyek }}</h4>
                            <span class="text-[10px] text-amber-400 font-bold">{{ $p->progress_persen }}%</span>
                        </div>
                        <p class="text-[11px] text-slate-400 mt-0.5">Klien: {{ $p->nama_klien }}</p>
                        
                        <!-- Progress bar -->
                        <div class="w-full bg-slate-800 h-1.5 rounded-full mt-2 overflow-hidden">
                            <div class="bg-amber-500 h-full rounded-full" style="width: {{ $p->progress_persen }}%"></div>
                        </div>

                        <div class="flex items-center justify-between text-[11px] mt-2 text-slate-300">
                            <span>Kontrak: Rp {{ number_format($p->nilai_kontrak, 0, ',', '.') }}</span>
                            <span class="text-emerald-400 font-bold">Tertagih: Rp {{ number_format($p->total_tertagih, 0, ',', '.') }}</span>
                        </div>
                    </div>
                @empty
                    <p class="text-xs text-slate-500 italic text-center py-4">Belum ada proyek berjalan.</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- CUGIL Operasional Bisnis Daur Ulang Plastik (Cuci Giling) -->
    <div class="rounded-3xl border border-blue-500/30 bg-gradient-to-r from-slate-900 via-[#0e1c31] to-slate-900 p-6 backdrop-blur-xl shadow-xl">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-blue-500/20 text-blue-400 flex items-center justify-center text-lg border border-blue-500/30">
                    <i class="fas fa-recycle"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Operasional Cuci Giling (CUGIL) PT PBS</h3>
                    <p class="text-xs text-slate-400">Pembelian Bahan Kresek &bull; Proses Cuci Giling &bull; Penjualan Produk Cacahan</p>
                </div>
            </div>
            <div class="flex items-center gap-2">
                <a href="{{ route('cugil.po.index') }}" class="px-3 py-1.5 rounded-lg bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold">PO Bahan</a>
                <a href="{{ route('cugil.raw.index') }}" class="px-3 py-1.5 rounded-lg bg-amber-600 hover:bg-amber-700 text-white text-xs font-semibold">Terima Bahan</a>
                <a href="{{ route('cugil.sales.index') }}" class="px-3 py-1.5 rounded-lg bg-emerald-600 hover:bg-emerald-700 text-white text-xs font-semibold">Penjualan</a>
            </div>
        </div>

        {{-- CUGIL Metrics --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-[11px] text-slate-400 block">Total Penjualan CUGIL</span>
                <strong class="text-base font-bold text-emerald-400 block mt-1">Rp {{ number_format($cugilTotalSales, 0, ',', '.') }}</strong>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-[11px] text-slate-400 block">Bahan Baku Masuk</span>
                <strong class="text-base font-bold text-amber-400 block mt-1">Rp {{ number_format($cugilTotalRawTagihan, 0, ',', '.') }}</strong>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-[11px] text-slate-400 block">Total Mitra Supplier</span>
                <strong class="text-base font-bold text-cyan-400 block mt-1">{{ $cugilTotalSupplier }} Mitra</strong>
            </div>
            <div class="p-4 rounded-2xl bg-slate-950/60 border border-slate-800">
                <span class="text-[11px] text-slate-400 block">Total Kastamer Pembeli</span>
                <strong class="text-base font-bold text-purple-400 block mt-1">{{ $cugilTotalCustomer }} Pembeli</strong>
            </div>
        </div>

        {{-- Two tables: Recent Raw Materials & Recent Sales --}}
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-5">
            {{-- Terima Bahan Terbaru --}}
            <div class="p-4 rounded-2xl bg-slate-950/50 border border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-amber-400 uppercase tracking-wider"><i class="fas fa-boxes-stacked mr-1.5"></i>Terima Bahan Terbaru</span>
                    <a href="{{ route('cugil.raw.index') }}" class="text-[11px] text-slate-400 hover:text-white">Semua &rarr;</a>
                </div>
                <div class="space-y-2">
                    @forelse($cugilRecentRaw as $r)
                        <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-semibold text-white block">{{ $r->nama_barang }}</span>
                                <span class="text-[10px] text-slate-400">{{ $r->nama_pemasok }} &bull; {{ $r->tanggal ? $r->tanggal->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-amber-400 block">{{ number_format($r->qty, 1) }} Kg</span>
                                <span class="text-[10px] text-slate-300">Rp {{ number_format($r->tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic py-2">Belum ada data terima bahan.</p>
                    @endforelse
                </div>
            </div>

            {{-- Penjualan Terbaru --}}
            <div class="p-4 rounded-2xl bg-slate-950/50 border border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <span class="text-xs font-bold text-emerald-400 uppercase tracking-wider"><i class="fas fa-hand-holding-dollar mr-1.5"></i>Penjualan CUGIL Terbaru</span>
                    <a href="{{ route('cugil.sales.index') }}" class="text-[11px] text-slate-400 hover:text-white">Semua &rarr;</a>
                </div>
                <div class="space-y-2">
                    @forelse($cugilRecentSales as $s)
                        <div class="p-2.5 rounded-xl bg-slate-900/60 border border-slate-800/80 flex items-center justify-between text-xs">
                            <div>
                                <span class="font-semibold text-white block">{{ $s->nama_barang }}</span>
                                <span class="text-[10px] text-slate-400">{{ $s->nama_buyer }} &bull; {{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}</span>
                            </div>
                            <div class="text-right">
                                <span class="font-bold text-emerald-400 block">{{ number_format($s->qty_terjual > 0 ? $s->qty_terjual : $s->qty_gudang, 1) }} Kg</span>
                                <span class="text-[10px] text-white font-medium">Rp {{ number_format($s->tagihan, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    @empty
                        <p class="text-xs text-slate-500 italic py-2">Belum ada data penjualan.</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
