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
        if (!Schema::hasTable('contract_machines')) {
            Schema::create('contract_machines', function (Blueprint $table) {
                $table->id();
                $table->foreignId('contract_id')->constrained()->onDelete('cascade');
                $table->foreignId('machine_category_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('brand_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('machine_model_id')->nullable()->constrained()->onDelete('set null');
                $table->integer('quantity')->default(1);
                $table->text('description')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contract_machines');
    }
};
