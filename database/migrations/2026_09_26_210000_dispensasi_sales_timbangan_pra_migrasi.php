<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Berikan dispensasi workflow foto timbangan untuk seluruh transaksi penjualan sebelum/pada tanggal migrasi (<= 25 Sep 2026)
        DB::table('workflow_checklists')
            ->where('module', 'cugil_sales')
            ->where('step_code', 'sales_timbangan')
            ->where('is_completed', false)
            ->whereIn('reference_id', function ($query) {
                $query->select('id')
                    ->from('cugil_sales')
                    ->where('tanggal', '<=', '2026-09-25');
            })
            ->update([
                'is_completed' => true,
                'completed_by' => 'System (Dispensasi Pra-Migrasi)',
                'completed_at' => now(),
                'notes'        => 'Dispensasi transaksi pra-migrasi (<= 25 Sep 2026)',
                'updated_at'   => now(),
            ]);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Revert dispensasi
        DB::table('workflow_checklists')
            ->where('module', 'cugil_sales')
            ->where('step_code', 'sales_timbangan')
            ->where('completed_by', 'System (Dispensasi Pra-Migrasi)')
            ->update([
                'is_completed' => false,
                'completed_by' => null,
                'completed_at' => null,
                'notes'        => null,
                'updated_at'   => now(),
            ]);
    }
};
