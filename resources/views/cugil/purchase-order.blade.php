@extends('layouts.admin')
@section('title', 'Purchase Order (PO) - CUGIL PBS-ERP PT Pinastika Bhakti Semesta')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-file-invoice mr-2 text-blue-400"></i>Purchase Order (PO) Bahan Baku CUGIL</h1>
            <p class="text-gray-400 text-sm mt-1">Order pembelian bahan kresek ke supplier (PO yang sudah tiba diproses penerimaannya di CUGIL RAW)</p>
        </div>
        <div class="flex items-center gap-2.5">
            <a href="{{ route('cugil.raw.index') }}" class="bg-amber-600/30 hover:bg-amber-600 border border-amber-500 text-amber-300 hover:text-white px-3.5 py-2 rounded-lg font-medium text-xs transition">
                <i class="fas fa-boxes-stacked mr-1.5"></i>Ke Penerimaan Bahan (CUGIL RAW)
            </a>
            @if(auth()->user()->canMutate())
            <button onclick="document.getElementById('modalTambahPO').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg font-medium shadow-lg hover:shadow-blue-600/30 transition text-sm">
                <i class="fas fa-plus mr-1.5"></i>Buat PO Baru
            </button>
            @else
            <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
                <i class="fas fa-eye text-xs"></i> Mode Pantau
            </span>
            @endif
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-sm font-medium">Total Dokumen PO</p>
            <p class="text-3xl font-bold text-white mt-1">{{ number_format($totalPO, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-sm font-medium">Total Volume Pesanan</p>
            <p class="text-3xl font-bold text-blue-400 mt-1">{{ number_format($totalQty, 2, ',', '.') }} <span class="text-sm font-normal text-gray-400">Kg/Satuan</span></p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-sm font-medium">Total Nilai Order (PO)</p>
            <p class="text-3xl font-bold text-emerald-400 mt-1">Rp {{ number_format($totalNilai, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('cugil.po.index') }}" class="bg-gray-800/80 p-4 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs text-gray-400 mb-1">Filter Supplier</label>
            <select name="supplier" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500">
                <option value="">Semua Supplier</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->kode_supplier }}" {{ request('supplier') == $sup->kode_supplier ? 'selected' : '' }}>
                        {{ $sup->kode_supplier }} - {{ $sup->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Status Terima</label>
            <select name="status" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="Belum" {{ request('status') == 'Belum' ? 'selected' : '' }}>Belum Diterima (Pending)</option>
                <option value="YA" {{ request('status') == 'YA' ? 'selected' : '' }}>Sudah Diterima (CUGIL RAW)</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Tanggal Mulai</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Tanggal Sampai</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            @if(request('supplier') || request('status') || request('dari') || request('sampai'))
                <a href="{{ route('cugil.po.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>

    {{-- Table PO --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-x-auto shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/60 border-b border-gray-700">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">No. PO</th>
                    <th class="px-4 py-3">Tanggal</th>
                    <th class="px-4 py-3">Supplier / Vendor</th>
                    <th class="px-4 py-3">Daftar Item Barang (SKU)</th>
                    <th class="px-4 py-3 text-right">Total Qty</th>
                    <th class="px-4 py-3 text-right">Total Order</th>
                    <th class="px-4 py-3 text-right">Ongkir</th>
                    <th class="px-4 py-3">Termin</th>
                    <th class="px-4 py-3 text-center">Status Terima</th>
                    <th class="px-4 py-3 text-center">Tindakan / Penerimaan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/60">
                @forelse($purchaseOrders as $i => $po)
                <tr class="hover:bg-gray-700/40 transition align-top">
                    <td class="px-4 py-3 text-gray-400 text-xs">{{ $purchaseOrders->firstItem() + $i }}</td>
                    <td class="px-4 py-3 font-mono text-blue-400 font-semibold text-xs">{{ $po->nomor_po }}</td>
                    <td class="px-4 py-3 text-gray-300 text-xs whitespace-nowrap">{{ $po->tanggal ? $po->tanggal->format('d/m/Y') : '-' }}</td>
                    <td class="px-4 py-3">
                        <div class="text-white font-medium">{{ $po->nama_vendor ?? ($po->supplier->nama_supplier ?? $po->kode_supplier) }}</div>
                        <div class="text-gray-400 text-xs font-mono">{{ $po->kode_supplier }}</div>
                    </td>
                    <td class="px-4 py-3">
                        @if($po->items && $po->items->count() > 0)
                            <div class="space-y-1.5 max-w-xs">
                                @foreach($po->items as $it)
                                    <div class="bg-gray-900/60 p-2 rounded-lg border border-gray-700/60 text-xs">
                                        <div class="font-medium text-white">{{ $it->nama_barang }}</div>
                                        <div class="text-[11px] text-gray-400 flex justify-between mt-0.5">
                                            <span>{{ number_format($it->qty, 1) }} {{ $it->satuan }} &times; Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</span>
                                            <span class="font-semibold text-emerald-400">Rp {{ number_format($it->harga_total, 0, ',', '.') }}</span>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-right font-medium text-white">{{ number_format($po->total_qty, 1) }} <span class="text-xs text-gray-400">Kg</span></td>
                    <td class="px-4 py-3 text-right font-bold text-emerald-400 whitespace-nowrap">Rp {{ number_format($po->total_nilai, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-right text-gray-300 text-xs whitespace-nowrap">
                        {{ $po->ongkos_angkut > 0 ? 'Rp ' . number_format($po->ongkos_angkut, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-4 py-3 whitespace-nowrap">
                        <span class="px-2 py-0.5 rounded text-xs {{ $po->termin_payment == 'BELI PUTUS' ? 'bg-purple-900/60 text-purple-300 border border-purple-700' : 'bg-amber-900/60 text-amber-300 border border-amber-700' }}">
                            {{ $po->termin_payment ?? 'Beli Putus' }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        @if($po->status_terima == 'YA' || $po->status_terima == 'Ya')
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-semibold bg-green-900 text-green-300 border border-green-700">
                                <i class="fas fa-check-circle mr-1"></i>DITERIMA (YA)
                            </span>
                        @else
                            <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-amber-900/70 text-amber-300 border border-amber-700">
                                <i class="fas fa-clock mr-1"></i>PENDING
                            </span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('cugil.po.print', $po->id) }}" target="_blank" class="inline-flex items-center px-2 py-1 rounded bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white text-xs font-semibold transition" title="Cetak Surat PO">
                                <i class="fas fa-print mr-1"></i>Cetak PO
                            </a>
                            @if($po->status_terima != 'YA' && $po->status_terima != 'Ya')
                                <a href="{{ route('cugil.raw.index', ['tarik_po' => $po->nomor_po]) }}" class="inline-flex items-center px-2 py-1 rounded bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium shadow transition" title="Truk tiba & timbang bahan masuk">
                                    <i class="fas fa-boxes-stacked mr-1"></i>Terima RAW
                                </a>
                            @else
                                <a href="{{ route('cugil.raw.index', ['supplier' => $po->kode_supplier]) }}" class="inline-flex items-center px-2 py-1 rounded bg-slate-700 text-slate-300 text-xs hover:text-white hover:bg-slate-600 transition">
                                    <i class="fas fa-eye mr-1"></i>RAW
                                </a>
                            @endif
                            @if(auth()->user()->canMutate())
                            <form action="{{ route('cugil.po.destroy', $po->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin MENGHAPUS Purchase Order {{ $po->nomor_po }}?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="inline-flex items-center px-2 py-1 rounded bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white text-xs transition" title="Hapus PO">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="11" class="px-4 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2 text-gray-600"></i>
                        <p>Belum ada data purchase order.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $purchaseOrders->links() }}</div>
</div>

{{-- Modal Buat PO Baru (Multi-SKU) --}}
<div id="modalTambahPO" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-4xl border border-gray-700 shadow-2xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-gray-700 mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-file-invoice text-blue-400"></i> Buat Purchase Order (PO) Baru &mdash; Multi-SKU Item
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahPO').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form action="{{ route('cugil.po.store') }}" method="POST" class="space-y-4">
            @csrf
            {{-- Header Form --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Nomor PO *</label>
                    <input type="text" name="nomor_po" value="{{ date('d') }}/PBS/{{ date('n/Y') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Tanggal PO *</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Pilih Supplier (1 Supplier) *</label>
                    <select name="kode_supplier" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->kode_supplier }}">{{ $sup->kode_supplier }} - {{ $sup->nama_supplier }} ({{ $sup->kota }})</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Detail Items (Multi-SKU) --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-blue-400 uppercase tracking-wider">
                        <i class="fas fa-boxes mr-1.5"></i>Daftar SKU Barang yang Dipesan
                    </h4>
                    <button type="button" onclick="tambahBarisItemPO()" class="px-3 py-1.5 bg-blue-600/30 hover:bg-blue-600 border border-blue-500 text-blue-300 hover:text-white rounded-lg text-xs font-semibold transition">
                        <i class="fas fa-plus mr-1"></i>+ Tambah Item Barang
                    </button>
                </div>

                <div id="containerItemPO" class="space-y-2">
                    {{-- Row 1 default --}}
                    <div class="item-po-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-[11px] text-gray-400 mb-1">Pilih Barang Katalog / Nama Barang *</label>
                            <input type="text" name="items[0][nama_barang]" list="listBarangPO" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Ketik / Pilih nama barang" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Qty *</label>
                            <input type="number" step="0.01" name="items[0][qty]" oninput="hitungSemuaPO()" class="po-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] text-gray-400 mb-1">Satuan</label>
                            <input type="text" name="items[0][satuan]" value="Kg" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-xs">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Harga Satuan (Rp) *</label>
                            <input type="number" step="0.01" name="items[0][harga_satuan]" oninput="hitungSemuaPO()" class="po-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Rp 0" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Subtotal</label>
                            <input type="text" readonly class="po-row-subtotal w-full bg-gray-950 border border-gray-800 text-emerald-400 font-bold rounded px-2.5 py-1.5 text-xs" value="Rp 0">
                        </div>
                        <div class="md:col-span-1 flex justify-center pb-1">
                            <button type="button" onclick="hapusBarisItemPO(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <datalist id="listBarangPO">
                @foreach($barangs as $brg)
                    <option value="{{ $brg->nama_barang }}">{{ $brg->kode_barang }} - {{ $brg->kategori }}</option>
                @endforeach
            </datalist>

            {{-- Footer Summary & Transport --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Ongkos Angkut (1 Dokumen/Armada)</label>
                    <input type="number" step="0.01" name="ongkos_angkut" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Termin Payment</label>
                    <select name="termin_payment" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                        <option value="BELI PUTUS">BELI PUTUS</option>
                        <option value="TEMPO">TEMPO</option>
                        <option value="TIMBANG BAYAR">TIMBANG BAYAR</option>
                        <option value="Cash">Cash</option>
                        <option value="TITIPAN">TITIPAN</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Grand Total Nilai PO (Semua SKU)</label>
                    <input type="text" id="po_grand_total_display" readonly class="w-full bg-gray-950 border border-gray-700 text-emerald-400 font-black text-base rounded-lg px-3 py-2" value="Rp 0">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Petugas Pembelian</label>
                    <input type="text" name="petugas" value="WINARDI" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Catatan Tambahan</label>
                    <input type="text" name="keterangan" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Catatan no truk, kondisi rafaksi, dll...">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                <button type="button" onclick="document.getElementById('modalTambahPO').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg text-sm font-medium shadow-md">
                    <i class="fas fa-save mr-1.5"></i>Simpan Dokumen PO
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let poItemIndex = 1;

function tambahBarisItemPO() {
    const container = document.getElementById('containerItemPO');
    const div = document.createElement('div');
    div.className = 'item-po-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end';
    div.innerHTML = `
        <div class="md:col-span-4">
            <label class="block text-[11px] text-gray-400 mb-1">Pilih Barang Katalog / Nama Barang *</label>
            <input type="text" name="items[${poItemIndex}][nama_barang]" list="listBarangPO" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Ketik / Pilih nama barang" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Qty *</label>
            <input type="number" step="0.01" name="items[${poItemIndex}][qty]" oninput="hitungSemuaPO()" class="po-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0" required>
        </div>
        <div class="md:col-span-1">
            <label class="block text-[11px] text-gray-400 mb-1">Satuan</label>
            <input type="text" name="items[${poItemIndex}][satuan]" value="Kg" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-xs">
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Harga Satuan (Rp) *</label>
            <input type="number" step="0.01" name="items[${poItemIndex}][harga_satuan]" oninput="hitungSemuaPO()" class="po-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Rp 0" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Subtotal</label>
            <input type="text" readonly class="po-row-subtotal w-full bg-gray-950 border border-gray-800 text-emerald-400 font-bold rounded px-2.5 py-1.5 text-xs" value="Rp 0">
        </div>
        <div class="md:col-span-1 flex justify-center pb-1">
            <button type="button" onclick="hapusBarisItemPO(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    poItemIndex++;
}

function hapusBarisItemPO(btn) {
    const rows = document.querySelectorAll('.item-po-row');
    if (rows.length > 1) {
        btn.closest('.item-po-row').remove();
        hitungSemuaPO();
    } else {
        alert('Minimal harus ada 1 item barang dalam Purchase Order.');
    }
}

function hitungSemuaPO() {
    const rows = document.querySelectorAll('.item-po-row');
    let grandTotal = 0;

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.po-row-qty').value) || 0;
        const harga = parseFloat(row.querySelector('.po-row-harga').value) || 0;
        const subtotal = qty * harga;
        row.querySelector('.po-row-subtotal').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        grandTotal += subtotal;
    });

    document.getElementById('po_grand_total_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
}
</script>
@endsection
