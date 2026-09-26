<?php

namespace App\Http\Controllers;

use App\Models\WorkflowChecklist;
use App\Models\WorkflowDefinition;
use App\Services\WorkflowService;
use Illuminate\Http\Request;

class WorkflowController extends Controller
{
    /**
     * Halaman utama Kontrol Workflow — overview semua modul.
     */
    public function index(Request $request)
    {
        $moduleLabels = WorkflowDefinition::moduleLabels();
        $moduleIcons = WorkflowDefinition::moduleIcons();
        $moduleColors = WorkflowDefinition::moduleColors();

        // Summary per modul
        $moduleSummaries = [];
        foreach (array_keys($moduleLabels) as $module) {
            $summary = WorkflowService::getModuleSummary($module);
            $summary['label'] = $moduleLabels[$module];
            $summary['icon'] = $moduleIcons[$module] ?? 'fa-circle';
            $summary['color'] = $moduleColors[$module] ?? 'slate';
            $moduleSummaries[$module] = $summary;
        }

        // Seluruh dokumen belum selesai (semua modul, max 30)
        $allIncomplete = [];
        foreach (array_keys($moduleLabels) as $module) {
            $docs = WorkflowService::getIncompleteDocuments($module, 10);
            foreach ($docs as &$doc) {
                $doc['module'] = $module;
                $doc['module_label'] = $moduleLabels[$module];
                $doc['module_color'] = $moduleColors[$module] ?? 'slate';
            }
            $allIncomplete = array_merge($allIncomplete, $docs);
        }
        // Sort: paling sedikit progresnya di atas
        usort($allIncomplete, fn($a, $b) => $a['progress']['percentage'] <=> $b['progress']['percentage']);
        $allIncomplete = array_slice($allIncomplete, 0, 30);

        // Seluruh skipped workflows (langkah terlewat)
        $allSkipped = [];
        foreach (array_keys($moduleLabels) as $module) {
            $skipped = WorkflowService::findSkippedWorkflows($module);
            foreach ($skipped as &$item) {
                $item['module'] = $module;
                $item['module_label'] = $moduleLabels[$module];
                $item['module_color'] = $moduleColors[$module] ?? 'slate';
            }
            $allSkipped = array_merge($allSkipped, $skipped);
        }

        // Global stats
        $totalChecklists = WorkflowChecklist::count();
        $completedChecklists = WorkflowChecklist::where('is_completed', true)->count();
        $pendingChecklists = $totalChecklists - $completedChecklists;
        $globalPercentage = $totalChecklists > 0 ? round(($completedChecklists / $totalChecklists) * 100) : 0;

        return view('workflow.index', compact(
            'moduleSummaries',
            'allIncomplete',
            'allSkipped',
            'totalChecklists',
            'completedChecklists',
            'pendingChecklists',
            'globalPercentage',
            'moduleLabels',
            'moduleIcons',
            'moduleColors'
        ));
    }

    /**
     * Detail workflow per modul — list dokumen dan progress-nya.
     */
    public function module(Request $request, string $module)
    {
        $moduleLabels = WorkflowDefinition::moduleLabels();
        if (!isset($moduleLabels[$module])) {
            abort(404, 'Modul tidak ditemukan.');
        }

        $moduleLabel = $moduleLabels[$module];
        $moduleIcon = WorkflowDefinition::moduleIcons()[$module] ?? 'fa-circle';
        $moduleColor = WorkflowDefinition::moduleColors()[$module] ?? 'slate';

        // Ambil semua definisi step
        $definitions = WorkflowDefinition::where('module', $module)
            ->where('is_active', true)
            ->orderBy('step_order')
            ->get();

        // Ambil semua dokumen dengan workflow
        $documents = WorkflowChecklist::where('module', $module)
            ->select('reference_id', 'reference_code')
            ->distinct()
            ->get();

        $documentList = [];
        foreach ($documents as $doc) {
            $progress = WorkflowService::getProgress($module, $doc->reference_id);
            $steps = WorkflowChecklist::where('module', $module)
                ->where('reference_id', $doc->reference_id)
                ->orderBy('step_order')
                ->get();

            $documentList[] = [
                'reference_id'   => $doc->reference_id,
                'reference_code' => $doc->reference_code,
                'progress'       => $progress,
                'steps'          => $steps,
            ];
        }

        // Sort: belum selesai di atas
        usort($documentList, function ($a, $b) {
            if ($a['progress']['is_complete'] !== $b['progress']['is_complete']) {
                return $a['progress']['is_complete'] ? 1 : -1;
            }
            return $a['progress']['percentage'] <=> $b['progress']['percentage'];
        });

        // Filter by status if requested
        $statusFilter = $request->query('status');
        if ($statusFilter === 'complete') {
            $documentList = array_filter($documentList, fn($d) => $d['progress']['is_complete']);
        } elseif ($statusFilter === 'incomplete') {
            $documentList = array_filter($documentList, fn($d) => !$d['progress']['is_complete']);
        }
        $documentList = array_values($documentList);

        $summary = WorkflowService::getModuleSummary($module);
        $skipped = WorkflowService::findSkippedWorkflows($module);

        return view('workflow.module', compact(
            'module', 'moduleLabel', 'moduleIcon', 'moduleColor',
            'definitions', 'documentList', 'summary', 'skipped', 'statusFilter'
        ));
    }

    /**
     * Tandai step sebagai selesai (AJAX/form).
     */
    public function completeStep(Request $request)
    {
        $request->validate([
            'module' => 'required|string',
            'reference_id' => 'required|integer',
            'step_code' => 'required|string',
            'notes' => 'nullable|string|max:500',
        ]);

        $user = auth()->user();
        $completedBy = $user->name ?? 'Unknown';

        // Cek role requirement
        $definition = WorkflowDefinition::where('module', $request->module)
            ->where('step_code', $request->step_code)
            ->first();

        if ($definition && $definition->required_role) {
            if (!$user->hasRole($definition->required_role) && !$user->isBOD() && !$user->isAdmin()) {
                return back()->with('error', 'Anda tidak memiliki hak akses untuk menyelesaikan langkah ini. Dibutuhkan role: ' . strtoupper($definition->required_role));
            }
        }

        $success = WorkflowService::completeStep(
            $request->module,
            $request->reference_id,
            $request->step_code,
            $completedBy,
            $request->notes
        );

        if ($success) {
            return back()->with('success', 'Langkah workflow berhasil diselesaikan oleh ' . $completedBy . '.');
        }

        return back()->with('warning', 'Langkah ini sudah diselesaikan sebelumnya atau tidak ditemukan.');
    }

    /**
     * Undo / batalkan penyelesaian step (hanya BOD/Admin).
     */
    public function uncompleteStep(Request $request)
    {
        $request->validate([
            'module' => 'required|string',
            'reference_id' => 'required|integer',
            'step_code' => 'required|string',
        ]);

        $success = WorkflowService::uncompleteStep(
            $request->module,
            $request->reference_id,
            $request->step_code
        );

        if ($success) {
            return back()->with('success', 'Langkah workflow berhasil dibatalkan (undo).');
        }

        return back()->with('warning', 'Langkah ini belum diselesaikan atau tidak ditemukan.');
    }

    /**
     * Inisialisasi workflow untuk dokumen yang sudah ada (bulk seeder).
     */
    public function seedExisting(Request $request)
    {
        $module = $request->input('module');

        if (!$module) {
            return back()->with('error', 'Modul harus dipilih.');
        }

        $userName = auth()->user()->name ?? 'System';
        $count = 0;

        switch ($module) {
            case 'cugil_po':
                $records = \App\Models\CugilPurchaseOrder::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        WorkflowService::initializeChecklist($module, \App\Models\CugilPurchaseOrder::class, $rec->id, $rec->nomor_po, 'po_created', $userName);
                        // Auto-complete "Barang Diterima" if status_terima = YA
                        if (strtoupper($rec->status_terima ?? '') === 'YA') {
                            WorkflowService::completeStep($module, $rec->id, 'po_received', 'System (Auto-Sync)');
                        }
                        $count++;
                    }
                }
                break;

            case 'cugil_raw':
                $records = \App\Models\CugilRawMaterial::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        $refCode = $rec->nomor_po ?? 'RAW-' . $rec->id;
                        WorkflowService::initializeChecklist($module, \App\Models\CugilRawMaterial::class, $rec->id, $refCode, 'raw_received', $userName);
                        if (strtoupper($rec->invoiced ?? '') === 'SUDAH') {
                            WorkflowService::completeStep($module, $rec->id, 'raw_invoiced', 'System (Auto-Sync)');
                        }
                        if (strtoupper($rec->status_lunas ?? '') === 'LUNAS') {
                            WorkflowService::completeStep($module, $rec->id, 'raw_payment', 'System (Auto-Sync)');
                        }
                        $count++;
                    }
                }
                break;

            case 'cugil_sales':
                $records = \App\Models\CugilSale::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        WorkflowService::initializeChecklist($module, \App\Models\CugilSale::class, $rec->id, $rec->id_penjualan, 'sales_order_created', $userName);
                        if ($rec->foto_timbangan) {
                            WorkflowService::completeStep($module, $rec->id, 'sales_timbangan', 'System (Auto-Sync)');
                        }
                        if (strtoupper($rec->invoiced ?? '') === 'SUDAH') {
                            WorkflowService::completeStep($module, $rec->id, 'sales_invoiced', 'System (Auto-Sync)');
                        }
                        if (strtoupper($rec->status_pelunasan ?? '') === 'LUNAS') {
                            WorkflowService::completeStep($module, $rec->id, 'sales_payment_received', 'System (Auto-Sync)');
                        }
                        $count++;
                    }
                }
                break;

            case 'pengajuan_dana':
                $records = \App\Models\PengajuanDana::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        WorkflowService::initializeChecklist($module, \App\Models\PengajuanDana::class, $rec->id, $rec->nomor_pengajuan, 'dana_submitted', $userName);
                        if (in_array($rec->status, ['Disetujui BOD', 'Dicairkan'])) {
                            WorkflowService::completeStep($module, $rec->id, 'dana_reviewed', 'System (Auto-Sync)');
                            WorkflowService::completeStep($module, $rec->id, 'dana_approved', 'System (Auto-Sync)');
                        }
                        if ($rec->status === 'Ditolak') {
                            WorkflowService::completeStep($module, $rec->id, 'dana_reviewed', 'System (Auto-Sync)');
                            WorkflowService::completeStep($module, $rec->id, 'dana_approved', 'System (Auto-Sync)', 'Pengajuan ditolak oleh BOD');
                        }
                        if ($rec->status === 'Dicairkan') {
                            WorkflowService::completeStep($module, $rec->id, 'dana_disbursed', 'System (Auto-Sync)');
                        }
                        $count++;
                    }
                }
                break;

            case 'proyek':
                $records = \App\Models\Proyek::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        WorkflowService::initializeChecklist($module, \App\Models\Proyek::class, $rec->id, $rec->kode_proyek, 'prj_registered', $userName);
                        if ($rec->invoices()->exists()) {
                            WorkflowService::completeStep($module, $rec->id, 'prj_invoice_issued', 'System (Auto-Sync)');
                        }
                        if (strtolower($rec->status_proyek ?? '') === 'selesai') {
                            WorkflowService::completeStep($module, $rec->id, 'prj_completed', 'System (Auto-Sync)');
                        }
                        $count++;
                    }
                }
                break;

            case 'pajak':
                $records = \App\Models\TransaksiPajak::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        WorkflowService::initializeChecklist($module, \App\Models\TransaksiPajak::class, $rec->id, $rec->kode_referensi, 'tax_recorded', $userName);
                        if ($rec->status_bayar === 'Sudah Disetor') {
                            WorkflowService::completeStep($module, $rec->id, 'tax_paid', 'System (Auto-Sync)');
                        }
                        if ($rec->status_lapor === 'Sudah Dilapor') {
                            WorkflowService::completeStep($module, $rec->id, 'tax_reported', 'System (Auto-Sync)');
                        }
                        $count++;
                    }
                }
                break;
        }

        return back()->with('success', "Berhasil menyinkronkan workflow untuk {$count} dokumen di modul " . (WorkflowDefinition::moduleLabels()[$module] ?? $module) . ".");
    }
}
