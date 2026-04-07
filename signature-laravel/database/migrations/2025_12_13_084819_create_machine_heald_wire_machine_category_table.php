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
        Schema::create('machine_heald_wire_machine_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_heald_wire_id');
            $table->foreign('machine_heald_wire_id', 'mhw_mc_wire_fk')->references('id')->on('machine_heald_wires')->onDelete('cascade');
            $table->unsignedBigInteger('machine_category_id');
            $table->foreign('machine_category_id', 'mhw_mc_cat_fk')->references('id')->on('machine_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_heald_wire_machine_category');
    }
};
