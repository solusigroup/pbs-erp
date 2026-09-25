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
    <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
        <form method="GET" action="{{ route('akuntansi.jurnal') }}" class="grid grid-cols-1 sm:grid-cols-4 lg:grid-cols-5 gap-3 text-xs">
            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Dari</label>
                <input type="date" name="tanggal_dari" value="{{ $tanggalDari ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Tanggal Sampai</label>
                <input type="date" name="tanggal_sampai" value="{{ $tanggalSampai ?? '' }}" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div>
                <label class="block text-[11px] text-slate-400 font-medium mb-1">Record per Halaman</label>
                <select name="per_page" onchange="this.form.submit()" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white font-semibold">
                    <option value="15" {{ ($perPage ?? 15) == 15 ? 'selected' : '' }}>15 Record</option>
                    <option value="30" {{ ($perPage ?? 15) == 30 ? 'selected' : '' }}>30 Record</option>
                    <option value="50" {{ ($perPage ?? 15) == 50 ? 'selected' : '' }}>50 Record</option>
                    <option value="100" {{ ($perPage ?? 15) == 100 ? 'selected' : '' }}>100 Record</option>
                    <option value="500" {{ ($perPage ?? 15) == 500 ? 'selected' : '' }}>500 Record</option>
                </select>
            </div>

            <div class="sm:col-span-1 lg:col-span-2 flex items-end gap-2">
                <div class="flex-1">
                    <label class="block text-[11px] text-slate-400 font-medium mb-1">Pencarian</label>
                    <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="No Transaksi / Deskripsi..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
                </div>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold transition flex items-center gap-1.5 h-[38px]">
                    <i class="fas fa-filter"></i>
                    <span>Filter</span>
                </button>
            </div>
        </form>
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

        <form method="POST" action="{{ route('akuntansi.storeJurnal') }}" class="space-y-4 text-xs">
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
            <div class="pt-2">
                <span class="block text-amber-400 font-bold uppercase tracking-wider mb-2">Baris Akun (Debit &amp; Kredit)</span>
                
                <div class="space-y-2">
                    <!-- Line 1: Debit -->
                    <div class="grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800">
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Akun Debit</label>
                            <select name="details[0][kode_akun]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akuns as $ak)
                                    <option value="{{ $ak->kode_akun }}">{{ $ak->kode_akun }} - {{ $ak->nama_akun }} ({{ $ak->kategori }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Debit (Rp)</label>
                            <input type="number" step="0.01" name="details[0][debit]" value="0" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Kredit (Rp)</label>
                            <input type="number" step="0.01" name="details[0][kredit]" value="0" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono">
                        </div>
                    </div>

                    <!-- Line 2: Kredit -->
                    <div class="grid grid-cols-12 gap-2 bg-slate-950/60 p-3 rounded-xl border border-slate-800">
                        <div class="col-span-12 sm:col-span-6">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Akun Kredit</label>
                            <select name="details[1][kode_akun]" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white">
                                <option value="">-- Pilih Akun --</option>
                                @foreach($akuns as $ak)
                                    <option value="{{ $ak->kode_akun }}">{{ $ak->kode_akun }} - {{ $ak->nama_akun }} ({{ $ak->kategori }})</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Debit (Rp)</label>
                            <input type="number" step="0.01" name="details[1][debit]" value="0" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono">
                        </div>
                        <div class="col-span-6 sm:col-span-3">
                            <label class="block text-[10px] text-slate-400 mb-0.5">Kredit (Rp)</label>
                            <input type="number" step="0.01" name="details[1][kredit]" value="0" required class="w-full px-2.5 py-1.5 rounded-lg bg-slate-900 border border-slate-700 text-white font-mono">
                        </div>
                    </div>
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahJurnal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white font-bold shadow">Simpan Jurnal Memorial</button>
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
                            <button type="button" onclick="openEditModal('{{ $j->id_jurnal }}', '{{ $j->tanggal->format('Y-m-d') }}', '{{ addslashes($j->deskripsi) }}')" class="px-2.5 py-1 rounded-lg bg-blue-500/20 hover:bg-blue-500 hover:text-white text-blue-400 text-[11px] font-semibold border border-blue-500/30 transition flex items-center gap-1">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            @if(!$j->is_posted)
                            <button type="submit" formaction="{{ route('akuntansi.jurnal.approve', $j->id_jurnal) }}" formmethod="POST" class="px-2.5 py-1 rounded-lg bg-emerald-500/20 hover:bg-emerald-500 hover:text-white text-emerald-400 text-[11px] font-semibold border border-emerald-500/30 transition flex items-center gap-1">
                                @method('PUT')
                                <i class="fas fa-check"></i> Approve
                            </button>
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

<!-- Modal Edit Jurnal (Header) -->
<div id="modalEditJurnal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-slate-950/80 backdrop-blur-sm">
    <div class="rounded-3xl border border-blue-500/30 bg-slate-900 shadow-2xl w-full max-w-lg overflow-hidden">
        <div class="flex items-center justify-between p-5 border-b border-slate-800 bg-slate-900">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-edit text-blue-400"></i>
                <span>Edit Jurnal</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalEditJurnal').classList.add('hidden')" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="" id="formEditJurnal" class="p-5 space-y-4 text-xs">
            @csrf
            @method('PUT')
            
            <div>
                <label class="block text-slate-300 font-semibold mb-1">Tanggal Transaksi</label>
                <input type="date" name="tanggal" id="editTanggal" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div>
                <label class="block text-slate-300 font-semibold mb-1">Deskripsi / Keterangan</label>
                <input type="text" name="deskripsi" id="editDeskripsi" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white">
            </div>

            <div class="flex items-center justify-end gap-3 pt-4">
                <button type="button" onclick="document.getElementById('modalEditJurnal').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-500 hover:bg-blue-600 text-white font-bold shadow">Update Jurnal</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openEditModal(id, tanggal, deskripsi) {
        document.getElementById('editTanggal').value = tanggal;
        document.getElementById('editDeskripsi').value = deskripsi;
        document.getElementById('formEditJurnal').action = '/akuntansi/jurnal/' + id;
        document.getElementById('modalEditJurnal').classList.remove('hidden');
    }
</script>

@endsection
