<?php

namespace App\Http\Controllers;

use App\Models\CugilBarang;
use Illuminate\Http\Request;

class CugilBarangController extends Controller
{
    public function index(Request $request)
    {
        $kategori = $request->query('kategori');
        $query = CugilBarang::query();

        if ($kategori) {
            $query->where('kategori', $kategori);
        }

        $barangs = $query->orderBy('kode_barang')->paginate(25)->withQueryString();
        $totalBarang = CugilBarang::count();
        $kategoris = CugilBarang::select('kategori')->distinct()->pluck('kategori');

        return view('cugil.barang', compact('barangs', 'totalBarang', 'kategoris', 'kategori'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_barang' => 'required|string|max:20|unique:cugil_barang,kode_barang',
            'nama_barang' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'kode_kategori' => 'nullable|string|max:10',
            'satuan' => 'required|string|max:10',
            'stok_awal' => 'nullable|numeric|min:0',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        $stokAwal = (float) ($request->stok_awal ?? 0);

        $data = $request->only([
            'kode_barang', 'nama_barang', 'kategori', 'kode_kategori', 'satuan', 'harga_beli', 'harga_jual'
        ]);
        $data['stok_awal'] = $stokAwal;
        $data['barang_masuk'] = 0;
        $data['barang_keluar'] = 0;
        $data['stok_akhir'] = $stokAwal;
        $data['is_active'] = $request->boolean('is_active', true);

        CugilBarang::create($data);

        return redirect()->route('cugil.barang.index')->with('success', 'Barang ' . $request->nama_barang . ' berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $barang = CugilBarang::findOrFail($id);

        $validated = $request->validate([
            'nama_barang' => 'required|string|max:100',
            'kategori' => 'required|string|max:50',
            'kode_kategori' => 'nullable|string|max:10',
            'satuan' => 'required|string|max:10',
            'harga_beli' => 'nullable|numeric|min:0',
            'harga_jual' => 'nullable|numeric|min:0',
        ]);

        $data = $request->only([
            'nama_barang', 'kategori', 'kode_kategori', 'satuan', 'harga_beli', 'harga_jual'
        ]);
        if ($request->has('is_active')) {
            $data['is_active'] = $request->boolean('is_active');
        }

        $barang->update($data);

        return redirect()->route('cugil.barang.index')->with('success', 'Barang ' . $barang->nama_barang . ' berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $barang = CugilBarang::findOrFail($id);
        $nama = $barang->nama_barang;
        $barang->delete();

        return redirect()->route('cugil.barang.index')->with('success', 'Barang ' . $nama . ' berhasil dihapus.');
    }
}
