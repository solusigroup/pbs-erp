@extends('layouts.admin')

@section('title', 'Jurnal Kas & Bank (BKM / BKK / Transfer) - Akuntansi')

@section('content')
<div class="space-y-6">
    <!-- Header Section -->
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 text-amber-400 shadow-lg shadow-orange-500/10">
                    <i class="fas fa-money-bill-transfer text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                        Jurnal Kas &amp; Bank
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 font-semibold font-mono">
                            SimpleAkunting 3-6
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pencatatan Bukti Kas Masuk (BKM), Bukti Kas Keluar (BKK) &amp; Transfer Antar Kas/Bank</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            @if(auth()->user()->canMutate())
            <button type="button" onclick="openModalImport()" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg shadow-purple-600/20 transition flex items-center gap-2">
                <i class="fas fa-file-excel"></i>
                <span>Impor Excel / CSV</span>
            </button>
            <button type="button" onclick="openModalKasMasuk()" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fas fa-arrow-down-left"></i>
                <span>+ Kas Masuk (BKM)</span>
            </button>
            <button type="button" onclick="openModalKasKeluar()" class="px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white text-xs font-bold shadow-lg shadow-rose-600/20 transition flex items-center gap-2">
                <i class="fas fa-arrow-up-right"></i>
                <span>+ Kas Keluar (BKK)</span>
            </button>
            <button type="button" onclick="openModalTransfer()" class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-lg shadow-sky-600/20 transition flex items-center gap-2">
                <i class="fas fa-repeat"></i>
                <span>Transfer Kas-Bank</span>
            </button>
            @else
            <span class="px-3.5 py-2 rounded-xl bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
                <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
            </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="p-4 rounded-2xl bg-emerald-950/60 border border-emerald-500/30 text-emerald-300 text-xs flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-circle-check text-emerald-400 text-base"></i>
                <span>{{ session('success') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-emerald-400 hover:text-white">&times;</button>
        </div>
    @endif

    @if(session('import_warnings'))
        <div class="p-4 rounded-2xl bg-amber-950/60 border border-amber-500/30 text-amber-300 text-xs space-y-1 shadow-lg">
            <div class="flex items-center gap-2 font-bold text-amber-400">
                <i class="fas fa-triangle-exclamation"></i>
                <span>Catatan Baris yang Dilewati Saat Impor:</span>
            </div>
            <ul class="list-disc list-inside space-y-0.5 text-[11px] text-amber-200/90 pl-1">
                @foreach(session('import_warnings') as $warn)
                    <li>{{ $warn }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/30 text-rose-300 text-xs space-y-2 shadow-lg">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-2.5">
                    <i class="fas fa-triangle-exclamation text-rose-400 text-base"></i>
                    <span>{{ session('error') }}</span>
                </div>
                <button type="button" onclick="this.parentElement.parentElement.remove()" class="text-rose-400 hover:text-white">&times;</button>
            </div>
            @if(session('import_errors'))
                <ul class="list-disc list-inside space-y-0.5 text-[11px] text-rose-200/90 pl-1 pt-1 border-t border-rose-900/60">
                    @foreach(session('import_errors') as $err)
                        <li>{{ $err }}</li>
                    @endforeach
                </ul>
            @endif
        </div>
    @endif

    <!-- 4 KPI Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400 font-medium">Total Kas Masuk (BKM)</span>
                <span class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-xs">
                    <i class="fas fa-circle-arrow-down"></i>
                </span>
            </div>
            <strong class="text-lg font-black text-emerald-400 block mt-2 font-mono">
                Rp {{ number_format($statsKasMasuk, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Periode filter aktif</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400 font-medium">Total Kas Keluar (BKK)</span>
                <span class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-xs">
                    <i class="fas fa-circle-arrow-up"></i>
                </span>
            </div>
            <strong class="text-lg font-black text-rose-400 block mt-2 font-mono">
                Rp {{ number_format($statsKasKeluar, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Periode filter aktif</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400 font-medium">Mutasi Transfer Kas</span>
                <span class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-xs">
                    <i class="fas fa-arrow-right-arrow-left"></i>
                </span>
            </div>
            <strong class="text-lg font-black text-sky-400 block mt-2 font-mono">
                Rp {{ number_format($statsTransfer, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Pemindahan dana kas-bank</span>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <span class="text-xs text-slate-400 font-medium">Arus Kas Bersih (Net)</span>
                <span class="w-8 h-8 rounded-xl {{ $netCashFlow >= 0 ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }} flex items-center justify-center text-xs">
                    <i class="fas fa-scale-balanced"></i>
                </span>
            </div>
            <strong class="text-lg font-black {{ $netCashFlow >= 0 ? 'text-emerald-400' : 'text-rose-400' }} block mt-2 font-mono">
                Rp {{ number_format($netCashFlow, 0, ',', '.') }}
            </strong>
            <span class="text-[10px] text-slate-500 mt-1 block">Masuk dikurangi Keluar</span>
        </div>
    </div>

    <!-- Filter & Search Toolbar -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
        <form method="GET" action="{{ route('akuntansi.jurnal-kas') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tipe Transaksi</label>
                <select name="tipe" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                    <option value="">-- Semua Tipe Transaksi --</option>
                    <option value="Kas Masuk" {{ $tipe === 'Kas Masuk' ? 'selected' : '' }}>Kas Masuk (BKM)</option>
                    <option value="Kas Keluar" {{ $tipe === 'Kas Keluar' ? 'selected' : '' }}>Kas Keluar (BKK)</option>
                    <option value="Transfer" {{ $tipe === 'Transfer' ? 'selected' : '' }}>Mutasi Transfer Kas</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Rekening Kas / Bank</label>
                <select name="kode_akun_kas" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                    <option value="">-- Semua Kas &amp; Bank --</option>
                    @foreach($cashAccounts as $ca)
                        <option value="{{ $ca->kode_akun }}" {{ $kodeKas === $ca->kode_akun ? 'selected' : '' }}>
                            {{ $ca->kode_akun }} - {{ $ca->nama_akun }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div class="flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] text-slate-400 font-medium mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ $search }}" placeholder="No Bukti / Uraian..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition flex items-center gap-1.5 h-[38px]">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Jurnal Kas Transaction List Table -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-list-check text-amber-400"></i>
                <span>Daftar Transaksi Kas &amp; Bank</span>
            </h3>
            <span class="text-xs text-slate-400">Total: <strong class="text-white">{{ $jurnals->total() }}</strong> entri</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No Transaksi</th>
                        <th class="py-3 px-4">Tanggal</th>
                        <th class="py-3 px-4">Tipe &amp; Referensi</th>
                        <th class="py-3 px-4">Uraian / Deskripsi</th>
                        <th class="py-3 px-4">Akun Kas/Bank</th>
                        <th class="py-3 px-4 text-right">Nominal (Rp)</th>
                        <th class="py-3 px-4 text-center">Petugas</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($jurnals as $j)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 font-bold text-white font-mono">
                                {{ $j->no_transaksi }}
                            </td>
                            <td class="py-3 px-4 text-slate-300 whitespace-nowrap">
                                {{ $j->tanggal->format('d/m/Y') }}
                            </td>
                            <td class="py-3 px-4">
                                @if($j->tipe_jurnal === 'Kas Masuk')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <i class="fas fa-arrow-down-left mr-1"></i>BKM (Kas Masuk)
                                    </span>
                                @elseif($j->tipe_jurnal === 'Kas Keluar')
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        <i class="fas fa-arrow-up-right mr-1"></i>BKK (Kas Keluar)
                                    </span>
                                @else
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-sky-500/10 text-sky-400 border border-sky-500/20">
                                        <i class="fas fa-repeat mr-1"></i>Transfer Kas
                                    </span>
                                @endif
                                @if($j->sumber_referensi)
                                    <div class="text-[10px] text-slate-400 mt-0.5 truncate max-w-[180px]" title="{{ $j->sumber_referensi }}">
                                        {{ $j->sumber_referensi }}
                                    </div>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-slate-200 font-medium">
                                {{ $j->deskripsi }}
                                <div class="text-[10px] text-slate-500 mt-0.5">
                                    {{ $j->details->count() }} baris jurnal pembukuan
                                </div>
                            </td>
                            <td class="py-3 px-4">
                                @php
                                    $cashLines = $j->details->filter(fn($d) => in_array($d->kode_akun, $cashAccounts->pluck('kode_akun')->toArray()));
                                @endphp
                                @foreach($cashLines as $cl)
                                    <div class="font-mono font-semibold text-amber-400 text-[11px]">
                                        {{ $cl->kode_akun }} - {{ $cl->akun->nama_akun ?? '' }}
                                    </div>
                                @endforeach
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold whitespace-nowrap {{ $j->tipe_jurnal === 'Kas Masuk' ? 'text-emerald-400' : ($j->tipe_jurnal === 'Kas Keluar' ? 'text-rose-400' : 'text-sky-400') }}">
                                Rp {{ number_format($j->total_debit, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-center text-slate-400 text-[11px]">
                                {{ $j->created_by ?? 'System' }}
                            </td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    <a href="{{ route('akuntansi.voucher', $j->id_jurnal) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-semibold border border-slate-700 transition inline-flex items-center gap-1.5" title="Cetak Bukti / Voucher Resmi">
                                        <i class="fas fa-print text-amber-400"></i>
                                        <span>Bukti</span>
                                    </a>
                                    @if(auth()->user()->canMutate())
                                    <form action="{{ route('akuntansi.destroyJurnal', $j->id_jurnal) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Hapus transaksi kas {{ $j->no_transaksi }}?\n\nSaldo kas/bank dan mutasi buku besar COA terkait akan OTOMATIS DI-ROLLBACK!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white text-[11px] font-semibold border border-rose-500/30 transition flex items-center gap-1" title="Hapus Transaksi Kas & Rollback Saldo">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-12 text-center text-slate-500">
                                <i class="fas fa-receipt text-3xl mb-2 block"></i>
                                Belum ada transaksi kas &amp; bank dalam periode yang dipilih.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($jurnals->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $jurnals->links() }}
            </div>
        @endif
    </div>
</div>

<!-- =================================================================================== -->
<!-- MODAL 1: INPUT KAS MASUK (BKM - BUKTI KAS MASUK) -->
<!-- =================================================================================== -->
<div id="modalKasMasuk" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="rounded-3xl border border-emerald-500/30 bg-slate-900 w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-emerald-500/10 text-emerald-400 flex items-center justify-center text-sm font-bold border border-emerald-500/20">
                    <i class="fas fa-arrow-down-left"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Input Bukti Kas Masuk (BKM)</h3>
                    <p class="text-[11px] text-slate-400">Penerimaan dana kas / transfer bank dari pelanggan atau sumber lainnya</p>
                </div>
            </div>
            <button type="button" onclick="closeModalKasMasuk()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('akuntansi.storeKasMasuk') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">No Bukti BKM</label>
                    <input type="text" name="no_transaksi" value="BKM-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Tanggal Masuk</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Setor ke Rekening (Debit)</label>
                    <select name="kode_akun_kas" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                        @foreach($cashAccounts as $ca)
                            <option value="{{ $ca->kode_akun }}">{{ $ca->kode_akun }} - {{ $ca->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Diterima Dari</label>
                    <input type="text" name="diterima_dari" required placeholder="Nama Klien / Pihak Pembayar / Bank" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Keterangan / Uraian Utama</label>
                    <input type="text" name="deskripsi" required placeholder="Contoh: Penerimaan Pembayaran Termin Proyek / Penjualan" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
            </div>

            <!-- Detail Akun Lawan (Credit lines) -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-amber-400 font-bold uppercase tracking-wider text-[11px]">
                        Alokasi Akun Lawan / Sumber Penerimaan (Kredit)
                    </label>
                    <button type="button" onclick="addRowKasMasuk()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 text-[11px] font-semibold border border-slate-700 flex items-center gap-1">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                <div id="containerKasMasukRows" class="space-y-2">
                    <!-- Row 1 -->
                    <div class="grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800 row-kas-masuk">
                        <div class="col-span-12 sm:col-span-5">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Akun Lawan (Pendapatan / Piutang / Modal)</label>
                            <select name="items[0][kode_akun_lawan]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                                <option value="">-- Pilih Akun Lawan --</option>
                                @foreach($counterpartAccounts as $cp)
                                    <option value="{{ $cp->kode_akun }}">{{ $cp->kode_akun }} - {{ $cp->nama_akun }} ({{ $cp->kategori }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 sm:col-span-4">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Keterangan Item</label>
                            <input type="text" name="items[0][keterangan]" placeholder="Uraian item penerimaan..." class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                        </div>
                        <div class="col-span-10 sm:col-span-2">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Nominal (Rp)</label>
                            <input type="number" step="0.01" name="items[0][nominal]" required oninput="calcTotalKasMasuk()" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono input-nominal-masuk">
                        </div>
                        <div class="col-span-2 sm:col-span-1 flex items-end justify-center pb-1">
                            <button type="button" onclick="removeRow(this, 'calcTotalKasMasuk')" class="text-slate-500 hover:text-rose-400 text-sm">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 p-3 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center justify-between font-bold">
                    <span class="text-slate-300">Total Kas Masuk (Debit):</span>
                    <span id="totalDisplayKasMasuk" class="text-emerald-400 text-base font-mono">Rp 0</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeModalKasMasuk()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold shadow-lg shadow-emerald-600/20">Simpan Bukti Kas Masuk</button>
            </div>
        </form>
    </div>
</div>

<!-- =================================================================================== -->
<!-- MODAL 2: INPUT KAS KELUAR (BKK - BUKTI KAS KELUAR) -->
<!-- =================================================================================== -->
<div id="modalKasKeluar" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="rounded-3xl border border-rose-500/30 bg-slate-900 w-full max-w-3xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-rose-500/10 text-rose-400 flex items-center justify-center text-sm font-bold border border-rose-500/20">
                    <i class="fas fa-arrow-up-right"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Input Bukti Kas Keluar (BKK)</h3>
                    <p class="text-[11px] text-slate-400">Pengeluaran dana untuk beban operasional, gaji, hutang supplier, atau pajak</p>
                </div>
            </div>
            <button type="button" onclick="closeModalKasKeluar()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('akuntansi.storeKasKeluar') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">No Bukti BKK</label>
                    <input type="text" name="no_transaksi" value="BKK-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Tanggal Keluar</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Dikeluarkan Dari Rekening (Kredit)</label>
                    <select name="kode_akun_kas" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                        @foreach($cashAccounts as $ca)
                            <option value="{{ $ca->kode_akun }}">{{ $ca->kode_akun }} - {{ $ca->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Dibayarkan Kepada</label>
                    <input type="text" name="dibayar_kepada" required placeholder="Nama Penerima / Vendor / Karyawan / Pihak Ketiga" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Keterangan / Uraian Utama</label>
                    <input type="text" name="deskripsi" required placeholder="Contoh: Pembayaran Gaji Karyawan / Beban Listrik Kantor" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
            </div>

            <!-- Detail Akun Lawan (Debit lines) -->
            <div class="pt-2">
                <div class="flex items-center justify-between mb-2">
                    <label class="block text-amber-400 font-bold uppercase tracking-wider text-[11px]">
                        Alokasi Akun Beban / Pos Pengeluaran (Debit)
                    </label>
                    <button type="button" onclick="addRowKasKeluar()" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-amber-400 text-[11px] font-semibold border border-slate-700 flex items-center gap-1">
                        <i class="fas fa-plus"></i> Tambah Baris
                    </button>
                </div>

                <div id="containerKasKeluarRows" class="space-y-2">
                    <!-- Row 1 -->
                    <div class="grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800 row-kas-keluar">
                        <div class="col-span-12 sm:col-span-5">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Pos Beban / Akun Lawan</label>
                            <select name="items[0][kode_akun_lawan]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                                <option value="">-- Pilih Akun Lawan --</option>
                                @foreach($counterpartAccounts as $cp)
                                    <option value="{{ $cp->kode_akun }}">{{ $cp->kode_akun }} - {{ $cp->nama_akun }} ({{ $cp->kategori }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-12 sm:col-span-4">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Keterangan Item</label>
                            <input type="text" name="items[0][keterangan]" placeholder="Rincian pos pengeluaran..." class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                        </div>
                        <div class="col-span-10 sm:col-span-2">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Nominal (Rp)</label>
                            <input type="number" step="0.01" name="items[0][nominal]" required oninput="calcTotalKasKeluar()" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono input-nominal-keluar">
                        </div>
                        <div class="col-span-2 sm:col-span-1 flex items-end justify-center pb-1">
                            <button type="button" onclick="removeRow(this, 'calcTotalKasKeluar')" class="text-slate-500 hover:text-rose-400 text-sm">&times;</button>
                        </div>
                    </div>
                </div>

                <div class="mt-3 p-3 rounded-xl bg-slate-950/80 border border-slate-800 flex items-center justify-between font-bold">
                    <span class="text-slate-300">Total Kas Keluar (Kredit):</span>
                    <span id="totalDisplayKasKeluar" class="text-rose-400 text-base font-mono">Rp 0</span>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeModalKasKeluar()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold shadow-lg shadow-rose-600/20">Simpan Bukti Kas Keluar</button>
            </div>
        </form>
    </div>
</div>

<!-- =================================================================================== -->
<!-- MODAL 3: TRANSFER ANTAR KAS & BANK -->
<!-- =================================================================================== -->
<div id="modalTransfer" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="rounded-3xl border border-sky-500/30 bg-slate-900 w-full max-w-xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl space-y-4 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-sm font-bold border border-sky-500/20">
                    <i class="fas fa-repeat"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Mutasi / Transfer Kas-Bank</h3>
                    <p class="text-[11px] text-slate-400">Pemindahan saldo antar rekening internal PT Pinastika Bhakti Semesta</p>
                </div>
            </div>
            <button type="button" onclick="closeModalTransfer()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('akuntansi.storeTransferKas') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">No Bukti Transfer</label>
                    <input type="text" name="no_transaksi" value="TRF-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Tanggal Mutasi</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Dari Kas / Bank (Sumber)</label>
                    <select name="kas_asal" id="selectKasAsal" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                        @foreach($cashAccounts as $ca)
                            <option value="{{ $ca->kode_akun }}">{{ $ca->kode_akun }} - {{ $ca->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Ke Kas / Bank (Tujuan)</label>
                    <select name="kas_tujuan" id="selectKasTujuan" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                        @foreach($cashAccounts as $ca)
                            <option value="{{ $ca->kode_akun }}" {{ $loop->iteration == 2 ? 'selected' : '' }}>{{ $ca->kode_akun }} - {{ $ca->nama_akun }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Nominal Transfer (Rp)</label>
                    <input type="number" step="0.01" name="nominal" required placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-medium mb-1">Biaya Admin Bank (Opsional)</label>
                    <input type="number" step="0.01" name="biaya_admin" value="0" placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-medium mb-1">Keterangan / Keperluan Transfer</label>
                <input type="text" name="deskripsi" required placeholder="Contoh: Pengisian Kas Operasional dari Rek Giro Mandiri" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeModalTransfer()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold shadow-lg shadow-sky-600/20">Proses Transfer Kas</button>
            </div>
        </form>
    </div>
</div>

<!-- =================================================================================== -->
<!-- MODAL 4: IMPOR TRANSAKSI KAS DARI EXCEL / CSV -->
<!-- =================================================================================== -->
<div id="modalImportExcel" class="fixed inset-0 z-50 bg-black/80 backdrop-blur-sm flex items-center justify-center p-4 hidden overflow-y-auto">
    <div class="rounded-3xl border border-purple-500/30 bg-slate-900 w-full max-w-2xl max-h-[90vh] overflow-y-auto p-6 shadow-2xl space-y-5 text-xs">
        <div class="flex items-center justify-between pb-3 border-b border-slate-800">
            <div class="flex items-center gap-2.5">
                <div class="w-8 h-8 rounded-xl bg-purple-500/10 text-purple-400 flex items-center justify-center text-sm font-bold border border-purple-500/20">
                    <i class="fas fa-file-excel"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white">Impor Transaksi Kas &amp; Bank</h3>
                    <p class="text-[11px] text-slate-400">Unggah file Excel (.xlsx / .xls) atau CSV untuk pembukuan massal otomatis</p>
                </div>
            </div>
            <button type="button" onclick="closeModalImport()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <!-- Download Template Banner -->
        <div class="p-4 rounded-2xl bg-gradient-to-r from-purple-500/10 to-indigo-500/10 border border-purple-500/30 flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
            <div>
                <strong class="text-white text-xs block flex items-center gap-1.5">
                    <i class="fas fa-download text-purple-400"></i> Unduh Format Template
                </strong>
                <p class="text-[11px] text-slate-400 mt-0.5">Gunakan format kolom standar agar data terimpor dengan valid.</p>
            </div>
            <a href="{{ route('akuntansi.downloadTemplateKas') }}" class="px-3.5 py-1.5 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold transition inline-flex items-center justify-center gap-2 shadow-md shadow-purple-600/20 whitespace-nowrap self-start sm:self-auto">
                <i class="fas fa-file-csv"></i>
                <span>Download Template (.csv)</span>
            </a>
        </div>

        <form method="POST" action="{{ route('akuntansi.importExcelKas') }}" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div>
                <label class="block text-slate-300 font-semibold mb-1">Pilih File Spreadsheet (.xlsx, .xls, atau .csv)</label>
                <div class="border-2 border-dashed border-slate-700 hover:border-purple-500/50 rounded-2xl p-6 text-center transition bg-slate-950/40 cursor-pointer" onclick="document.getElementById('file_transaksi_input').click()">
                    <i class="fas fa-cloud-arrow-up text-3xl text-purple-400 mb-2 block"></i>
                    <span id="file_selected_name" class="text-slate-300 font-medium block text-xs">Klik di sini untuk memilih file dari komputer</span>
                    <span class="text-[10px] text-slate-500 mt-1 block">Mendukung Microsoft Excel (.xlsx, .xls) dan CSV (.csv) hingga 10 MB</span>
                    <input type="file" id="file_transaksi_input" name="file_transaksi" accept=".xlsx,.xls,.csv,.txt" required class="hidden" onchange="updateFileName(this)">
                </div>
            </div>

            <!-- Petunjuk Struktur Kolom -->
            <div class="rounded-2xl border border-slate-800 bg-slate-950/60 p-4 space-y-2 text-[11px]">
                <strong class="text-amber-400 block uppercase tracking-wider text-[10px] font-bold">Urutan Kolom Excel / CSV:</strong>
                <ol class="list-decimal list-inside space-y-1 text-slate-400 pl-1">
                    <li><strong class="text-slate-200">No Transaksi</strong>: Nomor bukti (kosongkan untuk digenerate otomatis).</li>
                    <li><strong class="text-slate-200">Tanggal</strong>: Format <code class="text-amber-300">YYYY-MM-DD</code> atau <code class="text-amber-300">DD/MM/YYYY</code>.</li>
                    <li><strong class="text-slate-200">Tipe Transaksi</strong>: <code class="text-emerald-300">Kas Masuk</code> (BKM), <code class="text-rose-300">Kas Keluar</code> (BKK), atau <code class="text-sky-300">Transfer</code>.</li>
                    <li><strong class="text-slate-200">Kode Akun Kas</strong>: Contoh <code class="text-slate-200">1-1100</code> (Kas PBS) atau <code class="text-slate-200">1-1200</code> (Mandiri).</li>
                    <li><strong class="text-slate-200">Kode Akun Lawan</strong>: Contoh <code class="text-slate-200">4-1100</code> (Pendapatan), <code class="text-slate-200">6-1200</code> (Beban Ops), dll.</li>
                    <li><strong class="text-slate-200">Nominal</strong>: Jumlah uang rupiah (angka positif).</li>
                    <li><strong class="text-slate-200">Pihak Terkait</strong>: Nama pembayar / penerima uang.</li>
                    <li><strong class="text-slate-200">Keterangan</strong>: Uraian transaksi.</li>
                    <li><strong class="text-slate-200">Referensi</strong>: Nomor invoice / faktur pendukung (opsional).</li>
                </ol>
            </div>

            <div class="flex items-center justify-end gap-3 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeModalImport()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white font-bold shadow-lg shadow-purple-600/20 flex items-center gap-1.5">
                    <i class="fas fa-file-import"></i>
                    <span>Mulai Impor Data</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let kasMasukIndex = 1;
let kasKeluarIndex = 1;

const counterpartOptions = `@foreach($counterpartAccounts as $cp)<option value="{{ $cp->kode_akun }}">{{ $cp->kode_akun }} - {{ $cp->nama_akun }} ({{ $cp->kategori }})</option>@endforeach`;

function openModalImport() {
    document.getElementById('modalImportExcel').classList.remove('hidden');
}
function closeModalImport() {
    document.getElementById('modalImportExcel').classList.add('hidden');
}

function updateFileName(input) {
    if (input.files && input.files[0]) {
        document.getElementById('file_selected_name').innerHTML = '<span class="text-purple-400 font-bold">' + input.files[0].name + '</span> (' + (input.files[0].size / 1024).toFixed(1) + ' KB)';
    }
}

function openModalKasMasuk() {
    document.getElementById('modalKasMasuk').classList.remove('hidden');
}
function closeModalKasMasuk() {
    document.getElementById('modalKasMasuk').classList.add('hidden');
}

function openModalKasKeluar() {
    document.getElementById('modalKasKeluar').classList.remove('hidden');
}
function closeModalKasKeluar() {
    document.getElementById('modalKasKeluar').classList.add('hidden');
}

function openModalTransfer() {
    document.getElementById('modalTransfer').classList.remove('hidden');
}
function closeModalTransfer() {
    document.getElementById('modalTransfer').classList.add('hidden');
}

function addRowKasMasuk() {
    const container = document.getElementById('containerKasMasukRows');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800 row-kas-masuk';
    div.innerHTML = `
        <div class="col-span-12 sm:col-span-5">
            <label class="block text-[10px] text-slate-400 mb-0.5">Akun Lawan (Pendapatan / Piutang / Modal)</label>
            <select name="items[${kasMasukIndex}][kode_akun_lawan]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                <option value="">-- Pilih Akun Lawan --</option>
                ${counterpartOptions}
            </select>
        </div>
        <div class="col-span-12 sm:col-span-4">
            <label class="block text-[10px] text-slate-400 mb-0.5">Keterangan Item</label>
            <input type="text" name="items[${kasMasukIndex}][keterangan]" placeholder="Uraian item penerimaan..." class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
        </div>
        <div class="col-span-10 sm:col-span-2">
            <label class="block text-[10px] text-slate-400 mb-0.5">Nominal (Rp)</label>
            <input type="number" step="0.01" name="items[${kasMasukIndex}][nominal]" required oninput="calcTotalKasMasuk()" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono input-nominal-masuk">
        </div>
        <div class="col-span-2 sm:col-span-1 flex items-end justify-center pb-1">
            <button type="button" onclick="removeRow(this, 'calcTotalKasMasuk')" class="text-slate-500 hover:text-rose-400 text-sm">&times;</button>
        </div>
    `;
    container.appendChild(div);
    kasMasukIndex++;
}

function addRowKasKeluar() {
    const container = document.getElementById('containerKasKeluarRows');
    const div = document.createElement('div');
    div.className = 'grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800 row-kas-keluar';
    div.innerHTML = `
        <div class="col-span-12 sm:col-span-5">
            <label class="block text-[10px] text-slate-400 mb-0.5">Pos Beban / Akun Lawan</label>
            <select name="items[${kasKeluarIndex}][kode_akun_lawan]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                <option value="">-- Pilih Akun Lawan --</option>
                ${counterpartOptions}
            </select>
        </div>
        <div class="col-span-12 sm:col-span-4">
            <label class="block text-[10px] text-slate-400 mb-0.5">Keterangan Item</label>
            <input type="text" name="items[${kasKeluarIndex}][keterangan]" placeholder="Rincian pos pengeluaran..." class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
        </div>
        <div class="col-span-10 sm:col-span-2">
            <label class="block text-[10px] text-slate-400 mb-0.5">Nominal (Rp)</label>
            <input type="number" step="0.01" name="items[${kasKeluarIndex}][nominal]" required oninput="calcTotalKasKeluar()" class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono input-nominal-keluar">
        </div>
        <div class="col-span-2 sm:col-span-1 flex items-end justify-center pb-1">
            <button type="button" onclick="removeRow(this, 'calcTotalKasKeluar')" class="text-slate-500 hover:text-rose-400 text-sm">&times;</button>
        </div>
    `;
    container.appendChild(div);
    kasKeluarIndex++;
}

function removeRow(btn, callbackName) {
    const row = btn.closest('.row-kas-masuk, .row-kas-keluar');
    const container = row.parentElement;
    if (container.children.length > 1) {
        row.remove();
        if (callbackName === 'calcTotalKasMasuk') calcTotalKasMasuk();
        if (callbackName === 'calcTotalKasKeluar') calcTotalKasKeluar();
    } else {
        alert('Minimal harus ada 1 baris transaksi.');
    }
}

function calcTotalKasMasuk() {
    let total = 0;
    document.querySelectorAll('.input-nominal-masuk').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalDisplayKasMasuk').innerText = 'Rp ' + total.toLocaleString('id-ID');
}

function calcTotalKasKeluar() {
    let total = 0;
    document.querySelectorAll('.input-nominal-keluar').forEach(input => {
        total += parseFloat(input.value) || 0;
    });
    document.getElementById('totalDisplayKasKeluar').innerText = 'Rp ' + total.toLocaleString('id-ID');
}
</script>
@endsection
