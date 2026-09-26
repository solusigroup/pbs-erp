@extends('layouts.admin')

@section('title', 'Buku Jurnal Umum Memorial - Akuntansi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4">
        <div>
            <div class="flex items-center gap-2.5">
                <div class="p-2.5 rounded-2xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 text-amber-400 shadow-lg shadow-orange-500/10">
                    <i class="fas fa-receipt text-xl"></i>
                </div>
                <div>
                    <h2 class="text-xl font-black text-white tracking-tight flex items-center gap-2">
                        Buku Jurnal Umum Memorial
                        <span class="text-xs px-2.5 py-0.5 rounded-full bg-amber-500/10 text-amber-400 border border-amber-500/30 font-semibold font-mono">
                            Double Entry SAK
                        </span>
                    </h2>
                    <p class="text-xs text-slate-400 mt-0.5">Pencatatan jurnal umum manual, memorial, penyesuaian &amp; koreksi pembukuan</p>
                </div>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('akuntansi.jurnal-kas') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-money-bill-transfer text-amber-400"></i>
                <span>Jurnal Kas &amp; Bank</span>
            </a>
            @if(auth()->user()->canMutate())
            <form action="{{ route('akuntansi.jurnal.adjustHppCugil') }}" method="POST" class="inline" onsubmit="return confirm('Jalankan Jurnal Penyesuaian HPP CUGIL per tanggal {{ \Carbon\Carbon::parse($tanggalSampai ?? date('Y-m-d'))->format('d/m/Y') }}?\n\nSaldo akumulasi Persediaan Bahan Baku (1-1610) per tanggal tersebut akan dialokasikan ke HPP (5-1100).')">
                @csrf
                <input type="hidden" name="tanggal" value="{{ $tanggalSampai ?? date('Y-m-d') }}">
                <button type="submit" class="px-3.5 py-2 rounded-xl bg-cyan-600 hover:bg-cyan-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-lg shadow-cyan-500/20" title="Penyesuaian HPP CUGIL per Periode Tanggal Filter (Zeroing Persediaan Bahan Baku)">
                    <i class="fas fa-wand-magic-sparkles text-amber-300"></i>
                    <span>⚡ Penyesuaian HPP CUGIL</span>
                </button>
            </form>
            <button 
                type="button" 
                onclick="document.getElementById('modalTambahJurnal').classList.toggle('hidden')"
                class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition flex items-center gap-2"
            >
                <i class="fas fa-plus"></i>
                <span>+ Input Jurnal Memorial</span>
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

    @if(session('error'))
        <div class="p-4 rounded-2xl bg-rose-950/60 border border-rose-500/30 text-rose-300 text-xs flex items-center justify-between shadow-lg">
            <div class="flex items-center gap-2.5">
                <i class="fas fa-triangle-exclamation text-rose-400 text-base"></i>
                <span>{{ session('error') }}</span>
            </div>
            <button type="button" onclick="this.parentElement.remove()" class="text-rose-400 hover:text-white">&times;</button>
        </div>
    @endif

    <!-- Filter & Search Toolbar -->
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg space-y-3">
        <form method="GET" action="{{ route('akuntansi.jurnal') }}" id="filterJurnalForm" class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" id="filterTglDari" value="{{ $tanggalDari ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" id="filterTglSampai" value="{{ $tanggalSampai ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white focus:ring-1 focus:ring-amber-500">
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tipe Jurnal</label>
                <select name="tipe" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Tipe</option>
                    <option value="Umum" {{ ($tipe ?? '') == 'Umum' ? 'selected' : '' }}>Umum / Memorial</option>
                    <option value="Penyesuaian" {{ ($tipe ?? '') == 'Penyesuaian' ? 'selected' : '' }}>Penyesuaian</option>
                    <option value="Kas Masuk" {{ ($tipe ?? '') == 'Kas Masuk' ? 'selected' : '' }}>Kas Masuk</option>
                    <option value="Kas Keluar" {{ ($tipe ?? '') == 'Kas Keluar' ? 'selected' : '' }}>Kas Keluar</option>
                    <option value="Transfer" {{ ($tipe ?? '') == 'Transfer' ? 'selected' : '' }}>Transfer Kas-Bank</option>
                    <option value="Pajak" {{ ($tipe ?? '') == 'Pajak' ? 'selected' : '' }}>Pajak</option>
                    <option value="Penutup" {{ ($tipe ?? '') == 'Penutup' ? 'selected' : '' }}>Penutup</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Status Posting</label>
                <select name="status" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white focus:ring-1 focus:ring-amber-500">
                    <option value="">Semua Status</option>
                    <option value="draft" {{ ($status ?? '') == 'draft' ? 'selected' : '' }}>⏳ Hanya DRAFT</option>
                    <option value="posted" {{ ($status ?? '') == 'posted' ? 'selected' : '' }}>✅ POSTED</option>
                </select>
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Record per Halaman</label>
                <select name="per_page" onchange="submitFilterWithLoading()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-semibold focus:ring-1 focus:ring-amber-500">
                    <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 Record</option>
                    <option value="30" {{ ($perPage ?? 15) == 30 ? 'selected' : '' }}>30 Record</option>
                    <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50 Record</option>
                    <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100 Record</option>
                    <option value="500" {{ ($perPage ?? 15) == 500 ? 'selected' : '' }}>500 Record</option>
                </select>
            </div>

            <div class="flex items-end gap-2">
                <button type="submit" id="btnFilterJurnal" class="flex-1 px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition flex items-center justify-center gap-1.5 h-[38px] shadow-lg shadow-amber-500/20">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
                @if(($tanggalDari ?? '') || ($tanggalSampai ?? '') || ($search ?? '') || ($tipe ?? '') || ($status ?? ''))
                    <a href="{{ route('akuntansi.jurnal') }}" class="px-3 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white font-semibold transition flex items-center justify-center h-[38px] border border-slate-700" title="Reset Semua Filter">
                        <i class="fas fa-rotate-left"></i>
                    </a>
                @endif
            </div>

            <div class="col-span-1 sm:col-span-2 md:col-span-3 lg:col-span-6 flex flex-col sm:flex-row sm:items-center justify-between gap-2 pt-2 border-t border-slate-800/60">
                <!-- Search input -->
                <div class="relative flex-1 max-w-md">
                    <i class="fas fa-magnifying-glass absolute left-3 top-1/2 -translate-y-1/2 text-slate-500 text-xs"></i>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari No Transaksi, Deskripsi, No Ref..." class="w-full pl-8 pr-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs placeholder:text-slate-500 focus:ring-1 focus:ring-amber-500">
                </div>

                <!-- Quick Date Presets -->
                <div class="flex items-center gap-1.5 text-[11px] text-slate-400 flex-wrap">
                    <span class="text-[10px] text-slate-500 font-medium uppercase mr-1">Preset Cepat:</span>
                    <button type="button" onclick="setQuickDate('today')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60">Hari Ini</button>
                    <button type="button" onclick="setQuickDate('this_month')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60">Bulan Ini</button>
                    <button type="button" onclick="setQuickDate('last_month')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60">Bulan Lalu</button>
                    <button type="button" onclick="setQuickDate('this_year')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60">Tahun Ini</button>
                    <button type="button" onclick="setQuickDate('all')" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white transition border border-slate-700/60">Semua</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Results Status Bar -->
    <div class="flex flex-wrap items-center justify-between text-xs px-2 text-slate-400 gap-2">
        <div class="flex items-center gap-2">
            <span class="inline-flex items-center gap-1.5 font-medium text-slate-300">
                <i class="fas fa-layer-group text-amber-400"></i>
                Total <strong>{{ number_format($jurnals->total(), 0, ',', '.') }}</strong> Jurnal
            </span>
            @if($jurnals->total() > 0)
                <span class="text-slate-500">&bull;</span>
                <span class="text-slate-400">Menampilkan record ke-{{ $jurnals->firstItem() }} s/d {{ $jurnals->lastItem() }}</span>
            @endif
        </div>
        @if(($tanggalDari ?? '') || ($tanggalSampai ?? '') || ($search ?? '') || ($tipe ?? '') || ($status ?? ''))
            <div class="flex items-center gap-1.5 flex-wrap">
                <span class="text-[10px] text-amber-400 font-bold uppercase tracking-wider">Filter Aktif:</span>
                @if(($tanggalDari ?? '') && ($tanggalSampai ?? ''))
                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 text-[11px]">{{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }} - {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</span>
                @elseif($tanggalDari ?? '')
                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 text-[11px]">Dari {{ \Carbon\Carbon::parse($tanggalDari)->format('d/m/Y') }}</span>
                @elseif($tanggalSampai ?? '')
                    <span class="px-2 py-0.5 rounded bg-amber-500/10 text-amber-300 border border-amber-500/20 text-[11px]">Sampai {{ \Carbon\Carbon::parse($tanggalSampai)->format('d/m/Y') }}</span>
                @endif
                @if($tipe ?? '')
                    <span class="px-2 py-0.5 rounded bg-cyan-500/10 text-cyan-300 border border-cyan-500/20 text-[11px]">Tipe: {{ $tipe }}</span>
                @endif
                @if($status ?? '')
                    <span class="px-2 py-0.5 rounded bg-purple-500/10 text-purple-300 border border-purple-500/20 text-[11px]">Status: {{ strtoupper($status) }}</span>
                @endif
                @if($search ?? '')
                    <span class="px-2 py-0.5 rounded bg-slate-800 text-slate-200 border border-slate-700 text-[11px]">"{{ $search }}"</span>
                @endif
                <a href="{{ route('akuntansi.jurnal') }}" class="text-[11px] text-rose-400 hover:text-rose-300 hover:underline ml-1">Reset</a>
            </div>
        @endif
    </div>

    <!-- Modal Form Input Jurnal -->
    <div id="modalTambahJurnal" class="hidden rounded-3xl border border-amber-500/30 bg-slate-900/95 p-6 backdrop-blur-xl shadow-2xl">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-receipt text-amber-400"></i>
                <span>Pencatatan Jurnal Memorial Baru</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahJurnal').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('akuntansi.storeJurnal') }}" onsubmit="return validateTambahJurnal(event)" class="space-y-4 text-xs">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">No Transaksi</label>
                    <input type="text" name="no_transaksi" value="JU-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Tanggal</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Tipe Jurnal</label>
                    <select name="tipe_jurnal" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                        <option value="Umum">Umum / Memorial</option>
                        <option value="Penyesuaian">Penyesuaian (Adjustment)</option>
                        <option value="Pajak">Pajak (Tax Provision)</option>
                        <option value="Penutup">Penutup (Closing)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Referensi / Bukti</label>
                    <input type="text" name="sumber_referensi" placeholder="No Dokumen / Memo" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Deskripsi / Keterangan Transaksi</label>
                <input type="text" name="deskripsi" required placeholder="Contoh: Penyesuaian Beban Penyusutan Kendaraan Bulanan" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <!-- Jurnal Lines (Debit & Credit Pair) -->
            <div class="pt-2 border-t border-slate-800">
                <div class="flex items-center justify-between mb-3">
                    <div>
                        <span class="block text-amber-400 font-bold uppercase tracking-wider text-xs">Baris Akun (Debit &amp; Kredit)</span>
                        <p class="text-[11px] text-slate-400">Minimal 2 baris akun dengan total Debit harus sama dengan Kredit</p>
                    </div>
                    <button type="button" onclick="addTambahRow()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-amber-400 text-xs font-semibold border border-slate-700 hover:border-amber-500/50 transition flex items-center gap-1.5 shadow-sm">
                        <i class="fas fa-plus text-xs"></i>
                        <span>Tambah Baris</span>
                    </button>
                </div>

                <!-- Desktop Column Headers -->
                <div class="hidden sm:grid grid-cols-12 gap-2 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                    <div class="sm:col-span-4">Akun (COA) <span class="text-rose-400">*</span></div>
                    <div class="sm:col-span-3">Keterangan Baris</div>
                    <div class="sm:col-span-2 text-right">Debit (Rp)</div>
                    <div class="sm:col-span-2 text-right">Kredit (Rp)</div>
                    <div class="sm:col-span-1 text-center">Aksi</div>
                </div>
                
                <div id="containerTambahRows" class="space-y-2.5">
                    <!-- Populated dynamically via JS -->
                </div>

                <!-- Balance Summary Footer -->
                <div class="mt-4 p-4 rounded-2xl bg-slate-950/80 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 font-mono">
                    <div class="flex items-center gap-6">
                        <div>
                            <span class="text-[11px] text-slate-400 block font-sans">Total Debit:</span>
                            <span id="tambahTotalDebitDisplay" class="text-sm font-bold text-emerald-400">Rp 0</span>
                        </div>
                        <div class="border-l border-slate-800 pl-6">
                            <span class="text-[11px] text-slate-400 block font-sans">Total Kredit:</span>
                            <span id="tambahTotalKreditDisplay" class="text-sm font-bold text-rose-400">Rp 0</span>
                        </div>
                    </div>

                    <div id="tambahBalanceStatus" class="flex items-center gap-2">
                        <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-xs font-semibold flex items-center gap-1.5">
                            <i class="fas fa-circle-info"></i> Masukkan nominal
                        </span>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahJurnal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-semibold">Batal</button>
                <button type="submit" id="btnSubmitTambah" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold shadow flex items-center gap-2">
                    <i class="fas fa-save"></i>
                    <span>Simpan Jurnal Memorial</span>
                </button>
            </div>
        </form>
    </div>

    <!-- Jurnal List -->
    <div class="space-y-4">
        <!-- Hidden form for bulk approve -->
        <form method="POST" action="{{ route('akuntansi.jurnal.bulkApprove') }}" id="bulkApproveForm" class="hidden">
            @csrf
            <div id="bulkApproveInputs"></div>
        </form>

        <div class="flex flex-wrap items-center justify-between bg-slate-900/60 p-4 rounded-2xl border border-slate-800 shadow-lg">
            <div class="flex items-center gap-3 mb-2 sm:mb-0">
                <input type="checkbox" id="selectAll" onchange="document.querySelectorAll('.jurnal-checkbox').forEach(cb => cb.checked = this.checked)" class="w-4 h-4 rounded border-slate-700 text-amber-500 focus:ring-amber-500/50 bg-slate-950">
                <label for="selectAll" class="text-xs font-semibold text-slate-300 cursor-pointer">Pilih Semua DRAFT</label>
            </div>
            <button type="button" onclick="submitBulkApprove()" class="px-4 py-2 rounded-xl bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-bold transition flex items-center gap-2">
                <i class="fas fa-check-double"></i>
                <span>Bulk Approve Selected</span>
            </button>
        </div>

        <script>
            function submitBulkApprove() {
                const checked = document.querySelectorAll('.jurnal-checkbox:checked');
                if (checked.length === 0) {
                    alert('Pilih setidaknya satu jurnal untuk di-approve.');
                    return;
                }
                if (!confirm('Approve ' + checked.length + ' jurnal yang dipilih?')) return;
                
                const container = document.getElementById('bulkApproveInputs');
                container.innerHTML = '';
                checked.forEach(cb => {
                    const input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'jurnal_ids[]';
                    input.value = cb.value;
                    container.appendChild(input);
                });
                document.getElementById('bulkApproveForm').submit();
            }
        </script>

        @php
            $recordsPayload = [];
            foreach ($jurnals as $jItem) {
                $recordsPayload[$jItem->id_jurnal] = [
                    'id_jurnal' => $jItem->id_jurnal,
                    'no_transaksi' => $jItem->no_transaksi,
                    'tanggal' => $jItem->tanggal ? $jItem->tanggal->format('Y-m-d') : date('Y-m-d'),
                    'tipe_jurnal' => $jItem->tipe_jurnal ?? 'Umum',
                    'sumber_referensi' => $jItem->sumber_referensi ?? '',
                    'deskripsi' => $jItem->deskripsi ?? '',
                    'is_posted' => (bool)$jItem->is_posted,
                    'is_auto' => \App\Services\JurnalAutoService::isAutoGenerated($jItem),
                    'details' => $jItem->details ? $jItem->details->map(function($d) {
                        return [
                            'kode_akun' => $d->kode_akun,
                            'keterangan_baris' => $d->keterangan_baris ?? '',
                            'debit' => (float) $d->debit,
                            'kredit' => (float) $d->kredit,
                        ];
                    })->values()->toArray() : []
                ];
            }
        @endphp

        @forelse($jurnals as $j)
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl p-5 {{ !$j->is_posted ? 'ring-1 ring-amber-500/50 relative' : '' }}">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 mb-3 border-b border-slate-800/80 gap-2">
                    <div class="flex items-center gap-3">
                        @if(!$j->is_posted)
                        <input type="checkbox" name="jurnal_ids[]" value="{{ $j->id_jurnal }}" class="jurnal-checkbox w-4 h-4 rounded border-slate-700 text-amber-500 focus:ring-amber-500/50 bg-slate-950">
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-500 text-slate-900 animate-pulse shadow-lg shadow-amber-500/20">
                            DRAFT
                        </span>
                        @else
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-emerald-500/20 text-emerald-400 border border-emerald-500/30">
                            POSTED
                        </span>
                        @endif

                        @if(\App\Services\JurnalAutoService::isAutoGenerated($j))
                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-cyan-500/20 text-cyan-400 border border-cyan-500/30" title="Dihasilkan otomatis dari transaksi CUGIL">
                            🤖 AUTO
                        </span>
                        @endif

                        <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-500/10 text-amber-400 border border-amber-500/20">
                            {{ $j->tipe_jurnal }}
                        </span>
                        <h4 class="text-sm font-bold text-white font-mono">{{ $j->no_transaksi }}</h4>
                        <span class="text-xs text-slate-400">&bull; {{ $j->tanggal->format('d F Y') }}</span>
                    </div>
                    <div class="flex items-center gap-3 text-xs text-slate-400">
                        <div>
                            Oleh: <strong class="text-slate-300">{{ $j->created_by ?? 'System' }}</strong>
                            @if($j->sumber_referensi)
                                <span class="ml-2 px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-300">Ref: {{ $j->sumber_referensi }}</span>
                            @endif
                        </div>
                        <a href="{{ route('akuntansi.voucher', $j->id_jurnal) }}" target="_blank" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-semibold border border-slate-700 inline-flex items-center gap-1.5 transition">
                            <i class="fas fa-print text-amber-400"></i>
                            <span>Bukti</span>
                        </a>
                        @if(auth()->user()->canMutate())
                            <button type="button" 
                                onclick="openEditModal({{ $j->id_jurnal }})" 
                                data-id="{{ $j->id_jurnal }}"
                                class="px-2.5 py-1 rounded-lg bg-blue-500/20 hover:bg-blue-500 hover:text-white text-blue-400 text-[11px] font-semibold border border-blue-500/30 transition flex items-center gap-1 cursor-pointer"
                                title="Edit Formulir Jurnal (Header, Akun Debit & Kredit)">
                                <i class="fas fa-edit pointer-events-none"></i>
                                <span class="pointer-events-none">Edit</span>
                            </button>
                            @if(!$j->is_posted)
                            <form action="{{ route('akuntansi.jurnal.approve', $j->id_jurnal) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 hover:text-white text-emerald-400 text-[11px] font-semibold border border-emerald-500/30 transition flex items-center gap-1">
                                    <i class="fas fa-check"></i> Approve
                                </button>
                            </form>
                            @endif
                        <form action="{{ route('akuntansi.destroyJurnal', $j->id_jurnal) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Hapus jurnal umum {{ $j->no_transaksi }}?\n\nSaldo mutasi pada seluruh akun buku besar (COA) terkait akan OTOMATIS DI-ROLLBACK!')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white text-[11px] font-semibold border border-rose-500/30 transition flex items-center gap-1" title="Hapus Jurnal & Rollback Saldo Buku Besar">
                                <i class="fas fa-trash-can"></i>
                                <span>Hapus</span>
                            </button>
                        </form>
                        @endif
                    </div>
                </div>

                <p class="text-xs text-slate-300 mb-3">{{ $j->deskripsi }}</p>

                <!-- Details lines table -->
                <div class="rounded-2xl border border-slate-800/80 bg-slate-950/40 overflow-hidden">
                    <table class="w-full text-left text-xs">
                        <thead class="text-[10px] text-slate-400 uppercase font-bold border-b border-slate-800/80 bg-slate-950/60">
                            <tr>
                                <th class="py-2 px-3">Kode &amp; Nama Akun</th>
                                <th class="py-2 px-3">Keterangan</th>
                                <th class="py-2 px-3 text-right">Debit</th>
                                <th class="py-2 px-3 text-right">Kredit</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-800/60">
                            @foreach($j->details as $d)
                                <tr>
                                    <td class="py-2 px-3 font-semibold text-white">
                                        <span class="text-amber-400 font-mono">{{ $d->kode_akun }}</span> - {{ $d->akun->nama_akun ?? '' }}
                                    </td>
                                    <td class="py-2 px-3 text-slate-400">{{ $d->keterangan_baris ?? '-' }}</td>
                                    <td class="py-2 px-3 text-right text-emerald-400 font-mono font-medium">
                                        {{ $d->debit > 0 ? 'Rp ' . number_format($d->debit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-2 px-3 text-right text-rose-400 font-mono font-medium">
                                        {{ $d->kredit > 0 ? 'Rp ' . number_format($d->kredit, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                            @endforeach
                            <tr class="bg-slate-900/60 font-bold border-t border-slate-800">
                                <td colspan="2" class="py-2 px-3 text-right text-slate-300 text-[11px]">Total:</td>
                                <td class="py-2 px-3 text-right text-emerald-400 font-mono">Rp {{ number_format($j->total_debit, 0, ',', '.') }}</td>
                                <td class="py-2 px-3 text-right text-rose-400 font-mono">Rp {{ number_format($j->total_kredit, 0, ',', '.') }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-3xl border border-slate-800 bg-slate-900/60">
                <i class="fas fa-receipt text-3xl text-slate-600 mb-2"></i>
                <p class="text-xs text-slate-400">Belum ada jurnal umum yang dicatat.</p>
            </div>
        @endforelse

        @if($jurnals->hasPages())
            <div class="pt-2">
                {{ $jurnals->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Modal Edit Jurnal -->
<div id="modalEditJurnal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-3 sm:p-4 bg-slate-950/85 backdrop-blur-md overflow-y-auto">
    <div class="relative rounded-3xl border border-blue-500/40 bg-slate-900 shadow-2xl w-full max-w-5xl my-8 overflow-hidden flex flex-col max-h-[90vh]">
        <!-- Modal Header -->
        <div class="flex items-center justify-between px-6 py-4 border-b border-slate-800 bg-slate-900 sticky top-0 z-10">
            <div class="flex items-center gap-3">
                <div class="p-2.5 rounded-xl bg-blue-500/10 border border-blue-500/30 text-blue-400">
                    <i class="fas fa-edit text-lg"></i>
                </div>
                <div>
                    <h3 class="text-base font-bold text-white flex items-center gap-2">
                        <span>Edit Formulir Jurnal</span>
                        <span id="editNoTransaksiBadge" class="text-xs px-2.5 py-0.5 rounded-full bg-blue-500/10 text-blue-400 border border-blue-500/30 font-mono font-bold"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400">Perbarui tanggal, keterangan, serta komposisi akun Debit &amp; Kredit</p>
                </div>
            </div>
            <button type="button" onclick="closeEditModal()" class="text-slate-400 hover:text-white text-xl p-1 leading-none transition">&times;</button>
        </div>

        <!-- Modal Body (Scrollable) -->
        <form method="POST" action="" id="formEditJurnal" onsubmit="return validateEditJurnal(event)" class="flex flex-col flex-1 overflow-y-auto">
            @csrf
            @method('PUT')
            
            <div class="p-6 space-y-5 text-xs">
                <!-- Info Alert if posted -->
                <div id="editPostedAlert" class="hidden p-3 rounded-xl bg-amber-950/50 border border-amber-500/30 text-amber-300 text-xs flex items-center gap-2">
                    <i class="fas fa-info-circle text-amber-400 text-sm"></i>
                    <span><strong>Perhatian:</strong> Jurnal ini berstatus <em>POSTED</em>. Menyimpan perubahan akan otomatis membalik (rollback) mutasi lama dan membukukan mutasi baru pada Buku Besar terkait.</span>
                </div>

                <!-- Info Alert if auto-generated -->
                <div id="editAutoAlert" class="hidden p-3 rounded-xl bg-cyan-950/50 border border-cyan-500/30 text-cyan-300 text-xs flex items-center gap-2">
                    <i class="fas fa-robot text-cyan-400 text-sm"></i>
                    <span><strong>Informasi:</strong> Jurnal ini dihasilkan otomatis oleh transaksi sistem (🤖 AUTO). Mengubah baris akun akan menyesuaikan memorial ini dan buku besar akun terkait.</span>
                </div>

                <!-- Header Fields Grid -->
                <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">No Transaksi</label>
                        <input type="text" id="editNoTransaksi" readonly class="w-full px-3 py-2 rounded-xl bg-slate-950/60 border border-slate-800 text-slate-400 font-mono text-xs cursor-not-allowed">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tanggal Transaksi <span class="text-rose-400">*</span></label>
                        <input type="date" name="tanggal" id="editTanggal" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Tipe Jurnal</label>
                        <select name="tipe_jurnal" id="editTipeJurnal" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-blue-500">
                            <option value="Umum">Umum / Memorial</option>
                            <option value="Penyesuaian">Penyesuaian (Adjustment)</option>
                            <option value="Pajak">Pajak (Tax Provision)</option>
                            <option value="Penutup">Penutup (Closing)</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-slate-300 font-semibold mb-1">Referensi / Bukti</label>
                        <input type="text" name="sumber_referensi" id="editSumberReferensi" placeholder="No Dokumen / Memo" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-blue-500">
                    </div>
                </div>

                <div>
                    <label class="block text-slate-300 font-semibold mb-1">Deskripsi / Keterangan Transaksi <span class="text-rose-400">*</span></label>
                    <input type="text" name="deskripsi" id="editDeskripsi" required placeholder="Contoh: Penyesuaian Beban Penyusutan Kendaraan Bulanan" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-blue-500">
                </div>

                <!-- Lines Section -->
                <div class="pt-2 border-t border-slate-800">
                    <div class="flex items-center justify-between mb-3">
                        <div>
                            <span class="block text-blue-400 font-bold uppercase tracking-wider text-xs">Baris Akun (Debit &amp; Kredit)</span>
                            <p class="text-[11px] text-slate-400">Minimal 2 baris akun dengan total Debit harus sama dengan Kredit</p>
                        </div>
                        <button type="button" onclick="addEditRow()" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-blue-400 text-xs font-semibold border border-slate-700 hover:border-blue-500/50 transition flex items-center gap-1.5 shadow-sm">
                            <i class="fas fa-plus text-xs"></i>
                            <span>Tambah Baris</span>
                        </button>
                    </div>

                    <!-- Desktop Column Headers -->
                    <div class="hidden sm:grid grid-cols-12 gap-2 px-3 pb-1 text-[10px] font-bold uppercase tracking-wider text-slate-400">
                        <div class="sm:col-span-4">Akun (COA) <span class="text-rose-400">*</span></div>
                        <div class="sm:col-span-3">Keterangan Baris</div>
                        <div class="sm:col-span-2 text-right">Debit (Rp)</div>
                        <div class="sm:col-span-2 text-right">Kredit (Rp)</div>
                        <div class="sm:col-span-1 text-center">Aksi</div>
                    </div>

                    <!-- Row Container -->
                    <div id="containerEditRows" class="space-y-2.5">
                        <!-- Populated dynamically via JS -->
                    </div>

                    <!-- Balance Summary Footer -->
                    <div class="mt-4 p-4 rounded-2xl bg-slate-950/80 border border-slate-800 flex flex-col sm:flex-row sm:items-center justify-between gap-3 font-mono">
                        <div class="flex items-center gap-6">
                            <div>
                                <span class="text-[11px] text-slate-400 block font-sans">Total Debit:</span>
                                <span id="editTotalDebitDisplay" class="text-sm font-bold text-emerald-400">Rp 0</span>
                            </div>
                            <div class="border-l border-slate-800 pl-6">
                                <span class="text-[11px] text-slate-400 block font-sans">Total Kredit:</span>
                                <span id="editTotalKreditDisplay" class="text-sm font-bold text-rose-400">Rp 0</span>
                            </div>
                        </div>

                        <div id="editBalanceStatus" class="flex items-center gap-2">
                            <!-- Status badge here -->
                        </div>
                    </div>
                </div>
            </div>

            <!-- Modal Footer (Sticky Bottom) -->
            <div class="flex items-center justify-between px-6 py-4 border-t border-slate-800 bg-slate-900 sticky bottom-0 z-10">
                <span class="text-[11px] text-slate-400 font-sans">Pastikan total Debit dan Kredit seimbang sebelum menyimpan.</span>
                <div class="flex items-center gap-3">
                    <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold transition text-xs">Batal</button>
                    <button type="submit" id="btnSubmitEdit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold shadow-lg shadow-blue-500/20 transition flex items-center gap-2 text-xs">
                        <i class="fas fa-check"></i>
                        <span>Simpan Perubahan</span>
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    // List of active COA accounts
    let availableAccounts = {!! json_encode($akuns, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!} || [];
    if (!Array.isArray(availableAccounts)) {
        availableAccounts = Object.values(availableAccounts || {});
    }

    // Map of journal records for fast and reliable modal population
    window.jurnalRecords = {!! json_encode($recordsPayload, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP) !!} || {};

    function escapeHtml(text) {
        if (!text) return '';
        return String(text)
            .replace(/&/g, "&amp;")
            .replace(/</g, "&lt;")
            .replace(/>/g, "&gt;")
            .replace(/"/g, "&quot;")
            .replace(/'/g, "&#039;");
    }

    function formatRupiah(amount) {
        return 'Rp ' + Number(amount || 0).toLocaleString('id-ID', { minimumFractionDigits: 0, maximumFractionDigits: 2 });
    }

    function getAccountOptionsHtml(selectedCode = '') {
        let html = '<option value="">-- Pilih Akun --</option>';
        let found = false;
        const list = Array.isArray(availableAccounts) ? availableAccounts : Object.values(availableAccounts || {});
        list.forEach(ak => {
            if (!ak || !ak.kode_akun) return;
            const isSelected = String(ak.kode_akun) === String(selectedCode);
            if (isSelected) found = true;
            html += `<option value="${escapeHtml(ak.kode_akun)}" ${isSelected ? 'selected' : ''}>${escapeHtml(ak.kode_akun)} - ${escapeHtml(ak.nama_akun)} (${escapeHtml(ak.kategori || '')})</option>`;
        });
        if (selectedCode && !found) {
            html += `<option value="${escapeHtml(selectedCode)}" selected>${escapeHtml(selectedCode)} (Akun Terpilih)</option>`;
        }
        return html;
    }

    /* ─── Tambah Jurnal Dynamic Rows ─── */
    let tambahRowIndex = 0;

    function addTambahRow(kodeAkun = '', keterangan = '', debit = 0, kredit = 0) {
        const container = document.getElementById('containerTambahRows');
        if (!container) return;
        const idx = tambahRowIndex++;
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800 tambah-row items-center';
        row.id = `tambah-row-${idx}`;
        
        row.innerHTML = `
            <div class="col-span-12 sm:col-span-4">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Akun (COA)</label>
                <select name="details[${idx}][kode_akun]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-amber-500">
                    ${getAccountOptionsHtml(kodeAkun)}
                </select>
            </div>
            <div class="col-span-12 sm:col-span-3">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Keterangan Baris</label>
                <input type="text" name="details[${idx}][keterangan_baris]" value="${escapeHtml(keterangan)}" placeholder="Keterangan baris (opsional)..." class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-amber-500">
            </div>
            <div class="col-span-5 sm:col-span-2">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Debit (Rp)</label>
                <input type="number" step="0.01" min="0" name="details[${idx}][debit]" value="${debit}" oninput="calcTotalTambah()" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs text-right focus:ring-1 focus:ring-amber-500 input-tambah-debit">
            </div>
            <div class="col-span-5 sm:col-span-2">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Kredit (Rp)</label>
                <input type="number" step="0.01" min="0" name="details[${idx}][kredit]" value="${kredit}" oninput="calcTotalTambah()" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs text-right focus:ring-1 focus:ring-amber-500 input-tambah-kredit">
            </div>
            <div class="col-span-2 sm:col-span-1 flex items-center justify-center pt-3 sm:pt-0">
                <button type="button" onclick="removeTambahRow(this)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus Baris">
                    <i class="fas fa-trash-can text-sm"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        calcTotalTambah();
    }

    function removeTambahRow(btn) {
        const rows = document.querySelectorAll('#containerTambahRows .tambah-row');
        if (rows.length <= 2) {
            alert('Jurnal minimal harus memiliki 2 baris akun (Debit & Kredit).');
            return;
        }
        const row = btn.closest('.tambah-row');
        if (row) {
            row.remove();
            calcTotalTambah();
        }
    }

    function calcTotalTambah() {
        let totalDebit = 0;
        let totalKredit = 0;

        document.querySelectorAll('#containerTambahRows .input-tambah-debit').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });

        document.querySelectorAll('#containerTambahRows .input-tambah-kredit').forEach(input => {
            totalKredit += parseFloat(input.value) || 0;
        });

        const debitEl = document.getElementById('tambahTotalDebitDisplay');
        const kreditEl = document.getElementById('tambahTotalKreditDisplay');
        const statusEl = document.getElementById('tambahBalanceStatus');

        if (debitEl) debitEl.innerText = formatRupiah(totalDebit);
        if (kreditEl) kreditEl.innerText = formatRupiah(totalKredit);

        const diff = Math.abs(totalDebit - totalKredit);
        const isBalanced = diff < 0.01 && totalDebit > 0;

        if (statusEl) {
            if (totalDebit === 0 && totalKredit === 0) {
                statusEl.innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-xs font-semibold flex items-center gap-1.5">
                        <i class="fas fa-circle-info"></i> Masukkan nominal
                    </span>
                `;
            } else if (isBalanced) {
                statusEl.innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-check-circle"></i> SEIMBANG
                    </span>
                `;
            } else {
                statusEl.innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/30 text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-triangle-exclamation"></i> SELISIH: ${formatRupiah(diff)}
                    </span>
                `;
            }
        }
    }

    function validateTambahJurnal(e) {
        const rows = document.querySelectorAll('#containerTambahRows .tambah-row');
        if (rows.length < 2) {
            alert('Jurnal minimal harus memiliki 2 baris (Debit & Kredit).');
            e.preventDefault();
            return false;
        }

        // Clean up any extra empty rows if more than 2
        rows.forEach(r => {
            const select = r.querySelector('select');
            const deb = parseFloat(r.querySelector('.input-tambah-debit').value) || 0;
            const kre = parseFloat(r.querySelector('.input-tambah-kredit').value) || 0;
            if ((!select.value || select.value === '') && deb === 0 && kre === 0) {
                if (document.querySelectorAll('#containerTambahRows .tambah-row').length > 2) {
                    r.remove();
                }
            }
        });

        const activeRows = document.querySelectorAll('#containerTambahRows .tambah-row');
        let totalDebit = 0;
        let totalKredit = 0;
        let hasEmptyAccount = false;

        activeRows.forEach(r => {
            const select = r.querySelector('select');
            const deb = parseFloat(r.querySelector('.input-tambah-debit').value) || 0;
            const kre = parseFloat(r.querySelector('.input-tambah-kredit').value) || 0;

            if ((deb > 0 || kre > 0) && (!select.value || select.value === '')) {
                hasEmptyAccount = true;
            }

            totalDebit += deb;
            totalKredit += kre;
        });

        if (hasEmptyAccount) {
            alert('Pastikan semua baris yang memiliki nominal sudah dipilih akunnya.');
            e.preventDefault();
            return false;
        }

        if (totalDebit <= 0) {
            alert('Total nominal jurnal harus lebih dari 0.');
            e.preventDefault();
            return false;
        }

        if (Math.abs(totalDebit - totalKredit) >= 0.01) {
            alert(`Transaksi tidak seimbang! Total Debit (${formatRupiah(totalDebit)}) harus sama dengan Total Kredit (${formatRupiah(totalKredit)}). Selisih: ${formatRupiah(Math.abs(totalDebit - totalKredit))}`);
            e.preventDefault();
            return false;
        }

        return true;
    }

    /* ─── Edit Jurnal Dynamic Rows ─── */
    let editRowIndex = 0;

    function addEditRow(kodeAkun = '', keterangan = '', debit = 0, kredit = 0) {
        const container = document.getElementById('containerEditRows');
        if (!container) return;
        const idx = editRowIndex++;
        const row = document.createElement('div');
        row.className = 'grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800 edit-row items-center';
        row.id = `edit-row-${idx}`;
        
        row.innerHTML = `
            <div class="col-span-12 sm:col-span-4">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Akun (COA)</label>
                <select name="details[${idx}][kode_akun]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-blue-500">
                    ${getAccountOptionsHtml(kodeAkun)}
                </select>
            </div>
            <div class="col-span-12 sm:col-span-3">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Keterangan Baris</label>
                <input type="text" name="details[${idx}][keterangan_baris]" value="${escapeHtml(keterangan)}" placeholder="Keterangan baris (opsional)..." class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white text-xs focus:ring-1 focus:ring-blue-500">
            </div>
            <div class="col-span-5 sm:col-span-2">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Debit (Rp)</label>
                <input type="number" step="0.01" min="0" name="details[${idx}][debit]" value="${debit}" oninput="calcTotalEdit()" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs text-right focus:ring-1 focus:ring-blue-500 input-edit-debit">
            </div>
            <div class="col-span-5 sm:col-span-2">
                <label class="block text-[10px] text-slate-400 mb-0.5 sm:hidden">Kredit (Rp)</label>
                <input type="number" step="0.01" min="0" name="details[${idx}][kredit]" value="${kredit}" oninput="calcTotalEdit()" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono text-xs text-right focus:ring-1 focus:ring-blue-500 input-edit-kredit">
            </div>
            <div class="col-span-2 sm:col-span-1 flex items-center justify-center pt-3 sm:pt-0">
                <button type="button" onclick="removeEditRow(this)" class="p-1.5 rounded-lg text-slate-400 hover:text-rose-400 hover:bg-rose-500/10 transition" title="Hapus Baris">
                    <i class="fas fa-trash-can text-sm"></i>
                </button>
            </div>
        `;
        container.appendChild(row);
        calcTotalEdit();
    }

    function removeEditRow(btn) {
        const rows = document.querySelectorAll('#containerEditRows .edit-row');
        if (rows.length <= 2) {
            alert('Jurnal minimal harus memiliki 2 baris akun (Debit & Kredit).');
            return;
        }
        const row = btn.closest('.edit-row');
        if (row) {
            row.remove();
            calcTotalEdit();
        }
    }

    function calcTotalEdit() {
        let totalDebit = 0;
        let totalKredit = 0;

        document.querySelectorAll('#containerEditRows .input-edit-debit').forEach(input => {
            totalDebit += parseFloat(input.value) || 0;
        });

        document.querySelectorAll('#containerEditRows .input-edit-kredit').forEach(input => {
            totalKredit += parseFloat(input.value) || 0;
        });

        const debitEl = document.getElementById('editTotalDebitDisplay');
        const kreditEl = document.getElementById('editTotalKreditDisplay');
        const statusEl = document.getElementById('editBalanceStatus');

        if (debitEl) debitEl.innerText = formatRupiah(totalDebit);
        if (kreditEl) kreditEl.innerText = formatRupiah(totalKredit);

        const diff = Math.abs(totalDebit - totalKredit);
        const isBalanced = diff < 0.01 && totalDebit > 0;

        if (statusEl) {
            if (totalDebit === 0 && totalKredit === 0) {
                statusEl.innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-slate-800 text-slate-400 text-xs font-semibold flex items-center gap-1.5">
                        <i class="fas fa-circle-info"></i> Masukkan nominal
                    </span>
                `;
            } else if (isBalanced) {
                statusEl.innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-400 border border-emerald-500/30 text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-check-circle"></i> SEIMBANG
                    </span>
                `;
            } else {
                statusEl.innerHTML = `
                    <span class="px-3 py-1 rounded-full bg-rose-500/10 text-rose-400 border border-rose-500/30 text-xs font-bold flex items-center gap-1.5">
                        <i class="fas fa-triangle-exclamation"></i> SELISIH: ${formatRupiah(diff)}
                    </span>
                `;
            }
        }
    }

    function validateEditJurnal(e) {
        const rows = document.querySelectorAll('#containerEditRows .edit-row');
        if (rows.length < 2) {
            alert('Jurnal minimal harus memiliki 2 baris (Debit & Kredit).');
            e.preventDefault();
            return false;
        }

        // Clean up any extra empty rows if more than 2
        rows.forEach(r => {
            const select = r.querySelector('select');
            const deb = parseFloat(r.querySelector('.input-edit-debit').value) || 0;
            const kre = parseFloat(r.querySelector('.input-edit-kredit').value) || 0;
            if ((!select.value || select.value === '') && deb === 0 && kre === 0) {
                if (document.querySelectorAll('#containerEditRows .edit-row').length > 2) {
                    r.remove();
                }
            }
        });

        const activeRows = document.querySelectorAll('#containerEditRows .edit-row');
        let totalDebit = 0;
        let totalKredit = 0;
        let hasEmptyAccount = false;

        activeRows.forEach(r => {
            const select = r.querySelector('select');
            const deb = parseFloat(r.querySelector('.input-edit-debit').value) || 0;
            const kre = parseFloat(r.querySelector('.input-edit-kredit').value) || 0;

            if ((deb > 0 || kre > 0) && (!select.value || select.value === '')) {
                hasEmptyAccount = true;
            }

            totalDebit += deb;
            totalKredit += kre;
        });

        if (hasEmptyAccount) {
            alert('Pastikan semua baris yang memiliki nominal sudah dipilih akunnya.');
            e.preventDefault();
            return false;
        }

        if (totalDebit <= 0) {
            alert('Total nominal jurnal harus lebih dari 0.');
            e.preventDefault();
            return false;
        }

        if (Math.abs(totalDebit - totalKredit) >= 0.01) {
            alert(`Transaksi tidak seimbang! Total Debit (${formatRupiah(totalDebit)}) harus sama dengan Total Kredit (${formatRupiah(totalKredit)}). Selisih: ${formatRupiah(Math.abs(totalDebit - totalKredit))}`);
            e.preventDefault();
            return false;
        }

        return true;
    }

    function openEditModal(target, tanggalFallback, deskripsiFallback) {
        try {
            let id = null;
            if (typeof target === 'number' || typeof target === 'string') {
                id = target;
            } else if (target && typeof target === 'object') {
                if (target.getAttribute && target.getAttribute('data-id')) {
                    id = target.getAttribute('data-id');
                } else if (target.dataset && target.dataset.id) {
                    id = target.dataset.id;
                } else if (target.closest) {
                    const btn = target.closest('[data-id]');
                    if (btn) id = btn.getAttribute('data-id');
                }
            }

            let data = (id && window.jurnalRecords) ? window.jurnalRecords[id] : null;

            // Fallback for legacy data-jurnal attribute
            if (!data && target && typeof target === 'object' && target.dataset && target.dataset.jurnal) {
                try {
                    const raw = atob(target.dataset.jurnal);
                    const bytes = Uint8Array.from(raw, c => c.charCodeAt(0));
                    data = JSON.parse(new TextDecoder('utf-8').decode(bytes));
                } catch (e) {
                    console.error('Fallback parse error:', e);
                }
            }

            // Direct object support
            if (!data && target && typeof target === 'object' && target.id_jurnal) {
                data = target;
            }

            // Fallback if data still not found
            if (!data && id) {
                data = {
                    id_jurnal: id,
                    no_transaksi: 'JU-' + id,
                    tanggal: tanggalFallback || '',
                    tipe_jurnal: 'Umum',
                    sumber_referensi: '',
                    deskripsi: deskripsiFallback || '',
                    is_posted: false,
                    is_auto: false,
                    details: []
                };
            }

            if (!data) {
                console.error('Data jurnal tidak ditemukan untuk target:', target);
                alert('Data jurnal tidak ditemukan.');
                return;
            }

            const form = document.getElementById('formEditJurnal');
            if (form) form.action = '/akuntansi/jurnal/' + data.id_jurnal;

            const editNoTransaksi = document.getElementById('editNoTransaksi');
            if (editNoTransaksi) editNoTransaksi.value = data.no_transaksi || '';

            const editNoTransaksiBadge = document.getElementById('editNoTransaksiBadge');
            if (editNoTransaksiBadge) editNoTransaksiBadge.innerText = data.no_transaksi || '';

            const editTanggal = document.getElementById('editTanggal');
            if (editTanggal) editTanggal.value = data.tanggal || '';

            const editTipeJurnal = document.getElementById('editTipeJurnal');
            if (editTipeJurnal) editTipeJurnal.value = data.tipe_jurnal || 'Umum';

            const editSumberReferensi = document.getElementById('editSumberReferensi');
            if (editSumberReferensi) editSumberReferensi.value = data.sumber_referensi || '';

            const editDeskripsi = document.getElementById('editDeskripsi');
            if (editDeskripsi) editDeskripsi.value = data.deskripsi || '';

            const postedAlert = document.getElementById('editPostedAlert');
            if (postedAlert) {
                if (data.is_posted) {
                    postedAlert.classList.remove('hidden');
                } else {
                    postedAlert.classList.add('hidden');
                }
            }

            const autoAlert = document.getElementById('editAutoAlert');
            if (autoAlert) {
                if (data.is_auto) {
                    autoAlert.classList.remove('hidden');
                } else {
                    autoAlert.classList.add('hidden');
                }
            }

            const container = document.getElementById('containerEditRows');
            if (container) {
                container.innerHTML = '';
                editRowIndex = 0;

                if (data.details && data.details.length > 0) {
                    data.details.forEach(d => {
                        addEditRow(d.kode_akun, d.keterangan_baris, d.debit, d.kredit);
                    });
                }

                // Ensure at least 2 rows exist
                const currentRows = container.querySelectorAll('.edit-row').length;
                for (let i = currentRows; i < 2; i++) {
                    addEditRow();
                }
            }

            calcTotalEdit();

            const modal = document.getElementById('modalEditJurnal');
            if (modal) {
                modal.classList.remove('hidden');
            } else {
                console.error('Modal #modalEditJurnal tidak ditemukan');
            }
        } catch (err) {
            console.error('Error saat membuka modal edit jurnal:', err);
            alert('Terjadi kesalahan saat membuka formulir edit: ' + err.message);
        }
    }

    function closeEditModal() {
        const modal = document.getElementById('modalEditJurnal');
        if (modal) modal.classList.add('hidden');
    }

    /* ─── Fast Filter Helpers & Loading State ─── */
    function submitFilterWithLoading() {
        const btn = document.getElementById('btnFilterJurnal');
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Memuat...</span>';
        }
        document.getElementById('filterJurnalForm').submit();
    }

    function setQuickDate(type) {
        const now = new Date();
        const fromInput = document.getElementById('filterTglDari');
        const toInput = document.getElementById('filterTglSampai');
        
        function formatDate(d) {
            const year = d.getFullYear();
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const day = String(d.getDate()).padStart(2, '0');
            return `${year}-${month}-${day}`;
        }

        if (type === 'today') {
            const today = formatDate(now);
            fromInput.value = today;
            toInput.value = today;
        } else if (type === 'this_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth(), 1);
            fromInput.value = formatDate(firstDay);
            toInput.value = formatDate(now);
        } else if (type === 'last_month') {
            const firstDay = new Date(now.getFullYear(), now.getMonth() - 1, 1);
            const lastDay = new Date(now.getFullYear(), now.getMonth(), 0);
            fromInput.value = formatDate(firstDay);
            toInput.value = formatDate(lastDay);
        } else if (type === 'this_year') {
            const firstDay = new Date(now.getFullYear(), 0, 1);
            fromInput.value = formatDate(firstDay);
            toInput.value = formatDate(now);
        } else if (type === 'all') {
            fromInput.value = '';
            toInput.value = '';
        }
        submitFilterWithLoading();
    }

    // Initialize initial rows for Tambah Jurnal on load
    document.addEventListener('DOMContentLoaded', function() {
        const filterForm = document.getElementById('filterJurnalForm');
        if (filterForm) {
            filterForm.addEventListener('submit', function() {
                const btn = document.getElementById('btnFilterJurnal');
                if (btn) {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i><span>Memuat...</span>';
                }
            });
        }

        const containerTambah = document.getElementById('containerTambahRows');
        if (containerTambah && containerTambah.children.length === 0) {
            addTambahRow();
            addTambahRow();
        }

        // Click outside to close edit modal
        const modalEdit = document.getElementById('modalEditJurnal');
        if (modalEdit) {
            modalEdit.addEventListener('click', function(e) {
                if (e.target === modalEdit) {
                    closeEditModal();
                }
            });
        }

        // Escape key to close modals
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeEditModal();
                const modalTambah = document.getElementById('modalTambahJurnal');
                if (modalTambah) modalTambah.classList.add('hidden');
            }
        });
    });
</script>

@endsection
