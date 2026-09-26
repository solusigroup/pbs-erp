@extends('layouts.admin')

@section('title', 'Pusat Kontrol Workflow Akuntansi & Approval — PBS-ERP')

@section('content')
<div class="space-y-6">

    {{-- ═══ NOTIFIKASI FLASH MESSAGE ═══ --}}
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

    {{-- ═══ HEADER BANNER ═══ --}}
    <div class="relative overflow-hidden rounded-3xl border border-indigo-500/30 bg-gradient-to-r from-slate-900 via-[#131b38] to-slate-900 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-indigo-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-48 w-48 rounded-full bg-purple-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-2">
                    <i class="fas fa-stamp text-[10px]"></i>
                    <span>Kontrol Workflow Akuntansi &bull; Approval Jurnal &amp; Tutup Buku</span>
                </div>
                <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight">Pusat Kontrol Workflow Akuntansi PT PBS</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                    Kawal integritas pembukuan: verifikasi jurnal draft sebelum masuk Buku Besar (GL), deteksi transaksi tanpa jurnal, dan awasi siklus tutup buku bulanan.
                </p>

                <div class="flex flex-wrap items-center gap-2 mt-4 text-xs">
                    <a href="{{ route('workflow.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition flex items-center gap-1.5">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Kembali ke Overview Workflow</span>
                    </a>
                    <a href="{{ route('akuntansi.jurnal') }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 transition flex items-center gap-1.5">
                        <i class="fas fa-book-journal-whills text-xs"></i>
                        <span>Buku Jurnal Umum</span>
                    </a>
                    <a href="{{ route('akuntansi.laporan') }}" class="px-3.5 py-1.5 rounded-xl bg-emerald-600/30 hover:bg-emerald-600 text-emerald-300 hover:text-white border border-emerald-500/30 transition flex items-center gap-1.5">
                        <i class="fas fa-chart-line text-xs"></i>
                        <span>Laporan Laba Rugi PBS</span>
                    </a>
                </div>
            </div>

            {{-- Ring KPI Akuntansi --}}
            <div class="flex items-center gap-5 bg-slate-800/60 border border-slate-700/60 rounded-2xl px-5 py-4 shrink-0 shadow-lg">
                <div class="relative h-20 w-20 shrink-0">
                    <svg class="h-20 w-20 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.91" fill="none" stroke="currentColor" class="text-slate-700" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.91" fill="none"
                                stroke="{{ $approvalPercentage >= 95 ? '#10b981' : ($approvalPercentage >= 75 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="{{ $approvalPercentage }},100"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-black text-white">{{ $approvalPercentage }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Jurnal Sah di GL</p>
                    <p class="text-2xl font-black text-white">{{ number_format($postedJurnalsCount) }}<span class="text-sm text-slate-400 font-medium">/{{ number_format($totalJurnals) }}</span></p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        <span class="{{ $draftJurnalsCount > 0 ? 'text-amber-400 font-bold' : 'text-emerald-400 font-bold' }}">
                            {{ number_format($draftJurnalsCount) }}
                        </span> draft menunggu approval
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ KPI SUMMARY METRICS ═══ --}}
    @php
        $totalMissing = count($unjournalized['raw_materials']) + count($unjournalized['sales']);
    @endphp
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="font-semibold uppercase tracking-wider text-[10px]">Total Jurnal Terdaftar</span>
                <i class="fas fa-layer-group text-indigo-400"></i>
            </div>
            <div class="text-2xl font-black text-white">{{ number_format($totalJurnals) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Manual Memorial &amp; Transaksi Otomatis</div>
        </div>

        <div class="p-4 rounded-2xl border {{ $draftJurnalsCount > 0 ? 'border-amber-500/40 bg-amber-950/20' : 'border-slate-800 bg-slate-900/60' }} shadow-lg">
            <div class="flex items-center justify-between {{ $draftJurnalsCount > 0 ? 'text-amber-400' : 'text-slate-400' }} mb-2">
                <span class="font-semibold uppercase tracking-wider text-[10px]">Draft Belum Approve</span>
                <i class="fas fa-clock-rotate-left {{ $draftJurnalsCount > 0 ? 'text-amber-400 animate-spin-slow' : 'text-slate-500' }}"></i>
            </div>
            <div class="text-2xl font-black {{ $draftJurnalsCount > 0 ? 'text-amber-400' : 'text-slate-300' }}">{{ number_format($draftJurnalsCount) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">Perlu otorisasi Direksi / BOD</div>
        </div>

        <div class="p-4 rounded-2xl border {{ $totalMissing > 0 ? 'border-orange-500/40 bg-orange-950/20' : 'border-slate-800 bg-slate-900/60' }} shadow-lg">
            <div class="flex items-center justify-between {{ $totalMissing > 0 ? 'text-orange-400' : 'text-slate-400' }} mb-2">
                <span class="font-semibold uppercase tracking-wider text-[10px]">Transaksi Tanpa Jurnal</span>
                <i class="fas fa-file-circle-xmark {{ $totalMissing > 0 ? 'text-orange-400' : 'text-slate-500' }}"></i>
            </div>
            <div class="text-2xl font-black {{ $totalMissing > 0 ? 'text-orange-400' : 'text-slate-300' }}">{{ number_format($totalMissing) }}</div>
            <div class="text-[11px] text-slate-400 mt-1">RAW: {{ count($unjournalized['raw_materials']) }} | Sales: {{ count($unjournalized['sales']) }}</div>
        </div>

        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <div class="flex items-center justify-between text-slate-400 mb-2">
                <span class="font-semibold uppercase tracking-wider text-[10px]">Kepatuhan Tutup Buku</span>
                <i class="fas fa-book-bookmark text-teal-400"></i>
            </div>
            <div class="text-2xl font-black {{ $closingStatus['is_ready_to_close'] ? 'text-teal-400' : 'text-amber-400' }}">
                {{ $closingStatus['compliance_percentage'] }}%
            </div>
            <div class="text-[11px] text-slate-400 mt-1">Periode: {{ $closingStatus['period'] }} ({{ $closingStatus['passed_count'] }}/{{ $closingStatus['total_steps'] }} langkah)</div>
        </div>
    </div>

    {{-- ═══ TAB NAVIGATION ═══ --}}
    <div class="border-b border-slate-800 flex items-center gap-2 overflow-x-auto custom-scrollbar">
        <a href="{{ route('workflow.akuntansi', ['tab' => 'unapproved', 'period' => $period]) }}"
           class="px-5 py-3 text-xs font-bold transition border-b-2 flex items-center gap-2 whitespace-nowrap {{ $tab === 'unapproved' ? 'border-amber-400 text-amber-400 bg-amber-500/10' : 'border-transparent text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
            <i class="fas fa-stamp"></i>
            <span>Antrian Jurnal Belum Approve</span>
            @if($draftJurnalsCount > 0)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-900">
                {{ $draftJurnalsCount }}
            </span>
            @endif
        </a>

        <a href="{{ route('workflow.akuntansi', ['tab' => 'missing_journals', 'period' => $period]) }}"
           class="px-5 py-3 text-xs font-bold transition border-b-2 flex items-center gap-2 whitespace-nowrap {{ $tab === 'missing_journals' ? 'border-orange-400 text-orange-400 bg-orange-500/10' : 'border-transparent text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
            <i class="fas fa-file-circle-exclamation"></i>
            <span>Audit Transaksi Tanpa Jurnal</span>
            @if($totalMissing > 0)
            <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-orange-500 text-white">
                {{ $totalMissing }}
            </span>
            @endif
        </a>

        <a href="{{ route('workflow.akuntansi', ['tab' => 'closing', 'period' => $period]) }}"
           class="px-5 py-3 text-xs font-bold transition border-b-2 flex items-center gap-2 whitespace-nowrap {{ $tab === 'closing' ? 'border-teal-400 text-teal-400 bg-teal-500/10' : 'border-transparent text-slate-400 hover:text-white hover:bg-slate-800/40' }}">
            <i class="fas fa-book-bookmark"></i>
            <span>Siklus Kontrol Tutup Buku Bulanan</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-slate-800 text-slate-300">
                {{ $closingStatus['period'] }}
            </span>
        </a>
    </div>

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- ── TAB 1: ANTRIAN APPROVAL JURNAL DRAFT ─────────────────────────────── --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    @if($tab === 'unapproved')
    <div class="space-y-4">
        @if($unapprovedJournals->isEmpty())
        <div class="p-8 rounded-3xl border border-emerald-500/30 bg-emerald-950/20 text-center space-y-3">
            <div class="h-16 w-16 rounded-full bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-3xl mx-auto border border-emerald-500/40 shadow-lg shadow-emerald-500/20">
                <i class="fas fa-check-double"></i>
            </div>
            <h3 class="text-lg font-black text-white">Seluruh Jurnal Umum Telah Sah Di-Approve!</h3>
            <p class="text-xs text-slate-300 max-w-md mx-auto">
                Tidak ada jurnal umum berstatus DRAFT. Semua pencatatan transaksi manual maupun otomatis telah diposting ke General Ledger (Buku Besar).
            </p>
            <div class="pt-2">
                <a href="{{ route('akuntansi.jurnal') }}" class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 transition">
                    <i class="fas fa-eye text-emerald-400"></i>
                    <span>Buka Buku Jurnal Umum</span>
                </a>
            </div>
        </div>
        @else
        <form method="POST" action="{{ route('workflow.batchApproveJurnal') }}" id="batchApproveForm" onsubmit="return confirmBatchApprove()">
            @csrf
            <div id="hiddenBatchInputs"></div>
            
            {{-- Toolbar Batch Action --}}
            <div class="p-4 rounded-2xl border border-amber-500/30 bg-slate-900/80 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                <div class="flex items-center gap-3">
                    <label class="flex items-center gap-2 cursor-pointer select-none text-xs text-white font-bold">
                        <input type="checkbox" id="selectAllCheckbox" onchange="toggleSelectAll(this)" class="w-4 h-4 rounded border-slate-700 text-amber-500 focus:ring-amber-500/50 bg-slate-950">
                        <span>Pilih Semua Jurnal Draft ({{ $unapprovedJournals->count() }})</span>
                    </label>
                    <span id="selectedCounterBadge" class="hidden px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                        <span id="selectedCountText">0</span> dipilih
                    </span>
                </div>

                <div class="flex items-center gap-2">
                    <button type="submit" id="btnBatchApprove" class="px-4 py-2 rounded-xl bg-gradient-to-r from-emerald-600 to-teal-600 hover:from-emerald-500 hover:to-teal-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-emerald-500/20">
                        <i class="fas fa-check-double"></i>
                        <span>⚡ Approve Terpilih ke GL</span>
                    </button>
                </div>
            </div>
        </form>

        {{-- List of Unapproved Journal Cards --}}
        <div class="space-y-3 mt-4">
            @foreach($unapprovedJournals as $jurnal)
            @php
                $isAuto = \App\Services\JurnalAutoService::isAutoGenerated($jurnal);
                $daysOld = $jurnal->created_at ? \Carbon\Carbon::parse($jurnal->created_at)->diffInDays(now()) : 0;
                $isBalanced = abs((float)$jurnal->total_debit - (float)$jurnal->total_kredit) < 0.01;
                $formattedTanggal = $jurnal->tanggal ? \Carbon\Carbon::parse($jurnal->tanggal)->format('d/m/Y') : '-';
            @endphp
            <div class="rounded-2xl border border-amber-500/30 bg-slate-900/70 p-5 shadow-xl hover:border-amber-400/50 transition">
                <div class="flex flex-col md:flex-row md:items-center justify-between pb-3 mb-3 border-b border-slate-800 gap-3">
                    <div class="flex items-center gap-3 flex-wrap">
                        <input type="checkbox" name="jurnal_ids[]" value="{{ $jurnal->id_jurnal }}" form="batchApproveForm" onchange="updateSelectedCount()" class="jurnal-item-checkbox w-4 h-4 rounded border-slate-700 text-amber-500 focus:ring-amber-500/50 bg-slate-950">
                        
                        <span class="px-2.5 py-0.5 rounded-md text-[10px] font-bold uppercase tracking-wider bg-amber-500 text-slate-900 animate-pulse shadow-md shadow-amber-500/20">
                            DRAFT
                        </span>

                            @if($isAuto)
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-cyan-500/20 text-cyan-400 border border-cyan-500/30">
                                🤖 AUTO CUGIL
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-purple-500/20 text-purple-400 border border-purple-500/30">
                                ✍️ MEMORIAL
                            </span>
                            @endif

                            <span class="font-mono text-sm font-bold text-white">{{ $jurnal->no_transaksi }}</span>
                            <span class="text-xs text-slate-400">&bull; {{ $formattedTanggal }}</span>

                            {{-- Aging warning badge --}}
                            @if($daysOld >= 7)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/20 text-rose-400 border border-rose-500/30">
                                ⚠️ Pending {{ $daysOld }} hari
                            </span>
                            @elseif($daysOld >= 3)
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-amber-500/20 text-amber-400 border border-amber-500/30">
                                ⏳ Pending {{ $daysOld }} hari
                            </span>
                            @else
                            <span class="px-2 py-0.5 rounded-full text-[10px] font-medium bg-slate-800 text-slate-400">
                                Baru {{ $daysOld === 0 ? 'hari ini' : $daysOld . ' hari' }}
                            </span>
                            @endif
                        </div>

                        <div class="flex items-center gap-2">
                            <span class="text-xs text-slate-400 mr-2">Oleh: <strong class="text-slate-300">{{ $jurnal->created_by ?? 'System' }}</strong></span>
                            
                            {{-- Single Approve Form --}}
                            <form action="{{ route('akuntansi.jurnal.approve', $jurnal->id_jurnal) }}" method="POST" class="inline" onsubmit="return confirm('Otorisasi dan approve jurnal {{ $jurnal->no_transaksi }} ke Buku Besar?')">
                                @csrf
                                @method('PUT')
                                <button type="submit" class="px-3 py-1.5 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold transition flex items-center gap-1.5 shadow-md shadow-emerald-500/20">
                                    <i class="fas fa-check"></i>
                                    <span>Approve</span>
                                </button>
                            </form>

                            <a href="{{ route('akuntansi.voucher', $jurnal->id_jurnal) }}" target="_blank" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white text-xs font-semibold border border-slate-700 transition flex items-center gap-1">
                                <i class="fas fa-print text-amber-400 text-xs"></i>
                                <span>Bukti</span>
                            </a>
                        </div>
                    </div>

                    <p class="text-xs text-slate-200 mb-3 font-medium">
                        {{ $jurnal->deskripsi }}
                        @if($jurnal->sumber_referensi)
                        <span class="ml-2 px-2 py-0.5 rounded bg-slate-800 text-[10px] text-slate-400 font-mono">Ref: {{ $jurnal->sumber_referensi }}</span>
                        @endif
                    </p>

                    {{-- Journal Entry Details Table --}}
                    <div class="overflow-x-auto rounded-xl border border-slate-800/80 bg-slate-950/60 p-2">
                        <table class="w-full text-xs">
                            <thead>
                                <tr class="text-slate-400 border-b border-slate-800/60 text-[10px] uppercase">
                                    <th class="text-left py-1.5 px-3">Kode Akun &amp; Nama Rekening</th>
                                    <th class="text-left py-1.5 px-3">Keterangan Baris</th>
                                    <th class="text-right py-1.5 px-3">Debit (Rp)</th>
                                    <th class="text-right py-1.5 px-3">Kredit (Rp)</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-slate-800/40">
                                @foreach($jurnal->details as $d)
                                <tr class="hover:bg-slate-900/40">
                                    <td class="py-1.5 px-3">
                                        <span class="font-mono text-amber-400 font-bold">{{ $d->kode_akun }}</span>
                                        <span class="text-white ml-1">{{ $d->akun->nama_akun ?? '-' }}</span>
                                    </td>
                                    <td class="py-1.5 px-3 text-slate-300">{{ $d->keterangan_baris ?? '-' }}</td>
                                    <td class="py-1.5 px-3 text-right font-mono {{ (float)$d->debit > 0 ? 'text-emerald-400 font-bold' : 'text-slate-600' }}">
                                        {{ (float)$d->debit > 0 ? number_format($d->debit, 0, ',', '.') : '-' }}
                                    </td>
                                    <td class="py-1.5 px-3 text-right font-mono {{ (float)$d->kredit > 0 ? 'text-cyan-400 font-bold' : 'text-slate-600' }}">
                                        {{ (float)$d->kredit > 0 ? number_format($d->kredit, 0, ',', '.') : '-' }}
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr class="border-t border-slate-800 font-bold bg-slate-900/40">
                                    <td colspan="2" class="py-2 px-3 text-right uppercase text-[10px] text-slate-400">Total Transaksi:</td>
                                    <td class="py-2 px-3 text-right font-mono text-emerald-400">Rp {{ number_format($jurnal->total_debit, 0, ',', '.') }}</td>
                                    <td class="py-2 px-3 text-right font-mono text-cyan-400">Rp {{ number_format($jurnal->total_kredit, 0, ',', '.') }}</td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
                @endforeach
            </div>
        @endif
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- ── TAB 2: AUDIT TRANSAKSI TANPA JURNAL ──────────────────────────────── --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    @if($tab === 'missing_journals')
    <div class="space-y-6">
        <div class="p-4 rounded-2xl border border-orange-500/30 bg-orange-950/20 text-xs text-orange-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <i class="fas fa-triangle-exclamation text-2xl text-orange-400 shrink-0"></i>
                <div>
                    <h4 class="font-bold text-white text-sm">Pemeriksaan Integritas Transaksi Operasional CUGIL</h4>
                    <p class="text-slate-300 mt-0.5">
                        Setiap pembelian bahan baku (RAW) dan penjualan cacahan plastik (Sales) wajib tercatat di jurnal akuntansi agar biaya HPP dan pendapatan terakui secara sah.
                    </p>
                </div>
            </div>

            <form method="POST" action="{{ route('workflow.generateMissingJournals') }}" onsubmit="return confirm('Generate jurnal otomatis untuk seluruh transaksi dengan nilai tagihan valid?')">
                @csrf
                <input type="hidden" name="type" value="all">
                <button type="submit" class="px-4 py-2 rounded-xl bg-orange-500 hover:bg-orange-600 text-slate-900 font-black text-xs transition flex items-center gap-1.5 shadow-lg shadow-orange-500/20 shrink-0">
                    <i class="fas fa-wand-magic-sparkles"></i>
                    <span>⚡ Generate Jurnal Transaksi Valid</span>
                </button>
            </form>
        </div>

        {{-- Section RAW Material Tanpa Jurnal --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-amber-500/20 text-amber-400 flex items-center justify-center text-sm">
                        <i class="fas fa-boxes-stacked"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Bahan Baku (RAW Material) Belum Berjurnal</h3>
                        <p class="text-[11px] text-slate-400">Total {{ count($unjournalized['raw_materials']) }} transaksi penerimaan bahan baku</p>
                    </div>
                </div>
            </div>

            @if(empty($unjournalized['raw_materials']))
            <div class="p-6 text-center text-xs text-emerald-400 font-semibold bg-emerald-950/20 rounded-xl border border-emerald-500/20">
                <i class="fas fa-circle-check text-lg mb-1 block"></i>
                Seluruh transaksi penerimaan bahan baku (RAW Material) telah memiliki jurnal pembukuan!
            </div>
            @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                            <th class="text-left py-2 px-3">ID &amp; Tanggal</th>
                            <th class="text-left py-2 px-3">No. PO &amp; Pemasok</th>
                            <th class="text-right py-2 px-3">Nilai Tagihan</th>
                            <th class="text-left py-2 px-3">Diagnosa / Status Alur</th>
                            <th class="text-center py-2 px-3">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @foreach($unjournalized['raw_materials'] as $rm)
                        <tr class="hover:bg-slate-800/40">
                            <td class="py-2.5 px-3">
                                <span class="font-mono font-bold text-white">#RAW-{{ $rm['id'] }}</span>
                                <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($rm['tanggal'])->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-2.5 px-3">
                                <div class="text-amber-400 font-mono font-semibold">{{ $rm['nomor_po'] }}</div>
                                <div class="text-slate-300 text-[11px]">{{ $rm['pemasok'] }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold {{ $rm['tagihan'] > 0 ? 'text-white' : 'text-rose-400' }}">
                                Rp {{ number_format($rm['tagihan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if($rm['has_zero_value'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    <i class="fas fa-triangle-exclamation"></i> Tagihan Rp 0 (Input nilai)
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    <i class="fas fa-clock"></i> Jurnal Otomatis Belum Terbit
                                </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($rm['tagihan'] > 0)
                                <form method="POST" action="{{ route('workflow.generateMissingJournals') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="type" value="raw">
                                    <input type="hidden" name="id" value="{{ $rm['id'] }}">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-indigo-600 hover:bg-indigo-500 text-white text-[11px] font-bold transition flex items-center gap-1 mx-auto shadow-sm">
                                        <i class="fas fa-plus"></i> Generate Jurnal
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('cugil.raw.index') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-medium border border-slate-700 transition inline-block">
                                    Lengkapi Data
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>

        {{-- Section CUGIL Sales Tanpa Jurnal --}}
        <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-2.5">
                    <div class="h-8 w-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-sm">
                        <i class="fas fa-hand-holding-dollar"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white">Penjualan CUGIL Belum Berjurnal</h3>
                        <p class="text-[11px] text-slate-400">Total {{ count($unjournalized['sales']) }} transaksi penjualan produk cacahan</p>
                    </div>
                </div>
            </div>

            @if(empty($unjournalized['sales']))
            <div class="p-6 text-center text-xs text-emerald-400 font-semibold bg-emerald-950/20 rounded-xl border border-emerald-500/20">
                <i class="fas fa-circle-check text-lg mb-1 block"></i>
                Seluruh transaksi penjualan CUGIL telah memiliki jurnal pembukuan!
            </div>
            @else
            <div class="overflow-x-auto custom-scrollbar">
                <table class="w-full text-xs">
                    <thead>
                        <tr class="border-b border-slate-800 text-slate-400 uppercase tracking-wider text-[10px]">
                            <th class="text-left py-2 px-3">No. Penjualan &amp; Tanggal</th>
                            <th class="text-left py-2 px-3">Buyer / Pelanggan</th>
                            <th class="text-right py-2 px-3">Nilai Tagihan</th>
                            <th class="text-left py-2 px-3">Diagnosa / Status Alur</th>
                            <th class="text-center py-2 px-3">Aksi Pemulihan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-800/40">
                        @foreach($unjournalized['sales'] as $sl)
                        <tr class="hover:bg-slate-800/40">
                            <td class="py-2.5 px-3">
                                <span class="font-mono font-bold text-emerald-400">{{ $sl['id_penjualan'] }}</span>
                                <div class="text-[10px] text-slate-400">{{ \Carbon\Carbon::parse($sl['tanggal'])->format('d/m/Y') }}</div>
                            </td>
                            <td class="py-2.5 px-3 text-white font-medium">
                                {{ $sl['buyer'] }}
                            </td>
                            <td class="py-2.5 px-3 text-right font-mono font-bold {{ $sl['tagihan'] > 0 ? 'text-white' : 'text-rose-400' }}">
                                Rp {{ number_format($sl['tagihan'], 0, ',', '.') }}
                            </td>
                            <td class="py-2.5 px-3">
                                @if($sl['has_zero_value'])
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-rose-500/20 text-rose-300 border border-rose-500/30">
                                    <i class="fas fa-triangle-exclamation"></i> Tagihan Rp 0 (Input kuantum)
                                </span>
                                @else
                                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold bg-amber-500/20 text-amber-300 border border-amber-500/30">
                                    <i class="fas fa-clock"></i> Jurnal Penjualan Belum Terbit
                                </span>
                                @endif
                            </td>
                            <td class="py-2.5 px-3 text-center">
                                @if($sl['tagihan'] > 0)
                                <form method="POST" action="{{ route('workflow.generateMissingJournals') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="type" value="sales">
                                    <input type="hidden" name="id" value="{{ $sl['id'] }}">
                                    <button type="submit" class="px-2.5 py-1 rounded-lg bg-emerald-600 hover:bg-emerald-500 text-white text-[11px] font-bold transition flex items-center gap-1 mx-auto shadow-sm">
                                        <i class="fas fa-plus"></i> Generate Jurnal
                                    </button>
                                </form>
                                @else
                                <a href="{{ route('cugil.sales.index') }}" class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-300 text-[11px] font-medium border border-slate-700 transition inline-block">
                                    Lengkapi Data
                                </a>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        </div>
    </div>
    @endif

    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    {{-- ── TAB 3: SIKLUS KONTROL TUTUP BUKU BULANAN ─────────────────────────── --}}
    {{-- ════════════════════════════════════════════════════════════════════════ --}}
    @if($tab === 'closing')
    <div class="space-y-6">
        {{-- Periode Selector Toolbar --}}
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/70 shadow-lg flex flex-col sm:flex-row sm:items-center justify-between gap-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-teal-500/20 text-teal-400 flex items-center justify-center text-lg">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Siklus Kontrol Penutupan Buku (Monthly Closing Gatekeeper)</h3>
                    <p class="text-[11px] text-slate-400">Verifikasi 7 langkah wajib SAK ETAP sebelum periode pembukuan dinyatakan closed</p>
                </div>
            </div>

            <form method="GET" action="{{ route('workflow.akuntansi') }}" class="flex items-center gap-2">
                <input type="hidden" name="tab" value="closing">
                <label class="text-xs text-slate-400 font-medium">Periode:</label>
                <select name="period" onchange="this.form.submit()" class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-white font-mono text-xs focus:ring-1 focus:ring-teal-500">
                    @foreach($availablePeriods as $p)
                    @php
                        $pLabel = rescue(fn() => \Carbon\Carbon::parse($p . '-01')->translatedFormat('F Y'), $p);
                    @endphp
                    <option value="{{ $p }}" {{ $period === $p ? 'selected' : '' }}>
                        {{ $pLabel }} ({{ $p }})
                    </option>
                    @endforeach
                </select>
            </form>
        </div>

        {{-- Progress Banner Tutup Buku --}}
        <div class="p-6 rounded-3xl border {{ $closingStatus['is_ready_to_close'] ? 'border-teal-500/40 bg-teal-950/20' : 'border-amber-500/40 bg-amber-950/20' }} shadow-xl flex flex-col md:flex-row md:items-center justify-between gap-6">
            <div class="flex items-center gap-5">
                <div class="relative h-20 w-20 shrink-0">
                    <svg class="h-20 w-20 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.91" fill="none" stroke="currentColor" class="text-slate-700" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.91" fill="none"
                                stroke="{{ $closingStatus['compliance_percentage'] >= 85 ? '#14b8a6' : '#f59e0b' }}"
                                stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="{{ $closingStatus['compliance_percentage'] }},100"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-black text-white">{{ $closingStatus['compliance_percentage'] }}%</span>
                    </div>
                </div>

                <div>
                    @php
                        $periodDisplay = rescue(fn() => \Carbon\Carbon::parse($period . '-01')->translatedFormat('F Y'), $period);
                    @endphp
                    <h3 class="text-lg font-black text-white">
                        Kesiapan Tutup Buku Periode {{ $periodDisplay }}
                    </h3>
                    <p class="text-xs text-slate-300 mt-1">
                        {{ $closingStatus['passed_count'] }} dari {{ $closingStatus['total_steps'] }} langkah pengawasan administrasi &amp; akuntansi telah terpenuhi.
                    </p>
                    <p class="text-[11px] {{ $closingStatus['is_ready_to_close'] ? 'text-teal-400 font-bold' : 'text-amber-400 font-semibold' }} mt-1">
                        {{ $closingStatus['is_ready_to_close'] ? '✅ Seluruh syarat teknis terpenuhi. Siap disahkan oleh Direksi.' : '⚠️ Selesaikan langkah yang belum terpenuhi sebelum melakukan penutupan buku.' }}
                    </p>
                </div>
            </div>

            @if(auth()->check() && (auth()->user()?->isBOD() || auth()->user()?->isAdmin()))
            <form method="POST" action="{{ route('workflow.verifyClosingStep') }}" onsubmit="return confirm('Sahkan Laporan Keuangan dan Tutup Buku Periode {{ $period }} oleh Direksi?')">
                @csrf
                <input type="hidden" name="period" value="{{ $period }}">
                <input type="hidden" name="step_code" value="close_reports_approved">
                <input type="hidden" name="notes" value="Disahkan oleh {{ auth()->user()?->name ?? 'Direksi' }} (BOD/Direksi)">
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-gradient-to-r from-teal-500 to-emerald-600 hover:from-teal-400 hover:to-emerald-500 text-white font-bold text-xs shadow-lg shadow-teal-500/20 transition flex items-center gap-2">
                    <i class="fas fa-signature text-base"></i>
                    <span>Sahkan &amp; Tanda Tangan Direksi</span>
                </button>
            </form>
            @endif
        </div>

        {{-- 7 Langkah Tutup Buku Checklist --}}
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($closingStatus['steps'] as $idx => $step)
            <div class="p-5 rounded-2xl border {{ $step['is_pass'] ? 'border-emerald-500/30 bg-slate-900/60' : 'border-amber-500/30 bg-amber-950/20' }} shadow-lg transition">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-start gap-3">
                        <div class="h-9 w-9 rounded-xl {{ $step['is_pass'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }} flex items-center justify-center text-sm shrink-0 mt-0.5">
                            <i class="fas {{ $step['is_pass'] ? 'fa-check' : 'fa-hourglass-half' }}"></i>
                        </div>
                        <div>
                            <h4 class="text-xs font-bold text-white">{{ $step['name'] }}</h4>
                            <p class="text-[11px] text-slate-300 mt-1">{{ $step['description'] }}</p>
                        </div>
                    </div>

                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider shrink-0 {{ $step['is_pass'] ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/30' : 'bg-amber-500/20 text-amber-400 border border-amber-500/30' }}">
                        {{ $step['status_text'] }}
                    </span>
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif
</div>

<script>
function toggleSelectAll(master) {
    const checkboxes = document.querySelectorAll('.jurnal-item-checkbox');
    checkboxes.forEach(cb => cb.checked = master.checked);
    updateSelectedCount();
}

function updateSelectedCount() {
    const checked = document.querySelectorAll('.jurnal-item-checkbox:checked').length;
    const badge = document.getElementById('selectedCounterBadge');
    const text = document.getElementById('selectedCountText');
    if (checked > 0) {
        badge.classList.remove('hidden');
        text.innerText = checked;
    } else {
        badge.classList.add('hidden');
    }
}

function confirmBatchApprove() {
    const checkedBoxes = document.querySelectorAll('.jurnal-item-checkbox:checked');
    if (checkedBoxes.length === 0) {
        alert('Silakan pilih minimal satu jurnal draft terlebih dahulu.');
        return false;
    }
    const confirmed = confirm(`Konfirmasi Approval: Approve ${checkedBoxes.length} jurnal terpilih ke Buku Besar (General Ledger)? Saldo COA terkait akan diperbarui secara otomatis.`);
    if (!confirmed) return false;

    const container = document.getElementById('hiddenBatchInputs');
    if (container) {
        container.innerHTML = '';
        checkedBoxes.forEach(cb => {
            const input = document.createElement('input');
            input.type = 'hidden';
            input.name = 'jurnal_ids[]';
            input.value = cb.value;
            container.appendChild(input);
        });
    }
    return true;
}
</script>
@endsection
