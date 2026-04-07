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
        Schema::create('machine_chain_machine_category', function (Blueprint $table) {
            $table->id();
            $table->foreignId('machine_chain_id')->constrained()->onDelete('cascade');
            $table->foreignId('machine_category_id')->constrained()->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_chain_machine_category');
    }
};
