<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('slug', 50)->unique(); // e.g. bod, admin, finance_manager, tax_officer, cugil_operator, staff
            $table->string('name', 100);          // e.g. Board of Director
            $table->text('description')->nullable();
            $table->json('permissions')->nullable(); // Array of allowed permission strings
            $table->boolean('is_system')->default(false); // System roles cannot be deleted
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('roles');
    }
};
