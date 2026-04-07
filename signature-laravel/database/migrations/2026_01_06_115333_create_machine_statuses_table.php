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
        Schema::create('machine_statuses', function (Blueprint $table) {
            $table->id();
            $table->foreignId('contract_id')->nullable()->constrained()->onDelete('cascade');
            $table->foreignId('proforma_invoice_id')->nullable()->constrained()->onDelete('cascade');
            $table->date('contract_date')->nullable();
            $table->boolean('contract_date_completed')->default(false);
            $table->date('proforma_invoice_date')->nullable();
            $table->boolean('proforma_invoice_completed')->default(false);
            $table->date('china_payment_date')->nullable();
            $table->boolean('china_payment_completed')->default(false);
            $table->date('actual_dispatch_date')->nullable();
            $table->boolean('actual_dispatch_completed')->default(false);
            $table->date('expected_arrival_date')->nullable();
            $table->boolean('expected_arrival_completed')->default(false);
            $table->date('actual_arrival_date')->nullable();
            $table->boolean('actual_arrival_completed')->default(false);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_statuses');
    }
};
