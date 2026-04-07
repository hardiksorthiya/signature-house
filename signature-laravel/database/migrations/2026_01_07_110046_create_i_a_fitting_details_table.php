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
        Schema::create('i_a_fitting_details', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_category_id')->constrained()->onDelete('cascade');
            $table->integer('machine_number')->comment('Machine number from 1 to N');
            $table->string('detail_name');
            $table->text('value')->nullable();
            $table->string('value_type')->default('text')->comment('text, radio, textarea');
            $table->integer('sort_order')->default(0);
            $table->timestamps();
            
            $table->index(['proforma_invoice_id', 'machine_category_id', 'machine_number'], 'iafd_pi_cat_machine_idx');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('i_a_fitting_details');
    }
};
