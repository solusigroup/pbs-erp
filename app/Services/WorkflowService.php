<?php

namespace App\Services;

use App\Models\CugilPurchaseOrder;
use App\Models\CugilRawMaterial;
use App\Models\CugilSale;
use App\Models\JurnalUmum;
use App\Models\PengajuanDana;
use App\Models\Proyek;
use App\Models\TransaksiPajak;
use App\Models\WorkflowChecklist;
use App\Models\WorkflowDefinition;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    /**
     * Buat checklist workflow untuk sebuah dokumen/transaksi baru.
     *
     * @param string $module         Kode modul (cugil_po, cugil_raw, cugil_sales, akuntansi_jurnal, dll)
     * @param string $referenceType  Fully qualified class name model
     * @param int    $referenceId    ID primary key record
     * @param string $referenceCode  Nomor/kode dokumen yang human-readable
     * @param string|null $autoCompleteStep  Step code yang otomatis di-complete (biasanya step pertama)
     * @param string|null $completedBy       Nama user yang melakukan (untuk auto-complete)
     */
    public static function initializeChecklist(
        string $module,
        string $referenceType,
        int    $referenceId,
        string $referenceCode,
        ?string $autoCompleteStep = null,
        ?string $completedBy = null
    ): void {
        $definitions = WorkflowDefinition::where('module', $module)
            ->where('is_active', true)
            ->orderBy('step_order')
            ->get();

        if ($definitions->isEmpty()) {
            return;
        }

        $records = [];
        $now = now();

        foreach ($definitions as $def) {
            $isAutoCompleted = ($autoCompleteStep && $def->step_code === $autoCompleteStep);

            $records[] = [
                'module'         => $module,
                'reference_type' => $referenceType,
                'reference_id'   => $referenceId,
                'reference_code' => $referenceCode,
                'step_code'      => $def->step_code,
                'step_name'      => $def->step_name,
                'step_order'     => $def->step_order,
                'is_required'    => $def->is_required,
                'is_completed'   => $isAutoCompleted,
                'completed_at'   => $isAutoCompleted ? $now : null,
                'completed_by'   => $isAutoCompleted ? ($completedBy ?? 'System') : null,
                'notes'          => $isAutoCompleted ? 'Otomatis diselesaikan saat pembuatan dokumen' : null,
                'created_at'     => $now,
                'updated_at'     => $now,
            ];
        }

        // Gunakan upsert agar aman dari duplikat
        DB::table('workflow_checklists')->upsert(
            $records,
            ['module', 'reference_id', 'step_code'],
            ['step_name', 'step_order', 'is_required', 'is_completed', 'completed_at', 'completed_by', 'notes', 'updated_at']
        );
    }

    /**
     * Tandai step workflow sebagai selesai.
     */
    public static function completeStep(
        string $module,
        int    $referenceId,
        string $stepCode,
        string $completedBy,
        ?string $notes = null
    ): bool {
        $checklist = WorkflowChecklist::where('module', $module)
            ->where('reference_id', $referenceId)
            ->where('step_code', $stepCode)
            ->first();

        if (!$checklist || $checklist->is_completed) {
            return false;
        }

        $checklist->update([
            'is_completed'  => true,
            'completed_at'  => now(),
            'completed_by'  => $completedBy,
            'notes'         => $notes,
        ]);

        return true;
    }

    /**
     * Batalkan penyelesaian step (undo).
     */
    public static function uncompleteStep(
        string $module,
        int    $referenceId,
        string $stepCode
    ): bool {
        $checklist = WorkflowChecklist::where('module', $module)
            ->where('reference_id', $referenceId)
            ->where('step_code', $stepCode)
            ->first();

        if (!$checklist || !$checklist->is_completed) {
            return false;
        }

        $checklist->update([
            'is_completed'  => false,
            'completed_at'  => null,
            'completed_by'  => null,
            'notes'         => null,
        ]);

        return true;
    }

    /**
     * Hitung progres workflow sebuah dokumen.
     *
     * @return array{total: int, completed: int, percentage: int, is_complete: bool, current_step: ?string}
     */
    public static function getProgress(string $module, int $referenceId): array
    {
        $checklists = WorkflowChecklist::where('module', $module)
            ->where('reference_id', $referenceId)
            ->orderBy('step_order')
            ->get();

        $total = $checklists->where('is_required', true)->count();
        $completed = $checklists->where('is_required', true)->where('is_completed', true)->count();
        $percentage = $total > 0 ? round(($completed / $total) * 100) : 0;

        // Cari step berikutnya yang belum selesai
        $nextStep = $checklists->where('is_completed', false)->sortBy('step_order')->first();

        return [
            'total'        => $total,
            'completed'    => $completed,
            'percentage'   => (int) $percentage,
            'is_complete'  => ($completed >= $total),
            'current_step' => $nextStep?->step_name,
        ];
    }

    /**
     * Ambil ringkasan seluruh workflow per modul.
     *
     * @return array Ringkasan: total dokumen, selesai, berjalan, tertunda
     */
    public static function getModuleSummary(string $module): array
    {
        $documents = WorkflowChecklist::where('module', $module)
            ->select('reference_id')
            ->distinct()
            ->pluck('reference_id');

        $totalDocs = $documents->count();
        $completeDocs = 0;
        $inProgressDocs = 0;
        $stuckDocs = 0;

        foreach ($documents as $refId) {
            $progress = self::getProgress($module, $refId);
            if ($progress['is_complete']) {
                $completeDocs++;
            } elseif ($progress['completed'] > 0) {
                $inProgressDocs++;
            } else {
                $stuckDocs++;
            }
        }

        return [
            'total'       => $totalDocs,
            'complete'    => $completeDocs,
            'in_progress' => $inProgressDocs,
            'stuck'       => $stuckDocs,
        ];
    }

    /**
     * Ambil dokumen-dokumen yang memiliki langkah terlewatkan (gap).
     * Gap = ada step lebih tinggi yang sudah complete tapi step lebih rendah belum.
     */
    public static function findSkippedWorkflows(string $module): array
    {
        $documents = WorkflowChecklist::where('module', $module)
            ->select('reference_id', 'reference_code')
            ->distinct()
            ->get();

        $skipped = [];

        foreach ($documents as $doc) {
            $steps = WorkflowChecklist::where('module', $module)
                ->where('reference_id', $doc->reference_id)
                ->where('is_required', true)
                ->orderBy('step_order')
                ->get();

            $maxCompletedOrder = $steps->where('is_completed', true)->max('step_order') ?? 0;

            $missedSteps = $steps->filter(function ($step) use ($maxCompletedOrder) {
                return !$step->is_completed && $step->step_order < $maxCompletedOrder;
            });

            if ($missedSteps->isNotEmpty()) {
                $skipped[] = [
                    'reference_id'   => $doc->reference_id,
                    'reference_code' => $doc->reference_code,
                    'missed_steps'   => $missedSteps->pluck('step_name')->toArray(),
                ];
            }
        }

        return $skipped;
    }

    /**
     * Ambil semua dokumen dengan workflow yang belum lengkap (tapi ada progres).
     */
    public static function getIncompleteDocuments(string $module, int $limit = 20): array
    {
        $documents = WorkflowChecklist::where('module', $module)
            ->select('reference_id', 'reference_code')
            ->distinct()
            ->get();

        $incomplete = [];

        foreach ($documents as $doc) {
            $progress = self::getProgress($module, $doc->reference_id);

            if (!$progress['is_complete']) {
                $incomplete[] = [
                    'reference_id'   => $doc->reference_id,
                    'reference_code' => $doc->reference_code,
                    'progress'       => $progress,
                ];
            }
        }

        // Sort by percentage ascending (paling terancam dulu)
        usort($incomplete, fn($a, $b) => $a['progress']['percentage'] <=> $b['progress']['percentage']);

        return array_slice($incomplete, 0, $limit);
    }

    // ════════════════════════════════════════════════════════════════════════════
    // ── FITUR KONTROL WORKFLOW AKUNTANSI & PENCEGAHAN KELALAIAN ADMINISTRASI ──
    // ════════════════════════════════════════════════════════════════════════════

    /**
     * Dapatkan ringkasan peringatan workflow & akuntansi global (Active Bottlenecks).
     */
    public static function getPendingAlertsSummary(): array
    {
        // 1. Jurnal Draft Belum Approve (is_posted = false)
        $unapprovedJournalsCount = JurnalUmum::where('is_posted', false)->count();

        // 2. Transaksi CUGIL RAW tanpa jurnal
        $rawCount = CugilRawMaterial::count();
        $rawWithJurnal = JurnalUmum::where('sumber_referensi', 'like', 'AUTO-PEMBELIAN-RAW-%')->count();
        $unjournalizedRaw = max(0, $rawCount - $rawWithJurnal);

        // 3. Transaksi CUGIL Sales tanpa jurnal
        $salesCount = CugilSale::count();
        $salesWithJurnal = JurnalUmum::where('sumber_referensi', 'like', 'AUTO-PENJUALAN-SALE-%')->count();
        $unjournalizedSales = max(0, $salesCount - $salesWithJurnal);

        // 4. Sales CUGIL tanpa foto timbangan
        $salesNoTimbangan = CugilSale::whereNull('foto_timbangan')->orWhere('foto_timbangan', '')->count();

        // 5. CUGIL PO yang belum disetujui / belum terima
        $poPendingReceive = CugilPurchaseOrder::where(function ($q) {
            $q->whereNull('status_terima')->orWhere('status_terima', '!=', 'YA');
        })->count();

        // 6. Pajak terutang belum disetor / belum lapor
        $pajakUnpaid = TransaksiPajak::where(function ($q) {
            $q->whereNull('status_bayar')->orWhere('status_bayar', '!=', 'Sudah Disetor');
        })->count();

        $pajakUnreported = TransaksiPajak::where(function ($q) {
            $q->whereNull('status_lapor')->orWhere('status_lapor', '!=', 'Sudah Dilapor');
        })->count();

        // 7. Pengajuan Dana menunggu review / approval BOD
        $danaPending = PengajuanDana::whereIn('status', ['Diajukan', 'Review BOD'])->count();

        // Total alert aktif
        $totalAlerts = $unapprovedJournalsCount + $unjournalizedRaw + $unjournalizedSales + $pajakUnpaid + $pajakUnreported;

        return [
            'unapproved_journals' => $unapprovedJournalsCount,
            'unjournalized_raw'   => $unjournalizedRaw,
            'unjournalized_sales' => $unjournalizedSales,
            'sales_no_timbangan'  => $salesNoTimbangan,
            'po_pending_receive'  => $poPendingReceive,
            'pajak_unpaid'        => $pajakUnpaid,
            'pajak_unreported'    => $pajakUnreported,
            'dana_pending'        => $danaPending,
            'total_alerts'        => $totalAlerts,
        ];
    }

    /**
     * Ambil daftar jurnal umum yang masih berstatus DRAFT (belum di-approve).
     */
    public static function getUnapprovedJournals(array $filters = [], int $limit = 50)
    {
        $query = JurnalUmum::where('is_posted', false)
            ->with(['details.akun']);

        if (!empty($filters['search'])) {
            $search = $filters['search'];
            $query->where(function ($q) use ($search) {
                $q->where('no_transaksi', 'like', "%{$search}%")
                  ->orWhere('deskripsi', 'like', "%{$search}%")
                  ->orWhere('sumber_referensi', 'like', "%{$search}%");
            });
        }

        if (!empty($filters['tipe'])) {
            $query->where('tipe_jurnal', $filters['tipe']);
        }

        if (!empty($filters['tanggal_dari']) && !empty($filters['tanggal_sampai'])) {
            $query->whereBetween('tanggal', [$filters['tanggal_dari'], $filters['tanggal_sampai']]);
        }

        return $query->orderBy('tanggal', 'desc')
            ->orderBy('id_jurnal', 'desc')
            ->take($limit)
            ->get();
    }

    /**
     * Deteksi transaksi operasional CUGIL yang belum memiliki pembukuan jurnal.
     */
    public static function getUnjournalizedTransactions(): array
    {
        $results = [
            'raw_materials' => [],
            'sales'         => [],
        ];

        // 1. Cek CUGIL RAW Materials
        $rawMaterials = CugilRawMaterial::orderBy('tanggal', 'desc')->get();
        foreach ($rawMaterials as $raw) {
            $refKey = 'AUTO-PEMBELIAN-RAW-' . $raw->id;
            $hasJournal = JurnalUmum::where('sumber_referensi', $refKey)->exists();
            if (!$hasJournal) {
                $results['raw_materials'][] = [
                    'id'             => $raw->id,
                    'tanggal'        => $raw->tanggal,
                    'nomor_po'       => $raw->nomor_po ?? '-',
                    'pemasok'        => $raw->nama_pemasok ?? $raw->kode_supplier ?? 'Tanpa Nama',
                    'tagihan'        => (float) $raw->tagihan,
                    'status_lunas'   => $raw->status_lunas ?? 'BELUM',
                    'has_zero_value' => (float) $raw->tagihan <= 0,
                    'reason'         => (float) $raw->tagihan <= 0 
                        ? 'Nominal tagihan Rp 0 (perlu input harga/bobot valid)' 
                        : 'Jurnal otomatis belum dibukukan',
                ];
            }
        }

        // 2. Cek CUGIL Sales
        $sales = CugilSale::orderBy('tanggal', 'desc')->get();
        foreach ($sales as $sale) {
            $refKey = 'AUTO-PENJUALAN-SALE-' . $sale->id;
            $hasJournal = JurnalUmum::where('sumber_referensi', $refKey)->exists();
            if (!$hasJournal) {
                $results['sales'][] = [
                    'id'             => $sale->id,
                    'id_penjualan'   => $sale->id_penjualan,
                    'tanggal'        => $sale->tanggal,
                    'buyer'          => $sale->nama_buyer ?? $sale->kode_customer ?? 'Tanpa Nama',
                    'tagihan'        => (float) $sale->tagihan,
                    'status_lunas'   => $sale->status_pelunasan ?? 'BELUM',
                    'has_zero_value' => (float) $sale->tagihan <= 0,
                    'reason'         => (float) $sale->tagihan <= 0 
                        ? 'Nominal tagihan Rp 0 (perlu input harga/bobot valid)' 
                        : 'Jurnal otomatis belum dibukukan',
                ];
            }
        }

        return $results;
    }

    /**
     * Dapatkan analisis celah administrasi menyeluruh (Administrative Gap Audit).
     */
    public static function getAdministrativeGaps(): array
    {
        $gaps = [];

        // 1. Akuntansi — Jurnal Belum Approve
        $unapproved = JurnalUmum::where('is_posted', false)->count();
        if ($unapproved > 0) {
            $gaps[] = [
                'category'    => 'Akuntansi',
                'severity'    => 'critical',
                'title'       => "{$unapproved} Jurnal Umum Masih Berstatus DRAFT",
                'description' => 'Jurnal belum diposting ke Buku Besar sehingga laporan Laba Rugi dan Neraca belum merefleksikan angka terkini.',
                'action_label'=> 'Lihat Antrian Approval Jurnal',
                'action_route'=> route('workflow.akuntansi', ['tab' => 'unapproved']),
                'count'       => $unapproved,
            ];
        }

        // 2. Akuntansi — Transaksi Tanpa Jurnal
        $unjournalized = self::getUnjournalizedTransactions();
        $totalUnjournalized = count($unjournalized['raw_materials']) + count($unjournalized['sales']);
        if ($totalUnjournalized > 0) {
            $gaps[] = [
                'category'    => 'Akuntansi & CUGIL',
                'severity'    => 'warning',
                'title'       => "{$totalUnjournalized} Transaksi Operasional Belum Berjurnal",
                'description' => 'Terdapat transaksi pembelian RAW material atau Penjualan CUGIL yang belum memiliki nomor jurnal pembukuan resmi.',
                'action_label'=> 'Audit Transaksi Tanpa Jurnal',
                'action_route'=> route('workflow.akuntansi', ['tab' => 'missing_journals']),
                'count'       => $totalUnjournalized,
            ];
        }

        // 3. CUGIL Penjualan — Foto Bukti Timbangan Belum Diunggah
        $salesNoTimbangan = CugilSale::whereNull('foto_timbangan')->orWhere('foto_timbangan', '')->count();
        if ($salesNoTimbangan > 0) {
            $gaps[] = [
                'category'    => 'CUGIL Penjualan',
                'severity'    => 'warning',
                'title'       => "{$salesNoTimbangan} Penjualan Belum Memiliki Foto Slip Timbangan",
                'description' => 'Bukti fisik timbangan adalah syarat mutlak penagihan dan keabsahan kuantum penjualan manufaktur PBS.',
                'action_label'=> 'Buka Modul Penjualan CUGIL',
                'action_route'=> route('cugil.sales.index'),
                'count'       => $salesNoTimbangan,
            ];
        }

        // 4. CUGIL Penjualan — Belum Diterbitkan Invoice
        $salesNoInvoice = CugilSale::where(function ($q) {
            $q->whereNull('invoiced')->orWhere('invoiced', '!=', 'SUDAH');
        })->count();
        if ($salesNoInvoice > 0) {
            $gaps[] = [
                'category'    => 'CUGIL Penjualan',
                'severity'    => 'warning',
                'title'       => "{$salesNoInvoice} Penjualan Belum Diterbitkan Invoice Resmi",
                'description' => 'Penjualan telah dicatat tetapi invoice resmi belum ditandatangani atau dikirim ke buyer.',
                'action_label'=> 'Periksa Status Invoice Penjualan',
                'action_route'=> route('cugil.sales.index'),
                'count'       => $salesNoInvoice,
            ];
        }

        // 5. Pajak — Transaksi Pajak Belum Disetor (NTPN Kosong)
        $pajakUnpaid = TransaksiPajak::where(function ($q) {
            $q->whereNull('status_bayar')->orWhere('status_bayar', '!=', 'Sudah Disetor');
        })->count();
        if ($pajakUnpaid > 0) {
            $gaps[] = [
                'category'    => 'Perpajakan (Tax BOD)',
                'severity'    => 'critical',
                'title'       => "{$pajakUnpaid} Transaksi Pajak Belum Disetor ke Kas Negara",
                'description' => 'Terdapat kewajiban PPN atau PPh terutang yang belum tercatat Nomor Transaksi Penerimaan Negara (NTPN).',
                'action_label'=> 'Kelola Penyetoran Pajak',
                'action_route'=> route('pajak.index'),
                'count'       => $pajakUnpaid,
            ];
        }

        // 6. Pajak — Belum Lapor SPT Masa (BPE Kosong)
        $pajakUnreported = TransaksiPajak::where(function ($q) {
            $q->whereNull('status_lapor')->orWhere('status_lapor', '!=', 'Sudah Dilapor');
        })->count();
        if ($pajakUnreported > 0) {
            $gaps[] = [
                'category'    => 'Perpajakan (Tax BOD)',
                'severity'    => 'warning',
                'title'       => "{$pajakUnreported} Transaksi Pajak Belum Dilaporkan SPT Masa",
                'description' => 'Dokumen belum memiliki Bukti Penerimaan Elektronik (BPE) dari Direktorat Jenderal Pajak.',
                'action_label'=> 'Buka Manajemen Pajak',
                'action_route'=> route('pajak.index'),
                'count'       => $pajakUnreported,
            ];
        }

        // 7. Pengajuan Dana — Diajukan tapi belum diproses
        $danaPending = PengajuanDana::where('status', 'Diajukan')->count();
        if ($danaPending > 0) {
            $gaps[] = [
                'category'    => 'Pengajuan Dana',
                'severity'    => 'info',
                'title'       => "{$danaPending} Pengajuan Dana Menunggu Review & Approval",
                'description' => 'Pengajuan anggaran operasional perlu diverifikasi oleh Finance & Direksi.',
                'action_label'=> 'Tinjau Pengajuan Dana',
                'action_route'=> route('anggaran.index'),
                'count'       => $danaPending,
            ];
        }

        return $gaps;
    }

    /**
     * Hitung Kepatuhan Siklus Tutup Buku Bulanan (Monthly Closing Gatekeeper).
     */
    public static function getMonthlyClosingStatus(?string $periodYm = null): array
    {
        $periodYm = $periodYm ?: now()->format('Y-m');
        $startDate = Carbon::createFromFormat('Y-m', $periodYm)->startOfMonth()->format('Y-m-d');
        $endDate = Carbon::createFromFormat('Y-m', $periodYm)->endOfMonth()->format('Y-m-d');

        // Checkpoint 1: Transaksi operasional bulan ini tercatat
        $rawInPeriod = CugilRawMaterial::whereBetween('tanggal', [$startDate, $endDate])->count();
        $salesInPeriod = CugilSale::whereBetween('tanggal', [$startDate, $endDate])->count();
        $opsRecorded = ($rawInPeriod > 0 || $salesInPeriod > 0);

        // Checkpoint 2: Jurnal draft di bulan ini sudah 0 (semua sudah di-approve)
        $draftInPeriod = JurnalUmum::where('is_posted', false)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();
        $draftCleared = ($draftInPeriod === 0);

        // Checkpoint 3: Jurnal Penyesuaian HPP CUGIL telah dieksekusi di akhir bulan
        $hppAdjusted = JurnalUmum::where('is_posted', true)
            ->where(function ($q) {
                $q->where('deskripsi', 'like', '%HPP%')
                  ->orWhere('deskripsi', 'like', '%Penyesuaian Beban Pokok%')
                  ->orWhere('sumber_referensi', 'like', 'AUTO-ADJUST-HPP-%');
            })
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->exists();

        // Checkpoint 4: Rekonsiliasi Kas & Bank
        $cashJournals = JurnalUmum::where('is_posted', true)
            ->whereIn('tipe_jurnal', ['Kas Masuk', 'Kas Keluar', 'Transfer'])
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->count();
        $cashReconciled = ($cashJournals > 0);

        // Checkpoint 5: Trial Balance Balance
        $totalDebit = JurnalUmum::where('is_posted', true)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_debit');
        $totalKredit = JurnalUmum::where('is_posted', true)
            ->whereBetween('tanggal', [$startDate, $endDate])
            ->sum('total_kredit');
        $trialBalanceOk = (abs($totalDebit - $totalKredit) < 0.01);

        // Checkpoint 6: Pajak disetor & dilaporkan
        $unsettledTax = TransaksiPajak::whereBetween('tanggal_faktur_potong', [$startDate, $endDate])
            ->where(function ($q) {
                $q->where('status_bayar', '!=', 'Sudah Disetor')
                  ->orWhere('status_lapor', '!=', 'Sudah Dilapor');
            })
            ->count();
        $taxFinalized = ($unsettledTax === 0);

        // Checkpoint 7: Otorisasi Laporan Keuangan Direksi
        $closingChecklist = WorkflowChecklist::where('module', 'akuntansi_closing')
            ->where('reference_code', "CLOSING-{$periodYm}")
            ->where('step_code', 'close_reports_approved')
            ->first();
        $reportsApproved = $closingChecklist ? (bool)$closingChecklist->is_completed : false;

        $steps = [
            [
                'step_code'   => 'close_ops_recorded',
                'name'        => '1. Pencatatan Transaksi Operasional Lengkap',
                'description' => "CUGIL RAW: {$rawInPeriod} transaksi, Sales: {$salesInPeriod} transaksi",
                'is_pass'     => $opsRecorded,
                'status_text' => $opsRecorded ? 'Lengkap & Terverifikasi' : 'Belum Ada Transaksi Tercatat',
            ],
            [
                'step_code'   => 'close_draft_cleared',
                'name'        => '2. Kliring Jurnal Draft (0 Belum Approve)',
                'description' => $draftCleared ? 'Seluruh jurnal bulan ini telah di-approve' : "{$draftInPeriod} jurnal masih berstatus draft",
                'is_pass'     => $draftCleared,
                'status_text' => $draftCleared ? 'Klir (0 Draft)' : "Perlu Approval ({$draftInPeriod} Draft)",
            ],
            [
                'step_code'   => 'close_hpp_adjusted',
                'name'        => '3. Penyesuaian HPP CUGIL & Stok Akhir',
                'description' => $hppAdjusted ? 'Jurnal penyesuaian HPP CUGIL akhir periode telah dibukukan' : 'Jurnal penyesuaian HPP akhir bulan belum dibuat',
                'is_pass'     => $hppAdjusted,
                'status_text' => $hppAdjusted ? 'Sudah Disesuaikan' : 'Perlu Disesuaikan',
            ],
            [
                'step_code'   => 'close_cash_reconciled',
                'name'        => '4. Rekonsiliasi Kas & Rekening Koran Bank',
                'description' => "Tercatat {$cashJournals} mutasi kas/bank di periode ini",
                'is_pass'     => $cashReconciled,
                'status_text' => $cashReconciled ? 'Terekonsiliasi' : 'Perlu Rekonsiliasi',
            ],
            [
                'step_code'   => 'close_trial_balance',
                'name'        => '5. Pemeriksaan Keseimbangan Neraca Saldo',
                'description' => "Debit: Rp " . number_format($totalDebit, 0, ',', '.') . " | Kredit: Rp " . number_format($totalKredit, 0, ',', '.'),
                'is_pass'     => $trialBalanceOk,
                'status_text' => $trialBalanceOk ? 'Balance (Seimbang)' : 'Tidak Seimbang (Selisih)',
            ],
            [
                'step_code'   => 'close_tax_finalized',
                'name'        => '6. Finalisasi & Penyetoran Pajak Periode',
                'description' => $taxFinalized ? 'Semua pajak bulan ini telah disetor & dilaporkan' : "{$unsettledTax} dokumen pajak belum disetor/lapor",
                'is_pass'     => $taxFinalized,
                'status_text' => $taxFinalized ? 'Taat Pajak (Tuntas)' : "Tertunda ({$unsettledTax} Dokumen)",
            ],
            [
                'step_code'   => 'close_reports_approved',
                'name'        => '7. Pengesahan Laporan Keuangan oleh BOD',
                'description' => 'Otorisasi formal Direksi atas Laporan Laba Rugi, Neraca, dan Arus Kas',
                'is_pass'     => $reportsApproved,
                'status_text' => $reportsApproved ? 'Disetujui BOD' : 'Menunggu TTD Direksi',
            ],
        ];

        $passedCount = count(array_filter($steps, fn($s) => $s['is_pass']));
        $compliancePercentage = round(($passedCount / count($steps)) * 100);

        return [
            'period'                => $periodYm,
            'start_date'            => $startDate,
            'end_date'              => $endDate,
            'steps'                 => $steps,
            'passed_count'          => $passedCount,
            'total_steps'           => count($steps),
            'compliance_percentage' => (int) $compliancePercentage,
            'is_ready_to_close'     => ($passedCount >= 6), // Semua pre-requisite tuntas
        ];
    }

    /**
     * Sinkronisasi Cerdas Workflow untuk Modul Spesifik.
     */
    public static function syncModule(string $module, ?string $userName = null): int
    {
        $userName = $userName ?? 'System (Auto-Sync)';
        $count = 0;

        switch ($module) {
            case 'akuntansi_jurnal':
                $jurnals = JurnalUmum::all();
                foreach ($jurnals as $j) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $j->id_jurnal)->exists();
                    if (!$existing) {
                        self::initializeChecklist($module, JurnalUmum::class, $j->id_jurnal, $j->no_transaksi, 'jurnal_draft', $userName);
                        
                        // Validasi debit == kredit
                        if (abs((float)$j->total_debit - (float)$j->total_kredit) < 0.01 && (float)$j->total_debit > 0) {
                            self::completeStep($module, $j->id_jurnal, 'jurnal_balance_checked', $userName);
                        }

                        // Verifikasi dokumen bukti jika ada sumber_referensi
                        if (!empty($j->sumber_referensi)) {
                            self::completeStep($module, $j->id_jurnal, 'jurnal_supporting_doc', $userName, 'Ref: ' . $j->sumber_referensi);
                        }

                        // Step posted & approved
                        if ($j->is_posted) {
                            self::completeStep($module, $j->id_jurnal, 'jurnal_approved_posted', $j->created_by ?? $userName, 'Diposting ke Buku Besar');
                            self::completeStep($module, $j->id_jurnal, 'jurnal_voucher_archived', $userName, 'Bukti voucher sah');
                        }

                        $count++;
                    }
                }
                break;

            case 'akuntansi_closing':
                // Ambil daftar bulan yang memiliki transaksi
                $periods = JurnalUmum::select(DB::raw("DATE_FORMAT(tanggal, '%Y-%m') as ym"))
                    ->distinct()
                    ->orderBy('ym', 'desc')
                    ->pluck('ym');

                foreach ($periods as $ym) {
                    $refCode = "CLOSING-{$ym}";
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_code', $refCode)->exists();
                    if (!$existing) {
                        // Inisialisasi closing record dengan ID hash dari string period
                        $refId = (int) hexdec(substr(md5($refCode), 0, 8));
                        self::initializeChecklist($module, 'App\Models\AkuntansiClosing', $refId, $refCode, 'close_ops_recorded', $userName);

                        $status = self::getMonthlyClosingStatus($ym);
                        foreach ($status['steps'] as $s) {
                            if ($s['is_pass']) {
                                self::completeStep($module, $refId, $s['step_code'], $userName, $s['status_text']);
                            }
                        }
                        $count++;
                    }
                }
                break;

            case 'cugil_po':
                $records = CugilPurchaseOrder::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        self::initializeChecklist($module, CugilPurchaseOrder::class, $rec->id, $rec->nomor_po, 'po_created', $userName);
                        self::completeStep($module, $rec->id, 'po_approved', $userName);
                        self::completeStep($module, $rec->id, 'po_sent_supplier', $userName);

                        if (strtoupper($rec->status_terima ?? '') === 'YA' || $rec->rawMaterials()->exists()) {
                            self::completeStep($module, $rec->id, 'po_received', $userName, 'Barang diterima via RAW');
                        }
                        $count++;
                    }
                }
                break;

            case 'cugil_raw':
                $records = CugilRawMaterial::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        $refCode = $rec->nomor_po ?? 'RAW-' . $rec->id;
                        self::initializeChecklist($module, CugilRawMaterial::class, $rec->id, $refCode, 'raw_received', $userName);

                        if ((float)$rec->berat_netto > 0) {
                            self::completeStep($module, $rec->id, 'raw_weighed', $userName, 'Netto: ' . number_format($rec->berat_netto, 2) . ' Kg');
                        }

                        if (strtoupper($rec->invoiced ?? '') === 'SUDAH' || (float)$rec->tagihan > 0) {
                            self::completeStep($module, $rec->id, 'raw_invoiced', $userName);
                        }

                        if (strtoupper($rec->status_lunas ?? '') === 'LUNAS' || (float)$rec->payment >= (float)$rec->tagihan) {
                            self::completeStep($module, $rec->id, 'raw_payment', $userName);
                        }

                        // Jurnal posted check
                        $refKey = 'AUTO-PEMBELIAN-RAW-' . $rec->id;
                        if (JurnalUmum::where('sumber_referensi', $refKey)->where('is_posted', true)->exists()) {
                            self::completeStep($module, $rec->id, 'raw_journal_posted', $userName);
                        }

                        $count++;
                    }
                }
                break;

            case 'cugil_sales':
                $records = CugilSale::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        self::initializeChecklist($module, CugilSale::class, $rec->id, $rec->id_penjualan, 'sales_order_created', $userName);

                        if (!empty($rec->foto_timbangan)) {
                            self::completeStep($module, $rec->id, 'sales_timbangan', $userName, 'Foto timbangan ada');
                        }

                        self::completeStep($module, $rec->id, 'sales_surat_jalan', $userName);
                        self::completeStep($module, $rec->id, 'sales_shipped', $userName);

                        if (strtoupper($rec->invoiced ?? '') === 'SUDAH') {
                            self::completeStep($module, $rec->id, 'sales_invoiced', $userName);
                        }

                        if (strtoupper($rec->faktur_pajak ?? '') === 'SUDAH') {
                            self::completeStep($module, $rec->id, 'sales_faktur_pajak', $userName);
                        }

                        if (strtoupper($rec->status_pelunasan ?? '') === 'LUNAS') {
                            self::completeStep($module, $rec->id, 'sales_payment_received', $userName);
                        }

                        $refKey = 'AUTO-PENJUALAN-SALE-' . $rec->id;
                        if (JurnalUmum::where('sumber_referensi', $refKey)->where('is_posted', true)->exists()) {
                            self::completeStep($module, $rec->id, 'sales_journal_posted', $userName);
                        }

                        $count++;
                    }
                }
                break;

            case 'pengajuan_dana':
                $records = PengajuanDana::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        self::initializeChecklist($module, PengajuanDana::class, $rec->id, $rec->nomor_pengajuan, 'dana_submitted', $userName);
                        if (in_array($rec->status, ['Disetujui BOD', 'Dicairkan'])) {
                            self::completeStep($module, $rec->id, 'dana_reviewed', $userName);
                            self::completeStep($module, $rec->id, 'dana_approved', $userName);
                        }
                        if ($rec->status === 'Ditolak') {
                            self::completeStep($module, $rec->id, 'dana_reviewed', $userName);
                            self::completeStep($module, $rec->id, 'dana_approved', $userName, 'Pengajuan ditolak oleh BOD');
                        }
                        if ($rec->status === 'Dicairkan') {
                            self::completeStep($module, $rec->id, 'dana_disbursed', $userName);
                        }
                        $count++;
                    }
                }
                break;

            case 'proyek':
                $records = Proyek::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        self::initializeChecklist($module, Proyek::class, $rec->id, $rec->kode_proyek, 'prj_registered', $userName);
                        if ($rec->invoices()->exists()) {
                            self::completeStep($module, $rec->id, 'prj_invoice_issued', $userName);
                        }
                        if (strtolower($rec->status_proyek ?? '') === 'selesai') {
                            self::completeStep($module, $rec->id, 'prj_completed', $userName);
                        }
                        $count++;
                    }
                }
                break;

            case 'pajak':
                $records = TransaksiPajak::all();
                foreach ($records as $rec) {
                    $existing = WorkflowChecklist::where('module', $module)->where('reference_id', $rec->id)->exists();
                    if (!$existing) {
                        self::initializeChecklist($module, TransaksiPajak::class, $rec->id, $rec->kode_referensi, 'tax_recorded', $userName);
                        self::completeStep($module, $rec->id, 'tax_verified', $userName);

                        if ($rec->status_bayar === 'Sudah Disetor' || !empty($rec->ntpn)) {
                            self::completeStep($module, $rec->id, 'tax_paid', $userName, 'NTPN: ' . ($rec->ntpn ?? '-'));
                        }
                        if ($rec->status_lapor === 'Sudah Dilapor' || !empty($rec->bpe_spt)) {
                            self::completeStep($module, $rec->id, 'tax_reported', $userName, 'BPE: ' . ($rec->bpe_spt ?? '-'));
                            self::completeStep($module, $rec->id, 'tax_archived', $userName);
                        }
                        $count++;
                    }
                }
                break;
        }

        return $count;
    }

    /**
     * Sinkronisasi Seluruh Modul Sekaligus.
     */
    public static function syncAll(?string $userName = null): array
    {
        $modules = array_keys(WorkflowDefinition::moduleLabels());
        $results = [];

        foreach ($modules as $mod) {
            $results[$mod] = self::syncModule($mod, $userName);
        }

        return $results;
    }
}
