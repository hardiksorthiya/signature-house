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
        Schema::create('serial_numbers', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->onDelete('cascade');
            $table->foreignId('proforma_invoice_machine_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_category_id')->constrained()->onDelete('cascade');
            $table->string('serial_number')->nullable();
            $table->string('khata_number')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('serial_numbers');
    }
};
