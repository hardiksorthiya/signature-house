<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('machine_erection_machine_summaries', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('machine_category_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('machine_number');
            $table->date('machine_erection_date')->nullable();
            $table->date('installation_completed_date')->nullable();
            $table->timestamps();

            $table->unique(
                ['proforma_invoice_id', 'machine_category_id', 'machine_number'],
                'me_machine_summary_unique'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('machine_erection_machine_summaries');
    }
};
