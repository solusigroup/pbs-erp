@extends('layouts.admin')

@section('title', $moduleLabel . ' — Kontrol Workflow')

@section('content')
<div class="space-y-6">

    {{-- ═══ HEADER ═══ --}}
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-4">
            <a href="{{ route('workflow.index') }}" class="h-10 w-10 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 hover:text-white flex items-center justify-center transition border border-slate-700">
                <i class="fas fa-arrow-left"></i>
            </a>
            <div>
                <div class="flex items-center gap-2 mb-1">
                    <div class="h-8 w-8 rounded-lg bg-{{ $moduleColor }}-500/20 text-{{ $moduleColor }}-400 flex items-center justify-center text-sm">
                        <i class="fas {{ $moduleIcon }}"></i>
                    </div>
                    <h2 class="text-lg font-black text-white">{{ $moduleLabel }}</h2>
                </div>
                <p class="text-xs text-slate-400">
                    <span class="text-white font-bold">{{ $summary['total'] }}</span> dokumen &bull;
                    <span class="text-emerald-400 font-semibold">{{ $summary['complete'] }}</span> selesai &bull;
                    <span class="text-amber-400 font-semibold">{{ $summary['in_progress'] }}</span> proses &bull;
                    <span class="{{ $summary['stuck'] > 0 ? 'text-red-400' : 'text-slate-500' }} font-semibold">{{ $summary['stuck'] }}</span> stuck
                </p>
            </div>
        </div>

        {{-- Status Filter --}}
        <div class="flex items-center gap-2">
            <a href="{{ route('workflow.module', $module) }}"
               class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition {{ !$statusFilter ? 'bg-amber-500 text-white' : 'bg-slate-800 text-slate-300 hover:text-white border border-slate-700' }}">
                Semua
            </a>
            <a href="{{ route('workflow.module', ['module' => $module, 'status' => 'incomplete']) }}"
               class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition {{ $statusFilter === 'incomplete' ? 'bg-red-500 text-white' : 'bg-slate-800 text-slate-300 hover:text-white border border-slate-700' }}">
                <i class="fas fa-clock mr-1"></i> Belum Selesai
            </a>
            <a href="{{ route('workflow.module', ['module' => $module, 'status' => 'complete']) }}"
               class="px-3 py-1.5 rounded-lg text-[11px] font-bold transition {{ $statusFilter === 'complete' ? 'bg-emerald-500 text-white' : 'bg-slate-800 text-slate-300 hover:text-white border border-slate-700' }}">
                <i class="fas fa-check-double mr-1"></i> Selesai
            </a>
        </div>
    </div>

    {{-- ═══ WORKFLOW TEMPLATE (Definisi langkah) ═══ --}}
    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-5 shadow-lg backdrop-blur-md">
        <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">
            <i class="fas fa-route text-{{ $moduleColor }}-400 mr-1"></i> Template Alur Workflow
        </h3>
        <div class="flex flex-wrap items-center gap-1">
            @foreach($definitions as $i => $def)
            <div class="inline-flex items-center gap-1.5 px-2.5 py-1.5 rounded-lg bg-slate-800/60 border border-slate-700/50">
                <span class="h-5 w-5 rounded-full bg-{{ $moduleColor }}-500/30 text-{{ $moduleColor }}-400 flex items-center justify-center text-[10px] font-bold">{{ $def->step_order }}</span>
                <span class="text-[11px] font-semibold text-slate-200">{{ $def->step_name }}</span>
                @if($def->required_role)
                <span class="text-[9px] font-bold uppercase text-amber-400 bg-amber-500/20 px-1.5 py-0.5 rounded-full">{{ $def->required_role }}</span>
                @endif
            </div>
            @if($i < count($definitions) - 1)
            <i class="fas fa-chevron-right text-[8px] text-slate-600"></i>
            @endif
            @endforeach
        </div>
    </div>

    {{-- ═══ SKIPPED ALERT ═══ --}}
    @if(count($skipped) > 0)
    <div class="rounded-2xl border border-red-500/40 bg-red-950/30 p-4 shadow-lg">
        <h3 class="text-xs font-bold text-red-300 mb-2">
            <i class="fas fa-triangle-exclamation mr-1"></i> {{ count($skipped) }} dokumen memiliki langkah terlewatkan!
        </h3>
        <div class="space-y-1.5">
            @foreach($skipped as $sk)
            <div class="flex flex-wrap items-center gap-2 text-[11px]">
                <span class="font-bold text-white">{{ $sk['reference_code'] }}:</span>
                @foreach($sk['missed_steps'] as $ms)
                <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full bg-red-500/20 text-red-300 border border-red-500/30 text-[10px]">
                    <i class="fas fa-circle-xmark text-[8px]"></i> {{ $ms }}
                </span>
                @endforeach
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- ═══ DAFTAR DOKUMEN ═══ --}}
    @if(count($documentList) === 0)
    <div class="rounded-2xl border border-slate-800 bg-slate-900/60 p-8 text-center">
        <i class="fas fa-inbox text-4xl text-slate-600 mb-3"></i>
        <h3 class="text-base font-bold text-slate-400">Belum Ada Workflow</h3>
        <p class="text-xs text-slate-500 mt-1">Gunakan tombol "Sync" di halaman utama workflow untuk menginisialisasi data.</p>
    </div>
    @else
    <div class="space-y-4">
        @foreach($documentList as $doc)
        @php $p = $doc['progress']; @endphp
        <div class="rounded-2xl border {{ $p['is_complete'] ? 'border-emerald-500/30 bg-emerald-950/10' : 'border-slate-800 bg-slate-900/60' }} p-5 shadow-lg backdrop-blur-md">

            {{-- Document Header --}}
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-10 w-10 rounded-xl {{ $p['is_complete'] ? 'bg-emerald-500/20 text-emerald-400' : 'bg-amber-500/20 text-amber-400' }} flex items-center justify-center text-lg">
                        <i class="fas {{ $p['is_complete'] ? 'fa-check-double' : 'fa-file-circle-check' }}"></i>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-white">{{ $doc['reference_code'] }}</h4>
                        <p class="text-[10px] text-slate-400">
                            @if($p['is_complete'])
                            <span class="text-emerald-400 font-semibold"><i class="fas fa-check-circle mr-0.5"></i> Semua langkah selesai</span>
                            @else
                            Selanjutnya: <span class="text-amber-300 font-semibold">{{ $p['current_step'] ?? '-' }}</span>
                            @endif
                        </p>
                    </div>
                </div>

                {{-- Progress bar --}}
                <div class="flex items-center gap-3">
                    <div class="w-32 h-2 bg-slate-700 rounded-full overflow-hidden">
                        <div class="h-full rounded-full transition-all duration-500 {{ $p['percentage'] >= 80 ? 'bg-emerald-500' : ($p['percentage'] >= 50 ? 'bg-amber-500' : 'bg-red-500') }}" style="width:{{ $p['percentage'] }}%"></div>
                    </div>
                    <span class="text-xs font-bold {{ $p['percentage'] >= 80 ? 'text-emerald-400' : ($p['percentage'] >= 50 ? 'text-amber-400' : 'text-red-400') }}">
                        {{ $p['percentage'] }}%
                    </span>
                </div>
            </div>

            {{-- Steps Timeline --}}
            <div class="relative">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-2">
                    @foreach($doc['steps'] as $step)
                    <div class="flex items-start gap-3 p-3 rounded-xl {{ $step->is_completed ? 'bg-emerald-500/5 border border-emerald-500/20' : 'bg-slate-800/40 border border-slate-700/40' }}">
                        {{-- Step number --}}
                        <div class="h-7 w-7 rounded-full shrink-0 flex items-center justify-center text-[10px] font-bold {{ $step->is_completed ? 'bg-emerald-500 text-white' : 'bg-slate-700 text-slate-400 border border-slate-600' }}">
                            @if($step->is_completed)
                            <i class="fas fa-check"></i>
                            @else
                            {{ $step->step_order }}
                            @endif
                        </div>

                        <div class="flex-1 min-w-0">
                            <p class="text-[11px] font-bold {{ $step->is_completed ? 'text-emerald-300' : 'text-slate-200' }} truncate">
                                {{ $step->step_name }}
                            </p>
                            @if($step->is_completed)
                            <p class="text-[9px] text-emerald-400/70 mt-0.5">
                                <i class="fas fa-user-check mr-0.5"></i> {{ $step->completed_by }}
                                &bull; {{ $step->completed_at?->diffForHumans() }}
                            </p>
                            @if($step->notes)
                            <p class="text-[9px] text-slate-400 mt-0.5 italic">"{{ Str::limit($step->notes, 50) }}"</p>
                            @endif
                            @endif

                            {{-- Action buttons --}}
                            <div class="mt-1.5 flex items-center gap-1">
                                @if(!$step->is_completed)
                                <form method="POST" action="{{ route('workflow.completeStep') }}" class="inline">
                                    @csrf
                                    <input type="hidden" name="module" value="{{ $module }}">
                                    <input type="hidden" name="reference_id" value="{{ $doc['reference_id'] }}">
                                    <input type="hidden" name="step_code" value="{{ $step->step_code }}">
                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-emerald-500/20 text-emerald-400 hover:bg-emerald-500 hover:text-white transition" title="Tandai selesai">
                                        <i class="fas fa-check"></i> Selesai
                                    </button>
                                </form>
                                @else
                                @if(auth()->check() && (auth()->user()->isBOD() || auth()->user()->isAdmin()))
                                <form method="POST" action="{{ route('workflow.uncompleteStep') }}" class="inline" onsubmit="return confirm('Batalkan penyelesaian langkah ini?')">
                                    @csrf
                                    <input type="hidden" name="module" value="{{ $module }}">
                                    <input type="hidden" name="reference_id" value="{{ $doc['reference_id'] }}">
                                    <input type="hidden" name="step_code" value="{{ $step->step_code }}">
                                    <button type="submit" class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[9px] font-bold bg-slate-700/50 text-slate-400 hover:bg-red-500/20 hover:text-red-300 transition" title="Undo">
                                        <i class="fas fa-rotate-left"></i> Undo
                                    </button>
                                </form>
                                @endif
                                @endif
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endforeach
    </div>
    @endif
</div>
@endsection
