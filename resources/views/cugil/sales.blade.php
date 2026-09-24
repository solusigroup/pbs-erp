@extends('layouts.admin')
@section('title', 'Penjualan CUGIL - PBS-ERP PT Pinastika Bhakti Semesta')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-hand-holding-dollar mr-2 text-emerald-400"></i>Penjualan Hasil Cuci Giling (CUGIL)</h1>
            <p class="text-gray-400 text-sm mt-1">Pencatatan faktur, pengiriman, &amp; arsip foto slip timbangan kastamer (dilengkapi kompresi otomatis hemat server)</p>
        </div>
        @if(auth()->user()->canMutate())
        <div class="flex items-center gap-2">
            <form action="{{ route('cugil.lunaskanSemuaPiutang') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyesuaikan seluruh data penjualan menjadi LUNAS (Sisa Piutang = Rp 0)?')">
                @csrf
                <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 shadow-md shadow-amber-600/20" title="Sesuaikan faktual: Nolkan semua sisa piutang penjualan dan jadikan LUNAS">
                    <i class="fas fa-check-double text-xs"></i> Nolkan Sisa Piutang
                </button>
            </form>
            <form action="{{ route('cugil.syncAnomali') }}" method="POST" onsubmit="return confirm('Jalankan audit & sinkronisasi otomatis status pelunasan dan sisa hutang/piutang?')">
                @csrf
                <button type="submit" class="bg-indigo-600 hover:bg-indigo-500 text-white px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 shadow-md shadow-indigo-600/20" title="Sinkronkan status pelunasan LUNAS/SEBAGIAN/BELUM LUNAS dan koreksi selisih hitung">
                    <i class="fas fa-sync-alt text-xs"></i> Sinkron Status & Saldo
                </button>
            </form>
            <button onclick="document.getElementById('modalTambahSale').classList.remove('hidden')" class="bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg font-medium shadow-lg hover:shadow-emerald-600/30 transition">
                <i class="fas fa-plus mr-1.5"></i>Input Faktur Penjualan (Multi-Item)
            </button>
        </div>
        @else
        <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
            <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
        </span>
        @endif
    </div>

    @if(session('success'))
        <div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg flex items-center gap-2">
            <i class="fas fa-check-circle text-green-400"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Total Nilai Penjualan Net</p>
            <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Total Pembayaran Diterima</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Total Volume Terjual</p>
            <p class="text-2xl font-bold text-blue-400 mt-1">{{ number_format($totalTerjual, 1, ',', '.') }} <span class="text-xs font-normal text-gray-400">Kg</span></p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Faktur Belum Lunas</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($belumLunasCount, 0, ',', '.') }} <span class="text-sm font-normal text-gray-400">transaksi</span></p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('cugil.sales.index') }}" class="bg-gray-800/80 p-4 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs text-gray-400 mb-1">Kastamer / Pembeli</label>
            <select name="customer" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500">
                <option value="">Semua Kastamer</option>
                @foreach($customers as $cust)
                    <option value="{{ $cust->kode_customer }}" {{ request('customer') == $cust->kode_customer ? 'selected' : '' }}>
                        {{ $cust->kode_customer }} - {{ $cust->nama_customer }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Status Pembayaran</label>
            <select name="status" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                <option value="">Semua Status</option>
                <option value="LUNAS" {{ request('status') == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                <option value="BELUM LUNAS" {{ request('status') == 'BELUM LUNAS' ? 'selected' : '' }}>BELUM LUNAS</option>
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Dari Tanggal</label>
            <input type="date" name="dari" value="{{ request('dari') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Sampai Tanggal</label>
            <input type="date" name="sampai" value="{{ request('sampai') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
        </div>
        <div class="flex items-end gap-2">
            <button type="submit" class="flex-1 bg-emerald-600 hover:bg-emerald-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            @if(request('customer') || request('status') || request('dari') || request('sampai'))
                <a href="{{ route('cugil.sales.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>

    {{-- Table Sales --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-x-auto shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/60 border-b border-gray-700">
                <tr>
                    <th class="px-3 py-3">#</th>
                    <th class="px-3 py-3">ID Faktur</th>
                    <th class="px-3 py-3">Tanggal</th>
                    <th class="px-3 py-3">Pembeli (Kastamer)</th>
                    <th class="px-3 py-3">Daftar SKU Barang Dijual</th>
                    <th class="px-3 py-3 text-right">Total Qty</th>
                    <th class="px-3 py-3 text-right">Tagihan Net</th>
                    <th class="px-3 py-3 text-right">Payment</th>
                    <th class="px-3 py-3 text-center">Slip Timbangan</th>
                    <th class="px-3 py-3">Broker / Truk</th>
                    <th class="px-3 py-3 text-center">Status</th>
                    <th class="px-3 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/60">
                @forelse($sales as $i => $s)
                <tr class="hover:bg-gray-700/40 transition align-top">
                    <td class="px-3 py-2.5 text-gray-400 text-xs">{{ $sales->firstItem() + $i }}</td>
                    <td class="px-3 py-2.5 font-mono text-xs text-emerald-400 font-bold whitespace-nowrap">{{ $s->id_penjualan }}</td>
                    <td class="px-3 py-2.5 text-gray-300 text-xs whitespace-nowrap">{{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}</td>
                    <td class="px-3 py-2.5">
                        <div class="text-white font-semibold text-xs">{{ $s->nama_buyer ?? ($s->customer->nama_customer ?? $s->kode_customer) }}</div>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $s->kode_customer }}</span>
                    </td>
                    <td class="px-3 py-2.5">
                        @if($s->items && $s->items->count() > 0)
                            <div class="space-y-1.5 min-w-[220px]">
                                @foreach($s->items as $it)
                                    <div class="bg-gray-900/60 p-2 rounded-lg border border-gray-700/60 text-xs">
                                        <div class="font-medium text-white flex justify-between">
                                            <span>{{ $it->nama_barang }}</span>
                                            @if($it->jumlah_sak > 0)<span class="text-[10px] text-gray-400 font-normal">({{ $it->jumlah_sak }} sak)</span>@endif
                                        </div>
                                        <div class="text-[11px] text-gray-400 flex justify-between mt-0.5">
                                            <span>{{ number_format($it->qty_terjual, 1) }} Kg &times; Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</span>
                                            <span class="text-emerald-400 font-semibold">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @if($it->diskon_rupiah > 0)
                                            <div class="text-[10px] text-red-400">Diskon/Raf: -Rp {{ number_format($it->diskon_rupiah, 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-right font-medium text-white text-xs whitespace-nowrap">
                        <span class="font-bold">{{ number_format($s->total_qty, 1) }}</span> <span class="text-gray-400 text-[10px]">Kg</span>
                    </td>
                    <td class="px-3 py-2.5 text-right font-bold text-white text-xs whitespace-nowrap">Rp {{ number_format($s->tagihan, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5 text-right font-medium text-emerald-400 text-xs whitespace-nowrap">Rp {{ number_format($s->payment, 0, ',', '.') }}</td>

                    {{-- Foto Hasil Timbangan --}}
                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                        @if($s->foto_timbangan)
                            <button type="button" 
                                    onclick="bukaModalLihatTimbangan('{{ $s->id_penjualan }}', '{{ addslashes($s->nama_buyer ?? ($s->customer->nama_customer ?? $s->kode_customer)) }}', '{{ $s->foto_timbangan_url }}', '{{ number_format($s->total_qty, 1) }}', '{{ $s->tanggal ? $s->tanggal->format('d/m/Y') : '-' }}', '{{ route('cugil.sales.uploadTimbangan', $s->id) }}', '{{ route('cugil.sales.deleteTimbangan', $s->id) }}')" 
                                    class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg text-xs font-bold bg-cyan-500/15 text-cyan-300 border border-cyan-500/30 hover:bg-cyan-500 hover:text-slate-950 transition group shadow-sm" 
                                    title="Lihat Foto Slip Timbangan Kastamer">
                                <i class="fas fa-camera text-cyan-400 group-hover:text-slate-950"></i>
                                <span>Lihat Timbang</span>
                                <i class="fas fa-check-circle text-emerald-400 text-[10px] group-hover:text-slate-950"></i>
                            </button>
                        @else
                            <button type="button" 
                                    onclick="bukaModalUploadTimbangan('{{ $s->id }}', '{{ $s->id_penjualan }}', '{{ addslashes($s->nama_buyer ?? ($s->customer->nama_customer ?? $s->kode_customer)) }}', '{{ route('cugil.sales.uploadTimbangan', $s->id) }}')" 
                                    class="inline-flex items-center gap-1.5 px-2 py-1 rounded-lg text-[11px] font-medium bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-amber-400 border border-slate-700 transition" 
                                    title="Lampirkan Foto Slip Timbangan">
                                <i class="fas fa-camera text-amber-500/70"></i>
                                <span>+ Upload</span>
                            </button>
                        @endif
                    </td>

                    <td class="px-3 py-2.5 text-xs text-gray-300">
                        @if($s->broker)<div class="text-[11px] text-amber-300">Broker: {{ $s->broker }}</div>@endif
                        @if($s->truk)<div class="text-[10px] text-blue-400">Truk: {{ $s->truk }}</div>@endif
                        @if(!$s->broker && !$s->truk)<span class="text-gray-500">-</span>@endif
                    </td>
                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                        @if($s->status_pelunasan == 'LUNAS')
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-green-900/70 text-green-300 border border-green-700">LUNAS</span>
                        @elseif($s->status_pelunasan == 'SEBAGIAN')
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-amber-900/70 text-amber-300 border border-amber-700">SEBAGIAN</span>
                        @elseif($s->status_pelunasan == 'RETUR')
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-gray-700 text-gray-300">RETUR</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-900/70 text-red-300 border border-red-700">BELUM</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('cugil.sales.so', $s->id) }}" target="_blank" class="px-2 py-1 bg-amber-600/20 hover:bg-amber-600 text-amber-300 hover:text-white rounded text-xs transition font-semibold" title="Cetak Sales Order (SO)">
                                <i class="fas fa-file-contract mr-1"></i>SO
                            </a>
                            <a href="{{ route('cugil.sales.surat-jalan', $s->id) }}" target="_blank" class="px-2 py-1 bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white rounded text-xs transition font-semibold" title="Cetak Surat Jalan / DO">
                                <i class="fas fa-truck mr-1"></i>DO
                            </a>
                            <a href="{{ route('cugil.sales.invoice', $s->id) }}" target="_blank" class="px-2 py-1 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded text-xs transition font-semibold" title="Cetak Commercial Invoice">
                                <i class="fas fa-file-invoice-dollar mr-1"></i>Inv
                            </a>
                            <a href="{{ route('cugil.sales.faktur-pajak', $s->id) }}" target="_blank" class="px-2 py-1 bg-purple-600/20 hover:bg-purple-600 text-purple-300 hover:text-white rounded text-xs transition font-semibold" title="Cetak Faktur Pajak (PPN)">
                                <i class="fas fa-stamp mr-1"></i>Pajak
                            </a>
                            @if(auth()->user()->canMutate())
                            <button onclick="document.getElementById('modalBayarSale{{ $s->id }}').classList.remove('hidden')" class="px-2 py-1 bg-slate-700 hover:bg-slate-600 text-slate-200 rounded text-xs transition" title="Update Pelunasan & Data">
                                <i class="fas fa-edit"></i>
                            </button>
                            <form action="{{ route('cugil.sales.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS penjualan {{ $s->id_penjualan }}?\n\nSemua stok barang yang keluar pada penjualan ini akan OTOMATIS DI-ROLLBACK (dikembalikan) ke stok gudang!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded text-xs transition" title="Hapus Penjualan & Rollback Stok">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Modal Update Sale Payment & Attachment --}}
                <div id="modalBayarSale{{ $s->id }}" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
                    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700 shadow-2xl">
                        <div class="flex items-center justify-between pb-3 border-b border-gray-700 mb-4">
                            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                                <i class="fas fa-edit text-emerald-400"></i> Update Pelunasan &amp; Slip Timbangan
                            </h3>
                            <button type="button" onclick="document.getElementById('modalBayarSale{{ $s->id }}').classList.add('hidden')" class="text-gray-400 hover:text-white">
                                <i class="fas fa-times text-lg"></i>
                            </button>
                        </div>

                        <p class="text-xs text-gray-400 mb-4">{{ $s->id_penjualan }} &bull; {{ $s->nama_buyer }} (Tagihan Net: <strong class="text-white">Rp {{ number_format($s->tagihan, 0, ',', '.') }}</strong>)</p>
                        
                        <form action="{{ route('cugil.sales.updateStatus', $s->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-gray-300 mb-1">Jumlah Diterima / Payment (Rp) *</label>
                                <input type="number" step="0.01" name="payment" value="{{ $s->payment }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-medium text-gray-300 mb-1">Makelar / Broker</label>
                                    <input type="text" name="broker" value="{{ $s->broker }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-medium text-gray-300 mb-1">Armada / Truk</label>
                                    <input type="text" name="truk" value="{{ $s->truk }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                                </div>
                            </div>

                            {{-- Lampiran Foto Slip Timbangan Kastamer --}}
                            <div class="bg-gray-900/70 p-3.5 rounded-xl border border-gray-700 space-y-2">
                                <div class="flex items-center justify-between">
                                    <label class="block text-xs font-bold text-cyan-400">
                                        <i class="fas fa-camera mr-1"></i>Foto Hasil Timbangan Kastamer (Afuk / Mitra)
                                    </label>
                                    <span class="text-[10px] text-emerald-400 font-semibold bg-emerald-500/10 px-2 py-0.5 rounded border border-emerald-500/20">Auto Compress ~90%</span>
                                </div>

                                @if($s->foto_timbangan)
                                    <div class="flex items-center gap-3 p-2 bg-slate-800 rounded-lg border border-slate-700">
                                        <img src="{{ $s->foto_timbangan_url }}" alt="Slip Timbangan" class="h-14 w-14 object-cover rounded border border-slate-600">
                                        <div class="flex-1 text-xs">
                                            <p class="text-white font-semibold">Slip Timbangan Terlampir</p>
                                            <p class="text-[11px] text-gray-400">Pilih file baru jika ingin mengganti.</p>
                                        </div>
                                    </div>
                                @endif

                                <input type="file" name="foto_timbangan" accept="image/*" class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-semibold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer">
                            </div>

                            <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                                <button type="button" onclick="document.getElementById('modalBayarSale{{ $s->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="12" class="px-4 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2 text-gray-600"></i>
                        <p>Belum ada data transaksi penjualan.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $sales->links() }}</div>
</div>

{{-- Modal Input Penjualan Baru (Multi-SKU) --}}
<div id="modalTambahSale" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-4xl border border-gray-700 shadow-2xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-gray-700 mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-hand-holding-dollar text-emerald-400"></i> Input Faktur Penjualan CUGIL (Multi-SKU Item)
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahSale').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        <form action="{{ route('cugil.sales.store') }}" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            {{-- Header Form --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">ID / No Penjualan *</label>
                    <input type="text" name="id_penjualan" value="SALES-{{ rand(600, 999) }}/{{ date('n/Y') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-emerald-500 focus:border-emerald-500" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Tanggal Transaksi *</label>
                    <input type="date" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Kastamer (1 Pembeli) *</label>
                    <select name="kode_customer" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                        <option value="">-- Pilih Kastamer --</option>
                        @foreach($customers as $cust)
                            <option value="{{ $cust->kode_customer }}">{{ $cust->kode_customer }} - {{ $cust->nama_customer }} ({{ $cust->kota }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Tanggal Kirim</label>
                    <input type="date" name="tanggal_kirim" value="{{ date('Y-m-d') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            {{-- Detail Items (Multi-SKU Produk Cacahan) --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-emerald-400 uppercase tracking-wider">
                        <i class="fas fa-boxes mr-1.5"></i>Daftar SKU Barang yang Dijual
                    </h4>
                    <button type="button" onclick="tambahBarisItemSale()" class="px-3 py-1.5 bg-emerald-600/30 hover:bg-emerald-600 border border-emerald-500 text-emerald-300 hover:text-white rounded-lg text-xs font-semibold transition">
                        <i class="fas fa-plus mr-1"></i>+ Tambah Item Barang
                    </button>
                </div>

                <div id="containerItemSale" class="space-y-2">
                    {{-- Row 1 default --}}
                    <div class="item-sale-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-[11px] text-gray-400 mb-1">Pilih / Nama Produk Cacahan *</label>
                            <input type="text" name="items[0][nama_barang]" list="listBarangSale" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="HD Gilingan Hitam/PE/PP" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Qty Terjual (Kg) *</label>
                            <input type="number" step="0.01" name="items[0][qty_terjual]" oninput="hitungSemuaSale()" class="sale-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] text-gray-400 mb-1">Jml Sak</label>
                            <input type="number" name="items[0][jumlah_sak]" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-xs" placeholder="0">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Harga/Kg (Rp) *</label>
                            <input type="number" step="0.01" name="items[0][harga_satuan]" oninput="hitungSemuaSale()" class="sale-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Rp 0" required>
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] text-gray-400 mb-1">Rafaksi (%)</label>
                            <input type="number" step="0.01" name="items[0][diskon_persen]" oninput="hitungSemuaSale()" class="sale-row-diskon w-full bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-xs" placeholder="0.15">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] text-gray-400 mb-1">Net</label>
                            <input type="text" readonly class="sale-row-subtotal w-full bg-gray-950 border border-gray-800 text-emerald-400 font-bold rounded px-2 py-1.5 text-xs" value="Rp 0">
                        </div>
                        <div class="md:col-span-1 flex justify-center pb-1">
                            <button type="button" onclick="hapusBarisItemSale(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <datalist id="listBarangSale">
                @foreach($barangs as $brg)
                    <option value="{{ $brg->nama_barang }}">{{ $brg->kode_barang }} - Rp {{ number_format($brg->harga_jual, 0, ',', '.') }}/Kg</option>
                @endforeach
            </datalist>

            {{-- Footer Summary: 1 Ongkos Angkut, Fee Makelar, Payment --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Ongkos Angkut (1 Armada)</label>
                    <input type="number" step="0.01" name="ongkos_angkut" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Total Tagihan Net Penjualan</label>
                    <input type="text" id="sale_grand_total_display" readonly class="w-full bg-gray-950 border border-gray-700 text-emerald-400 font-black text-base rounded-lg px-3 py-2" value="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Payment Diterima (Rp)</label>
                    <input type="number" step="0.01" name="payment" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Fee Makelar / Broker (Rp)</label>
                    <input type="number" step="0.01" name="fee_makelar" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Rp 0">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Nama Broker / Makelar</label>
                    <input type="text" name="broker" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="ARIS / REZA / dll">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Truk Ekspedisi</label>
                    <input type="text" name="truk" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Fuso / Nopol Truk">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Sales PIC</label>
                    <input type="text" name="sales" value="ACH. CHUMAIDI" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
            </div>

            {{-- Lampiran Foto Slip Timbangan Kastamer dengan Kompresi Otomatis --}}
            <div class="bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 p-4 rounded-xl border border-cyan-500/30">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2 mb-2">
                    <label class="text-xs font-bold text-cyan-400 flex items-center gap-1.5">
                        <i class="fas fa-camera text-base text-cyan-400"></i>
                        <span>Lampiran Foto Hasil Timbangan Kastamer (Afuk / Mitra)</span>
                    </label>
                    <span class="inline-flex items-center gap-1 text-[11px] text-emerald-400 font-bold bg-emerald-500/10 px-2.5 py-0.5 rounded-full border border-emerald-500/20">
                        <i class="fas fa-compress-arrows-alt text-[10px]"></i> Auto-Compression Engine (~90% Hemat Server)
                    </span>
                </div>
                <p class="text-[11px] text-slate-400 mb-3">Foto slip timbangan langsung dari HP akan otomatis dioptimasi resolusi &amp; dikompresi kualitasnya tanpa mengurangi keterbacaan tulisan tangan.</p>
                
                <div class="flex flex-col sm:flex-row items-center gap-4">
                    <input type="file" name="foto_timbangan" id="inputFotoTimbanganBaru" accept="image/*" onchange="previewFotoTimbangan(this, 'previewFotoContainerBaru')" class="w-full text-xs text-gray-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer">
                    <div id="previewFotoContainerBaru" class="hidden shrink-0">
                        <img id="imgPreviewBaru" src="" alt="Preview" class="h-16 w-16 object-cover rounded-lg border-2 border-cyan-400 shadow">
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-xs font-medium text-gray-300 mb-1">Catatan Tambahan</label>
                <input type="text" name="remark" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="No bukti timbang, kondisi cacahan, dll...">
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                <button type="button" onclick="document.getElementById('modalTambahSale').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-emerald-600 hover:bg-emerald-700 text-white rounded-lg text-sm font-medium shadow-md">
                    <i class="fas fa-save mr-1.5"></i>Simpan Faktur Penjualan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Modal Pratinjau Foto Timbangan (Full Visualizer & Zoom) --}}
<div id="modalLihatFotoTimbangan" class="hidden fixed inset-0 bg-black/85 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-900 rounded-2xl w-full max-w-2xl border border-slate-700 shadow-2xl overflow-hidden flex flex-col max-h-[92vh]">
        <!-- Modal Header -->
        <div class="p-4 bg-slate-950 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-cyan-500/10 border border-cyan-500/30 flex items-center justify-center text-cyan-400 text-lg">
                    <i class="fas fa-scale-balanced"></i>
                </div>
                <div>
                    <h3 class="text-sm font-bold text-white flex items-center gap-2">
                        <span>Hasil Timbangan Kastamer</span>
                        <span id="modalTimbangIdBadge" class="text-xs font-mono px-2 py-0.5 rounded bg-slate-800 text-cyan-300"></span>
                    </h3>
                    <p class="text-[11px] text-slate-400 mt-0.5">
                        Pembeli: <span id="modalTimbangBuyer" class="text-slate-200 font-semibold"></span> &bull; 
                        Tanggal: <span id="modalTimbangTanggal" class="text-slate-200"></span> &bull; 
                        Tonase: <span id="modalTimbangQty" class="text-emerald-400 font-bold font-mono"></span> Kg
                    </p>
                </div>
            </div>
            <button type="button" onclick="tutupModalLihatTimbangan()" class="h-8 w-8 rounded-lg bg-slate-800 hover:bg-slate-700 text-slate-400 hover:text-white flex items-center justify-center transition">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <!-- Image Body -->
        <div class="p-4 bg-slate-950/60 overflow-auto flex-1 flex items-center justify-center min-h-[350px]">
            <img id="modalTimbangImg" src="" alt="Slip Timbangan" class="max-h-[65vh] w-auto max-w-full object-contain rounded-xl border border-slate-800 shadow-2xl transition-transform duration-200">
        </div>

        <!-- Modal Footer Actions -->
        <div class="p-4 bg-slate-950 border-t border-slate-800 flex flex-wrap items-center justify-between gap-3">
            <div class="flex items-center gap-2">
                <button type="button" onclick="cetakFotoTimbanganModal()" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                    <i class="fas fa-print text-amber-400"></i>
                    <span>Cetak Slip</span>
                </button>
                <a id="modalTimbangDownloadBtn" href="#" download="slip_timbangan.jpg" class="px-3 py-1.5 bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 rounded-xl text-xs font-semibold flex items-center gap-1.5 transition">
                    <i class="fas fa-download text-cyan-400"></i>
                    <span>Unduh</span>
                </a>
            </div>

            <div class="flex items-center gap-2">
                <!-- Form Ganti Foto -->
                <form id="formGantiFotoTimbangan" method="POST" enctype="multipart/form-data" class="inline-flex items-center gap-1.5">
                    @csrf
                    <label class="px-3 py-1.5 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold cursor-pointer transition flex items-center gap-1.5 shadow">
                        <i class="fas fa-camera-rotate"></i>
                        <span>Ganti Foto</span>
                        <input type="file" name="foto_timbangan" accept="image/*" onchange="document.getElementById('formGantiFotoTimbangan').submit()" class="hidden">
                    </label>
                </form>

                <!-- Form Hapus Foto -->
                <form id="formHapusFotoTimbangan" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus foto slip timbangan ini?')" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="px-3 py-1.5 bg-rose-600/20 hover:bg-rose-600 text-rose-300 hover:text-white border border-rose-500/30 rounded-xl text-xs font-semibold transition flex items-center gap-1.5">
                        <i class="fas fa-trash"></i>
                        <span>Hapus</span>
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

{{-- Modal Quick Upload Foto Timbangan --}}
<div id="modalUploadFotoTimbanganSingle" class="hidden fixed inset-0 bg-black/75 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-2xl p-6 w-full max-w-md border border-gray-700 shadow-2xl">
        <div class="flex items-center justify-between pb-3 border-b border-gray-700 mb-4">
            <h3 class="text-base font-bold text-white flex items-center gap-2">
                <i class="fas fa-camera text-cyan-400"></i> Upload Foto Slip Timbangan
            </h3>
            <button type="button" onclick="document.getElementById('modalUploadFotoTimbanganSingle').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <p class="text-xs text-slate-300 mb-3">
            Faktur: <span id="quickUploadId" class="font-bold text-cyan-400 font-mono"></span><br>
            Kastamer: <span id="quickUploadBuyer" class="text-white font-semibold"></span>
        </p>

        <form id="formQuickUploadFoto" method="POST" enctype="multipart/form-data" class="space-y-4">
            @csrf
            <div class="bg-gray-900/70 p-4 rounded-xl border border-dashed border-cyan-500/40 text-center">
                <i class="fas fa-cloud-arrow-up text-3xl text-cyan-400 mb-2"></i>
                <p class="text-xs text-white font-medium mb-1">Pilih atau Ambil Foto Slip Timbangan</p>
                <p class="text-[11px] text-slate-400 mb-3">Otomatis dioptimasi &amp; dikompresi oleh sistem.</p>
                <input type="file" name="foto_timbangan" accept="image/*" required class="w-full text-xs text-gray-400 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-cyan-600 file:text-white hover:file:bg-cyan-500 cursor-pointer">
            </div>

            <div class="flex justify-end gap-3 pt-2">
                <button type="button" onclick="document.getElementById('modalUploadFotoTimbanganSingle').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-xl text-xs font-semibold">
                    Batal
                </button>
                <button type="submit" class="px-4 py-2 bg-cyan-600 hover:bg-cyan-500 text-white rounded-xl text-xs font-bold shadow flex items-center gap-1.5">
                    <i class="fas fa-upload"></i>
                    <span>Upload &amp; Kompres</span>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let saleItemIndex = 1;

function tambahBarisItemSale() {
    const container = document.getElementById('containerItemSale');
    const div = document.createElement('div');
    div.className = 'item-sale-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end';
    div.innerHTML = `
        <div class="md:col-span-4">
            <label class="block text-[11px] text-gray-400 mb-1">Pilih / Nama Produk Cacahan *</label>
            <input type="text" name="items[${saleItemIndex}][nama_barang]" list="listBarangSale" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="HD Gilingan Hitam/PE/PP" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Qty Terjual (Kg) *</label>
            <input type="number" step="0.01" name="items[${saleItemIndex}][qty_terjual]" oninput="hitungSemuaSale()" class="sale-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0" required>
        </div>
        <div class="md:col-span-1">
            <label class="block text-[11px] text-gray-400 mb-1">Jml Sak</label>
            <input type="number" name="items[${saleItemIndex}][jumlah_sak]" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-xs" placeholder="0">
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Harga/Kg (Rp) *</label>
            <input type="number" step="0.01" name="items[${saleItemIndex}][harga_satuan]" oninput="hitungSemuaSale()" class="sale-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Rp 0" required>
        </div>
        <div class="md:col-span-1">
            <label class="block text-[11px] text-gray-400 mb-1">Rafaksi (%)</label>
            <input type="number" step="0.01" name="items[${saleItemIndex}][diskon_persen]" oninput="hitungSemuaSale()" class="sale-row-diskon w-full bg-gray-700 border border-gray-600 text-white rounded px-2 py-1.5 text-xs" placeholder="0.15">
        </div>
        <div class="md:col-span-1">
            <label class="block text-[11px] text-gray-400 mb-1">Net</label>
            <input type="text" readonly class="sale-row-subtotal w-full bg-gray-950 border border-gray-800 text-emerald-400 font-bold rounded px-2 py-1.5 text-xs" value="Rp 0">
        </div>
        <div class="md:col-span-1 flex justify-center pb-1">
            <button type="button" onclick="hapusBarisItemSale(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    saleItemIndex++;
}

function hapusBarisItemSale(btn) {
    const rows = document.querySelectorAll('.item-sale-row');
    if (rows.length > 1) {
        btn.closest('.item-sale-row').remove();
        hitungSemuaSale();
    } else {
        alert('Minimal harus ada 1 item barang yang dijual.');
    }
}

function hitungSemuaSale() {
    const rows = document.querySelectorAll('.item-sale-row');
    let grandTotal = 0;

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.sale-row-qty').value) || 0;
        const harga = parseFloat(row.querySelector('.sale-row-harga').value) || 0;
        const diskonP = parseFloat(row.querySelector('.sale-row-diskon').value) || 0;
        const subtotalAwal = qty * harga;
        const diskonRp = subtotalAwal * diskonP;
        const subtotalNet = Math.max(0, subtotalAwal - diskonRp);
        row.querySelector('.sale-row-subtotal').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotalNet);
        grandTotal += subtotalNet;
    });

    document.getElementById('sale_grand_total_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
}

function previewFotoTimbangan(input, previewContainerId) {
    const container = document.getElementById(previewContainerId);
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            container.querySelector('img').src = e.target.result;
            container.classList.remove('hidden');
        };
        reader.readAsDataURL(input.files[0]);
    } else {
        container.classList.add('hidden');
    }
}

function bukaModalLihatTimbangan(idPenjualan, buyer, imgUrl, qty, tanggal, actionUpload, actionDelete) {
    document.getElementById('modalTimbangIdBadge').innerText = idPenjualan;
    document.getElementById('modalTimbangBuyer').innerText = buyer;
    document.getElementById('modalTimbangTanggal').innerText = tanggal;
    document.getElementById('modalTimbangQty').innerText = qty;
    document.getElementById('modalTimbangImg').src = imgUrl;
    document.getElementById('modalTimbangDownloadBtn').href = imgUrl;
    document.getElementById('formGantiFotoTimbangan').action = actionUpload;
    document.getElementById('formHapusFotoTimbangan').action = actionDelete;
    document.getElementById('modalLihatFotoTimbangan').classList.remove('hidden');
}

function tutupModalLihatTimbangan() {
    document.getElementById('modalLihatFotoTimbangan').classList.add('hidden');
}

function bukaModalUploadTimbangan(id, idPenjualan, buyer, actionUrl) {
    document.getElementById('quickUploadId').innerText = idPenjualan;
    document.getElementById('quickUploadBuyer').innerText = buyer;
    document.getElementById('formQuickUploadFoto').action = actionUrl;
    document.getElementById('modalUploadFotoTimbanganSingle').classList.remove('hidden');
}

function cetakFotoTimbanganModal() {
    const imgUrl = document.getElementById('modalTimbangImg').src;
    const idPenjualan = document.getElementById('modalTimbangIdBadge').innerText;
    const buyer = document.getElementById('modalTimbangBuyer').innerText;
    const tanggal = document.getElementById('modalTimbangTanggal').innerText;
    const qty = document.getElementById('modalTimbangQty').innerText;

    const printWin = window.open('', '_blank');
    printWin.document.write(`
        <!DOCTYPE html>
        <html>
        <head>
            <title>Slip Timbangan - ${idPenjualan}</title>
            <style>
                @page { size: auto; margin: 15mm; }
                body { font-family: sans-serif; text-align: center; color: #111; padding: 10px; }
                .header { margin-bottom: 20px; border-bottom: 2px solid #333; padding-bottom: 10px; }
                .title { font-size: 18px; font-weight: bold; text-transform: uppercase; }
                .meta { font-size: 13px; color: #444; margin-top: 5px; }
                img { max-width: 100%; max-height: 75vh; object-fit: contain; border: 1px solid #ccc; border-radius: 6px; box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
                .footer { margin-top: 20px; font-size: 11px; color: #777; }
            </style>
        </head>
        <body>
            <div class="header">
                <div class="title">PT PINASTIKA BHAKTI SEMESTA</div>
                <div class="meta">ARSIP SLIP HASIL TIMBANGAN KASTAMER (PENJUALAN)</div>
                <div class="meta"><strong>Faktur:</strong> ${idPenjualan} &bull; <strong>Kastamer:</strong> ${buyer} &bull; <strong>Tanggal:</strong> ${tanggal} &bull; <strong>Tonase:</strong> ${qty} Kg</div>
            </div>
            <img src="${imgUrl}" onload="window.print(); window.close();" />
            <div class="footer">Dicetak dari Sistem ERP PT Pinastika Bhakti Semesta pada ${new Date().toLocaleString('id-ID')}</div>
        </body>
        </html>
    `);
    printWin.document.close();
}
</script>
@endsection
