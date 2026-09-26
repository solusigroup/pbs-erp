<?php

namespace App\Http\Controllers;

use App\Models\PengajuanDana;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class PengajuanDanaController extends Controller
{
    public function index(Request $request)
    {
        $statusFilter = $request->query('status');
        $query = PengajuanDana::query();

        if ($statusFilter) {
            $query->where('status', $statusFilter);
        }

        $pengajuans = $query->latest('tanggal_pengajuan')->paginate(15);
        $totalDiajukan = PengajuanDana::sum('nominal_diajukan');
        $totalDisetujui = PengajuanDana::where('status', 'Disetujui BOD')->sum('nominal_disetujui');
        $totalMenunggu = PengajuanDana::where('status', 'Menunggu Approval')->count();

        return view('anggaran.index', compact('pengajuans', 'totalDiajukan', 'totalDisetujui', 'totalMenunggu', 'statusFilter'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'nomor_pengajuan' => 'required|string|unique:pengajuan_dana,nomor_pengajuan',
            'tanggal_pengajuan' => 'required|date',
            'pemohon' => 'required|string',
            'departemen' => 'required|string',
            'kategori_biaya' => 'required|string',
            'keperluan' => 'required|string',
            'nominal_diajukan' => 'required|numeric|min:1000',
        ]);

        $validated['status'] = 'Menunggu Approval';
        $validated['nominal_disetujui'] = 0;

        $pengajuan = PengajuanDana::create($validated);

        // Auto-inisialisasi workflow checklist
        WorkflowService::initializeChecklist(
            'pengajuan_dana', PengajuanDana::class, $pengajuan->id, $pengajuan->nomor_pengajuan,
            'dana_submitted', auth()->user()->name ?? 'System'
        );

        return redirect()->route('anggaran.index')->with('success', 'Pengajuan dana ' . $request->nomor_pengajuan . ' berhasil diajukan dan menunggu approval BOD Finance.');
    }

    public function approve(Request $request, $id)
    {
        $pengajuan = PengajuanDana::findOrFail($id);

        $request->validate([
            'nominal_disetujui' => 'required|numeric|min:0',
            'catatan_bod' => 'nullable|string',
            'metode_pencairan' => 'required|string',
            'no_bukti_cair' => 'nullable|string',
        ]);

        $pengajuan->update([
            'status' => 'Disetujui BOD',
            'nominal_disetujui' => $request->nominal_disetujui,
            'catatan_bod' => $request->catatan_bod ?? 'Disetujui oleh BOD Finance & Tax (Kurniawan)',
            'approved_at' => now(),
            'approved_by' => auth()->user()->name ?? 'Kurniawan, S.E. (BOD Finance)',
            'metode_pencairan' => $request->metode_pencairan,
            'no_bukti_cair' => $request->no_bukti_cair,
        ]);

        // Auto-complete workflow: review & approval
        $userName = auth()->user()->name ?? 'BOD Finance';
        WorkflowService::completeStep('pengajuan_dana', $pengajuan->id, 'dana_reviewed', $userName, 'Direview & disetujui');
        WorkflowService::completeStep('pengajuan_dana', $pengajuan->id, 'dana_approved', $userName, 'Disetujui oleh BOD');

        return redirect()->route('anggaran.index')->with('success', 'Pengajuan dana ' . $pengajuan->nomor_pengajuan . ' berhasil disetujui oleh BOD Finance.');
    }

    public function reject(Request $request, $id)
    {
        $pengajuan = PengajuanDana::findOrFail($id);

        $request->validate([
            'catatan_bod' => 'required|string',
        ]);

        $pengajuan->update([
            'status' => 'Ditolak',
            'nominal_disetujui' => 0,
            'catatan_bod' => $request->catatan_bod,
            'approved_at' => now(),
            'approved_by' => auth()->user()->name ?? 'Kurniawan, S.E. (BOD Finance)',
        ]);

        // Auto-complete workflow: review & rejection
        $userName = auth()->user()->name ?? 'BOD Finance';
        WorkflowService::completeStep('pengajuan_dana', $pengajuan->id, 'dana_reviewed', $userName, 'Direview oleh BOD');
        WorkflowService::completeStep('pengajuan_dana', $pengajuan->id, 'dana_approved', $userName, 'Ditolak oleh BOD: ' . $request->catatan_bod);

        return redirect()->route('anggaran.index')->with('warning', 'Pengajuan dana ' . $pengajuan->nomor_pengajuan . ' telah ditolak.');
    }
}
