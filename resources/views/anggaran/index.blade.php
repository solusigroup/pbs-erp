@extends('layouts.admin')

@section('title', 'Approval Anggaran & Pengajuan Kas - PT PBS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white">Pengajuan Dana & Approval Anggaran</h2>
            <p class="text-xs text-slate-400 mt-0.5">Pengendalian arus keluar kas & verifikasi anggaran operasional PT Pinastika Bhakti Semesta</p>
        </div>
        @if(auth()->user()->canMutate())
        <button 
            type="button" 
            onclick="document.getElementById('modalTambahPengajuan').classList.toggle('hidden')"
            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition flex items-center gap-2 self-start"
        >
            <i class="fas fa-plus"></i>
            <span>Buat Pengajuan Dana Baru</span>
        </button>
        @else
        <span class="px-3 py-1.5 rounded-xl bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5 self-start">
            <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
        </span>
        @endif
    </div>

    <!-- 3 Summary Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60">
            <span class="text-xs text-slate-400 block">Total Nominal Diajukan</span>
            <strong class="text-xl font-black text-white block mt-1">Rp {{ number_format($totalDiajukan, 0, ',', '.') }}</strong>
        </div>
        <div class="p-4 rounded-2xl border border-emerald-500/30 bg-emerald-500/5">
            <span class="text-xs text-emerald-300 block">Total Disetujui BOD (Cair)</span>
            <strong class="text-xl font-black text-emerald-400 block mt-1">Rp {{ number_format($totalDisetujui, 0, ',', '.') }}</strong>
        </div>
        <div class="p-4 rounded-2xl border border-amber-500/30 bg-amber-500/5">
            <span class="text-xs text-amber-300 block">Antrean Menunggu Approval BOD</span>
            <strong class="text-xl font-black text-amber-400 block mt-1">{{ $totalMenunggu }} Berkas</strong>
        </div>
    </div>

    <!-- Modal Form Buat Pengajuan Dana -->
    <div id="modalTambahPengajuan" class="hidden rounded-3xl border border-amber-500/30 bg-slate-900/95 p-6 backdrop-blur-xl shadow-2xl">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-amber-400"></i>
                <span>Form Pengajuan Kas / Anggaran Operasional</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahPengajuan').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('anggaran.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Pengajuan</label>
                    <input type="text" name="nomor_pengajuan" value="REQ-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Pengajuan</label>
                    <input type="date" name="tanggal_pengajuan" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pemohon</label>
                    <input type="text" name="pemohon" value="{{ auth()->user()->name }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Departemen</label>
                    <select name="departemen" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        <option value="Finance & Tax">Finance & Tax</option>
                        <option value="Operations">Operations / Lapangan</option>
                        <option value="General Affair & HR">General Affair & HR</option>
                        <option value="IT & Infrastructure">IT & Infrastructure</option>
                        <option value="Management">Management</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori Biaya</label>
                    <input type="text" name="kategori_biaya" required placeholder="Operasional, Perjalanan Dinas, Pajak, Capex" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nominal Diajukan (Rp)</label>
                    <input type="number" step="0.01" name="nominal_diajukan" required placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Rincian Keperluan Penggunaan Dana</label>
                <textarea name="keperluan" rows="2" required placeholder="Jelaskan secara spesifik urgensi dan rincian alokasi anggaran..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs"></textarea>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahPengajuan').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow">Ajukan ke BOD Finance</button>
            </div>
        </form>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 text-xs">
        <a href="{{ route('anggaran.index') }}" class="px-3 py-1.5 rounded-lg border {{ !$statusFilter ? 'bg-amber-500 border-amber-500 text-white font-bold' : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-800' }}">Semua Pengajuan</a>
        @foreach(['Menunggu Approval' => 'Menunggu Approval', 'Disetujui BOD' => 'Disetujui BOD', 'Ditolak' => 'Ditolak'] as $st => $lbl)
            <a href="{{ route('anggaran.index', ['status' => $st]) }}" class="px-3 py-1.5 rounded-lg border {{ $statusFilter === $st ? 'bg-amber-500 border-amber-500 text-white font-bold' : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-800' }}">
                {{ $lbl }}
            </a>
        @endforeach
    </div>

    <!-- Requests Table -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">No & Tanggal</th>
                        <th class="py-3 px-4">Pemohon & Divisi</th>
                        <th class="py-3 px-4">Keperluan & Kategori</th>
                        <th class="py-3 px-4 text-right">Nominal Diajukan</th>
                        <th class="py-3 px-4 text-right">Nominal Disetujui</th>
                        <th class="py-3 px-4 text-center">Status BOD</th>
                        <th class="py-3 px-4 text-center">Tindakan BOD</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($pengajuans as $p)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-amber-400 font-mono block">{{ $p->nomor_pengajuan }}</span>
                                <span class="text-[10px] text-slate-400">{{ $p->tanggal_pengajuan->format('d/m/Y') }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <strong class="text-white block">{{ $p->pemohon }}</strong>
                                <span class="text-[10px] text-slate-400">{{ $p->departemen }}</span>
                            </td>
                            <td class="py-3 px-4 max-w-[250px]">
                                <span class="text-slate-200 block truncate">{{ $p->keperluan }}</span>
                                <span class="text-[10px] text-amber-400 font-medium">Kat: {{ $p->kategori_biaya }}</span>
                                @if($p->catatan_bod)
                                    <p class="text-[10px] text-slate-400 italic mt-0.5">Catatan BOD: "{{ $p->catatan_bod }}"</p>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-medium text-white">
                                Rp {{ number_format($p->nominal_diajukan, 0, ',', '.') }}
                            </td>
                            <td class="py-3 px-4 text-right font-mono font-bold {{ $p->nominal_disetujui > 0 ? 'text-emerald-400' : 'text-slate-500' }}">
                                Rp {{ number_format($p->nominal_disetujui, 0, ',', '.') }}
                                @if($p->metode_pencairan)
                                    <span class="block text-[9px] text-slate-400 font-normal">({{ $p->metode_pencairan }})</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold {{ $p->status === 'Disetujui BOD' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : ($p->status === 'Ditolak' ? 'bg-rose-500/10 text-rose-400 border border-rose-500/20' : 'bg-amber-500/10 text-amber-400 border border-amber-500/20') }}">
                                    {{ $p->status }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if($p->status === 'Menunggu Approval')
                                    @if(auth()->user()->canMutate())
                                    <div class="flex items-center justify-center gap-1.5">
                                        <!-- Fast Approve Form -->
                                        <form method="POST" action="{{ route('anggaran.approve', $p->id) }}">
                                            @csrf
                                            <input type="hidden" name="nominal_disetujui" value="{{ $p->nominal_diajukan }}">
                                            <input type="hidden" name="metode_pencairan" value="Transfer Mandiri">
                                            <button type="submit" title="Setujui Penuh" class="px-2.5 py-1 rounded-lg bg-emerald-500 hover:bg-emerald-600 text-white text-[10px] font-bold">
                                                <i class="fas fa-check"></i> Setujui
                                            </button>
                                        </form>

                                        <!-- Fast Reject Form -->
                                        <form method="POST" action="{{ route('anggaran.reject', $p->id) }}" onsubmit="return confirm('Tolak pengajuan dana ini?')">
                                            @csrf
                                            <input type="hidden" name="catatan_bod" value="Ditolak oleh BOD Finance (efisiensi anggaran)">
                                            <button type="submit" title="Tolak" class="px-2.5 py-1 rounded-lg bg-rose-500/20 hover:bg-rose-500 text-rose-300 hover:text-white text-[10px] font-bold border border-rose-500/30">
                                                <i class="fas fa-times"></i>
                                            </button>
                                        </form>
                                    </div>
                                    @else
                                    <span class="text-[10px] text-amber-400/80 font-medium italic">Menunggu Review BOD</span>
                                    @endif
                                @else
                                    <span class="text-[10px] text-slate-500 italic">Selesai direview</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-8 text-center text-slate-500 italic">Belum ada pengajuan dana.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $pengajuans->links() }}
        </div>
    </div>
</div>
@endsection
