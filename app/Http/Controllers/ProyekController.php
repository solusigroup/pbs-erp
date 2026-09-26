<?php

namespace App\Http\Controllers;

use App\Models\InvoiceProyek;
use App\Models\Proyek;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class ProyekController extends Controller
{
    public function index()
    {
        $proyeks = Proyek::with('invoices')->latest()->get();
        $totalNilaiKontrak = Proyek::sum('nilai_kontrak');
        $totalTertagih = Proyek::sum('total_tertagih');
        $totalTerbayar = Proyek::sum('total_terbayar');

        return view('proyek.index', compact('proyeks', 'totalNilaiKontrak', 'totalTertagih', 'totalTerbayar'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_proyek' => 'required|string|unique:proyek,kode_proyek',
            'nama_proyek' => 'required|string',
            'nama_klien' => 'required|string',
            'pic_klien' => 'nullable|string',
            'telepon_klien' => 'nullable|string',
            'tanggal_mulai' => 'required|date',
            'tanggal_selesai_target' => 'nullable|date',
            'nilai_kontrak' => 'required|numeric|min:0',
            'keterangan' => 'nullable|string',
        ]);

        $validated['total_tertagih'] = 0;
        $validated['total_terbayar'] = 0;
        $validated['progress_persen'] = 0;
        $validated['status_proyek'] = 'Berjalan';

        Proyek::create($validated);

        return redirect()->route('proyek.index')->with('success', 'Proyek ' . $request->nama_proyek . ' berhasil didaftarkan.');
    }

    public function storeInvoice(Request $request, $id)
    {
        $proyek = Proyek::findOrFail($id);

        $validated = $request->validate([
            'nomor_invoice' => 'required|string|unique:invoice_proyek,nomor_invoice',
            'termin_ke' => 'required|string',
            'tanggal_invoice' => 'required|date',
            'jatuh_tempo' => 'required|date',
            'nominal_tagihan' => 'required|numeric|min:0',
            'ppn_nominal' => 'nullable|numeric|min:0',
            'pph_nominal' => 'nullable|numeric|min:0',
        ]);

        $ppn = $validated['ppn_nominal'] ?? 0;
        $pph = $validated['pph_nominal'] ?? 0;
        $totalBersih = $validated['nominal_tagihan'] + $ppn - $pph;

        InvoiceProyek::create([
            'id_proyek' => $proyek->id,
            'nomor_invoice' => $validated['nomor_invoice'],
            'termin_ke' => $validated['termin_ke'],
            'tanggal_invoice' => $validated['tanggal_invoice'],
            'jatuh_tempo' => $validated['jatuh_tempo'],
            'nominal_tagihan' => $validated['nominal_tagihan'],
            'ppn_nominal' => $ppn,
            'pph_nominal' => $pph,
            'total_bersih' => $totalBersih,
            'status_bayar' => 'Belum Bayar',
        ]);

        $proyek->total_tertagih += $totalBersih;
        $proyek->save();

        return redirect()->route('proyek.index')->with('success', 'Invoice ' . $validated['nomor_invoice'] . ' berhasil diterbitkan.');
    }
}
