@extends('layouts.admin')
@section('title', 'Daftar Kastamer - CUGIL PBS-ERP PT Pinastika Bhakti Semesta')
@section('content')
<div class="space-y-6">
    <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-white"><i class="fas fa-users mr-2 text-green-400"></i>Daftar Kastamer CUGIL</h1>
            <p class="text-gray-400 text-sm mt-1">Master data pembeli hasil cuci giling</p>
        </div>
        @if(auth()->user()->canMutate())
        <div class="flex items-center gap-2">
            <form action="{{ route('cugil.lunaskanSemuaPiutang') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menyesuaikan seluruh data penjualan selain Mustofa (termasuk PT Hamparan/Afuk) menjadi LUNAS (Sisa Piutang = Rp 0)?')">
                @csrf
                <button type="submit" class="bg-amber-600 hover:bg-amber-500 text-white px-3.5 py-2 rounded-lg text-sm font-semibold transition flex items-center gap-1.5 shadow-md shadow-amber-600/20" title="Sesuaikan faktual: Nolkan sisa piutang penjualan (PT Hamparan / Afuk & customer lainnya jadi LUNAS, hanya Mustofa tersisa)">
                    <i class="fas fa-check-double text-xs"></i> Nolkan Sisa Piutang (Kecuali Mustofa)
                </button>
            </form>
            <button onclick="document.getElementById('modalTambah').classList.remove('hidden')" class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg"><i class="fas fa-plus mr-1"></i>Tambah Kastamer</button>
        </div>
        @else
        <span class="px-3 py-1.5 rounded-lg bg-slate-800 text-cyan-400 border border-cyan-500/30 text-xs font-bold flex items-center gap-1.5">
            <i class="fas fa-eye text-xs"></i> Mode Pantau (Read-Only)
        </span>
        @endif
    </div>

    @if(session('success'))<div class="bg-green-900/50 border border-green-700 text-green-300 px-4 py-3 rounded-lg">{{ session('success') }}</div>@endif

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700"><p class="text-gray-400 text-sm">Total Kastamer</p><p class="text-3xl font-bold text-white mt-1">{{ $totalCustomer }}</p></div>
        <div class="bg-gray-800 rounded-xl p-5 border border-gray-700"><p class="text-gray-400 text-sm">Total Piutang</p><p class="text-3xl font-bold text-yellow-400 mt-1">Rp {{ number_format($totalPiutang, 0, ',', '.') }}</p></div>
    </div>

    <form method="GET" action="{{ route('cugil.customer.index') }}" class="flex gap-2">
        <input type="text" name="search" value="{{ $search }}" placeholder="Cari nama/kode/kota kastamer..." class="flex-1 bg-gray-800 border border-gray-600 text-white rounded-lg px-4 py-2 focus:ring-green-500 focus:border-green-500">
        <button type="submit" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"><i class="fas fa-search"></i></button>
        @if($search)<a href="{{ route('cugil.customer.index') }}" class="bg-gray-700 hover:bg-gray-600 text-white px-4 py-2 rounded-lg"><i class="fas fa-times"></i></a>@endif
    </form>

    <div class="bg-gray-800 rounded-xl border border-gray-700 overflow-x-auto">
        <table class="w-full text-sm text-left">
            <thead class="text-xs text-gray-400 uppercase bg-gray-900/50">
                <tr>
                    <th class="px-4 py-3">#</th>
                    <th class="px-4 py-3">Kode</th>
                    <th class="px-4 py-3">Nama Kastamer</th>
                    <th class="px-4 py-3">Kota</th>
                    <th class="px-4 py-3">Telepon</th>
                    <th class="px-4 py-3">Item Barang</th>
                    <th class="px-4 py-3 text-right">Piutang</th>
                    @if(auth()->user()->canMutate())
                    <th class="px-4 py-3 text-center">Aksi</th>
                    @endif
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $i => $c)
                <tr class="border-t border-gray-700 hover:bg-gray-700/50">
                    <td class="px-4 py-3 text-gray-400">{{ $customers->firstItem() + $i }}</td>
                    <td class="px-4 py-3 text-green-400 font-mono text-xs">{{ $c->kode_customer }}</td>
                    <td class="px-4 py-3 text-white font-medium">{{ $c->nama_customer }}</td>
                    <td class="px-4 py-3 text-gray-300">{{ $c->kota ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-300">{{ $c->telepon ?? '-' }}</td>
                    <td class="px-4 py-3 text-gray-400 text-xs max-w-[200px] truncate">{{ $c->item_barang ?? '-' }}</td>
                    <td class="px-4 py-3 text-right {{ $c->piutang > 0 ? 'text-yellow-400' : 'text-gray-400' }}">Rp {{ number_format($c->piutang, 0, ',', '.') }}</td>
                    @if(auth()->user()->canMutate())
                    <td class="px-4 py-3 text-center">
                        <button onclick="document.getElementById('editModal{{ $c->id }}').classList.remove('hidden')" class="text-yellow-400 hover:text-yellow-300 mr-2"><i class="fas fa-edit"></i></button>
                        <form action="{{ route('cugil.customer.destroy', $c->id) }}" method="POST" class="inline" onsubmit="return confirm('Hapus kastamer {{ $c->nama_customer }}?')">@csrf @method('DELETE')<button type="submit" class="text-red-400 hover:text-red-300"><i class="fas fa-trash"></i></button></form>
                    </td>
                    @endif
                </tr>
                <div id="editModal{{ $c->id }}" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
                    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700">
                        <h3 class="text-lg font-bold text-white mb-4">Edit Kastamer: {{ $c->kode_customer }}</h3>
                        <form action="{{ route('cugil.customer.update', $c->id) }}" method="POST" class="space-y-3">@csrf @method('PUT')
                            <input type="text" name="nama_customer" value="{{ $c->nama_customer }}" class="w-full bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" required>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="alamat" value="{{ $c->alamat }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Alamat">
                                <input type="text" name="kota" value="{{ $c->kota }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kota">
                            </div>
                            <div class="grid grid-cols-2 gap-3">
                                <input type="text" name="telepon" value="{{ $c->telepon }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Telepon">
                                <input type="text" name="item_barang" value="{{ $c->item_barang }}" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Item Barang">
                            </div>
                            <div class="flex justify-end gap-2 pt-2">
                                <button type="button" onclick="this.closest('[id^=editModal]').classList.add('hidden')" class="px-4 py-2 bg-gray-600 text-white rounded">Batal</button>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
                            </div>
                        </form>
                    </div>
                </div>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-gray-500">Belum ada data kastamer.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    <div class="mt-4">{{ $customers->links() }}</div>
</div>

<div id="modalTambah" class="hidden fixed inset-0 bg-black/60 z-50 flex items-center justify-center p-4">
    <div class="bg-gray-800 rounded-xl p-6 w-full max-w-lg border border-gray-700">
        <h3 class="text-lg font-bold text-white mb-4"><i class="fas fa-plus-circle mr-2 text-green-400"></i>Tambah Kastamer Baru</h3>
        <form action="{{ route('cugil.customer.store') }}" method="POST" class="space-y-3">@csrf
            <div class="grid grid-cols-2 gap-3">
                <input type="text" name="kode_customer" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Kode Kastamer *" required>
                <input type="text" name="nama_customer" class="bg-gray-700 border border-gray-600 text-white rounded px-3 py-2" placeholder="Nama Kastamer *" required>
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
                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection
