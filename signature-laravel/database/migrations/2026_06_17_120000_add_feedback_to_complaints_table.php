<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->string('feedback_status')->nullable()->after('remarks');
            $table->text('feedback_remarks')->nullable()->after('feedback_status');
            $table->timestamp('feedback_at')->nullable()->after('feedback_remarks');
            $table->foreignId('feedback_by')->nullable()->after('feedback_at')->constrained('users')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('complaints', function (Blueprint $table) {
            $table->dropForeign(['feedback_by']);
            $table->dropColumn(['feedback_status', 'feedback_remarks', 'feedback_at', 'feedback_by']);
        });
    }
};
