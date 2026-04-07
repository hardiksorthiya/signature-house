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
        Schema::create('machine_e_read_machine_category', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('machine_e_read_id');
            $table->foreign('machine_e_read_id', 'mer_mc_read_fk')->references('id')->on('machine_e_reads')->onDelete('cascade');
            $table->unsignedBigInteger('machine_category_id');
            $table->foreign('machine_category_id', 'mer_mc_cat_fk')->references('id')->on('machine_categories')->onDelete('cascade');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('machine_e_read_machine_category');
    }
};
