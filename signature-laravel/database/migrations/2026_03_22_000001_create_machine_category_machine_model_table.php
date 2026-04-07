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
        if (Schema::hasTable('machine_category_machine_model')) {
            return;
        }

        Schema::create('machine_category_machine_model', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_category_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_model_id')->constrained()->onDelete('cascade');
            $table->timestamps();

            $table->unique(['machine_category_id', 'machine_model_id'], 'mc_mm_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_category_machine_model');
    }
};
