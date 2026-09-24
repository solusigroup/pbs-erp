@extends('layouts.admin')

@section('title', 'Chart of Accounts (COA) - Akuntansi')

@section('content')
<div class="space-y-6">
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-4">
        <div class="flex items-center gap-2.5">
            <div class="p-2.5 rounded-2xl bg-gradient-to-br from-amber-500/20 to-orange-500/20 border border-amber-500/30 text-amber-400 shadow-lg shadow-orange-500/10">
                <i class="fas fa-book-bookmark text-xl"></i>
            </div>
            <div>
                <h2 class="text-xl font-black text-white">Chart of Accounts (Daftar Akun)</h2>
                <p class="text-xs text-slate-400 mt-0.5">Klasifikasi akun pembukuan standar SAK PT Pinastika Bhakti Semesta</p>
            </div>
        </div>
        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('akuntansi.jurnal-kas') }}" class="px-3.5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20 transition flex items-center gap-2">
                <i class="fas fa-money-bill-transfer"></i>
                <span>Jurnal Kas &amp; Bank</span>
            </a>
            <a href="{{ route('akuntansi.buku-kas') }}" class="px-3.5 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-bold shadow-lg shadow-emerald-600/20 transition flex items-center gap-2">
                <i class="fas fa-wallet"></i>
                <span>Buku Kas &amp; Bank</span>
            </a>
            <a href="{{ route('akuntansi.buku-besar') }}" class="px-3.5 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white text-xs font-bold shadow-lg shadow-sky-600/20 transition flex items-center gap-2">
                <i class="fas fa-book-journal-whills"></i>
                <span>Buku Besar (GL)</span>
            </a>
            <a href="{{ route('akuntansi.laporan') }}" class="px-3.5 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs font-semibold border border-slate-700 transition flex items-center gap-2">
                <i class="fas fa-file-invoice-dollar text-slate-400"></i>
                <span>Laba Rugi &amp; Neraca</span>
            </a>
            @if(auth()->user()->isAdmin())
            <button type="button" onclick="openModalTambahAkun()" class="px-3.5 py-2 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-xs font-bold shadow-lg shadow-purple-600/20 transition flex items-center gap-2">
                <i class="fas fa-plus"></i>
                <span>+ Tambah Akun COA</span>
            </button>
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

    <!-- 5 Summary Badges -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-3">
        <div class="p-3.5 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-[11px] text-slate-400 block font-medium">Total Aset (Aktiva)</span>
            <strong class="text-base font-black text-emerald-400 block mt-1 font-mono">Rp {{ number_format($totalAset, 0, ',', '.') }}</strong>
        </div>
        <div class="p-3.5 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-[11px] text-slate-400 block font-medium">Total Kewajiban (Hutang)</span>
            <strong class="text-base font-black text-rose-400 block mt-1 font-mono">Rp {{ number_format($totalKewajiban, 0, ',', '.') }}</strong>
        </div>
        <div class="p-3.5 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-[11px] text-slate-400 block font-medium">Total Ekuitas / Modal</span>
            <strong class="text-base font-black text-amber-400 block mt-1 font-mono">Rp {{ number_format($totalEkuitas, 0, ',', '.') }}</strong>
        </div>
        <div class="p-3.5 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-[11px] text-slate-400 block font-medium">Total Pendapatan</span>
            <strong class="text-base font-black text-emerald-400 block mt-1 font-mono">Rp {{ number_format($totalPendapatan ?? 0, 0, ',', '.') }}</strong>
        </div>
        <div class="p-3.5 rounded-2xl border border-slate-800 bg-slate-900/60 shadow-lg">
            <span class="text-[11px] text-slate-400 block font-medium">Total Beban Usaha</span>
            <strong class="text-base font-black text-rose-400 block mt-1 font-mono">Rp {{ number_format($totalBeban ?? 0, 0, ',', '.') }}</strong>
        </div>
    </div>

    <!-- Filter Kategori Tabs & Search Toolbar -->
    <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between gap-3">
        <div class="flex flex-wrap gap-1.5 text-xs">
            <a href="{{ route('akuntansi.index') }}" class="px-3 py-1.5 rounded-lg border {{ !$kategori ? 'bg-amber-500 border-amber-500 text-white font-bold shadow-md shadow-amber-500/20' : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-800' }}">Semua Akun</a>
            @foreach(['Aset Lancar', 'Aset Tetap', 'Kewajiban', 'Ekuitas', 'Pendapatan', 'Beban'] as $kat)
                <a href="{{ route('akuntansi.index', ['kategori' => $kat]) }}" class="px-3 py-1.5 rounded-lg border {{ $kategori === $kat ? 'bg-amber-500 border-amber-500 text-white font-bold shadow-md shadow-amber-500/20' : 'bg-slate-900 border-slate-800 text-slate-300 hover:bg-slate-800' }}">
                    {{ $kat }}
                </a>
            @endforeach
        </div>

        <form method="GET" action="{{ route('akuntansi.index') }}" class="flex items-center gap-2">
            @if($kategori)
                <input type="hidden" name="kategori" value="{{ $kategori }}">
            @endif
            <input type="text" name="search" value="{{ $search ?? '' }}" placeholder="Cari kode/nama akun..." class="px-3 py-1.5 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs w-48">
            <button type="submit" class="px-3 py-1.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 text-xs border border-slate-700">
                <i class="fas fa-search"></i>
            </button>
        </form>
    </div>

    <!-- Accounts Table -->
    <div class="rounded-3xl border border-slate-800 bg-slate-900/60 overflow-hidden shadow-xl">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-950/60 text-slate-400 text-[10px] uppercase font-bold tracking-wider border-b border-slate-800">
                    <tr>
                        <th class="py-3 px-4">Kode Akun</th>
                        <th class="py-3 px-4">Nama Akun</th>
                        <th class="py-3 px-4">Kategori</th>
                        <th class="py-3 px-4">Tipe Akun</th>
                        <th class="py-3 px-4 text-center">Saldo Normal</th>
                        <th class="py-3 px-4 text-right">Saldo Awal</th>
                        <th class="py-3 px-4 text-right">Saldo Berjalan</th>
                        <th class="py-3 px-4 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($akuns as $a)
                        <tr class="hover:bg-slate-800/30 transition">
                            <td class="py-3 px-4 font-bold text-amber-400 font-mono">{{ $a->kode_akun }}</td>
                            <td class="py-3 px-4 font-semibold text-white">{{ $a->nama_akun }}</td>
                            <td class="py-3 px-4">
                                <span class="px-2 py-0.5 rounded text-[10px] bg-slate-800 border border-slate-700 text-slate-300">
                                    {{ $a->kategori }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-slate-400">{{ $a->tipe_akun }}</td>
                            <td class="py-3 px-4 text-center">
                                <span class="px-2 py-0.5 rounded text-[10px] font-bold {{ $a->saldo_normal === 'Debit' ? 'bg-sky-500/10 text-sky-400 border border-sky-500/20' : 'bg-emerald-500/10 text-emerald-400 border border-emerald-500/20' }}">
                                    {{ $a->saldo_normal }}
                                </span>
                            </td>
                            <td class="py-3 px-4 text-right text-slate-400 font-mono">Rp {{ number_format($a->saldo_awal, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-right font-bold font-mono text-white">Rp {{ number_format($a->saldo_berjalan, 0, ',', '.') }}</td>
                            <td class="py-3 px-4 text-center whitespace-nowrap">
                                <div class="flex items-center justify-center gap-1.5">
                                    @if($a->tipe_akun === 'Kas & Bank')
                                        <a href="{{ route('akuntansi.buku-kas', ['kode_akun' => $a->kode_akun]) }}" class="px-2 py-1 rounded-lg bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 text-[10px] font-bold border border-emerald-500/30 inline-flex items-center gap-1" title="Buku Kas & Bank">
                                            <i class="fas fa-wallet"></i> Buku Kas
                                        </a>
                                    @else
                                        <a href="{{ route('akuntansi.buku-besar', ['kode_akun' => $a->kode_akun]) }}" class="px-2 py-1 rounded-lg bg-sky-500/10 text-sky-400 hover:bg-sky-500/20 text-[10px] font-bold border border-sky-500/30 inline-flex items-center gap-1" title="Buku Besar">
                                            <i class="fas fa-book"></i> Buku Besar
                                        </a>
                                    @endif

                                    @if(auth()->user()->isAdmin())
                                    <button 
                                        type="button" 
                                        onclick="openModalEditAkun({{ json_encode($a) }})" 
                                        class="px-2 py-1 rounded-lg bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border border-amber-500/30 text-[10px] font-bold transition inline-flex items-center gap-1" 
                                        title="Edit Akun COA"
                                    >
                                        <i class="fas fa-edit"></i> Edit
                                    </button>

                                    <form action="{{ route('akuntansi.akun.destroy', $a->kode_akun) }}" method="POST" class="inline" onsubmit="return confirm('PERINGATAN: Apakah Anda yakin ingin menghapus akun COA [{{ $a->kode_akun }}] {{ $a->nama_akun }}?\n\nAkun hanya dapat dihapus jika belum memiliki catatan mutasi jurnal!')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-2 py-1 rounded-lg bg-rose-500/10 hover:bg-rose-500 text-rose-400 hover:text-white border border-rose-500/20 text-[10px] font-bold transition inline-flex items-center" title="Hapus Akun COA">
                                            <i class="fas fa-trash-can"></i>
                                        </button>
                                    </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="py-8 text-center text-slate-500">
                                Tidak ada akun yang sesuai dengan filter pencarian.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL TAMBAH AKUN COA (Khusus Superuser / BOD / Admin)                     -->
<!-- ========================================================================= -->
<div id="modalTambahAkun" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-plus-circle text-purple-400"></i>
                <span>Tambah Akun COA Baru (Chart of Accounts)</span>
            </h3>
            <button type="button" onclick="closeModalTambahAkun()" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form method="POST" action="{{ route('akuntansi.akun.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Akun <span class="text-rose-400">*</span></label>
                    <input type="text" name="kode_akun" required placeholder="Contoh: 1-1150" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Akun <span class="text-rose-400">*</span></label>
                    <input type="text" name="nama_akun" required placeholder="Contoh: Rekening BCA Bisnis" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori SAK <span class="text-rose-400">*</span></label>
                    <select name="kategori" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        <option value="Aset Lancar">Aset Lancar</option>
                        <option value="Aset Tetap">Aset Tetap</option>
                        <option value="Kewajiban">Kewajiban</option>
                        <option value="Ekuitas">Ekuitas</option>
                        <option value="Pendapatan">Pendapatan</option>
                        <option value="Beban">Beban</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tipe Akun <span class="text-rose-400">*</span></label>
                    <input type="text" name="tipe_akun" required placeholder="Kas & Bank / Piutang / Persediaan / dll" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Saldo Normal <span class="text-rose-400">*</span></label>
                    <select name="saldo_normal" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-bold">
                        <option value="Debit">Debit</option>
                        <option value="Kredit">Kredit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Saldo Awal (Rp)</label>
                    <input type="number" step="0.01" name="saldo_awal" value="0" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeModalTambahAkun()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-600/20">Simpan Akun</button>
            </div>
        </form>
    </div>
</div>

<!-- ========================================================================= -->
<!-- MODAL EDIT AKUN COA (Khusus Superuser / BOD / Admin)                       -->
<!-- ========================================================================= -->
<div id="modalEditAkun" class="fixed inset-0 z-50 bg-slate-950/80 backdrop-blur-sm hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-700 rounded-3xl max-w-lg w-full p-6 shadow-2xl space-y-4">
        <div class="flex items-center justify-between border-b border-slate-800 pb-3">
            <h3 class="text-sm font-bold text-white flex items-center gap-2">
                <i class="fas fa-edit text-amber-400"></i>
                <span>Edit Akun COA: <span id="edit_kode_display" class="font-mono text-amber-400"></span></span>
            </h3>
            <button type="button" onclick="closeModalEditAkun()" class="text-slate-400 hover:text-white">&times;</button>
        </div>

        <form id="formEditAkun" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Akun (Kunci Permanen)</label>
                    <input type="text" id="edit_kode_akun" disabled class="w-full px-3 py-2 rounded-xl bg-slate-800 border border-slate-700 text-slate-400 text-xs font-mono cursor-not-allowed">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Akun <span class="text-rose-400">*</span></label>
                    <input type="text" id="edit_nama_akun" name="nama_akun" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kategori SAK <span class="text-rose-400">*</span></label>
                    <select id="edit_kategori" name="kategori" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                        <option value="Aset Lancar">Aset Lancar</option>
                        <option value="Aset Tetap">Aset Tetap</option>
                        <option value="Kewajiban">Kewajiban</option>
                        <option value="Ekuitas">Ekuitas</option>
                        <option value="Pendapatan">Pendapatan</option>
                        <option value="Beban">Beban</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Tipe Akun <span class="text-rose-400">*</span></label>
                    <input type="text" id="edit_tipe_akun" name="tipe_akun" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Saldo Normal <span class="text-rose-400">*</span></label>
                    <select id="edit_saldo_normal" name="saldo_normal" required class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-bold">
                        <option value="Debit">Debit</option>
                        <option value="Kredit">Kredit</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Saldo Awal (Rp)</label>
                    <input type="number" step="0.01" id="edit_saldo_awal" name="saldo_awal" class="w-full px-3 py-2 rounded-xl bg-slate-950 border border-slate-700 text-white text-xs font-mono">
                </div>
            </div>

            <div class="flex items-center justify-end gap-2 pt-3 border-t border-slate-800">
                <button type="button" onclick="closeModalEditAkun()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-slate-950 text-xs font-bold shadow-lg shadow-amber-500/20">Perbarui Akun</button>
            </div>
        </form>
    </div>
</div>

<script>
function openModalTambahAkun() {
    document.getElementById('modalTambahAkun').classList.remove('hidden');
}
function closeModalTambahAkun() {
    document.getElementById('modalTambahAkun').classList.add('hidden');
}

function openModalEditAkun(akun) {
    document.getElementById('formEditAkun').action = `/akuntansi/akun/${encodeURIComponent(akun.kode_akun)}`;
    document.getElementById('edit_kode_display').innerText = akun.kode_akun;
    document.getElementById('edit_kode_akun').value = akun.kode_akun;
    document.getElementById('edit_nama_akun').value = akun.nama_akun;
    document.getElementById('edit_kategori').value = akun.kategori;
    document.getElementById('edit_tipe_akun').value = akun.tipe_akun;
    document.getElementById('edit_saldo_normal').value = akun.saldo_normal;
    document.getElementById('edit_saldo_awal').value = akun.saldo_awal;

    document.getElementById('modalEditAkun').classList.remove('hidden');
}
function closeModalEditAkun() {
    document.getElementById('modalEditAkun').classList.add('hidden');
}
</script>
@endsection
