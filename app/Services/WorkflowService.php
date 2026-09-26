<?php

namespace App\Services;

use App\Models\WorkflowChecklist;
use App\Models\WorkflowDefinition;
use Illuminate\Support\Facades\DB;

class WorkflowService
{
    /**
     * Buat checklist workflow untuk sebuah dokumen/transaksi baru.
     *
     * @param string $module         Kode modul (cugil_po, cugil_raw, cugil_sales, dll)
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
        $stuckDocs = 0; // Dokumen yang belum ada progres sama sekali

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
}
