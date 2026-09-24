<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('cugil_sales', function (Blueprint $table) {
            if (!Schema::hasColumn('cugil_sales', 'foto_timbangan')) {
                $table->string('foto_timbangan', 255)->nullable()->after('status_timbangan');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('cugil_sales', function (Blueprint $table) {
            if (Schema::hasColumn('cugil_sales', 'foto_timbangan')) {
                $table->dropColumn('foto_timbangan');
            }
        });
    }
};
