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
        if (!Schema::hasTable('leads')) {
            Schema::create('leads', function (Blueprint $table) {
            $table->id();
            $table->enum('type', ['new', 'old'])->default('new');
            $table->foreignId('business_id')->nullable()->constrained()->onDelete('set null');
            $table->string('name');
            $table->string('phone_number')->unique();
            $table->foreignId('state_id')->constrained()->onDelete('cascade');
            $table->foreignId('city_id')->constrained()->onDelete('cascade');
            $table->foreignId('area_id')->constrained()->onDelete('cascade');
            $table->integer('quantity');
            $table->foreignId('status_id')->constrained()->onDelete('cascade');
            // Old form specific fields
            $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
            $table->integer('machine_quantity')->nullable();
            $table->string('running_since')->nullable();
            $table->timestamps();
        });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('leads');
    }
};
