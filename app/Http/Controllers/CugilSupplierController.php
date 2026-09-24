<?php

namespace App\Http\Controllers;

use App\Models\CugilSupplier;
use Illuminate\Http\Request;

class CugilSupplierController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = CugilSupplier::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_supplier', 'like', "%{$search}%")
                  ->orWhere('kode_supplier', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('kode_supplier')->paginate(20)->withQueryString();
        $totalSupplier = CugilSupplier::count();
        $totalHutang = CugilSupplier::sum('hutang');

        return view('cugil.supplier', compact('suppliers', 'totalSupplier', 'totalHutang', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_supplier' => 'required|string|max:20|unique:cugil_suppliers,kode_supplier',
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:30',
            'item_barang' => 'nullable|string',
            'bank' => 'nullable|string|max:50',
            'no_rekening' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'kode_supplier', 'nama_supplier', 'alamat', 'kota', 'telepon', 'item_barang', 'bank', 'no_rekening'
        ]);
        $data['hutang'] = 0;
        $data['is_active'] = $request->boolean('is_active', true);

        CugilSupplier::create($data);

        return redirect()->route('cugil.supplier.index')->with('success', 'Supplier ' . $request->nama_supplier . ' berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $supplier = CugilSupplier::findOrFail($id);

        $validated = $request->validate([
            'nama_supplier' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:30',
            'item_barang' => 'nullable|string',
            'bank' => 'nullable|string|max:50',
            'no_rekening' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'nama_supplier', 'alamat', 'kota', 'telepon', 'item_barang', 'bank', 'no_rekening'
        ]);
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $supplier->update($data);

        return redirect()->route('cugil.supplier.index')->with('success', 'Supplier ' . $supplier->nama_supplier . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = CugilSupplier::findOrFail($id);
        $nama = $supplier->nama_supplier;
        $supplier->delete();

        return redirect()->route('cugil.supplier.index')->with('success', 'Supplier ' . $nama . ' berhasil dihapus.');
    }
}
