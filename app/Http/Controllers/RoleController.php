<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class RoleController extends Controller
{
    /**
     * Tampilkan Matriks Role & Hak Akses (Permissions)
     */
    public function index()
    {
        $roles = Role::withCount('users')->orderBy('is_system', 'desc')->orderBy('name')->get();
        $availablePermissions = Role::availablePermissions();

        return view('roles.index', compact('roles', 'availablePermissions'));
    }

    /**
     * Buat Role Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100',
            'slug' => 'required|string|max:50|alpha_dash|unique:roles,slug',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        Role::create([
            'name' => $request->name,
            'slug' => strtolower($request->slug),
            'description' => $request->description,
            'permissions' => $request->permissions ?? [],
            'is_system' => false,
        ]);

        return redirect()->route('roles.index')->with('success', 'Role jabatan ' . $request->name . ' berhasil dibuat.');
    }

    /**
     * Update Hak Akses & Deskripsi Role
     */
    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:100',
            'description' => 'nullable|string',
            'permissions' => 'nullable|array',
        ]);

        $data = [
            'name' => $request->name,
            'description' => $request->description,
        ];

        // Untuk BOD dan Admin, pertahankan full permissions
        if ($role->slug === 'bod' || $role->slug === 'admin') {
            $data['permissions'] = ['*'];
        } else {
            $data['permissions'] = $request->permissions ?? [];
        }

        $role->update($data);

        return redirect()->route('roles.index')->with('success', 'Hak akses untuk role ' . $role->name . ' berhasil diperbarui.');
    }

    /**
     * Hapus Role Custom
     */
    public function destroy($id)
    {
        $role = Role::findOrFail($id);

        if ($role->is_system) {
            return back()->with('error', 'Role bawaan sistem (' . $role->name . ') dilindungi dan tidak dapat dihapus.');
        }

        if ($role->users()->count() > 0) {
            return back()->with('error', 'Role ' . $role->name . ' masih digunakan oleh ' . $role->users()->count() . ' pengguna aktif. Pindahkan pengguna ke role lain terlebih dahulu.');
        }

        $name = $role->name;
        $role->delete();

        return redirect()->route('roles.index')->with('success', 'Role ' . $name . ' berhasil dihapus.');
    }
}
