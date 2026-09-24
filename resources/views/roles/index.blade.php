@extends('layouts.admin')

@section('title', 'Manajemen Role & Hak Akses (RBAC)')

@section('content')
<div class="space-y-6">
    <!-- Header Page -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 bg-gradient-to-r from-slate-900 via-slate-850 to-slate-900 p-6 rounded-2xl border border-slate-800 shadow-xl">
        <div>
            <div class="flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-purple-400 mb-1">
                <i class="fas fa-shield-halved"></i>
                <span>Role-Based Access Control (RBAC)</span>
            </div>
            <h1 class="text-2xl sm:text-3xl font-black text-white tracking-tight">Manajemen Role &amp; Otoritas</h1>
            <p class="text-slate-400 text-sm mt-1">Konfigurasi peran jabatan, batas wewenang, dan matriks hak akses per modul PT Pinastika Bhakti Semesta.</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('users.index') }}" class="px-4 py-2.5 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-200 border border-slate-700 text-xs font-semibold flex items-center gap-2 shadow transition">
                <i class="fas fa-users text-amber-400"></i>
                <span>Daftar Pengguna</span>
            </a>
            <button onclick="openCreateRoleModal()" class="px-4 py-2.5 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold flex items-center gap-2 shadow-lg shadow-purple-500/20 transition">
                <i class="fas fa-plus"></i>
                <span>Tambah Role Baru</span>
            </button>
        </div>
    </div>

    <!-- Role Cards Grid -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
            @php
                $roleColors = [
                    'bod' => 'from-amber-600/20 to-orange-600/10 border-amber-500/40 text-amber-400',
                    'admin' => 'from-rose-600/20 to-red-600/10 border-rose-500/40 text-rose-400',
                    'finance_manager' => 'from-blue-600/20 to-indigo-600/10 border-blue-500/40 text-blue-400',
                    'tax_officer' => 'from-purple-600/20 to-violet-600/10 border-purple-500/40 text-purple-400',
                    'cugil_operator' => 'from-emerald-600/20 to-teal-600/10 border-emerald-500/40 text-emerald-400',
                    'staff' => 'from-slate-700/30 to-slate-800/20 border-slate-700 text-slate-300',
                ];
                $colorClass = $roleColors[$role->slug] ?? 'from-slate-800/40 to-slate-900/40 border-slate-800 text-slate-300';
                $perms = $role->permissions ?? [];
                $isFull = in_array('*', $perms);
            @endphp
            <div class="bg-gradient-to-br {{ $colorClass }} bg-slate-900/90 rounded-2xl border p-6 flex flex-col justify-between shadow-xl relative overflow-hidden">
                <div>
                    <div class="flex items-center justify-between mb-3">
                        <span class="text-[10px] font-mono font-bold uppercase tracking-wider px-2.5 py-1 rounded-full bg-slate-800/80 text-slate-300 border border-slate-700">
                            {{ $role->slug }}
                        </span>
                        @if($role->is_system)
                            <span class="text-[10px] font-bold text-amber-400 flex items-center gap-1 bg-amber-500/10 px-2 py-0.5 rounded border border-amber-500/20">
                                <i class="fas fa-lock text-[9px]"></i> Sistem Inti
                            </span>
                        @endif
                    </div>

                    <h3 class="text-lg font-bold text-white">{{ $role->name }}</h3>
                    <p class="text-xs text-slate-400 mt-1.5 leading-relaxed">{{ $role->description ?? 'Tidak ada deskripsi.' }}</p>

                    <div class="mt-4 pt-3 border-t border-slate-800/80">
                        <div class="flex items-center justify-between text-xs mb-2">
                            <span class="text-slate-400 font-semibold">Pengguna Terdaftar:</span>
                            <span class="font-bold text-white bg-slate-800 px-2 py-0.5 rounded">{{ $role->users_count }} Akun</span>
                        </div>
                        <div class="flex items-center justify-between text-xs">
                            <span class="text-slate-400 font-semibold">Cakupan Izin:</span>
                            @if($isFull)
                                <span class="font-bold text-amber-400">Semua Modul (Super Access)</span>
                            @else
                                <span class="font-bold text-emerald-400">{{ count($perms) }} Modul Diizinkan</span>
                            @endif
                        </div>
                    </div>
                </div>

                <div class="mt-6 pt-4 border-t border-slate-800 flex items-center justify-between">
                    <button onclick="openEditRoleModal({{ json_encode($role) }})" class="px-3 py-1.5 rounded-lg bg-slate-800 hover:bg-slate-700 text-xs font-bold text-white flex items-center gap-1.5 transition">
                        <i class="fas fa-sliders text-purple-400 text-xs"></i>
                        <span>Atur Hak Akses</span>
                    </button>

                    @if(!$role->is_system && $role->users_count === 0)
                        <form method="POST" action="{{ route('roles.destroy', $role->id) }}" onsubmit="return confirm('Apakah Anda yakin ingin menghapus role {{ $role->name }}?')">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="p-1.5 rounded-lg text-rose-400 hover:bg-rose-500/10 transition" title="Hapus Role">
                                <i class="fas fa-trash-can text-xs"></i>
                            </button>
                        </form>
                    @endif
                </div>
            </div>
        @endforeach
    </div>

    <!-- Matriks Hak Akses Lengkap (Permission Matrix Reference) -->
    <div class="bg-slate-900/90 rounded-2xl border border-slate-800 shadow-xl overflow-hidden">
        <div class="p-5 border-b border-slate-800 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <i class="fas fa-table-list text-purple-400"></i>
                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Matriks Referensi Wewenang per Modul PBS-ERP</h3>
            </div>
            <span class="text-xs text-slate-400">BOD &amp; Admin memiliki hak akses penuh otomatis</span>
        </div>

        <div class="p-5 space-y-6">
            @foreach($availablePermissions as $groupName => $groupPerms)
                <div class="bg-slate-800/40 rounded-xl p-4 border border-slate-800">
                    <h4 class="text-xs font-bold text-amber-400 uppercase tracking-wider mb-3 flex items-center gap-2">
                        <i class="fas fa-folder text-amber-500"></i>
                        <span>Modul {{ $groupName }}</span>
                    </h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-3">
                        @foreach($groupPerms as $key => $label)
                            <div class="p-2.5 rounded-lg bg-slate-900/60 border border-slate-800 flex items-start gap-2 text-xs">
                                <i class="fas fa-circle-check text-emerald-400 text-[11px] mt-0.5 shrink-0"></i>
                                <div>
                                    <span class="font-bold text-slate-200 block font-mono text-[11px] text-amber-300">{{ $key }}</span>
                                    <span class="text-slate-400 text-[11px] leading-tight block mt-0.5">{{ $label }}</span>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>

<!-- MODAL 1: Tambah Role Baru -->
<div id="createRoleModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl p-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-shield-halved text-purple-400 text-lg"></i>
                <h3 class="text-base font-bold text-white">Tambah Role Jabatan Baru</h3>
            </div>
            <button onclick="closeCreateRoleModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form method="POST" action="{{ route('roles.store') }}" class="space-y-4">
            @csrf
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Role <span class="text-rose-400">*</span></label>
                    <input type="text" name="name" required placeholder="e.g. Kepala Gudang Bahan Baku" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-300 mb-1">Kode Identifier (Slug) <span class="text-rose-400">*</span></label>
                    <input type="text" name="slug" required placeholder="e.g. kepala_gudang" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white font-mono focus:outline-none focus:border-purple-500">
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi Wewenang</label>
                <textarea name="description" rows="2" placeholder="Deskripsi tugas dan otoritas role ini..." class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div>
                <label class="block text-xs font-bold text-slate-200 mb-2">Pilih Hak Akses (Permissions):</label>
                <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar p-3 rounded-xl bg-slate-800/50 border border-slate-800">
                    @foreach($availablePermissions as $groupName => $groupPerms)
                        <div>
                            <span class="text-[11px] font-bold text-amber-400 block mb-1 uppercase">{{ $groupName }}</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                @foreach($groupPerms as $key => $label)
                                    <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer bg-slate-900/50 p-2 rounded-lg border border-slate-800/80">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="rounded bg-slate-800 border-slate-700 text-purple-600 focus:ring-0">
                                        <span class="text-[11px]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeCreateRoleModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-500/20">Simpan Role Baru</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL 2: Edit Hak Akses Role -->
<div id="editRoleModal" class="fixed inset-0 bg-black/80 z-50 hidden flex items-center justify-center p-4">
    <div class="bg-slate-900 border border-slate-800 rounded-2xl w-full max-w-2xl max-h-[90vh] overflow-y-auto custom-scrollbar shadow-2xl p-6">
        <div class="flex items-center justify-between border-b border-slate-800 pb-4 mb-4">
            <div class="flex items-center gap-2">
                <i class="fas fa-sliders text-purple-400 text-lg"></i>
                <h3 class="text-base font-bold text-white">Konfigurasi Hak Akses Role</h3>
            </div>
            <button onclick="closeEditRoleModal()" class="text-slate-400 hover:text-white text-lg">&times;</button>
        </div>

        <form id="editRoleForm" method="POST" action="" class="space-y-4">
            @csrf
            @method('PUT')

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Nama Role <span class="text-rose-400">*</span></label>
                <input type="text" id="edit_role_name" name="name" required class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500">
            </div>

            <div>
                <label class="block text-xs font-semibold text-slate-300 mb-1">Deskripsi Wewenang</label>
                <textarea id="edit_role_description" name="description" rows="2" class="w-full bg-slate-800 border border-slate-700 rounded-xl px-3 py-2 text-xs text-white focus:outline-none focus:border-purple-500"></textarea>
            </div>

            <div id="permissionsContainer">
                <label class="block text-xs font-bold text-slate-200 mb-2">Hak Akses Modul (Permissions):</label>
                <div class="space-y-3 max-h-60 overflow-y-auto custom-scrollbar p-3 rounded-xl bg-slate-800/50 border border-slate-800">
                    @foreach($availablePermissions as $groupName => $groupPerms)
                        <div>
                            <span class="text-[11px] font-bold text-amber-400 block mb-1 uppercase">{{ $groupName }}</span>
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-1.5">
                                @foreach($groupPerms as $key => $label)
                                    <label class="flex items-center gap-2 text-xs text-slate-300 hover:text-white cursor-pointer bg-slate-900/50 p-2 rounded-lg border border-slate-800/80">
                                        <input type="checkbox" name="permissions[]" value="{{ $key }}" class="edit-perm-checkbox rounded bg-slate-800 border-slate-700 text-purple-600 focus:ring-0">
                                        <span class="text-[11px]">{{ $label }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="pt-4 border-t border-slate-800 flex items-center justify-end gap-3">
                <button type="button" onclick="closeEditRoleModal()" class="px-4 py-2 rounded-xl bg-slate-800 hover:bg-slate-700 text-slate-300 text-xs font-semibold">Batal</button>
                <button type="submit" class="px-5 py-2 rounded-xl bg-purple-600 hover:bg-purple-700 text-white text-xs font-bold shadow-lg shadow-purple-500/20">Perbarui Hak Akses</button>
            </div>
        </form>
    </div>
</div>

<script>
    function openCreateRoleModal() {
        document.getElementById('createRoleModal').classList.remove('hidden');
    }
    function closeCreateRoleModal() {
        document.getElementById('createRoleModal').classList.add('hidden');
    }

    function openEditRoleModal(role) {
        document.getElementById('editRoleForm').action = `/roles/${role.id}`;
        document.getElementById('edit_role_name').value = role.name || '';
        document.getElementById('edit_role_description').value = role.description || '';

        const isSuper = role.slug === 'bod' || role.slug === 'admin';
        const perms = role.permissions || [];
        const checkboxes = document.querySelectorAll('.edit-perm-checkbox');

        checkboxes.forEach(cb => {
            if (isSuper) {
                cb.checked = true;
                cb.disabled = true;
            } else {
                cb.disabled = false;
                cb.checked = perms.includes(cb.value);
            }
        });

        document.getElementById('editRoleModal').classList.remove('hidden');
    }
    function closeEditRoleModal() {
        document.getElementById('editRoleModal').classList.add('hidden');
    }
</script>
@endsection
