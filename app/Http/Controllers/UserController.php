<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    /**
     * Tampilkan Daftar Pengguna Internal PBS-ERP
     */
    public function index(Request $request)
    {
        $search = $request->query('search');
        $roleFilter = $request->query('role');
        $statusFilter = $request->query('status');

        $query = User::with('roleData');

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('position', 'like', "%{$search}%")
                  ->orWhere('department', 'like', "%{$search}%");
            });
        }

        if ($roleFilter) {
            $query->where('role', $roleFilter);
        }

        if ($statusFilter !== null && $statusFilter !== '') {
            $query->where('is_active', $statusFilter === '1');
        }

        $users = $query->orderBy('role')->orderBy('name')->paginate(15)->withQueryString();
        $roles = Role::orderBy('name')->get();

        // Ringkasan Pengguna
        $totalUsers = User::count();
        $activeUsers = User::where('is_active', true)->count();
        $inactiveUsers = User::where('is_active', false)->count();

        return view('users.index', compact(
            'users', 'roles', 'totalUsers', 'activeUsers', 'inactiveUsers',
            'search', 'roleFilter', 'statusFilter'
        ));
    }

    /**
     * Buat Akun Pengguna Baru
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:150',
            'email' => 'required|email|max:150|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
            'role' => 'required|string|exists:roles,slug',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
        ]);

        User::create([
            'name' => $request->name,
            'email' => strtolower($request->email),
            'password' => Hash::make($request->password),
            'role' => $request->role,
            'position' => $request->position,
            'department' => $request->department,
            'phone' => $request->phone,
            'is_active' => $request->boolean('is_active', true),
            'email_verified_at' => now(),
        ]);

        return redirect()->route('users.index')->with('success', 'Pengguna ' . $request->name . ' (' . $request->position . ') berhasil ditambahkan ke sistem.');
    }

    /**
     * Update Informasi Pengguna
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'name' => 'required|string|max:150',
            'email' => ['required', 'email', 'max:150', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|string|exists:roles,slug',
            'position' => 'required|string|max:100',
            'department' => 'required|string|max:100',
            'phone' => 'nullable|string|max:30',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        // Proteksi: jangan ubah role akun BOD utama jika sedang login
        if ($user->email === 'kurniawan@pinastika.co.id' && $request->role !== 'bod') {
            return back()->with('error', 'Role akun Dewan Direksi (BOD Utama) tidak dapat diubah.');
        }

        $data = [
            'name' => $request->name,
            'email' => strtolower($request->email),
            'role' => $request->role,
            'position' => $request->position,
            'department' => $request->department,
            'phone' => $request->phone,
        ];

        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('users.index')->with('success', 'Data pengguna ' . $user->name . ' berhasil diperbarui.');
    }

    /**
     * Toggle Status Aktif / Nonaktif Pengguna
     */
    public function toggleStatus($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menonaktifkan akun yang sedang digunakan saat ini.');
        }

        if ($user->email === 'kurniawan@pinastika.co.id') {
            return back()->with('error', 'Akun Dewan Direksi Utama tidak dapat dinonaktifkan.');
        }

        $user->is_active = !$user->is_active;
        $user->save();

        $statusStr = $user->is_active ? 'diaktifkan' : 'dinonaktifkan';
        return redirect()->route('users.index')->with('success', 'Akun ' . $user->name . ' berhasil ' . $statusStr . '.');
    }

    /**
     * Hapus Pengguna
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);

        if ($user->id === Auth::id()) {
            return back()->with('error', 'Anda tidak dapat menghapus akun Anda sendiri.');
        }

        if ($user->email === 'kurniawan@pinastika.co.id') {
            return back()->with('error', 'Akun Dewan Direksi (BOD Utama) dilindungi dan tidak dapat dihapus.');
        }

        $name = $user->name;
        $user->delete();

        return redirect()->route('users.index')->with('success', 'Pengguna ' . $name . ' berhasil dihapus dari sistem.');
    }
}
