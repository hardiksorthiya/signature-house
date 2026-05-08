<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('old_data_machines', function (Blueprint $table) {
            $table->id();
            $table->foreignId('old_data_id')->constrained('old_data')->onDelete('cascade');
            $table->foreignId('machine_category_id')->nullable()->constrained()->nullOnDelete();
            $table->string('machine_model')->nullable();
            $table->string('serial_number')->nullable();
            $table->string('khata_number')->nullable();
            $table->date('date_of_manufacturing')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('old_data_machines');
    }
};
