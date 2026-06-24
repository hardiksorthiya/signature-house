<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('machine_erection_machine_summaries', function (Blueprint $table) {
            $table->boolean('certificate_received')->nullable()->after('installation_completed_date');
        });
    }

    public function down(): void
    {
        Schema::table('machine_erection_machine_summaries', function (Blueprint $table) {
            $table->dropColumn('certificate_received');
        });
    }
};
