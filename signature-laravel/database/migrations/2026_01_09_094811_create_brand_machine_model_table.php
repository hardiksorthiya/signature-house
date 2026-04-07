<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('brand_machine_model', function (Blueprint $table) {
            $table->id();
            $table->foreignId('brand_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_model_id')->constrained()->onDelete('cascade');
            $table->timestamps();
            
            // Ensure unique combination
            $table->unique(['brand_id', 'machine_model_id']);
        });
        
        // Migrate existing brand_id relationships to pivot table
        DB::statement("
            INSERT INTO brand_machine_model (brand_id, machine_model_id, created_at, updated_at)
            SELECT brand_id, id as machine_model_id, created_at, updated_at
            FROM machine_models
            WHERE brand_id IS NOT NULL
        ");
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('brand_machine_model');
    }
};
