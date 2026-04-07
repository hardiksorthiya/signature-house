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
        Schema::create('pi_spare_lists', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->onDelete('cascade');
            $table->string('document_name'); // Delivery document / spare item name
            $table->string('quantity')->nullable(); // Number or text e.g. "Loose as Extra"
            $table->boolean('is_custom')->default(false); // true = user-added row
            $table->unsignedInteger('sort_order')->default(0);
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('pi_spare_lists');
    }
};
