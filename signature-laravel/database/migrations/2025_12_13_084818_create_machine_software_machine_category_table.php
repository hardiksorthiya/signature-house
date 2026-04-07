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
        Schema::create('machine_software_machine_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_software_id');
            $table->foreign('machine_software_id', 'msw_mc_soft_fk')->references('id')->on('machine_softwares')->onDelete('cascade');
            $table->unsignedBigInteger('machine_category_id');
            $table->foreign('machine_category_id', 'msw_mc_cat_fk')->references('id')->on('machine_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_software_machine_category');
    }
};
