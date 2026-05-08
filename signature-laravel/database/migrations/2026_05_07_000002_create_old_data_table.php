<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('old_data', function (Blueprint $table) {
            $table->id();
            $table->string('firm_name');
            $table->string('client_name');
            $table->string('phone_number_1', 30);
            $table->string('phone_number_2', 30)->nullable();
            $table->string('city')->nullable();
            $table->string('area')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('old_data');
    }
};
