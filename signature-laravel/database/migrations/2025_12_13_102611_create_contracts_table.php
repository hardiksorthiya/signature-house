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
        if (!Schema::hasTable('contracts')) {
            Schema::create('contracts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('lead_id')->nullable()->constrained()->onDelete('set null');
                $table->foreignId('business_firm_id')->constrained()->onDelete('cascade');
                $table->string('contract_number')->unique();
                $table->string('buyer_name');
                $table->string('company_name')->nullable();
                $table->text('contact_address')->nullable();
                $table->foreignId('state_id')->constrained()->onDelete('cascade');
                $table->foreignId('city_id')->constrained()->onDelete('cascade');
                $table->foreignId('area_id')->constrained()->onDelete('cascade');
                $table->string('email')->nullable();
                $table->string('phone_number');
                $table->string('phone_number_2')->nullable();
                $table->string('gst')->nullable();
                $table->string('pan')->nullable();
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('contracts');
    }
};
