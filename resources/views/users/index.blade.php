@extends('layouts.admin')

@section('title', 'Manajemen Pengguna & Otoritas')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-amber-500 mb-1">
                <i class="fas fa-users-gear"></i>
                <span>User Governance &amp; Identity</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Manajemen Pengguna Internal</h1>
            <p class="text-slate-400 text-sm mt-1">Pengaturan akun personil, hak akses jabatan, dan kredensial sistem PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('roles.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold flex items-center gap-2 shadow transition">
                <i class="fas fa-shield-halved text-purple-400"></i>
                <span>Kelola Role &amp; Hak Akses</span>
            </a>
            <button onclick="openCreateModal()" class="px-4 py-2.5 rounded-xl bg-gradient-to-r from-amber-500 to-orange-600 hover:from-amber-600 hover:to-orange-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-orange-500/20 transition">
                <i class="fas fa-user-plus"></i>
                <span>Tambah Pengguna</span>
            </button>
        </div>
    </div>

    <!-- 4 KPI Metrics Cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Total Pengguna</span>
                    <h3 class="text-2xl font-black text-white mt-1">{{ $totalUsers }} <span class="text-xs font-normal text-slate-400">Akun</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">Personil terdaftar internal</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-amber-500/10 border border-amber-500/20 flex items-center justify-center text-amber-400 text-xl">
                    <i class="fas fa-users"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Pengguna Aktif</span>
                    <h3 class="text-2xl font-black text-emerald-400 mt-1">{{ $activeUsers }} <span class="text-xs font-normal text-slate-400">Akun</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">Memiliki akses ke sistem</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 flex items-center justify-center text-emerald-400 text-xl">
                    <i class="fas fa-user-check"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Akun Nonaktif</span>
                    <h3 class="text-2xl font-black text-rose-400 mt-1">{{ $inactiveUsers }} <span class="text-xs font-normal text-slate-400">Akun</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">Akses dinonaktifkan</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-rose-500/10 border border-rose-500/20 flex items-center justify-center text-rose-400 text-xl">
                    <i class="fas fa-user-slash"></i>
                </div>
            </div>
        </div>

        <div class="bg-slate-900/90 p-5 rounded-2xl border border-slate-800 shadow-lg relative overflow-hidden">
            <div class="flex items-center justify-between">
                <div>
                    <span class="text-[11px] font-bold uppercase tracking-wider text-slate-400">Role Terdaftar</span>
                    <h3 class="text-2xl font-black text-purple-400 mt-1">{{ $roles->count() }} <span class="text-xs font-normal text-slate-400">Role</span></h3>
                    <p class="text-[11px] text-slate-400 mt-1">Tingkatan hak wewenang</p>
                </div>
                <div class="h-12 w-12 rounded-2xl bg-purple-500/10 border border-purple-500/20 flex items-center justify-center text-purple-400 text-xl">
                    <i class="fas fa-shield-halved"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Filter Form -->
    <div class="bg-slate-900/80 p-5 rounded-2xl border border-slate-800 shadow-lg">
        <form method="GET" action="{{ route('users.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-3 items-end">
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Pencarian Personil</label>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, email, jabatan..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3.5 py-2 text-xs text-white placeholder-slate-500 focus:outline-none focus:border-amber-500">
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Filter Role</label>
                <select name="role" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">-- Semua Role --</option>
                    @foreach($roles as $r)
                        <option value="{{ $r->slug }}" {{ request('role') == $r->slug ? 'selected' : '' }}>{{ $r->name }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-[11px] font-semibold text-slate-400 mb-1">Status Akun</label>
                <select name="status" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    <option value="">-- Semua Status --</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Aktif</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Nonaktif</option>
                </select>
            </div>
            <div class="flex items-center gap-2">
                <button type="submit" class="flex-1 bg-amber-500 hover:bg-amber-600 text-white font-bold py-2 rounded-xl text-xs flex items-center justify-center gap-1.5 shadow transition">
                    <i class="fas fa-filter text-[10px]"></i>
                    <span>Terapkan</span>
                </button>
                <a href="{{ route('users.index') }}" class="bg-slate-800 hover:bg-slate-700 text-slate-300 font-semibold py-2 px-3 rounded-xl text-xs border border-slate-700 transition" title="Reset Filter">
                    <i class="fas fa-rotate-left"></i>
                </a>
            </div>
        </form>
    </div>

    <!-- User List Table -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-4 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-address-book text-amber-500"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Daftar Akun Personil Internal PT PBS</h3>
            </div>
            <span class="text-xs text-slate-400">Total: <strong class="text-white">{{ $users->total() }}</strong> Akun</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-300">
                <thead class="bg-slate-800/80 text-[11px] uppercase tracking-wider text-slate-400 border-b border-slate-700">
                    <tr>
                        <th class="px-4 py-3">Pengguna</th>
                        <th class="px-4 py-3">Jabatan &amp; Departemen</th>
                        <th class="px-4 py-3">Role Otoritas</th>
                        <th class="px-4 py-3">Kontak / Telepon</th>
                        <th class="px-4 py-3 text-center">Status</th>
                        <th class="px-4 py-3 text-center">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-800/60">
                    @forelse($users as $u)
                        @php
                            $roleBadges = [
                                'bod' => 'bg-amber-500/10 text-amber-400 border-amber-500/30',
                                'admin' => 'bg-rose-500/10 text-rose-400 border-rose-500/30',
                                'finance_manager' => 'bg-blue-500/10 text-blue-400 border-blue-500/30',
                                'tax_officer' => 'bg-purple-500/10 text-purple-400 border-purple-500/30',
                                'cugil_operator' => 'bg-emerald-500/10 text-emerald-400 border-emerald-500/30',
                                'staff' => 'bg-slate-700/50 text-slate-300 border-slate-600',
                                'auditor' => 'bg-cyan-500/10 text-cyan-400 border-cyan-500/30',
                            ];
                            $badgeClass = $roleBadges[$u->role] ?? 'bg-slate-800 text-slate-300 border-slate-700';
                            $initials = strtoupper(substr($u->name, 0, 1));
                        @endphp
                        <tr class="hover:bg-slate-800/40 transition">
                            <td class="px-4 py-3">
                                <div class="flex items-center gap-3">
                                    <div class="h-9 w-9 rounded-xl bg-gradient-to-tr from-amber-600 to-orange-500 flex items-center justify-center text-white font-black text-sm shrink-0 shadow">
                                        @if($u->email === 'kurniawan@pinastika.co.id')
                                            <img src="{{ asset('images/ayahrompi.png') }}" alt="Avatar" class="h-full w-full object-cover rounded-xl" onerror="this.style.display='none'">
                                        @endif
                                        <span>{{ $initials }}</span>
                                    </div>
                                    <div>
                                        <span class="font-bold text-white block">{{ $u->name }}</span>
                                        <span class="text-[11px] text-slate-400 font-mono">{{ $u->email }}</span>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-3">
                                <span class="font-semibold text-slate-200 block">{{ $u->position ?? '-' }}</span>
                                <span class="text-[10px] text-slate-400 block">{{ $u->department ?? '-' }}</span>
                            </td>
                            <td class="px-4 py-3">
                                <span class="px-2.5 py-1 rounded-full text-[10px] font-bold border {{ $badgeClass }}">
                                    {{ $u->roleData->name ?? strtoupper($u->role) }}
                                </span>
                            </td>
                            <td class="px-4 py-3 font-mono text-[11px] text-slate-400">
                                {{ $u->phone ?? '-' }}
                            </td>
                            <td class="px-4 py-3 text-center">
                                @if($u->is_active)
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-500/10 text-emerald-400 border border-emerald-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-emerald-400"></span>
                                        <span>Aktif</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-rose-500/10 text-rose-400 border border-rose-500/20">
                                        <span class="h-1.5 w-1.5 rounded-full bg-rose-400"></span>
                                        <span>Nonaktif</span>
                                    </span>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <div class="flex items-center justify-center gap-1.5">
                                    <button onclick="openEditModal({{ json_encode($u) }})" class="h-8 w-8 rounded-lg bg-blue-500/10 hover:bg-blue-500/20 text-blue-400 flex items-center justify-center border border-blue-500/20 transition" title="Edit Pengguna">
                                        <i class="fas fa-pen-to-square text-xs"></i>
                                    </button>
                                    
                                    @if($u->id !== auth()->id() && $u->email !== 'kurniawan@pinastika.co.id')
                                        <form method="POST" action="{{ route('users.toggle-status', $u->id) }}" class="inline">
                                            @csrf
                                            @method('PATCH')
                                            <button type="submit" class="h-8 w-8 rounded-lg {{ $u->is_active ? 'bg-amber-500/10 hover:bg-amber-500/20 text-amber-400 border-amber-500/20' : 'bg-emerald-500/10 hover:bg-emerald-500/20 text-emerald-400 border-emerald-500/20' }} flex items-center justify-center border transition" title="{{ $u->is_active ? 'Nonaktifkan Akun' : 'Aktifkan Akun' }}">
                                                <i class="fas {{ $u->is_active ? 'fa-user-slash' : 'fa-user-check' }} text-xs"></i>
                                            </button>
                                        </form>

                                        <form method="POST" action="{{ route('users.destroy', $u->id) }}" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna {{ $u->name }}?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="h-8 w-8 rounded-lg bg-rose-500/10 hover:bg-rose-500/20 text-rose-400 flex items-center justify-center border border-rose-500/20 transition" title="Hapus Pengguna">
                                                <i class="fas fa-trash-can text-xs"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-4 py-8 text-center text-slate-400">Tidak ada data pengguna yang sesuai filter.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($users->hasPages())
            <div class="p-4 border-t border-slate-800">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>

<!-- MODAL 1: Tambah Pengguna Baru -->
<div id="createModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl p-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-user-plus text-amber-500 text-lg"></i>
                <h3 class="text-base font-bold text-white">Tambah Pengguna Baru</h3>
            </div>
            <button onclick="closeCreateModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('users.store') }}" class="space-y-4">
            @csrf
            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap &amp; Gelar <span class="text-rose-400">*</span></label>
                <input type="text" name="name" required placeholder="e.g. Achmad Chumaidi, S.T." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Email Login <span class="text-rose-400">*</span></label>
                    <input type="email" name="email" required placeholder="user@pinastika.co.id" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Telepon / WA</label>
                    <input type="text" name="phone" placeholder="+62 8..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Role / Hak Otoritas <span class="text-rose-400">*</span></label>
                    <select name="role" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                        @foreach($roles as $r)
                            <option value="{{ $r->slug }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Departemen <span class="text-rose-400">*</span></label>
                    <input type="text" name="department" required placeholder="e.g. Finance & Tax, Operations, Gudang" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Jabatan / Posisi <span class="text-rose-400">*</span></label>
                <input type="text" name="position" required placeholder="e.g. Kepala Pabrik & Logistik CUGIL" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Password <span class="text-rose-400">*</span></label>
                    <input type="password" name="password" required placeholder="Min 8 karakter" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Konfirmasi Password <span class="text-rose-400">*</span></label>
                    <input type="password" name="password_confirmation" required placeholder="Ulangi password" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" value="1" id="is_active_create" checked class="rounded bg-slate-800 border-slate-700 text-amber-500 focus:ring-0">
                <label for="is_active_create" class="text-xs text-slate-300 font-semibold cursor-pointer">Aktifkan akun segera setelah dibuat</label>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCreateModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-amber-500 hover:bg-amber-600 text-white text-xs font-bold shadow-lg shadow-orange-500/20">Simpan Pengguna</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: Edit Pengguna -->
<div id="editModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl p-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-user-pen text-blue-400 text-lg"></i>
                <h3 class="text-base font-bold text-white">Edit Data Pengguna</h3>
            </div>
            <button onclick="closeEditModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form id="editForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Lengkap &amp; Gelar <span class="text-rose-400">*</span></label>
                <input type="text" id="edit_name" name="name" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Alamat Email Login <span class="text-rose-400">*</span></label>
                    <input type="email" id="edit_email" name="email" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nomor Telepon / WA</label>
                    <input type="text" id="edit_phone" name="phone" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Role / Hak Otoritas <span class="text-rose-400">*</span></label>
                    <select id="edit_role" name="role" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                        @foreach($roles as $r)
                            <option value="{{ $r->slug }}">{{ $r->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Departemen <span class="text-rose-400">*</span></label>
                    <input type="text" id="edit_department" name="department" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Jabatan / Posisi <span class="text-rose-400">*</span></label>
                <input type="text" id="edit_position" name="position" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-blue-500">
            </div>

            <div class="p-3.5 rounded-xl bg-slate-800/60 border border-slate-800 space-y-3">
                <span class="text-xs font-bold text-amber-400 block">Reset Password (Opsional)</span>
                <p class="text-[11px] text-slate-400">Kosongkan jika tidak ingin mengubah password pengguna ini.</p>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                    <div>
                        <input type="password" name="password" placeholder="Password baru" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                    <div>
                        <input type="password" name="password_confirmation" placeholder="Ulangi password baru" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-amber-500">
                    </div>
                </div>
            </div>

            <div class="flex items-center gap-2 pt-2">
                <input type="checkbox" name="is_active" value="1" id="edit_is_active" class="rounded bg-slate-800 border-slate-700 text-blue-500 focus:ring-0">
                <label for="edit_is_active" class="text-xs text-slate-300 font-semibold cursor-pointer">Status Akun Aktif</label>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white text-xs font-bold shadow-lg shadow-blue-500/20">Perbarui Pengguna</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateModal() {
        document.getElementById('createModal').classList.remove('hidden');
    }
    function closeCreateModal() {
        document.getElementById('createModal').classList.add('hidden');
    }

    function openEditModal(user) {
        document.getElementById('editForm').action = `/users/${user.id}`;
        document.getElementById('edit_name').value = user.name || '';
        document.getElementById('edit_email').value = user.email || '';
        document.getElementById('edit_phone').value = user.phone || '';
        document.getElementById('edit_role').value = user.role || 'staff';
        document.getElementById('edit_department').value = user.department || '';
        document.getElementById('edit_position').value = user.position || '';
        document.getElementById('edit_is_active').checked = user.is_active ? true : false;
        
        document.getElementById('editModal').classList.remove('hidden');
    }
    function closeEditModal() {
        document.getElementById('editModal').classList.add('hidden');
    }
</script>
@endsection
