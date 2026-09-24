@extends('layouts.admin')
@section('title', 'Daftar Supplier - CUGIL PBS-ERP PT Pinastika Bhakti Semesta')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-truck mr-2 text-blue-400"></i>Daftar Supplier CUGIL</h1>
            <p class="text-gray-400 text-sm mt-1">Master data pemasok bahan baku cuci giling</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-1"></i>Tambah Supplier</button>
    </div>

    @if(session('success'))<div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-sm">Total Supplier</p>
            <p class="text-3xl font-bold text-white mt-1">{{ $totalSupplier }}</p>
        </div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700">
            <p class="text-gray-400 text-sm">Total Hutang</p>
            <p class="text-3xl font-bold text-red-400 mt-1">Rp {{ number_format($totalHutang, 0, ',', '.') }}</p>
        </div>
    </div>

    {{-- Search --}}
    <form method="GET" action="{{ route('cugil.supplier.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/kode/kota supplier..." class="flex-1 bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-blue-500 focus:border-blue-500">
        <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        @if($search)<a href="{{ route('cugil.supplier.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"><i class="fas fa-times"></i></a>@endif
    </form>

    {{-- Table --}}
    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Supplier</th>
                    <th class="px-4 py-3">Kota</th>
                    <th class="px-4 py-3">Telepon</th>
                    <th class="px-4 py-3">Item Barang</th>
                    <th class="px-4 py-3 text-right">Hutang</th>
                    <th class="px-4 py-3 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($suppliers as $i => $s)
                <tr class="border-t border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-400">{{ $suppliers->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-blue-400 font-mono text-xs">{{ $s->kode_supplier }}</td>
                    <td class="px-4 py-3 text-white font-medium">{{ $s->nama_supplier }}</td>
                    <td class="px-4 py-3 text-gray-300">{{ $s->kota ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-300">{{ $s->telepon ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs max-w-[200px] truncate">{{ $s->item_barang ?? '-' }}</td>
                    <td class="px-4 py-3 text-right {{ $s->hutang > 0 ? 'text-red-400' : 'text-gray-400' }}">Rp {{ number_format($s->hutang, 0, ',', '.') }}</td>
                    <td class="px-4 py-3 text-center">
                        <button onclick="document.getElementById('editModal{{ $s->id }}').classList.remove('hidden')" class="text-yellow-400 hover:text-yellow-300 mr-2"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('cugil.supplier.destroy', $s->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus supplier {{ $s->nama_supplier }}?')">@csrf @method('DELETE')<button type="submit" class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                {{-- Edit Modal --}}
                <div id="editModal{{ $s->id }}" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
                    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4">Edit Supplier: {{ $s->kode_supplier }}</h3>
                        <form action="{{ route('cugil.supplier.update', $s->id) }}" method="POST" class="space-y-3">@csrf @method('PUT')
                            <input type="text" name="nama_supplier" value="{{ $s->nama_supplier }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Nama Supplier" required>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="alamat" value="{{ $s->alamat }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Alamat">
                                <input type="text" name="kota" value="{{ $s->kota }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kota">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="telepon" value="{{ $s->telepon }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Telepon">
                                <input type="text" name="item_barang" value="{{ $s->item_barang }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Item Barang">
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" onclick="this.closest('[id^=editModal]').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada data supplier.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $suppliers->links() }}</div>
</div>

{{-- Modal Tambah --}}
<div id="modalTambah" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700">
        <h3 class="text-lg font-bold text-white mb-4"><i class="fas fa-plus-circle mr-2 text-blue-400"></i>Tambah Supplier Baru</h3>
        <form action="{{ route('cugil.supplier.store') }}" method="POST" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="kode_supplier" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kode Supplier *" required>
                <input type="text" name="nama_supplier" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Nama Supplier *" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="alamat" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Alamat">
                <input type="text" name="kota" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kota">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="telepon" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Telepon / WA">
                <input type="text" name="item_barang" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Item Barang">
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="bank" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Nama Bank">
                <input type="text" name="no_rekening" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="No Rekening">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
