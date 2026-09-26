@extends('layouts.admin')

@section('title', 'Pusat Audit Kelalaian Administrasi — PBS-ERP')

@section('content')
<div class="space-y-6">

    {{-- ═══ HEADER BANNER ═══ --}}
    <div class="relative overflow-hidden rounded-3xl border border-rose-500/30 bg-gradient-to-r from-slate-900 via-[#261318] to-slate-900 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-rose-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-48 w-48 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-500/20 text-rose-400 border border-rose-500/30 mb-2">
                    <i class="fas fa-shield-halved text-[10px]"></i>
                    <span>Integritas Bisnis &bull; Pencegahan Temuan Audit &amp; Kebocoran Finansial</span>
                </div>
                <h2 class="text-xl sm:text-3xl font-black text-white tracking-tight">Pusat Audit Celah Kelalaian Administrasi</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1 max-w-2xl">
                    Diagnosa otomatis mendeteksi setiap tahapan transaksi bisnis dan akuntansi yang terlewat, belum lunas, belum disetor pajaknya, atau belum di-approve.
                </p>

                <div class="flex flex-wrap items-center gap-2 mt-4 text-xs">
                    <a href="{{ route('workflow.index') }}" class="px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white border border-slate-700 transition flex items-center gap-1.5">
                        <i class="fas fa-arrow-left text-xs"></i>
                        <span>Kembali ke Overview Workflow</span>
                    </a>
                    <a href="{{ route('workflow.akuntansi') }}" class="px-3.5 py-1.5 rounded-xl bg-indigo-600/30 hover:bg-indigo-600 text-indigo-300 hover:text-white border border-indigo-500/30 transition flex items-center gap-1.5">
                        <i class="fas fa-stamp text-xs"></i>
                        <span>Pusat Kontrol Akuntansi</span>
                    </a>
                </div>
            </div>

            <div class="flex items-center gap-4 bg-slate-800/60 border border-slate-700/60 rounded-2xl px-5 py-4 shrink-0 shadow-lg">
                <div class="h-14 w-14 rounded-2xl bg-rose-500/20 text-rose-400 flex items-center justify-center text-2xl border border-rose-500/30 shrink-0">
                    <i class="fas fa-triangle-exclamation"></i>
                </div>
                <div>
                    <p class="text-xs font-bold uppercase tracking-wider text-slate-400">Total Anomali Terdeteksi</p>
                    <p class="text-2xl font-black text-rose-400">{{ count($adminGaps) }} Kategori</p>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        {{ $alertsSummary['total_alerts'] }} item dokumen memerlukan tindak lanjut
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ ACTIVE GAPS DIAGNOSTIC CARDS ═══ --}}
    <div>
        <h3 class="text-sm font-bold text-white uppercase tracking-wider mb-3 px-1 flex items-center gap-2">
            <i class="fas fa-list-check text-rose-400"></i>
            <span>Daftar Diagnosa Kelalaian &amp; Resiko Administrasi</span>
        </h3>

        @if(empty($adminGaps))
        <div class="p-8 rounded-3xl border border-emerald-500/30 bg-emerald-950/20 text-center space-y-2">
            <i class="fas fa-check-circle text-4xl text-emerald-400"></i>
            <h4 class="text-base font-bold text-white">Sempurna! Tidak Ditemukan Celah Administrasi</h4>
            <p class="text-xs text-slate-300">Seluruh dokumen operasional, perpajakan, dan akuntansi tertib dan lengkap sesuai SOP PT PBS.</p>
        </div>
        @else
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach($adminGaps as $gap)
            @php
                $isCritical = $gap['severity'] === 'critical';
                $isWarning = $gap['severity'] === 'warning';
                $borderClass = $isCritical ? 'border-rose-500/40 bg-rose-950/20' : ($isWarning ? 'border-amber-500/40 bg-amber-950/20' : 'border-blue-500/40 bg-blue-950/20');
                $badgeClass = $isCritical ? 'bg-rose-500/20 text-rose-400 border border-rose-500/30' : ($isWarning ? 'bg-amber-500/20 text-amber-400 border border-amber-500/30' : 'bg-blue-500/20 text-blue-400 border border-blue-500/30');
            @endphp
            <div class="rounded-2xl border {{ $borderClass }} p-5 shadow-lg flex flex-col justify-between">
                <div>
                    <div class="flex items-center justify-between gap-2 mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $badgeClass }}">
                            {{ $gap['category'] }} &bull; {{ strtoupper($gap['severity']) }}
                        </span>
                        <span class="text-lg font-black text-white font-mono">
                            {{ $gap['count'] }} Dokumen
                        </span>
                    </div>

                    <h4 class="text-sm font-bold text-white mb-1.5">{{ $gap['title'] }}</h4>
                    <p class="text-xs text-slate-300 leading-relaxed">{{ $gap['description'] }}</p>
                </div>

                <div class="pt-4 mt-4 border-t border-slate-800/60 flex items-center justify-between">
                    <span class="text-[11px] text-slate-400">Resiko audit &amp; integritas buku</span>
                    <a href="{{ $gap['action_route'] }}" class="inline-flex items-center gap-1.5 px-3.5 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-white text-xs font-bold border border-slate-700 hover:border-amber-500/50 transition shadow-sm">
                        <span>{{ $gap['action_label'] }}</span>
                        <i class="fas fa-arrow-right text-[10px] text-amber-400"></i>
                    </a>
                </div>
            </div>
            @endforeach
        </div>
        @endif
    </div>

    {{-- ═══ LANGKAH TERLEWATKAN (SKIPPED STEPS) ═══ --}}
    @if(count($allSkipped) > 0)
    <div class="rounded-2xl border border-red-500/40 bg-red-950/20 p-5 shadow-lg space-y-3">
        <div class="flex items-center gap-3">
            <div class="h-10 w-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-lg shrink-0">
                <i class="fas fa-forward-step"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Dokumen yang Melompati Tahapan SOP (Skipped Step Log)</h3>
                <p class="text-[11px] text-slate-400">Daftar transaksi yang diselesaikan sebagian tanpa menyelesaikan tahap awal yang wajib</p>
            </div>
        </div>

        <div class="space-y-2 max-h-80 overflow-y-auto custom-scrollbar">
            @foreach($allSkipped as $item)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-slate-900/80 border border-slate-800 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-{{ $item['module_color'] }}-500/20 text-{{ $item['module_color'] }}-400">
                        {{ $item['module_label'] }}
                    </span>
                    <span class="text-xs font-mono font-bold text-white">{{ $item['reference_code'] }}</span>
                </div>
                <div class="flex flex-wrap gap-1.5">
                    @foreach($item['missed_steps'] as $ms)
                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-medium bg-rose-500/20 text-rose-300 border border-rose-500/30">
                        <i class="fas fa-xmark text-[9px]"></i> {{ $ms }}
                    </span>
                    @endforeach
                </div>
            </div>
            @endforeach
        </div>
    </div>
    @endif

</div>
@endsection
