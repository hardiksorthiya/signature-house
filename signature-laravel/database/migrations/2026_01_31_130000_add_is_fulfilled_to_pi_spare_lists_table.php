<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('pi_spare_lists', function (Blueprint $table) {
            $table->boolean('is_fulfilled')->default(false)->after('is_custom');
        });
    }

    public function down(): void
    {
        Schema::table('pi_spare_lists', function (Blueprint $table) {
            $table->dropColumn('is_fulfilled');
        });
    }
};
