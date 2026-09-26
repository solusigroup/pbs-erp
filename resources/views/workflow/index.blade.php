@extends('layouts.admin')

@section('title', 'Kontrol Workflow & Akuntansi — PBS-ERP')

@section('content')
<div class="space-y-6">

    {{-- ═══ HEADER BANNER ═══ --}}
    <div class="relative overflow-hidden rounded-3xl border border-amber-500/30 bg-gradient-to-r from-slate-900 via-[#101d33] to-slate-900 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/30 mb-2">
                    <i class="fas fa-route text-[10px]"></i>
                    <span>Kontrol Workflow &bull; Pencegahan Kelalaian Administrasi &amp; Akuntansi</span>
                </div>
                <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight">Kontrol Workflow Bisnis &amp; Akuntansi PT PBS</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                    Sistem kendali terpadu untuk memastikan setiap langkah administrasi, penerbitan invoice, perpajakan, hingga approval jurnal dan tutup buku berjalan tertib tanpa ada alur terlewatkan.
                </p>

                {{-- Fast Action Buttons --}}
                <div class="flex flex-wrap items-center gap-2.5 mt-4">
                    <a href="{{ route('workflow.akuntansi') }}" class="px-4 py-2 rounded-xl bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-500 hover:to-purple-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-indigo-500/20">
                        <i class="fas fa-stamp text-amber-300"></i>
                        <span>Pusat Approval Jurnal &amp; Tutup Buku</span>
                        @if(($alertsSummary['unapproved_journals'] ?? 0) > 0)
                        <span class="px-2 py-0.5 rounded-full bg-amber-400 text-slate-900 text-[10px] font-black animate-pulse">
                            {{ $alertsSummary['unapproved_journals'] }} Pending
                        </span>
                        @endif
                    </a>

                    <a href="{{ route('workflow.audit') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                        <i class="fas fa-magnifying-glass-chart text-amber-400"></i>
                        <span>Audit Celah Administrasi</span>
                    </a>

                    <form method="POST" action="{{ route('workflow.seedAllExisting') }}" class="inline" onsubmit="return confirm('Jalankan sinkronisasi workflow otomatis untuk seluruh data operasional, perpajakan, dan akuntansi?')">
                        @csrf
                        <button type="submit" class="px-3.5 py-2 rounded-xl bg-teal-600/30 hover:bg-teal-600 text-teal-300 hover:text-white border border-teal-500/30 text-xs font-bold transition flex items-center gap-2 shadow-sm">
                            <i class="fas fa-rotate text-teal-400"></i>
                            <span>Sinkronkan Semua Data</span>
                        </button>
                    </form>
                </div>
            </div>

            {{-- Global Compliance Ring --}}
            <div class="flex items-center gap-5 bg-slate-800/60 border border-slate-700/60 rounded-2xl px-5 py-4 shrink-0 shadow-lg">
                <div class="relative h-20 w-20 shrink-0">
                    <svg class="h-20 w-20 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.91" fill="none" stroke="currentColor" class="text-slate-700" stroke-width="3"/>
                        <circle cx="18" cy="18" r="15.91" fill="none"
                                stroke="{{ $globalPercentage >= 80 ? '#10b981' : ($globalPercentage >= 50 ? '#f59e0b' : '#ef4444') }}"
                                stroke-width="3" stroke-linecap="round"
                                stroke-dasharray="{{ $globalPercentage }},100"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-lg font-black text-white">{{ $globalPercentage }}%</span>
                    </div>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Kepatuhan Global</p>
                    <p class="text-2xl font-black text-white">{{ number_format($completedChecklists) }}<span class="text-sm text-slate-400 font-medium">/{{ number_format($totalChecklists) }}</span></p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        <span class="text-amber-400 font-semibold">{{ number_format($pendingChecklists) }}</span> langkah belum tuntas
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ACTIVE BOTTLENECK BARRIER (Pencegahan Kelalaian) ═══ --}}
    @if(($alertsSummary['total_alerts'] ?? 0) > 0)
    <div class="rounded-2xl border border-amber-500/40 bg-gradient-to-r from-amber-950/40 via-slate-900 to-amber-950/30 p-5 shadow-xl">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <div class="flex items-center gap-3.5">
                <div class="h-11 w-11 rounded-2xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-xl shrink-0 border border-amber-500/30 shadow-md shadow-amber-500/10">
                    <i class="fas fa-bell-concierge"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-amber-300 flex items-center gap-2">
                        <span>Peringatan Titik Kritis Administrasi &amp; Pembukuan</span>
                        <span class="px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-500 text-slate-900 uppercase">
                            {{ $alertsSummary['total_alerts'] }} Butuh Tindakan
                        </span>
                    </h3>
                    <p class="text-xs text-slate-300 mt-0.5">
                        Sistem mendeteksi transaksi atau dokumen yang belum memenuhi syarat kelengkapan administrasi dan akuntansi:
                    </p>
                </div>
            </div>

            <div class="flex flex-wrap items-center gap-2">
                <a href="{{ route('workflow.akuntansi') }}" class="px-3 py-1.5 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-900 text-xs font-bold transition flex items-center gap-1.5 shadow-md">
                    <i class="fas fa-arrow-right"></i>
                    <span>Buka Pusat Approval</span>
                </a>
            </div>
        </div>

        {{-- Mini Metrics Grid --}}
        <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mt-4 pt-3 border-t border-amber-500/20 text-xs">
            <a href="{{ route('workflow.akuntansi', ['tab' => 'unapproved']) }}" class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-indigo-500/50 transition">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[11px] text-slate-400">Jurnal Belum Approve</span>
                    <i class="fas fa-stamp text-indigo-400"></i>
                </div>
                <div class="text-lg font-black {{ ($alertsSummary['unapproved_journals'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-300' }}">
                    {{ number_format($alertsSummary['unapproved_journals'] ?? 0) }}
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Draft belum masuk GL</div>
            </a>

            <a href="{{ route('workflow.akuntansi', ['tab' => 'missing_journals']) }}" class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-amber-500/50 transition">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[11px] text-slate-400">Transaksi Tanpa Jurnal</span>
                    <i class="fas fa-file-circle-exclamation text-amber-400"></i>
                </div>
                <div class="text-lg font-black {{ (($alertsSummary['unjournalized_raw'] ?? 0) + ($alertsSummary['unjournalized_sales'] ?? 0)) > 0 ? 'text-amber-400' : 'text-slate-300' }}">
                    {{ number_format(($alertsSummary['unjournalized_raw'] ?? 0) + ($alertsSummary['unjournalized_sales'] ?? 0)) }}
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">RAW &amp; Sales CUGIL</div>
            </a>

            <a href="{{ route('cugil.sales.index') }}" class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-emerald-500/50 transition">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[11px] text-slate-400">Sales Tanpa Timbangan</span>
                    <i class="fas fa-weight-scale text-emerald-400"></i>
                </div>
                <div class="text-lg font-black {{ ($alertsSummary['sales_no_timbangan'] ?? 0) > 0 ? 'text-amber-400' : 'text-slate-300' }}">
                    {{ number_format($alertsSummary['sales_no_timbangan'] ?? 0) }}
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Bukti timbangan fisik</div>
            </a>

            <a href="{{ route('pajak.index') }}" class="p-3 rounded-xl bg-slate-900/80 border border-slate-800 hover:border-rose-500/50 transition">
                <div class="flex items-center justify-between mb-1">
                    <span class="text-[11px] text-slate-400">Pajak Tertunda</span>
                    <i class="fas fa-shield-halved text-rose-400"></i>
                </div>
                <div class="text-lg font-black {{ (($alertsSummary['pajak_unpaid'] ?? 0) + ($alertsSummary['pajak_unreported'] ?? 0)) > 0 ? 'text-rose-400' : 'text-slate-300' }}">
                    {{ number_format(($alertsSummary['pajak_unpaid'] ?? 0) + ($alertsSummary['pajak_unreported'] ?? 0)) }}
                </div>
                <div class="text-[10px] text-slate-500 mt-0.5">Belum setor / lapor</div>
            </a>
        </div>
    </div>
    @endif

    {{-- ═══ MODUL CARDS ═══ --}}
    <div>
        <div class="flex items-center justify-between mb-3 px-1">
            <h3 class="text-sm font-bold text-white uppercase tracking-wider flex items-center gap-2">
                <i class="fas fa-cubes text-amber-400"></i>
                <span>Modul Bisnis &amp; Siklus Administrasi</span>
            </h3>
            <span class="text-xs text-slate-400">Pilih modul untuk melihat rincian dokumen &amp; tahapan</span>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
            @foreach($moduleSummaries as $moduleKey => $summary)
            @php
                $total = $summary['total'];
                $complete = $summary['complete'];
                $pct = $total > 0 ? round(($complete / $total) * 100) : 0;
                $colorMap = [
                    'indigo'  => ['border' => 'border-indigo-500/30', 'bg' => 'bg-indigo-500/10', 'text' => 'text-indigo-400', 'ring' => '#6366f1'],
                    'teal'    => ['border' => 'border-teal-500/30', 'bg' => 'bg-teal-500/10', 'text' => 'text-teal-400', 'ring' => '#14b8a6'],
                    'blue'    => ['border' => 'border-blue-500/30', 'bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'ring' => '#3b82f6'],
                    'amber'   => ['border' => 'border-amber-500/30', 'bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'ring' => '#f59e0b'],
                    'emerald' => ['border' => 'border-emerald-500/30', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'ring' => '#10b981'],
                    'purple'  => ['border' => 'border-purple-500/30', 'bg' => 'bg-purple-500/10', 'text' => 'text-purple-400', 'ring' => '#a855f7'],
                    'sky'     => ['border' => 'border-sky-500/30', 'bg' => 'bg-sky-500/10', 'text' => 'text-sky-400', 'ring' => '#0ea5e9'],
                    'rose'    => ['border' => 'border-rose-500/30', 'bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'ring' => '#f43f5e'],
                ];
                $c = $colorMap[$summary['color']] ?? $colorMap['blue'];
            @endphp
            <a href="{{ $moduleKey === 'akuntansi_jurnal' || $moduleKey === 'akuntansi_closing' ? route('workflow.akuntansi') : route('workflow.module', $moduleKey) }}"
               class="group block rounded-2xl border {{ $c['border'] }} bg-slate-900/60 p-4 hover:bg-slate-800/60 transition backdrop-blur-md shadow-lg relative overflow-hidden">
                <div class="flex items-center justify-between mb-3">
                    <div class="flex items-center gap-2.5 overflow-hidden">
                        <div class="h-9 w-9 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center text-sm shrink-0">
                            <i class="fas {{ $summary['icon'] }}"></i>
                        </div>
                        <div class="truncate">
                            <h4 class="text-xs font-bold text-white group-hover:text-amber-300 transition truncate">{{ $summary['label'] }}</h4>
                            <p class="text-[10px] text-slate-400">{{ $total }} dokumen terlacak</p>
                        </div>
                    </div>

                    {{-- Mini ring --}}
                    <div class="relative h-10 w-10 shrink-0">
                        <svg class="h-10 w-10 -rotate-90" viewBox="0 0 36 36">
                            <circle cx="18" cy="18" r="15.91" fill="none" stroke="currentColor" class="text-slate-700" stroke-width="3.5"/>
                            <circle cx="18" cy="18" r="15.91" fill="none" stroke="{{ $c['ring'] }}" stroke-width="3.5" stroke-linecap="round" stroke-dasharray="{{ $pct }},100"/>
                        </svg>
                        <div class="absolute inset-0 flex items-center justify-center">
                            <span class="text-[10px] font-black text-white">{{ $pct }}%</span>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-3 gap-1.5 text-center text-[10px] font-semibold">
                    <div class="bg-emerald-500/10 text-emerald-400 rounded-lg py-1">
                        <i class="fas fa-check"></i> {{ $complete }} Selesai
                    </div>
                    <div class="bg-amber-500/10 text-amber-400 rounded-lg py-1">
                        <i class="fas fa-spinner"></i> {{ $summary['in_progress'] }} Proses
                    </div>
                    <div class="{{ $summary['stuck'] > 0 ? 'bg-red-500/10 text-red-400' : 'bg-slate-800 text-slate-500' }} rounded-lg py-1">
                        <i class="fas fa-circle-exclamation"></i> {{ $summary['stuck'] }} Stuck
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>

    {{-- ═══ ALERT: LANGKAH TERLEWATKAN (SKIPPED WORKFLOWS) ═══ --}}
    @if(count($allSkipped) > 0)
    <div class="rounded-2xl border border-red-500/40 bg-red-950/30 p-5 shadow-lg">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-lg">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-red-300">⚠️ Peringatan: Langkah Administrasi Terlewatkan (Skipped Steps)!</h3>
                <p class="text-[11px] text-red-400/80">Dokumen berikut memiliki langkah lanjutan yang diselesaikan padahal langkah sebelumnya belum tuntas.</p>
            </div>
        </div>

        <div class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
            @foreach($allSkipped as $item)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-red-950/40 border border-red-500/20 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-{{ $item['module_color'] }}-500/20 text-{{ $item['module_color'] }}-400">
                        {{ $item['module_label'] }}
                    </span>
                    <span class="text-xs font-bold text-white font-mono">{{ $item['reference_code'] }}</span>
                </div>
                <div class="flex flex-wrap gap-1">
                    @foreach($item['missed_steps'] as $ms)
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full text-[10px] font-medium bg-red-500/20 text-red-300 border border-red-500/30">
                        <i class="fas fa-circle-xmark text-[8px]"></i> {{ $ms }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══ DAFTAR DOKUMEN BELUM SELESAI ═══ --}}
    @if(count($allIncomplete) > 0)
    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg backdrop-blur-md">
        <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                    <i class="fas fa-clipboard-list"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Dokumen dengan Workflow Belum Lengkap</h3>
                    <p class="text-[11px] text-slate-400">Diurutkan berdasarkan yang paling minim progres penyelesaiannya</p>
                </div>
            </div>
        </div>

        <div class="overflow-x-auto custom-scrollbar">
            <table class="w-full text-xs">
                <thead>
                    <tr class="border-b border-slate-700/60 text-slate-400 uppercase tracking-wider text-[10px]">
                        <th class="text-left py-2 px-3">Modul</th>
                        <th class="text-left py-2 px-3">No. Dokumen</th>
                        <th class="text-center py-2 px-3">Progres</th>
                        <th class="text-left py-2 px-3">Langkah Selanjutnya</th>
                        <th class="text-center py-2 px-3">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/50">
                    @foreach($allIncomplete as $doc)
                    @php $p = $doc['progress']; @endphp
                    <tr class="hover:bg-slate-800/40 transition">
                        <td class="py-2.5 px-3">
                            <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-{{ $doc['module_color'] }}-500/20 text-{{ $doc['module_color'] }}-400">
                                {{ $doc['module_label'] }}
                            </span>
                        </td>
                        <td class="py-2.5 px-3 font-bold text-white font-mono">{{ $doc['reference_code'] }}</td>
                        <td class="py-2.5 px-3 text-center">
                            <div class="inline-flex items-center gap-2">
                                <div class="w-20 h-1.5 bg-slate-700 rounded-full overflow-hidden">
                                    <div class="h-full rounded-full {{ $p['percentage'] >= 80 ? 'bg-emerald-500' : ($p['percentage'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $p['percentage'] }}%"></div>
                                </div>
                                <span class="text-[10px] font-bold {{ $p['percentage'] >= 80 ? 'text-emerald-400' : ($p['percentage'] >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                                    {{ $p['completed'] }}/{{ $p['total'] }}
                                </span>
                            </div>
                        </td>
                        <td class="py-2.5 px-3 text-slate-300">
                            <i class="fas fa-arrow-right text-[8px] text-amber-400 mr-1"></i>
                            {{ $p['current_step'] ?? '-' }}
                        </td>
                        <td class="py-2.5 px-3 text-center">
                            <a href="{{ $doc['module'] === 'akuntansi_jurnal' || $doc['module'] === 'akuntansi_closing' ? route('workflow.akuntansi') : route('workflow.module', $doc['module']) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500/20 text-amber-400 hover:bg-amber-500 hover:text-white transition">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ═══ SINKRONISASI DATA EKSISTING ═══ --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg backdrop-blur-md">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg">
                    <i class="fas fa-rotate"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white">Sinkronisasi Workflow Data Lama</h3>
                    <p class="text-[11px] text-slate-400">Generate checklist otomatis untuk dokumen yang diinput sebelum modul kontrol diaktifkan</p>
                </div>
            </div>

            <form method="POST" action="{{ route('workflow.seedAllExisting') }}" onsubmit="return confirm('Sinkronkan seluruh 8 modul data bisnis dan akuntansi sekaligus?')">
                @csrf
                <button type="submit" class="px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold transition flex items-center gap-2 shadow-lg shadow-sky-500/20">
                    <i class="fas fa-bolt"></i>
                    <span>Sync Seluruh Modul Sekaligus</span>
                </button>
            </form>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($moduleLabels as $mk => $ml)
            <form method="POST" action="{{ route('workflow.seedExisting') }}" class="inline" onsubmit="return confirm('Sinkronkan workflow untuk modul {{ $ml }}?')">
                @csrf
                <input type="hidden" name="module" value="{{ $mk }}">
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-xl text-[11px] font-semibold bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition">
                    <i class="fas fa-rotate text-sky-400 text-[10px]"></i>
                    {{ $ml }}
                </button>
            </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
