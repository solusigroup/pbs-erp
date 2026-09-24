<?php

namespace App\Http\Controllers;

use App\Models\CugilCustomer;
use Illuminate\Http\Request;

class CugilCustomerController extends Controller
{
    public function index(Request $request)
    {
        $search = $request->query('search');
        $query = CugilCustomer::query();

        if ($search) {
            $query->where(function ($q) use ($search) {
                $q->where('nama_customer', 'like', "%{$search}%")
                  ->orWhere('kode_customer', 'like', "%{$search}%")
                  ->orWhere('kota', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('kode_customer')->paginate(20)->withQueryString();
        $totalCustomer = CugilCustomer::count();
        $totalPiutang = CugilCustomer::sum('piutang');

        return view('cugil.customer', compact('customers', 'totalCustomer', 'totalPiutang', 'search'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_customer' => 'required|string|max:20|unique:cugil_customers,kode_customer',
            'nama_customer' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:30',
            'item_barang' => 'nullable|string',
            'bank' => 'nullable|string|max:50',
            'no_rekening' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'kode_customer', 'nama_customer', 'alamat', 'kota', 'telepon', 'item_barang', 'bank', 'no_rekening'
        ]);
        $data['piutang'] = 0;
        $data['is_active'] = $request->boolean('is_active', true);

        CugilCustomer::create($data);

        return redirect()->route('cugil.customer.index')->with('success', 'Kastamer ' . $request->nama_customer . ' berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $customer = CugilCustomer::findOrFail($id);

        $validated = $request->validate([
            'nama_customer' => 'required|string|max:100',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string|max:50',
            'telepon' => 'nullable|string|max:30',
            'item_barang' => 'nullable|string',
            'bank' => 'nullable|string|max:50',
            'no_rekening' => 'nullable|string|max:50',
        ]);

        $data = $request->only([
            'nama_customer', 'alamat', 'kota', 'telepon', 'item_barang', 'bank', 'no_rekening'
        ]);
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $customer->update($data);

        return redirect()->route('cugil.customer.index')->with('success', 'Kastamer ' . $customer->nama_customer . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $customer = CugilCustomer::findOrFail($id);
        $nama = $customer->nama_customer;
        $customer->delete();

        return redirect()->route('cugil.customer.index')->with('success', 'Kastamer ' . $nama . ' berhasil dihapus.');
    }
}
