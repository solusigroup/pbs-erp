@extends('layouts.admin')
@section('title', 'Daftar Barang - CUGIL PBS-ERP PT Pinastika Bhakti Semesta')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-boxes mr-2 text-purple-400"></i>Daftar Barang CUGIL</h1>
            <p class="text-gray-400 text-sm mt-1">Katalog bahan baku &amp; hasil cuci giling ({{ $totalBarang }} item)</p>
        </div>
        <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-1"></i>Tambah Barang</button>
    </div>
    @if(session('success'))<div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif

    {{-- Filter Kategori --}}
    <div class="flex flex-wrap gap-2">
        <a href="{{ route('cugil.barang.index') }}" class="px-3 py-1.5 rounded-lg text-sm {{ !$kategori ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">Semua</a>
        @foreach($kategoris as $kat)
        <a href="{{ route('cugil.barang.index', ['kategori' => $kat]) }}" class="px-3 py-1.5 rounded-lg text-sm {{ $kategori === $kat ? 'bg-purple-600 text-white' : 'bg-gray-700 text-gray-300 hover:bg-gray-600' }}">{{ $kat }}</a>
        @endforeach
    </div>

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/50">
                <tr><th class="px-3 py-3">#</th><th class="px-3 py-3">Kode</th><th class="px-3 py-3">Nama Barang</th><th class="px-3 py-3">Kategori</th><th class="px-3 py-3">Satuan</th><th class="px-3 py-3 text-right">Stok Awal</th><th class="px-3 py-3 text-right">Masuk</th><th class="px-3 py-3 text-right">Keluar</th><th class="px-3 py-3 text-right">Stok Akhir</th><th class="px-3 py-3 text-right">Hrg Beli</th><th class="px-3 py-3 text-right">Hrg Jual</th><th class="px-3 py-3 text-center">Aksi</th></tr>
            </thead>
            <tbody>
                @forelse($barangs as $i => $b)
                <tr class="border-t border-gray-700 hover:bg-gray-700/50">
                    <td class="px-3 py-2 text-gray-400">{{ $barangs->firstItem() + $i }}</td>
                    <td class="px-3 py-2 text-purple-400 font-mono text-xs">{{ $b->kode_barang }}</td>
                    <td class="px-3 py-2 text-white font-medium">{{ $b->nama_barang }}</td>
                    <td class="px-3 py-2"><span class="px-2 py-0.5 rounded text-xs {{ str_contains($b->kategori, 'BAHAN') ? 'bg-yellow-900 text-yellow-300' : (str_contains($b->kategori, 'CUCI') ? 'bg-blue-900 text-blue-300' : 'bg-gray-700 text-gray-300') }}">{{ $b->kategori }}</span></td>
                    <td class="px-3 py-2 text-gray-300">{{ $b->satuan }}</td>
                    <td class="px-3 py-2 text-right text-gray-300">{{ number_format($b->stok_awal, 1) }}</td>
                    <td class="px-3 py-2 text-right text-green-400">{{ number_format($b->barang_masuk, 1) }}</td>
                    <td class="px-3 py-2 text-right text-red-400">{{ number_format($b->barang_keluar, 1) }}</td>
                    <td class="px-3 py-2 text-right font-bold {{ $b->stok_akhir < 0 ? 'text-red-400' : 'text-white' }}">{{ number_format($b->stok_akhir, 1) }}</td>
                    <td class="px-3 py-2 text-right text-gray-300">{{ $b->harga_beli > 0 ? number_format($b->harga_beli, 0, ',', '.') : '-' }}</td>
                    <td class="px-3 py-2 text-right text-gray-300">{{ $b->harga_jual > 0 ? number_format($b->harga_jual, 0, ',', '.') : '-' }}</td>
                    <td class="px-3 py-2 text-center">
                        <button onclick="document.getElementById('editBarang{{ $b->id }}').classList.remove('hidden')" class="text-yellow-400 hover:text-yellow-300 mr-1"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('cugil.barang.destroy', $b->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus barang {{ $b->nama_barang }}?')">@csrf @method('DELETE')<button type="submit" class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button></form>
                    </td>
                </tr>
                <div id="editBarang{{ $b->id }}" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
                    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4">Edit: {{ $b->kode_barang }}</h3>
                        <form action="{{ route('cugil.barang.update', $b->id) }}" method="POST" class="space-y-3">@csrf @method('PUT')
                            <input type="text" name="nama_barang" value="{{ $b->nama_barang }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" required>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="kategori" value="{{ $b->kategori }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" required>
                                <input type="text" name="satuan" value="{{ $b->satuan }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" required>
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="number" step="0.01" name="harga_beli" value="{{ $b->harga_beli }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Harga Beli">
                                <input type="number" step="0.01" name="harga_jual" value="{{ $b->harga_jual }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Harga Jual">
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" onclick="this.closest('[id^=editBarang]').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="12" class="px-4 py-8 text-center text-gray-500">Belum ada data barang.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $barangs->links() }}</div>
</div>

<div id="modalTambah" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700">
        <h3 class="text-lg font-bold text-white mb-4"><i class="fas fa-plus-circle mr-2 text-purple-400"></i>Tambah Barang Baru</h3>
        <form action="{{ route('cugil.barang.store') }}" method="POST" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="kode_barang" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kode Barang *" required>
                <input type="text" name="nama_barang" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Nama Barang *" required>
            </div>
            <div class="grid grid-cols-3 gap-3">
                <input type="text" name="kategori" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kategori *" required>
                <input type="text" name="kode_kategori" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kode Kat.">
                <input type="text" name="satuan" value="Kg" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Satuan *" required>
            </div>
            <div class="grid grid-cols-2 gap-3">
                <input type="number" step="0.01" name="harga_beli" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Harga Beli">
                <input type="number" step="0.01" name="harga_jual" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Harga Jual">
            </div>
            <div class="flex justify-end gap-2 pt-2">
                <button type="button" onclick="document.getElementById('modalTambah').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded">Batal</button>
                <button type="submit" class="px-4 py-2 bg-purple-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
