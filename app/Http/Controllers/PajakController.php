<?php

namespace App\Http\Controllers;

use App\Models\TransaksiPajak;
use Illuminate\Http\Request;

class PajakController extends Controller
{
    public function index(Request $request)
    {
        $jenisFilter = $request->query('jenis');
        $query = TransaksiPajak::query();

        if ($jenisFilter) {
            $query->where('jenis_pajak', $jenisFilter);
        }

        $transaksis = $query->latest('tanggal_faktur_potong')->paginate(15);

        // Perhitungan Rekapitulasi Pajak
        $ppnKeluaran = TransaksiPajak::where('jenis_pajak', 'PPN_KELUARAN')->sum('nominal_pajak');
        $ppnMasukan = TransaksiPajak::where('jenis_pajak', 'PPN_MASUKAN')->sum('nominal_pajak');
        $ppnKurangBayar = max(0, $ppnKeluaran - $ppnMasukan);

        $pph21Total = TransaksiPajak::where('jenis_pajak', 'PPH_21')->sum('nominal_pajak');
        $pph23Total = TransaksiPajak::where('jenis_pajak', 'PPH_23')->sum('nominal_pajak');
        $pph42Total = TransaksiPajak::where('jenis_pajak', 'PPH_4_2')->sum('nominal_pajak');
        $totalPajakBelumSetor = TransaksiPajak::where('status_bayar', 'Belum Disetor')->sum('nominal_pajak');

        return view('pajak.index', compact(
            'transaksis',
            'ppnKeluaran',
            'ppnMasukan',
            'ppnKurangBayar',
            'pph21Total',
            'pph23Total',
            'pph42Total',
            'totalPajakBelumSetor',
            'jenisFilter'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'kode_referensi' => 'required|string|unique:transaksi_pajak,kode_referensi',
            'jenis_pajak' => 'required|string',
            'masa_pajak' => 'required|string',
            'tahun_pajak' => 'required|integer',
            'tanggal_faktur_potong' => 'required|date',
            'nomor_dokumen' => 'nullable|string',
            'lawan_transaksi' => 'required|string',
            'npwp_lawan_transaksi' => 'nullable|string',
            'dpp' => 'required|numeric|min:0',
            'tarif_persen' => 'required|numeric|min:0',
            'nominal_pajak' => 'required|numeric|min:0',
            'status_bayar' => 'required|string',
            'catatan' => 'nullable|string',
        ]);

        $validated['reviewed_by'] = 'Kurniawan, S.E. (BOD Finance & Tax)';

        TransaksiPajak::create($validated);

        return redirect()->route('pajak.index')->with('success', 'Transaksi pajak ' . $request->kode_referensi . ' berhasil dicatat.');
    }

    public function updateStatus(Request $request, $id)
    {
        $pajak = TransaksiPajak::findOrFail($id);

        $request->validate([
            'status_bayar' => 'nullable|string',
            'ntpn' => 'nullable|string',
            'tanggal_setor' => 'nullable|date',
            'status_lapor' => 'nullable|string',
            'bpe_spt' => 'nullable|string',
            'tanggal_lapor' => 'nullable|date',
        ]);

        if ($request->filled('status_bayar')) {
            $pajak->status_bayar = $request->status_bayar;
            $pajak->ntpn = $request->ntpn;
            $pajak->tanggal_setor = $request->tanggal_setor;
        }

        if ($request->filled('status_lapor')) {
            $pajak->status_lapor = $request->status_lapor;
            $pajak->bpe_spt = $request->bpe_spt;
            $pajak->tanggal_lapor = $request->tanggal_lapor;
        }

        $pajak->save();

        return redirect()->route('pajak.index')->with('success', 'Status pajak ' . $pajak->kode_referensi . ' berhasil diperbarui.');
    }
}
