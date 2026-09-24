@extends('layouts.admin')

@section('title', 'Manajemen Pajak & Kepatuhan Fiskal - PT PBS')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div>
            <h2 class="text-xl font-black text-white">Manajemen Perpajakan Korporasi</h2>
            <p class="text-xs text-slate-400 mt-0.5">Pengawasan kepatuhan fiskal PPN & PPh PT Pinastika Bhakti Semesta &bull; Supervisi: Kurniawan, S.E. (BOD)</p>
        </div>
        @if(auth()->user()->canMutate())
        <button 
            type="button" 
            onclick="document.getElementById('modalTambahPajak').classList.toggle('hidden')"
            class="px-4 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition flex items-center gap-2 self-start"
        >
            <i class="fas fa-plus"></i>
            <span>Catat Dokumen Pajak Baru</span>
        </button>
        @else
        <span class="px-3 py-1.5 rounded-xl bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5 self-start">
            <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
        </span>
        @endif
    </div>

    <!-- 4 Tax Summary Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60">
            <span class="text-xs text-slate-400 block">PPN Keluaran (Penjualan)</span>
            <strong class="text-xl font-black text-white block mt-1">Rp {{ number_format($ppnKeluaran, 0, ',', '.') }}</strong>
            <span class="text-[10px] text-amber-400 block mt-1">Faktur Pajak Terbit</span>
        </div>
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60">
            <span class="text-xs text-slate-400 block">PPN Masukan (Dapat Dikreditkan)</span>
            <strong class="text-xl font-black text-white block mt-1">Rp {{ number_format($ppnMasukan, 0, ',', '.') }}</strong>
            <span class="text-[10px] text-emerald-400 block mt-1">Kredit Pajak Masukan</span>
        </div>
        <div class="p-4 rounded-2xl border border-slate-800 bg-slate-900/60">
            <span class="text-xs text-slate-400 block">PPh 21 & PPh 23 Terutang</span>
            <strong class="text-xl font-black text-amber-400 block mt-1">Rp {{ number_format($pph21Total + $pph23Total, 0, ',', '.') }}</strong>
            <span class="text-[10px] text-slate-400 block mt-1">PPh 21: Rp {{ number_format($pph21Total, 0, ',', '.') }} &bull; PPh 23: Rp {{ number_format($pph23Total, 0, ',', '.') }}</span>
        </div>
        <div class="p-4 rounded-2xl border border-rose-500/30 bg-rose-500/5">
            <span class="text-xs text-rose-300 block">Total Pajak Belum Disetor</span>
            <strong class="text-xl font-black text-rose-400 block mt-1">Rp {{ number_format($totalPajakBelumSetor, 0, ',', '.') }}</strong>
            <span class="text-[10px] text-rose-400 block mt-1">Menunggu Generate Kode Billing</span>
        </div>
    </div>

    <!-- Modal Form Catat Dokumen Pajak -->
    <div id="modalTambahPajak" class="hidden rounded-3xl border border-amber-500/30 bg-slate-900/95 p-6 backdrop-blur-xl shadow-2xl">
        <div class="flex items-center justify-between pb-4 mb-4 border-b border-slate-800">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-invoice text-amber-400"></i>
                <span>Pencatatan Dokumen / Faktur Pajak Baru</span>
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahPajak').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('pajak.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Referensi</label>
                    <input type="text" name="kode_referensi" value="TAX-PBS-{{ date('Ymd-His') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Jenis Pajak</label>
                    <select name="jenis_pajak" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        <option value="PPN_KELUARAN">PPN Keluaran (Penjualan / 11%)</option>
                        <option value="PPN_MASUKAN">PPN Masukan (Pembelian / 11%)</option>
                        <option value="PPH_21">PPh Pasal 21 (Gaji & Honor)</option>
                        <option value="PPH_23">PPh Pasal 23 (Jasa & Sewa / 2%)</option>
                        <option value="PPH_4_2">PPh Pasal 4 ayat 2 (Final)</option>
                        <option value="PPH_25_29">PPh Badan (Tahunan)</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Masa & Tahun Pajak</label>
                    <div class="flex gap-2">
                        <input type="text" name="masa_pajak" value="{{ \Carbon\Carbon::now()->translatedFormat('F') }}" placeholder="Masa" required class="w-2/3 px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        <input type="number" name="tahun_pajak" value="{{ date('Y') }}" required class="w-1/3 px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tanggal Dokumen</label>
                    <input type="date" name="tanggal_faktur_potong" value="{{ date('Y-m-d') }}" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">No Faktur / Bukti Potong (DJP)</label>
                    <input type="text" name="nomor_dokumen" placeholder="Contoh: 010.002-26.000123" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lawan Transaksi</label>
                    <input type="text" name="lawan_transaksi" required placeholder="PT / CV / Orang Pribadi" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">NPWP Lawan Transaksi</label>
                    <input type="text" name="npwp_lawan_transaksi" placeholder="00.000.000.0-000.000" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">DPP (Rp)</label>
                    <input type="number" step="0.01" name="dpp" id="tax_dpp" required placeholder="Dasar Pengenaan" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tarif (%)</label>
                    <input type="number" step="0.01" name="tarif_persen" id="tax_rate" value="11" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nominal Pajak (Rp)</label>
                    <input type="number" step="0.01" name="nominal_pajak" id="tax_nominal" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Status Penyetoran</label>
                    <select name="status_bayar" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        <option value="Belum Disetor">Belum Disetor</option>
                        <option value="Sudah Disetor">Sudah Disetor</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Catatan Tambahan</label>
                    <input type="text" name="catatan" placeholder="Keterangan pengawasan" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="flex items-center justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalTambahPajak').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow">Simpan Data Pajak</button>
            </div>
        </form>
    </div>

    <!-- Filter Tabs -->
    <div class="flex flex-wrap gap-2 text-xs">
        <a href="{{ route('pajak.index') }}" class="px-3 py-1.5 rounded-lg border {{ !$jenisFilter ? 'bg-amber-500 border-amber-500 text-white font-bold' : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-800' }}">Semua Pajak</a>
        @foreach(['PPN_KELUARAN' => 'PPN Keluaran', 'PPN_MASUKAN' => 'PPN Masukan', 'PPH_21' => 'PPh 21', 'PPH_23' => 'PPh 23', 'PPH_4_2' => 'PPh 4(2)'] as $val => $label)
            <a href="{{ route('pajak.index', ['jenis' => $val]) }}" class="px-3 py-1.5 rounded-lg border {{ $jenisFilter === $val ? 'bg-amber-500 border-amber-500 text-white font-bold' : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-800' }}">
                {{ $label }}
            </a>
        @endforeach
    </div>

    <!-- Tax Records Table -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Ref & Dokumen</th>
                        <th class="py-3 px-4">Jenis & Masa</th>
                        <th class="py-3 px-4">Lawan Transaksi</th>
                        <th class="py-3 px-4 text-right">DPP</th>
                        <th class="py-3 px-4 text-right">Pajak</th>
                        <th class="py-3 px-4 text-center">Status Setor</th>
                        <th class="py-3 px-4 text-center">Status Lapor SPT</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($transaksis as $t)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4">
                                <span class="font-bold text-amber-400 font-mono block">{{ $t->kode_referensi }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t->nomor_dokumen ?? 'Tanpa Dokumen' }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <span class="font-bold text-white block">{{ $t->jenis_pajak }}</span>
                                <span class="text-[10px] text-slate-400">{{ $t->masa_pajak }} {{ $t->tahun_pajak }}</span>
                            </td>
                            <td class="py-3 px-4">
                                <strong class="text-white block">{{ $t->lawan_transaksi }}</strong>
                                <span class="text-[10px] text-slate-400 font-mono">{{ $t->npwp_lawan_transaksi ?? '-' }}</span>
                            </td>
                            <td class="py-3 px-4 text-right font-mono">Rp {{ number_format($t->dpp, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-mono font-bold text-amber-400">
                                Rp {{ number_format($t->nominal_pajak, 0, ',', '.') }}
                                <span class="text-[10px] text-slate-400 font-normal block">({{ $t->tarif_persen }}%)</span>
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->status_bayar === 'Sudah Disetor' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-rose-500/10 text-rose-400 border border-rose-500/20' }}">
                                    {{ $t->status_bayar }}
                                </span>
                                @if($t->ntpn)
                                    <span class="block text-[9px] text-slate-400 font-mono mt-0.5">NTPN: {{ $t->ntpn }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $t->status_lapor === 'Sudah Dilapor' ? 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' : 'bg-slate-800 text-slate-400' }}">
                                    {{ $t->status_lapor }}
                                </span>
                                @if($t->bpe_spt)
                                    <span class="block text-[9px] text-slate-400 font-mono mt-0.5">BPE: {{ $t->bpe_spt }}</span>
                                @endif
                            </td>
                            <td class="py-3 px-4 text-center">
                                @if(auth()->user()->canMutate())
                                <button 
                                    type="button" 
                                    onclick="openUpdateModal('{{ $t->id }}', '{{ $t->kode_referensi }}', '{{ $t->status_bayar }}', '{{ $t->ntpn }}', '{{ $t->status_lapor }}', '{{ $t->bpe_spt }}')"
                                    class="px-2.5 py-1 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-200 text-[11px] font-medium"
                                >
                                    Update
                                </button>
                                @else
                                <span class="text-[10px] text-slate-500 italic">Arsip Fiskal</span>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500 italic">Belum ada catatan pajak.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="p-4 border-t border-slate-800">
            {{ $transaksis->links() }}
        </div>
    </div>
</div>

<!-- Modal Update Status Setor & Lapor -->
<div id="modalUpdatePajak" class="hidden fixed inset-0 z-50 bg-black/60 backdrop-blur-sm flex items-center justify-center p-4">
    <div class="rounded-3xl border border-slate-700 bg-slate-900 p-6 max-w-md w-full shadow-2xl">
        <div class="flex items-center justify-between pb-3 mb-4 border-b border-slate-800">
            <h4 class="text-sm font-bold text-white">Update Status Setor & Lapor DJP</h4>
            <button onclick="document.getElementById('modalUpdatePajak').classList.add('hidden')" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form id="formUpdatePajak" method="POST" action="" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status Penyetoran (Billing)</label>
                <select name="status_bayar" id="modal_status_bayar" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    <option value="Belum Disetor">Belum Disetor</option>
                    <option value="Sudah Disetor">Sudah Disetor</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Transaksi Penerimaan Negara (NTPN)</label>
                <input type="text" name="ntpn" id="modal_ntpn" placeholder="16 Karakter Bukti Setor" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Status Pelaporan SPT Masa</label>
                <select name="status_lapor" id="modal_status_lapor" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                    <option value="Belum Dilapor">Belum Dilapor</option>
                    <option value="Sudah Dilapor">Sudah Dilapor</option>
                </select>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Bukti Penerimaan Elektronik (BPE)</label>
                <input type="text" name="bpe_spt" id="modal_bpe" placeholder="No Tanda Terima DJP Online" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
            </div>

            <div class="flex items-center justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalUpdatePajak').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-slate-800 text-slate-300 text-xs">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow">Perbarui</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openUpdateModal(id, ref, bayar, ntpn, lapor, bpe) {
        const modal = document.getElementById('modalUpdatePajak');
        const form = document.getElementById('formUpdatePajak');
        form.action = '/pajak/' + id + '/update-status';
        document.getElementById('modal_status_bayar').value = bayar || 'Belum Disetor';
        document.getElementById('modal_ntpn').value = ntpn || '';
        document.getElementById('modal_status_lapor').value = lapor || 'Belum Dilapor';
        document.getElementById('modal_bpe').value = bpe || '';
        modal.classList.remove('hidden');
    }

    // Auto-calculate tax nominal
    const dppInput = document.getElementById('tax_dpp');
    const rateInput = document.getElementById('tax_rate');
    const nomInput = document.getElementById('tax_nominal');
    if (dppInput && rateInput && nomInput) {
        function calc() {
            const d = parseFloat(dppInput.value) || 0;
            const r = parseFloat(rateInput.value) || 0;
            nomInput.value = Math.round(d * (r / 100));
        }
        dppInput.addEventListener('input', calc);
        rateInput.addEventListener('input', calc);
    }
</script>
@endsection
