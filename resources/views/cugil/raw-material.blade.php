@extends('layouts.admin')
@section('title', 'Terima Bahan Baku (Raw Material) - CUGIL PBS-ERP PT Pinastika Bhakti Semesta')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-boxes-stacked mr-2 text-amber-400"></i>Penerimaan Bahan Baku (CUGIL RAW)</h1>
            <p class="text-gray-400 text-sm mt-1">Penerimaan &amp; timbang bahan masuk dari Purchase Order (PO) yang sudah terkirim/tiba di PT PBS</p>
        </div>
        <div class="flex items-center gap-2.5">
            @if(auth()->user()->canMutate())
            <button onclick="bukaModalRaw(null)" class="bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg font-medium shadow-lg hover:shadow-amber-600/30 transition text-sm">
                <i class="fas fa-plus mr-1.5"></i>Input Bahan Masuk (Tarik PO / Manual)
            </button>
            @else
            <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
                <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
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

    {{-- Pending PO Notification Banner --}}
    @if(isset($pendingPOs) && $pendingPOs->count() > 0)
        <div class="bg-gradient-to-r from-blue-900/40 via-amber-900/30 to-blue-900/40 p-4 rounded-2xl border border-amber-500/40 flex flex-col md:flex-row md:items-center md:justify-between gap-3 shadow-md">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-xl bg-amber-500/20 text-amber-400 flex items-center justify-center text-lg border border-amber-500/30 shrink-0">
                    <i class="fas fa-truck-ramp-box"></i>
                </div>
                <div>
                    <h4 class="text-xs sm:text-sm font-bold text-white">Ada {{ $pendingPOs->count() }} Purchase Order (PO) Menunggu Timbang Diterima</h4>
                    <p class="text-[11px] text-gray-300">Pilih PO di bawah untuk langsung memproses penerimaan dan timbang masuk ke gudang PBS</p>
                </div>
            </div>
            <div class="flex flex-wrap items-center gap-2">
                @foreach($pendingPOs->take(3) as $ppo)
                    <button type="button" onclick="bukaModalDenganPO('{{ $ppo->nomor_po }}')" class="px-3 py-1.5 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-xs font-semibold shadow transition">
                        <i class="fas fa-download mr-1"></i>Terima PO: {{ $ppo->nomor_po }}
                    </button>
                @endforeach
            </div>
        </div>
    @endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Total Tagihan Bersih</p>
            <p class="text-2xl font-bold text-white mt-1">Rp {{ number_format($totalTagihan, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Total Pembayaran Kas</p>
            <p class="text-2xl font-bold text-emerald-400 mt-1">Rp {{ number_format($totalPayment, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Sisa Tagihan / Hutang</p>
            <p class="text-2xl font-bold text-red-400 mt-1">Rp {{ number_format($totalSisa, 0, ',', '.') }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-xs font-medium uppercase">Belum Lunas</p>
            <p class="text-2xl font-bold text-amber-400 mt-1">{{ number_format($belumLunasCount, 0, ',', '.') }} <span class="text-sm font-normal text-gray-400">dokumen</span></p>
        </div>
    </div>

    {{-- Filter Form --}}
    <form method="GET" action="{{ route('cugil.raw.index') }}" class="bg-gray-800/80 p-4 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-5 gap-3">
        <div>
            <label class="block text-xs text-gray-400 mb-1">Supplier</label>
            <select name="supplier" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500">
                <option value="">Semua Supplier</option>
                @foreach($suppliers as $sup)
                    <option value="{{ $sup->kode_supplier }}" {{ request('supplier') == $sup->kode_supplier ? 'selected' : '' }}>
                        {{ $sup->kode_supplier }} - {{ $sup->nama_supplier }}
                    </option>
                @endforeach
            </select>
        </div>
        <div>
            <label class="block text-xs text-gray-400 mb-1">Status Pelunasan</label>
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
            <button type="submit" class="flex-1 bg-amber-600 hover:bg-amber-700 text-white px-4 py-2 rounded-lg text-sm font-medium">
                <i class="fas fa-filter mr-1"></i>Filter
            </button>
            @if(request('supplier') || request('status') || request('dari') || request('sampai'))
                <a href="{{ route('cugil.raw.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-3 py-2 rounded-lg text-sm">
                    <i class="fas fa-times"></i>
                </a>
            @endif
        </div>
    </form>

    {{-- Table Raw Materials --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-x-auto shadow-sm">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/60 border-b border-gray-700">
                <tr>
                    <th class="px-3 py-3">#</th>
                    <th class="px-3 py-3">Ref PO Asal</th>
                    <th class="px-3 py-3">Tanggal Timbang</th>
                    <th class="px-3 py-3">Pemasok</th>
                    <th class="px-3 py-3">Daftar SKU Bahan Masuk</th>
                    <th class="px-3 py-3 text-right">Total Qty (Kg)</th>
                    <th class="px-3 py-3 text-right">Bruto</th>
                    <th class="px-3 py-3 text-right">Total Rafaksi</th>
                    <th class="px-3 py-3 text-right">Tagihan Net</th>
                    <th class="px-3 py-3 text-right">Dibayar</th>
                    <th class="px-3 py-3 text-right">Sisa</th>
                    <th class="px-3 py-3 text-center">Status</th>
                    <th class="px-3 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-700/60">
                @forelse($rawMaterials as $i => $raw)
                <tr class="hover:bg-gray-700/40 transition align-top">
                    <td class="px-3 py-2.5 text-gray-400 text-xs">{{ $rawMaterials->firstItem() + $i }}</td>
                    <td class="px-3 py-2.5 font-mono text-xs text-amber-400 font-semibold whitespace-nowrap">
                        @if($raw->nomor_po)
                            <a href="{{ route('cugil.po.index', ['supplier' => $raw->kode_supplier]) }}" class="hover:underline flex items-center gap-1">
                                <i class="fas fa-link text-[10px]"></i> {{ $raw->nomor_po }}
                            </a>
                        @else
                            <span class="text-gray-500 italic">Beli Langsung</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-gray-300 text-xs whitespace-nowrap">{{ $raw->tanggal ? $raw->tanggal->format('d/m/Y') : '-' }}</td>
                    <td class="px-3 py-2.5">
                        <div class="text-gray-200 text-xs font-semibold">{{ $raw->nama_pemasok ?? $raw->kode_supplier }}</div>
                        <span class="text-[10px] text-gray-400 font-mono">{{ $raw->kode_supplier }}</span>
                        @if($raw->truk)
                            <div class="text-[10px] text-blue-400 mt-0.5"><i class="fas fa-truck text-[9px] mr-1"></i>{{ $raw->truk }}</div>
                        @endif
                    </td>
                    <td class="px-3 py-2.5">
                        @if($raw->items && $raw->items->count() > 0)
                            <div class="space-y-1.5 min-w-[220px]">
                                @foreach($raw->items as $it)
                                    <div class="bg-gray-900/60 p-2 rounded-lg border border-gray-700/60 text-xs">
                                        <div class="font-medium text-white">{{ $it->nama_barang }}</div>
                                        <div class="text-[11px] text-gray-400 flex justify-between mt-0.5">
                                            <span>{{ number_format($it->qty, 1) }} Kg &times; Rp {{ number_format($it->harga_satuan, 0, ',', '.') }}</span>
                                            <span class="text-amber-300 font-medium">Rp {{ number_format($it->subtotal, 0, ',', '.') }}</span>
                                        </div>
                                        @if($it->diskon_rafaksi > 0)
                                            <div class="text-[10px] text-red-400">Rafaksi: -Rp {{ number_format($it->diskon_rafaksi, 0, ',', '.') }}</div>
                                        @endif
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <span class="text-gray-400 text-xs italic">-</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-right font-bold text-white text-xs whitespace-nowrap">{{ number_format($raw->total_qty, 1) }} Kg</td>
                    <td class="px-3 py-2.5 text-right text-gray-300 text-xs whitespace-nowrap">Rp {{ number_format($raw->total_bruto, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5 text-right text-red-400 text-xs whitespace-nowrap">
                        {{ $raw->total_rafaksi > 0 ? '-Rp ' . number_format($raw->total_rafaksi, 0, ',', '.') : '-' }}
                    </td>
                    <td class="px-3 py-2.5 text-right font-bold text-white text-xs whitespace-nowrap">Rp {{ number_format($raw->tagihan, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5 text-right font-medium text-emerald-400 text-xs whitespace-nowrap">Rp {{ number_format($raw->payment, 0, ',', '.') }}</td>
                    <td class="px-3 py-2.5 text-right font-medium {{ $raw->sisa_tagihan > 0 ? 'text-red-400' : 'text-gray-400' }} text-xs whitespace-nowrap">
                        Rp {{ number_format($raw->sisa_tagihan, 0, ',', '.') }}
                    </td>
                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                        @if($raw->status_lunas == 'LUNAS')
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-green-900/70 text-green-300 border border-green-700">LUNAS</span>
                        @else
                            <span class="px-2 py-0.5 rounded text-[10px] font-semibold bg-red-900/70 text-red-300 border border-red-700">BELUM</span>
                        @endif
                    </td>
                    <td class="px-3 py-2.5 text-center whitespace-nowrap">
                        <div class="flex items-center justify-center gap-1">
                            <a href="{{ route('cugil.raw.tanda-terima', $raw->id) }}" target="_blank" class="px-2 py-1 bg-blue-600/20 hover:bg-blue-600 text-blue-300 hover:text-white rounded text-xs transition font-semibold" title="Cetak Tanda Terima & Tiket Timbang">
                                <i class="fas fa-print mr-1"></i>Bukti
                            </a>
                            @if(auth()->user()->canMutate())
                            <button onclick="document.getElementById('modalBayarRaw{{ $raw->id }}').classList.remove('hidden')" class="px-2 py-1 bg-emerald-600/20 hover:bg-emerald-600 text-emerald-300 hover:text-white rounded text-xs transition" title="Update Pelunasan">
                                <i class="fas fa-money-bill-wave mr-1"></i>Bayar
                            </button>
                            <form action="{{ route('cugil.raw.destroy', $raw->id) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin MENGHAPUS penerimaan bahan baku ini ({{ $raw->nama_pemasok }})?\n\n- Stok barang yang masuk akan OTOMATIS DIKURANGI/ROLLBACK dari gudang!\n- Jika terkait PO, status PO akan otomatis dikembalikan menjadi Belum Diterima!')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="px-2 py-1 bg-rose-500/20 hover:bg-rose-600 text-rose-300 hover:text-white rounded text-xs transition" title="Hapus Bahan Masuk & Rollback Stok">
                                    <i class="fas fa-trash-can"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>

                {{-- Modal Update Payment --}}
                <div id="modalBayarRaw{{ $raw->id }}" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4">
                    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-md border border-gray-700 shadow-2xl">
                        <h3 class="text-lg font-bold text-white mb-2">Update Pembayaran Bahan Masuk</h3>
                        <p class="text-xs text-gray-400 mb-4">{{ $raw->nama_pemasok }} &bull; Total Tagihan: <strong class="text-white">Rp {{ number_format($raw->tagihan, 0, ',', '.') }}</strong></p>
                        
                        <form action="{{ route('cugil.raw.updateStatus', $raw->id) }}" method="POST" class="space-y-4">
                            @csrf
                            @method('PUT')
                            <div>
                                <label class="block text-xs font-medium text-gray-300 mb-1">Jumlah Pembayaran / Payment (Rp) *</label>
                                <input type="number" step="0.01" name="payment" value="{{ $raw->payment }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm focus:ring-amber-500 focus:border-amber-500" required>
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-300 mb-1">Armada / Truk</label>
                                <input type="text" name="truk" value="{{ $raw->truk }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="No Polisi / Sopir">
                            </div>
                            <div>
                                <label class="block text-xs font-medium text-gray-300 mb-1">Status Ongkos Truk</label>
                                <select name="status_truk_lunas" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                                    <option value="LUNAS" {{ $raw->status_truk_lunas == 'LUNAS' ? 'selected' : '' }}>LUNAS</option>
                                    <option value="BELUM" {{ $raw->status_truk_lunas == 'BELUM' ? 'selected' : '' }}>BELUM</option>
                                </select>
                            </div>
                            <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                                <button type="button" onclick="document.getElementById('modalBayarRaw{{ $raw->id }}').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium">Simpan Perubahan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="13" class="px-4 py-10 text-center text-gray-500">
                        <i class="fas fa-inbox text-3xl mb-2 text-gray-600"></i>
                        <p>Belum ada data penerimaan bahan baku.</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $rawMaterials->links() }}</div>
</div>

{{-- Modal Input Penerimaan Bahan Masuk (Tarik dari PO / Multi-SKU) --}}
<div id="modalTambahRaw" class="hidden fixed inset-0 bg-black/70 z-50 flex items-center justify-center p-4 overflow-y-auto">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-4xl border border-gray-700 shadow-2xl my-8">
        <div class="flex items-center justify-between pb-3 border-b border-gray-700 mb-4">
            <h3 class="text-lg font-bold text-white flex items-center gap-2">
                <i class="fas fa-boxes-stacked text-amber-400"></i> Input Penerimaan Bahan Masuk (CUGIL RAW)
            </h3>
            <button type="button" onclick="document.getElementById('modalTambahRaw').classList.add('hidden')" class="text-gray-400 hover:text-white">
                <i class="fas fa-times text-lg"></i>
            </button>
        </div>

        {{-- Tarik Data dari PO Banner Selector --}}
        <div class="p-3.5 rounded-xl bg-blue-950/40 border border-blue-500/40 mb-4">
            <label class="block text-xs font-bold text-blue-300 uppercase tracking-wider mb-1.5">
                <i class="fas fa-file-import mr-1"></i>Tarik Data Otomatis dari Purchase Order (PO):
            </label>
            <div class="flex flex-col sm:flex-row items-center gap-2">
                <select id="pilihPOModal" onchange="tarikDataDariPO(this.value)" class="w-full sm:flex-1 bg-gray-900 border border-blue-500/50 text-white rounded-lg px-3 py-2 text-sm focus:ring-blue-500 focus:border-blue-500 font-mono">
                    <option value="">-- Pilih Nomor PO untuk Auto-Fill Item & Harga --</option>
                    @foreach($allPOs as $po)
                        <option value="{{ $po->nomor_po }}" {{ request('tarik_po') == $po->nomor_po ? 'selected' : '' }}>
                            PO: {{ $po->nomor_po }} &bull; {{ $po->nama_vendor }} &bull; Tgl: {{ $po->tanggal->format('d/m/Y') }} ({{ $po->items->count() }} SKU, Total: {{ number_format($po->total_qty, 1) }} Kg)
                        </option>
                    @endforeach
                </select>
                <span class="text-[11px] text-gray-400 whitespace-nowrap">atau input manual di bawah</span>
            </div>
        </div>

        <form action="{{ route('cugil.raw.store') }}" method="POST" class="space-y-4">
            @csrf
            {{-- Header Form --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Nomor PO / Ref</label>
                    <input type="text" id="raw_nomor_po" name="nomor_po" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm font-mono text-amber-400 font-semibold" placeholder="Contoh: 0260/PBS/12/2023">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Tanggal Timbang *</label>
                    <input type="date" id="raw_tanggal" name="tanggal" value="{{ date('Y-m-d') }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Pemasok / Supplier (1 Pengirim) *</label>
                    <select id="raw_kode_supplier" name="kode_supplier" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" required>
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->kode_supplier }}">{{ $sup->kode_supplier }} - {{ $sup->nama_supplier }} ({{ $sup->kota }})</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Batch Produksi</label>
                    <input type="text" id="raw_batch" name="batch_produksi" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Contoh: 05-BUDI / 3-ARIS">
                </div>
            </div>

            {{-- Detail Items (Multi-SKU Bahan Masuk) --}}
            <div class="space-y-3">
                <div class="flex items-center justify-between">
                    <h4 class="text-sm font-bold text-amber-400 uppercase tracking-wider">
                        <i class="fas fa-boxes mr-1.5"></i>Daftar SKU Bahan yang Ditimbang Masuk
                    </h4>
                    <button type="button" onclick="tambahBarisItemRaw()" class="px-3 py-1.5 bg-amber-600/30 hover:bg-amber-600 border border-amber-500 text-amber-300 hover:text-white rounded-lg text-xs font-semibold transition">
                        <i class="fas fa-plus mr-1"></i>+ Tambah Item Bahan
                    </button>
                </div>

                <div id="containerItemRaw" class="space-y-2">
                    {{-- Row 1 default --}}
                    <div class="item-raw-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end">
                        <div class="md:col-span-4">
                            <label class="block text-[11px] text-gray-400 mb-1">Pilih / Nama Bahan Baku *</label>
                            <input type="text" name="items[0][nama_barang]" list="listBarangRaw" class="raw-row-nama w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Contoh: HD Kresek Putih/Merah" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Qty Timbang (Kg) *</label>
                            <input type="number" step="0.01" name="items[0][qty]" oninput="hitungSemuaRaw()" class="raw-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Harga Satuan (Rp) *</label>
                            <input type="number" step="0.01" name="items[0][harga_satuan]" oninput="hitungSemuaRaw()" class="raw-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Rp 0" required>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[11px] text-gray-400 mb-1">Rafaksi/Kotor (Rp)</label>
                            <input type="number" step="0.01" name="items[0][diskon_rafaksi]" oninput="hitungSemuaRaw()" class="raw-row-rafaksi w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0">
                        </div>
                        <div class="md:col-span-1">
                            <label class="block text-[11px] text-gray-400 mb-1">Net</label>
                            <input type="text" readonly class="raw-row-subtotal w-full bg-gray-950 border border-gray-800 text-amber-400 font-bold rounded px-2 py-1.5 text-xs" value="Rp 0">
                        </div>
                        <div class="md:col-span-1 flex justify-center pb-1">
                            <button type="button" onclick="hapusBarisItemRaw(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <datalist id="listBarangRaw">
                @foreach($barangs as $brg)
                    <option value="{{ $brg->nama_barang }}">{{ $brg->kode_barang }} - {{ $brg->kategori }}</option>
                @endforeach
            </datalist>

            {{-- Footer Summary & Payment --}}
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4 bg-gray-900/60 p-4 rounded-xl border border-gray-700">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Ongkos Angkut (1 Armada)</label>
                    <input type="number" step="0.01" id="raw_ongkos_angkut" name="ongkos_angkut" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Total Tagihan Net</label>
                    <input type="text" id="raw_grand_total_display" readonly class="w-full bg-gray-950 border border-gray-700 text-amber-400 font-black text-base rounded-lg px-3 py-2" value="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Dibayar / Kas Keluar (Rp)</label>
                    <input type="number" step="0.01" name="payment" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Rp 0">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Armada Truk / Sopir</label>
                    <input type="text" name="truk" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Truk W 8093 NN / ARIS">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Petugas Timbang</label>
                    <input type="text" name="petugas" value="WINARDI" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-medium text-gray-300 mb-1">Catatan Tambahan</label>
                    <input type="text" id="raw_remark" name="remark" class="w-full bg-gray-700 border border-gray-600 text-white rounded-lg px-3 py-2 text-sm" placeholder="Kondisi barang basah/kotor...">
                </div>
            </div>

            <div class="flex justify-end gap-3 pt-3 border-t border-gray-700">
                <button type="button" onclick="document.getElementById('modalTambahRaw').classList.add('hidden')" class="px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg text-sm">
                    Batal
                </button>
                <button type="submit" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-lg text-sm font-medium shadow-md">
                    <i class="fas fa-save mr-1.5"></i>Simpan Penerimaan Bahan
                </button>
            </div>
        </form>
    </div>
</div>

{{-- Data PO Payload for Instant JavaScript Pre-fill --}}
<script>
const dataPOs = @json($allPOs);
let rawItemIndex = 1;

function bukaModalRaw() {
    document.getElementById('modalTambahRaw').classList.remove('hidden');
}

function bukaModalDenganPO(nomorPo) {
    bukaModalRaw();
    document.getElementById('pilihPOModal').value = nomorPo;
    tarikDataDariPO(nomorPo);
}

function tarikDataDariPO(nomorPo) {
    if (!nomorPo) return;
    const po = dataPOs.find(p => p.nomor_po === nomorPo);
    if (!po) return;

    // Fill Header
    document.getElementById('raw_nomor_po').value = po.nomor_po;
    document.getElementById('raw_kode_supplier').value = po.kode_supplier;
    document.getElementById('raw_ongkos_angkut').value = po.ongkos_angkut || 0;
    if (po.keterangan) {
        document.getElementById('raw_remark').value = 'Re: ' + po.keterangan;
    }

    // Fill Items
    const container = document.getElementById('containerItemRaw');
    container.innerHTML = '';
    rawItemIndex = 0;

    if (po.items && po.items.length > 0) {
        po.items.forEach((it, idx) => {
            const div = document.createElement('div');
            div.className = 'item-raw-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end';
            div.innerHTML = `
                <div class="md:col-span-4">
                    <label class="block text-[11px] text-gray-400 mb-1">Nama Bahan Baku *</label>
                    <input type="text" name="items[${idx}][nama_barang]" value="${it.nama_barang}" list="listBarangRaw" class="raw-row-nama w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" required>
                    <input type="hidden" name="items[${idx}][kode_barang]" value="${it.kode_barang || ''}">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] text-gray-400 mb-1">Qty Timbang (Kg) *</label>
                    <input type="number" step="0.01" name="items[${idx}][qty]" value="${it.qty}" oninput="hitungSemuaRaw()" class="raw-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs font-bold" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] text-gray-400 mb-1">Harga Satuan (Rp) *</label>
                    <input type="number" step="0.01" name="items[${idx}][harga_satuan]" value="${it.harga_satuan}" oninput="hitungSemuaRaw()" class="raw-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" required>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-[11px] text-gray-400 mb-1">Rafaksi/Kotor (Rp)</label>
                    <input type="number" step="0.01" name="items[${idx}][diskon_rafaksi]" value="0" oninput="hitungSemuaRaw()" class="raw-row-rafaksi w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0">
                </div>
                <div class="md:col-span-1">
                    <label class="block text-[11px] text-gray-400 mb-1">Net</label>
                    <input type="text" readonly class="raw-row-subtotal w-full bg-gray-950 border border-gray-800 text-amber-400 font-bold rounded px-2 py-1.5 text-xs" value="Rp 0">
                </div>
                <div class="md:col-span-1 flex justify-center pb-1">
                    <button type="button" onclick="hapusBarisItemRaw(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                        <i class="fas fa-trash"></i>
                    </button>
                </div>
            `;
            container.appendChild(div);
            rawItemIndex++;
        });
    }

    hitungSemuaRaw();
}

function tambahBarisItemRaw() {
    const container = document.getElementById('containerItemRaw');
    const div = document.createElement('div');
    div.className = 'item-raw-row bg-gray-900/40 p-3 rounded-xl border border-gray-700 grid grid-cols-1 md:grid-cols-12 gap-3 items-end';
    div.innerHTML = `
        <div class="md:col-span-4">
            <label class="block text-[11px] text-gray-400 mb-1">Pilih / Nama Bahan Baku *</label>
            <input type="text" name="items[${rawItemIndex}][nama_barang]" list="listBarangRaw" class="raw-row-nama w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Contoh: HD Kresek Putih/Merah" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Qty Timbang (Kg) *</label>
            <input type="number" step="0.01" name="items[${rawItemIndex}][qty]" oninput="hitungSemuaRaw()" class="raw-row-qty w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Harga Satuan (Rp) *</label>
            <input type="number" step="0.01" name="items[${rawItemIndex}][harga_satuan]" oninput="hitungSemuaRaw()" class="raw-row-harga w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="Rp 0" required>
        </div>
        <div class="md:col-span-2">
            <label class="block text-[11px] text-gray-400 mb-1">Rafaksi/Kotor (Rp)</label>
            <input type="number" step="0.01" name="items[${rawItemIndex}][diskon_rafaksi]" oninput="hitungSemuaRaw()" class="raw-row-rafaksi w-full bg-gray-700 border border-gray-600 text-white rounded px-2.5 py-1.5 text-xs" placeholder="0">
        </div>
        <div class="md:col-span-1">
            <label class="block text-[11px] text-gray-400 mb-1">Net</label>
            <input type="text" readonly class="raw-row-subtotal w-full bg-gray-950 border border-gray-800 text-amber-400 font-bold rounded px-2 py-1.5 text-xs" value="Rp 0">
        </div>
        <div class="md:col-span-1 flex justify-center pb-1">
            <button type="button" onclick="hapusBarisItemRaw(this)" class="text-red-400 hover:text-red-300 p-1 text-sm" title="Hapus Baris">
                <i class="fas fa-trash"></i>
            </button>
        </div>
    `;
    container.appendChild(div);
    rawItemIndex++;
}

function hapusBarisItemRaw(btn) {
    const rows = document.querySelectorAll('.item-raw-row');
    if (rows.length > 1) {
        btn.closest('.item-raw-row').remove();
        hitungSemuaRaw();
    } else {
        alert('Minimal harus ada 1 item bahan baku.');
    }
}

function hitungSemuaRaw() {
    const rows = document.querySelectorAll('.item-raw-row');
    let grandTotal = 0;

    rows.forEach(row => {
        const qty = parseFloat(row.querySelector('.raw-row-qty').value) || 0;
        const harga = parseFloat(row.querySelector('.raw-row-harga').value) || 0;
        const rafaksi = parseFloat(row.querySelector('.raw-row-rafaksi').value) || 0;
        const subtotal = Math.max(0, (qty * harga) - rafaksi);
        row.querySelector('.raw-row-subtotal').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(subtotal);
        grandTotal += subtotal;
    });

    document.getElementById('raw_grand_total_display').value = 'Rp ' + new Intl.NumberFormat('id-ID').format(grandTotal);
}

// Auto-open modal if URL has ?tarik_po=
document.addEventListener('DOMContentLoaded', function() {
    const urlParams = new URLSearchParams(window.location.search);
    const tarikPo = urlParams.get('tarik_po');
    if (tarikPo) {
        bukaModalDenganPO(tarikPo);
    }
});
</script>
@endsection
