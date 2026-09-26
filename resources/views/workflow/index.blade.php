@extends('layouts.admin')

@section('title', 'Kontrol Workflow — PBS-ERP')

@section('content')
<div class="space-y-6">

    {{-- ═══ HEADER BANNER ═══ --}}
    <div class="relative overflow-hidden rounded-3xl border border-amber-500/30 bg-gradient-to-r from-slate-900 via-[#101d33] to-slate-900 p-6 sm:p-8 shadow-2xl backdrop-blur-xl">
        <div class="absolute -right-20 -top-20 h-64 w-64 rounded-full bg-amber-500/10 blur-3xl pointer-events-none"></div>
        <div class="absolute -left-20 -bottom-20 h-48 w-48 rounded-full bg-blue-500/10 blur-3xl pointer-events-none"></div>

        <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-4 relative z-10">
            <div>
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-500/20 text-amber-400 border border-amber-500/30 mb-2">
                    <i class="fas fa-route text-[10px]"></i>
                    <span>Kontrol Workflow &bull; Pencegahan Kelalaian Administrasi</span>
                </div>
                <h2 class="text-xl sm:text-2xl font-black text-white">Kontrol Workflow Bisnis PT PBS</h2>
                <p class="text-xs sm:text-sm text-slate-300 mt-1">
                    Pantau dan pastikan setiap langkah administrasi terlaksana &mdash; tidak ada alur yang terlewatkan.
                </p>
            </div>

            {{-- Global Compliance Ring --}}
            <div class="flex items-center gap-5 bg-slate-800/50 border border-slate-700/60 rounded-2xl px-5 py-4">
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
                        <span class="text-amber-400 font-semibold">{{ number_format($pendingChecklists) }}</span> langkah masih tertunda
                    </p>
                </div>
            </div>
        </div>
    </div>

    {{-- ═══ MODUL CARDS ═══ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
        @foreach($moduleSummaries as $moduleKey => $summary)
        @php
            $total = $summary['total'];
            $complete = $summary['complete'];
            $pct = $total > 0 ? round(($complete / $total) * 100) : 0;
            $colorMap = [
                'blue' => ['border' => 'border-blue-500/30', 'bg' => 'bg-blue-500/10', 'text' => 'text-blue-400', 'ring' => '#3b82f6'],
                'amber' => ['border' => 'border-amber-500/30', 'bg' => 'bg-amber-500/10', 'text' => 'text-amber-400', 'ring' => '#f59e0b'],
                'emerald' => ['border' => 'border-emerald-500/30', 'bg' => 'bg-emerald-500/10', 'text' => 'text-emerald-400', 'ring' => '#10b981'],
                'purple' => ['border' => 'border-purple-500/30', 'bg' => 'bg-purple-500/10', 'text' => 'text-purple-400', 'ring' => '#a855f7'],
                'sky' => ['border' => 'border-sky-500/30', 'bg' => 'bg-sky-500/10', 'text' => 'text-sky-400', 'ring' => '#0ea5e9'],
                'rose' => ['border' => 'border-rose-500/30', 'bg' => 'bg-rose-500/10', 'text' => 'text-rose-400', 'ring' => '#f43f5e'],
            ];
            $c = $colorMap[$summary['color']] ?? $colorMap['blue'];
        @endphp
        <a href="{{ route('workflow.module', $moduleKey) }}"
           class="group block rounded-2xl border {{ $c['border'] }} bg-slate-900/60 p-5 hover:bg-slate-800/60 transition backdrop-blur-md shadow-lg">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl {{ $c['bg'] }} {{ $c['text'] }} flex items-center justify-center text-base">
                        <i class="fas {{ $summary['icon'] }}"></i>
                    </div>
                    <div>
                        <h3 class="text-sm font-bold text-white group-hover:text-amber-300 transition">{{ $summary['label'] }}</h3>
                        <p class="text-[10px] text-slate-400">{{ $total }} dokumen terlacak</p>
                    </div>
                </div>

                {{-- Mini ring --}}
                <div class="relative h-12 w-12 shrink-0">
                    <svg class="h-12 w-12 -rotate-90" viewBox="0 0 36 36">
                        <circle cx="18" cy="18" r="15.91" fill="none" stroke="currentColor" class="text-slate-700" stroke-width="3.5"/>
                        <circle cx="18" cy="18" r="15.91" fill="none" stroke="{{ $c['ring'] }}" stroke-width="3.5" stroke-linecap="round" stroke-dasharray="{{ $pct }},100"/>
                    </svg>
                    <div class="absolute inset-0 flex items-center justify-center">
                        <span class="text-[11px] font-black text-white">{{ $pct }}%</span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-2 text-center text-[10px] font-semibold">
                <div class="bg-emerald-500/10 text-emerald-400 rounded-lg py-1.5">
                    <i class="fas fa-check-circle"></i> {{ $complete }} Selesai
                </div>
                <div class="bg-amber-500/10 text-amber-400 rounded-lg py-1.5">
                    <i class="fas fa-spinner"></i> {{ $summary['in_progress'] }} Proses
                </div>
                <div class="{{ $summary['stuck'] > 0 ? 'bg-red-500/10 text-red-400' : 'bg-slate-700/50 text-slate-500' }} rounded-lg py-1.5">
                    <i class="fas fa-circle-exclamation"></i> {{ $summary['stuck'] }} Stuck
                </div>
            </div>
        </a>
        @endforeach
    </div>

    {{-- ═══ ALERT: LANGKAH TERLEWATKAN ═══ --}}
    @if(count($allSkipped) > 0)
    <div class="rounded-2xl border border-red-500/40 bg-red-950/30 p-5 shadow-lg">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl bg-red-500/20 text-red-400 flex items-center justify-center text-lg">
                <i class="fas fa-triangle-exclamation"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-red-300">⚠️ Peringatan: Langkah Administrasi Terlewatkan!</h3>
                <p class="text-[11px] text-red-400/80">Dokumen berikut memiliki langkah yang di-skip — beresiko audit finding.</p>
            </div>
        </div>

        <div class="space-y-2 max-h-64 overflow-y-auto custom-scrollbar">
            @foreach($allSkipped as $item)
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 bg-red-950/40 border border-red-500/20 rounded-xl px-4 py-3">
                <div class="flex items-center gap-3">
                    <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-bold uppercase bg-{{ $item['module_color'] }}-500/20 text-{{ $item['module_color'] }}-400">
                        {{ $item['module_label'] }}
                    </span>
                    <span class="text-xs font-bold text-white">{{ $item['reference_code'] }}</span>
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
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl bg-amber-500/10 text-amber-400 flex items-center justify-center text-lg">
                <i class="fas fa-clipboard-list"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Dokumen dengan Workflow Belum Lengkap</h3>
                <p class="text-[11px] text-slate-400">Prioritas dari yang paling minim progres</p>
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
                        <td class="py-2.5 px-3 font-bold text-white">{{ $doc['reference_code'] }}</td>
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
                            <a href="{{ route('workflow.module', $doc['module']) }}" class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-[10px] font-bold bg-amber-500/20 text-amber-400 hover:bg-amber-500 hover:text-white transition">
                                <i class="fas fa-eye"></i> Detail
                            </a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <div class="rounded-2xl border border-emerald-500/30 bg-emerald-950/20 p-6 text-center">
        <i class="fas fa-check-double text-4xl text-emerald-400 mb-3"></i>
        <h3 class="text-lg font-bold text-emerald-300">Semua Workflow Tuntas!</h3>
        <p class="text-sm text-emerald-400/80">Tidak ada dokumen dengan langkah administrasi tertunda. Excellent!</p>
    </div>
    @endif

    {{-- ═══ SINKRONISASI DATA LAMA ═══ --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg backdrop-blur-md">
        <div class="flex items-center gap-3 mb-4">
            <div class="h-10 w-10 rounded-xl bg-sky-500/10 text-sky-400 flex items-center justify-center text-lg">
                <i class="fas fa-rotate"></i>
            </div>
            <div>
                <h3 class="text-sm font-bold text-white">Sinkronisasi Workflow Data Lama</h3>
                <p class="text-[11px] text-slate-400">Buat workflow checklist untuk dokumen yang sudah ada sebelum fitur ini diaktifkan</p>
            </div>
        </div>

        <div class="flex flex-wrap gap-2">
            @foreach($moduleLabels as $mk => $ml)
            <form method="POST" action="{{ route('workflow.seedExisting') }}" class="inline" onsubmit="return confirm('Sinkronkan workflow untuk semua dokumen di modul {{ $ml }}?')">
                @csrf
                <input type="hidden" name="module" value="{{ $mk }}">
                <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl text-[11px] font-bold bg-slate-800 hover:bg-sky-600 text-slate-300 hover:text-white border border-slate-700 hover:border-sky-500 transition">
                    <i class="fas fa-rotate text-sky-400"></i>
                    Sync {{ $ml }}
                </button>
            </form>
            @endforeach
        </div>
    </div>
</div>
@endsection
