@extends('layouts.admin')

@section('title', 'Manajemen Proyek & Billing Termin - PT PBS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white">Proyek & Billing Kontrak Klien</h2>
            <p class="text-xs text-slate-400 mt-0.5">Pengendalian kontrak kerja sama, termin penagihan, dan piutang PT Pinastika Bhakti Semesta</p>
        </div>
        <button 
            type="button" 
            onclick="document.getElementById('modalTambahProyek').classList.toggle('hidden')"
            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition flex items-center gap-2 self-start"
        >
            <i class="fas fa-plus"></i>
            <span>Daftarkan Proyek Baru</span>
        </button>
    </div>

    <!-- 3 Summary Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60">
            <span class="text-xs text-slate-400 block">Total Nilai Kontrak Proyek</span>
            <strong class="text-xl font-black text-white block mt-1">Rp {{ number_format($totalNilaiKontrak, 0, ',', '.') }}</strong>
        </div>
        <div class="p-4 rounded-2xl border border-sky-500/30 bg-sky-500/5">
            <span class="text-xs text-sky-300 block">Total Invoice Diterbitkan (Tertagih)</span>
            <strong class="text-xl font-black text-sky-400 block mt-1">Rp {{ number_format($totalTertagih, 0, ',', '.') }}</strong>
        </div>
        <div class="p-4 rounded-2xl border border-emerald-500/30 bg-emerald-500/5">
            <span class="text-xs text-emerald-300 block">Total Pembayaran Masuk (Lunas)</span>
            <strong class="text-xl font-black text-emerald-400 block mt-1">Rp {{ number_format($totalTerbayar, 0, ',', '.') }}</strong>
        </div>
    </div>

    <!-- Modal Form Tambah Proyek -->
    <div id="modalTambahProyek" class="hidden rounded-3xl border border-amber-500/30 bg-slate-900/95 p-6 backdrop-blur-xl shadow-2xl">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-diagram-project text-amber-400"></i>
                <span>Pendaftaran Kontrak / Proyek Baru</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahProyek').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('proyek.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Proyek</label>
                    <input type="text" name="kode_proyek" value="PRJ-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div class="sm:col-span-2">
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Pekerjaan / Proyek</label>
                    <input type="text" name="nama_proyek" required placeholder="Contoh: Pengadaan Sistem Informasi & Pelatihan Akuntansi" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Klien / Instansi</label>
                    <input type="text" name="nama_klien" required placeholder="PT / Dinas / BUMDesa" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">PIC Klien & Jabatan</label>
                    <input type="text" name="pic_klien" placeholder="Bpk/Ibu ..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Telepon Klien</label>
                    <input type="text" name="telepon_klien" placeholder="+62 ..." class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nilai Total Kontrak (Rp)</label>
                    <input type="number" step="0.01" name="nilai_kontrak" required placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Mulai</label>
                    <input type="date" name="tanggal_mulai" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Target Selesai</label>
                    <input type="date" name="tanggal_selesai_target" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Keterangan / Scope Pekerjaan</label>
                <input type="text" name="keterangan" placeholder="Catatan kontrak dan teknis pelaksanaan" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahProyek').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow">Daftarkan Proyek</button>
            </div>
        </form>
    </div>

    <!-- Proyek List Cards -->
    <div class="space-y-4">
        @forelse($proyeks as $p)
            <div class="rounded-3xl border border-slate-800 bg-slate-900/60 p-6 shadow-xl space-y-4">
                <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between pb-3 border-b border-slate-800/80 gap-3">
                    <div>
                        <div class="flex items-center gap-2">
                            <span class="px-2.5 py-0.5 rounded text-[10px] font-bold font-mono bg-amber-500/10 text-amber-400 border border-amber-500/20">
                                {{ $p->kode_proyek }}
                            </span>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-slate-800 text-slate-300">
                                {{ $p->status_proyek }}
                            </span>
                        </div>
                        <h3 class="text-base font-bold text-white mt-1.5">{{ $p->nama_proyek }}</h3>
                        <p class="text-xs text-slate-400 mt-0.5">Klien: <strong class="text-slate-200">{{ $p->nama_klien }}</strong> &bull; PIC: {{ $p->pic_klien ?? '-' }} ({{ $p->telepon_klien ?? '-' }})</p>
                    </div>

                    <div class="text-right">
                        <span class="text-xs text-slate-400 block">Nilai Kontrak</span>
                        <strong class="text-lg font-black text-amber-400 font-mono">Rp {{ number_format($p->nilai_kontrak, 0, ',', '.') }}</strong>
                    </div>
                </div>

                <!-- Progress & Billing Details -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-3 text-xs bg-slate-950/40 p-4 rounded-2xl border border-slate-800/80">
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold">Masa Pengerjaan</span>
                        <p class="text-slate-200 mt-0.5">{{ $p->tanggal_mulai->format('d M Y') }} s/d {{ $p->tanggal_selesai_target ? $p->tanggal_selesai_target->format('d M Y') : 'Selesai' }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold">Total Tertagih</span>
                        <p class="text-sky-400 font-mono font-bold mt-0.5">Rp {{ number_format($p->total_tertagih, 0, ',', '.') }}</p>
                    </div>
                    <div>
                        <span class="text-[10px] text-slate-400 uppercase font-bold">Total Pembayaran Masuk</span>
                        <p class="text-emerald-400 font-mono font-bold mt-0.5">Rp {{ number_format($p->total_terbayar, 0, ',', '.') }}</p>
                    </div>
                </div>

                <!-- Invoices associated with this Project -->
                <div class="pt-2">
                    <div class="flex items-center justify-between mb-2">
                        <span class="text-xs font-bold text-slate-300 uppercase tracking-wider">Riwayat Invoice & Termin Penagihan</span>
                        <button 
                            type="button" 
                            onclick="openInvoiceModal('{{ $p->id }}', '{{ $p->nama_proyek }}')"
                            class="px-2.5 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[11px] font-semibold"
                        >
                            + Terbitkan Invoice Termin
                        </button>
                    </div>

                    @if($p->invoices->count() > 0)
                        <div class="divide-y divide-slate-800/60 rounded-xl border border-slate-800 bg-slate-950/60 overflow-hidden text-xs">
                            @foreach($p->invoices as $inv)
                                <div class="p-3 flex items-center justify-between">
                                    <div>
                                        <span class="font-bold text-white font-mono">{{ $inv->nomor_invoice }}</span>
                                        <span class="text-slate-400 ml-2">({{ $inv->termin_ke }})</span>
                                        <span class="text-[10px] text-slate-500 block mt-0.5">Tgl: {{ $inv->tanggal_invoice->format('d/m/Y') }} &bull; JT: {{ $inv->jatuh_tempo->format('d/m/Y') }}</span>
                                    </div>
                                    <div class="text-right">
                                        <span class="font-bold font-mono text-white block">Rp {{ number_format($inv->total_bersih, 0, ',', '.') }}</span>
                                        <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $inv->status_bayar === 'Lunas' ? 'bg-emerald-500/10 text-emerald-400' : 'bg-rose-500/10 text-rose-400' }}">
                                            {{ $inv->status_bayar }}
                                        </span>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-xs text-slate-500 italic">Belum ada invoice termin diterbitkan.</p>
                    @endif
                </div>
            </div>
        @empty
            <div class="p-12 text-center rounded-3xl border border-slate-800 bg-slate-900/60">
                <i class="fas fa-diagram-project text-3xl text-slate-600 mb-2"></i>
                <p class="text-xs text-slate-400">Belum ada kontrak proyek terdaftar.</p>
            </div>
        @endforelse
    </div>
</div>

<!-- Modal Terbitkan Invoice Termin -->
<div id="modalTambahInvoice" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="rounded-3xl border border-slate-700 bg-slate-900 p-6 max-w-lg w-full shadow-2xl">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-800">
            <div>
                <h4 class="text-sm font-bold text-white">Terbitkan Invoice Termin Proyek</h4>
                <p id="modal_proyek_title" class="text-xs text-amber-400 mt-0.5"></p>
            </div>
            <button onclick="document.getElementById('modalTambahInvoice').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form id="formTambahInvoice" method="POST" action="" class="space-y-4">
            @csrf
            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Invoice</label>
                    <input type="text" name="nomor_invoice" value="INV-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Termin Ke</label>
                    <input type="text" name="termin_ke" placeholder="Termin 1 / DP / Pelunasan" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Invoice</label>
                    <input type="date" name="tanggal_invoice" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jatuh Tempo</label>
                    <input type="date" name="jatuh_tempo" value="{{ date('Y-m-d', strtotime('+14 days')) }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nominal DPP (Rp)</label>
                    <input type="number" step="0.01" name="nominal_tagihan" required placeholder="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">PPN (Rp)</label>
                    <input type="number" step="0.01" name="ppn_nominal" value="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Potongan PPh (Rp)</label>
                    <input type="number" step="0.01" name="pph_nominal" value="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahInvoice').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow">Terbitkan Invoice</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openInvoiceModal(proyekId, proyekTitle) {
        const modal = document.getElementById('modalTambahInvoice');
        const form = document.getElementById('formTambahInvoice');
        document.getElementById('modal_proyek_title').innerText = proyekTitle;
        form.action = '/proyek/' + proyekId + '/invoice';
        modal.classList.remove('hidden');
    }
</script>
@endsection
