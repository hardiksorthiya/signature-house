<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->date('production_date')->nullable()->after('khata_number');
            $table->string('name_plate_path')->nullable()->after('production_date');
        });
    }

    public function down(): void
    {
        Schema::table('serial_numbers', function (Blueprint $table) {
            $table->dropColumn(['production_date', 'name_plate_path']);
        });
    }
};
