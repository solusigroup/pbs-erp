<?php

namespace App\Http\Controllers;

use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\JurnalUmum;
use App\Models\WorkflowChecklist;
use App\Models\WorkflowDefinition;
use App\Services\JurnalAutoService;
use App\Services\WorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class WorkflowController extends Controller
{
    /**
     * Halaman utama Kontrol Workflow — overview semua modul bisnis & akuntansi.
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

        // Dokumen belum selesai (semua modul, max 30)
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
        usort($allIncomplete, fn($a, $b) => $a['progress']['percentage'] <=> $b['progress']['percentage']);
        $allIncomplete = array_slice($allIncomplete, 0, 30);

        // Skipped workflows (langkah terlewat)
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

        // Peringatan Kelalaian Administrasi & Akuntansi
        $alertsSummary = WorkflowService::getPendingAlertsSummary();
        $adminGaps = WorkflowService::getAdministrativeGaps();
        $unapprovedJournals = WorkflowService::getUnapprovedJournals([], 5);

        // Global stats
        $totalChecklists = rescue(fn() => WorkflowChecklist::count(), 0);
        $completedChecklists = rescue(fn() => WorkflowChecklist::where('is_completed', true)->count(), 0);
        $pendingChecklists = max(0, $totalChecklists - $completedChecklists);
        $globalPercentage = $totalChecklists > 0 ? round(($completedChecklists / $totalChecklists) * 100) : 0;

        return view('workflow.index', compact(
            'moduleSummaries',
            'allIncomplete',
            'allSkipped',
            'alertsSummary',
            'adminGaps',
            'unapprovedJournals',
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
     * Pusat Kontrol Workflow Akuntansi & Approval Jurnal (Dedicated Hub).
     */
    public function akuntansi(Request $request)
    {
        $tab = $request->query('tab', 'unapproved');
        $rawPeriod = $request->query('period');

        try {
            $period = !empty($rawPeriod) ? \Carbon\Carbon::parse($rawPeriod . '-01')->format('Y-m') : now()->format('Y-m');
        } catch (\Throwable $e) {
            $period = now()->format('Y-m');
        }

        // 1. Data Jurnal Belum Approve (Draft)
        $filters = [
            'search'        => $request->query('search'),
            'tipe'          => $request->query('tipe'),
            'tanggal_dari'  => $request->query('tanggal_dari'),
            'tanggal_sampai'=> $request->query('tanggal_sampai'),
        ];
        $unapprovedJournals = WorkflowService::getUnapprovedJournals($filters, 100);

        // 2. Data Transaksi Tanpa Jurnal
        $unjournalized = WorkflowService::getUnjournalizedTransactions();

        // 3. Status Tutup Buku Bulanan
        $closingStatus = WorkflowService::getMonthlyClosingStatus($period);

        // Summary Akuntansi
        $totalJurnals = rescue(fn() => JurnalUmum::count(), 0);
        $draftJurnalsCount = rescue(fn() => JurnalUmum::where('is_posted', false)->count(), 0);
        $postedJurnalsCount = max(0, $totalJurnals - $draftJurnalsCount);
        $approvalPercentage = $totalJurnals > 0 ? round(($postedJurnalsCount / $totalJurnals) * 100) : 100;

        // Ambil daftar periode bulan untuk selector
        $availablePeriods = rescue(function () {
            return JurnalUmum::select(DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as ym"))
                ->whereNotNull('tanggal')
                ->distinct()
                ->orderBy('ym', 'desc')
                ->pluck('ym')
                ->filter(fn($val) => !empty($val) && is_string($val))
                ->values()
                ->toArray();
        }, []);

        if (empty($availablePeriods)) {
            $availablePeriods = [now()->format('Y-m')];
        }
        if (!in_array($period, $availablePeriods)) {
            array_unshift($availablePeriods, $period);
        }

        return view('workflow.akuntansi', compact(
            'tab',
            'period',
            'unapprovedJournals',
            'unjournalized',
            'closingStatus',
            'totalJurnals',
            'draftJurnalsCount',
            'postedJurnalsCount',
            'approvalPercentage',
            'availablePeriods'
        ));
    }

    /**
     * Batch / Bulk Approve Jurnal Draft.
     */
    public function batchApproveJurnal(Request $request, JurnalAutoService $jurnalService)
    {
        if ($request->isMethod('get')) {
            return redirect()->route('workflow.akuntansi');
        }

        $jurnalIds = $request->input('jurnal_ids', []);

        if (empty($jurnalIds) || !is_array($jurnalIds)) {
            return back()->with('error', 'Pilih minimal satu jurnal untuk di-approve.');
        }

        $user = auth()->user();
        $approverName = $user->name ?? 'BOD Finance';

        $approvedCount = 0;
        $failedCount = 0;

        foreach ($jurnalIds as $id) {
            $jurnal = JurnalUmum::with('details')->find($id);
            if ($jurnal && !$jurnal->is_posted) {
                if ($jurnalService->approveJurnal($jurnal)) {
                    $approvedCount++;

                    // Update workflow checklist jika ada
                    WorkflowService::completeStep(
                        'akuntansi_jurnal',
                        $jurnal->id_jurnal,
                        'jurnal_approved_posted',
                        $approverName,
                        'Disetujui via Batch Approval'
                    );
                    WorkflowService::completeStep(
                        'akuntansi_jurnal',
                        $jurnal->id_jurnal,
                        'jurnal_voucher_archived',
                        $approverName,
                        'Voucher sah di-posting'
                    );
                } else {
                    $failedCount++;
                }
            }
        }

        $msg = "Berhasil meng-approve {$approvedCount} jurnal ke Buku Besar.";
        if ($failedCount > 0) {
            $msg .= " ({$failedCount} jurnal gagal diproses).";
        }

        return back()->with('success', $msg);
    }

    /**
     * Generate Otomatis Jurnal untuk Transaksi CUGIL yang Belum Terjurnal.
     */
    public function generateMissingJournals(Request $request, JurnalAutoService $jurnalService)
    {
        $type = $request->input('type'); // 'raw', 'sales', or 'all'
        $id = $request->input('id');

        $generatedCount = 0;

        if ($type === 'raw' || $type === 'all') {
            $query = CugilRawMaterial::query();
            if ($id) {
                $query->where('id', $id);
            }
            $raws = $query->get();

            foreach ($raws as $raw) {
                if ((float)$raw->tagihan > 0) {
                    $jurnal = $jurnalService->createJurnalPembelian($raw);
                    if ($jurnal) {
                        $generatedCount++;
                        // Auto-approve jika diminta
                        if ($request->boolean('auto_approve')) {
                            $jurnalService->approveJurnal($jurnal);
                        }
                    }
                }
            }
        }

        if ($type === 'sales' || $type === 'all') {
            $query = CugilSale::query();
            if ($id) {
                $query->where('id', $id);
            }
            $sales = $query->get();

            foreach ($sales as $sale) {
                if ((float)$sale->tagihan > 0) {
                    $jurnal = $jurnalService->createJurnalPenjualan($sale);
                    if ($jurnal) {
                        $generatedCount++;
                        if ($request->boolean('auto_approve')) {
                            $jurnalService->approveJurnal($jurnal);
                        }
                    }
                }
            }
        }

        return back()->with('success', "Berhasil membukukan {$generatedCount} jurnal untuk transaksi operasional.");
    }

    /**
     * Pusat Audit Kelalaian Administrasi (All-in-One Gap Audit).
     */
    public function audit(Request $request)
    {
        $adminGaps = WorkflowService::getAdministrativeGaps();
        $alertsSummary = WorkflowService::getPendingAlertsSummary();
        $allSkipped = [];

        foreach (array_keys(WorkflowDefinition::moduleLabels()) as $module) {
            $skipped = WorkflowService::findSkippedWorkflows($module);
            foreach ($skipped as &$item) {
                $item['module'] = $module;
                $item['module_label'] = WorkflowDefinition::moduleLabels()[$module];
                $item['module_color'] = WorkflowDefinition::moduleColors()[$module] ?? 'slate';
            }
            $allSkipped = array_merge($allSkipped, $skipped);
        }

        return view('workflow.audit', compact('adminGaps', 'alertsSummary', 'allSkipped'));
    }

    /**
     * Verifikasi Langkah Tutup Buku Bulanan.
     */
    public function verifyClosingStep(Request $request)
    {
        $request->validate([
            'period'    => 'required|string',
            'step_code' => 'required|string',
            'notes'     => 'nullable|string',
        ]);

        $period = $request->period;
        $refCode = "CLOSING-{$period}";
        $refId = (int) hexdec(substr(md5($refCode), 0, 8));
        $userName = auth()->user()->name ?? 'BOD Direksi';

        // Pastikan checklist diinisialisasi
        WorkflowService::initializeChecklist('akuntansi_closing', 'App\Models\AkuntansiClosing', $refId, $refCode, null, $userName);

        $success = WorkflowService::completeStep('akuntansi_closing', $refId, $request->step_code, $userName, $request->notes ?? 'Diverifikasi oleh Direksi');

        if ($success) {
            return back()->with('success', "Langkah [{$request->step_code}] periode {$period} berhasil disahkan oleh {$userName}.");
        }

        return back()->with('info', "Langkah [{$request->step_code}] periode {$period} sudah disahkan sebelumnya.");
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
     * Inisialisasi workflow untuk dokumen yang sudah ada (single module seeder).
     */
    public function seedExisting(Request $request)
    {
        $module = $request->input('module');

        if (!$module) {
            return redirect()->route('workflow.index')->with('error', 'Modul harus dipilih.');
        }

        $userName = auth()->user()->name ?? 'System';
        $count = WorkflowService::syncModule($module, $userName);

        return redirect()->route('workflow.module', $module)->with('success', "Berhasil menyinkronkan workflow untuk {$count} dokumen di modul " . (WorkflowDefinition::moduleLabels()[$module] ?? $module) . ".");
    }

    /**
     * Inisialisasi dan sinkronkan SELURUH workflow dokumen di semua modul sekaligus.
     */
    public function seedAllExisting(Request $request)
    {
        @ini_set('max_execution_time', '300');
        @set_time_limit(300);

        $userName = auth()->user()->name ?? 'System (Auto-Sync)';
        $results = WorkflowService::syncAll($userName);
        $totalSynced = array_sum($results);

        return redirect()->route('workflow.index')->with('success', "Sinkronisasi tuntas! Total {$totalSynced} dokumen di seluruh lini bisnis & akuntansi berhasil disinkronkan.");
    }
}
