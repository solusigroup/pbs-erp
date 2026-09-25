<?php

namespace App\Http\Controllers;

use App\Models\Perusahaan;
use App\Models\User;
use App\Models\CompanyDirector;
use App\Models\DocumentSignature;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class PerusahaanController extends Controller
{
    public function index()
    {
        $perusahaan = Perusahaan::first();
        if (!$perusahaan) {
            $perusahaan = Perusahaan::create([
                'nama_perusahaan' => 'PT Pinastika Bhakti Semesta',
                'singkatan' => 'PBS',
                'npwp' => '43.688.232.8-602.000',
                'alamat' => 'Jl. Suromulang Barat VI/20, Mojokerto',
                'kota' => 'Mojokerto',
                'provinsi' => 'Jawa Timur',
                'telepon' => '+62 821 4164 3495',
                'email' => 'kurniawan@pinastika.co.id',
                'website' => 'www.pinastika.co.id',
                'bank_nama' => 'Bank Mandiri',
                'bank_rekening' => '142-00-1234567-8',
                'bank_atas_nama' => 'PT Pinastika Bhakti Semesta',
                'bod_finance_tax' => 'Kurniawan, S.E., Ak., CA., M.Ak., CMA., CIBA., CIAP.',
            ]);
        }

        $directors = CompanyDirector::orderBy('urutan')->get();
        $signatures = DocumentSignature::with('director')->orderBy('urutan')->get()->groupBy('jenis_dokumen');
        $bod = User::where('role', 'bod')->get();
        $staff = User::where('role', '!=', 'bod')->get();

        return view('perusahaan.index', compact('perusahaan', 'directors', 'signatures', 'bod', 'staff'));
    }

    public function update(Request $request)
    {
        $perusahaan = Perusahaan::first();
        if (!$perusahaan) {
            $perusahaan = new Perusahaan();
        }

        $validated = $request->validate([
            'nama_perusahaan' => 'required|string',
            'singkatan' => 'required|string',
            'npwp' => 'nullable|string',
            'alamat' => 'nullable|string',
            'kota' => 'nullable|string',
            'provinsi' => 'nullable|string',
            'telepon' => 'nullable|string',
            'email' => 'nullable|email',
            'website' => 'nullable|string',
            'bank_nama' => 'nullable|string',
            'bank_rekening' => 'nullable|string',
            'bank_atas_nama' => 'nullable|string',
            'bod_finance_tax' => 'required|string',
        ]);

        $perusahaan->fill($validated)->save();

        return redirect()->route('perusahaan.index')->with('success', 'Profil PT Pinastika Bhakti Semesta berhasil diperbarui.');
    }

    // ─── DIRECTORS CRUD ─────────────────────────────────────────────────────────

    public function storeDirector(Request $request)
    {
        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'jabatan' => 'required|string|max:100',
            'nik' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['urutan'] = $request->urutan ?? ((CompanyDirector::max('urutan') ?? 0) + 1);

        CompanyDirector::create($validated);

        return redirect()->route('perusahaan.index', ['tab' => 'bod'])->with('success', 'Anggota Dewan Direksi (' . $validated['nama'] . ') berhasil ditambahkan.');
    }

    public function updateDirector(Request $request, $id)
    {
        $director = CompanyDirector::findOrFail($id);

        $validated = $request->validate([
            'nama' => 'required|string|max:150',
            'jabatan' => 'required|string|max:100',
            'nik' => 'nullable|string|max:50',
            'npwp' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:100',
            'telepon' => 'nullable|string|max:50',
            'keterangan' => 'nullable|string',
            'urutan' => 'nullable|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $director->update($validated);

        return redirect()->route('perusahaan.index', ['tab' => 'bod'])->with('success', 'Data Direksi (' . $director->nama . ') berhasil diperbarui.');
    }

    public function destroyDirector($id)
    {
        $director = CompanyDirector::findOrFail($id);
        $nama = $director->nama;
        $director->delete();

        return redirect()->route('perusahaan.index', ['tab' => 'bod'])->with('success', 'Anggota Direksi (' . $nama . ') berhasil dihapus.');
    }

    // ─── DOCUMENT SIGNATURES CRUD & BULK UPDATE ────────────────────────────────

    public function updateSignatures(Request $request)
    {
        $request->validate([
            'signatures' => 'required|array',
            'signatures.*.id' => 'required|exists:document_signatures,id',
            'signatures.*.label_judul' => 'required|string|max:100',
            'signatures.*.nama_penandatangan' => 'required|string|max:150',
            'signatures.*.jabatan_penandatangan' => 'required|string|max:100',
            'signatures.*.organisasi' => 'nullable|string|max:150',
            'signatures.*.catatan' => 'nullable|string',
        ]);

        foreach ($request->signatures as $sigData) {
            $sig = DocumentSignature::find($sigData['id']);
            if ($sig) {
                $sig->update([
                    'label_judul' => $sigData['label_judul'],
                    'nama_penandatangan' => $sigData['nama_penandatangan'],
                    'jabatan_penandatangan' => $sigData['jabatan_penandatangan'],
                    'organisasi' => $sigData['organisasi'] ?? null,
                    'catatan' => $sigData['catatan'] ?? null,
                    'director_id' => !empty($sigData['director_id']) ? $sigData['director_id'] : null,
                ]);
            }
        }

        $activeDoc = $request->input('active_doc', 'sales_order');
        return redirect()->route('perusahaan.index', ['tab' => 'signatures', 'doc' => $activeDoc])->with('success', 'Konfigurasi otorisasi signature dokumen berhasil disimpan.');
    }

    public function storeSignature(Request $request)
    {
        $validated = $request->validate([
            'jenis_dokumen' => 'required|string|in:sales_order,invoice,faktur_pajak,surat_jalan,purchase_order,raw_material',
            'posisi_kode' => 'required|string|max:50',
            'label_judul' => 'required|string|max:100',
            'nama_penandatangan' => 'required|string|max:150',
            'jabatan_penandatangan' => 'required|string|max:100',
            'organisasi' => 'nullable|string|max:150',
            'catatan' => 'nullable|string',
            'director_id' => 'nullable|exists:company_directors,id',
            'urutan' => 'nullable|integer',
        ]);

        $validated['urutan'] = $request->urutan ?? ((DocumentSignature::where('jenis_dokumen', $request->jenis_dokumen)->max('urutan') ?? 0) + 1);

        DocumentSignature::create($validated);

        return redirect()->route('perusahaan.index', ['tab' => 'signatures', 'doc' => $request->jenis_dokumen])->with('success', 'Blok otorisasi tanda tangan baru berhasil ditambahkan.');
    }

    public function destroySignature($id)
    {
        $sig = DocumentSignature::findOrFail($id);
        $doc = $sig->jenis_dokumen;
        $sig->delete();

        return redirect()->route('perusahaan.index', ['tab' => 'signatures', 'doc' => $doc])->with('success', 'Blok tanda tangan berhasil dihapus.');
    }

    public function resetSignatures()
    {
        Artisan::call('db:seed', [
            '--class' => 'DirectorAndSignatureSeeder',
            '--force' => true
        ]);

        return redirect()->route('perusahaan.index', ['tab' => 'signatures'])->with('success', 'Konfigurasi tanda tangan dokumen telah di-reset ke standar default.');
    }
}
