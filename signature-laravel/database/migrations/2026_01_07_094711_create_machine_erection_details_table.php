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
        Schema::create('machine_erection_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_category_id')->constrained()->onDelete('cascade');
            $table->string('point_to_follow');
            $table->integer('machine_number')->comment('Machine number from 1 to 10');
            $table->date('date')->nullable();
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['proforma_invoice_id', 'machine_category_id'], 'med_pi_cat_idx');
            $table->index(['machine_category_id', 'point_to_follow', 'machine_number'], 'med_cat_point_machine_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_erection_details');
    }
};
