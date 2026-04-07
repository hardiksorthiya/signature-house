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
        Schema::table('contracts', function (Blueprint $table) {
            $table->text('customer_signature')->nullable()->after('difference_specification_in_print');
            $table->enum('approval_status', ['pending', 'approved', 'rejected'])->default('pending')->after('customer_signature');
            $table->foreignId('approved_by')->nullable()->after('approval_status')->constrained('users')->onDelete('set null');
            $table->timestamp('approved_at')->nullable()->after('approved_by');
            $table->text('approval_notes')->nullable()->after('approved_at');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropForeign(['approved_by']);
            $table->dropColumn([
                'customer_signature',
                'approval_status',
                'approved_by',
                'approved_at',
                'approval_notes',
            ]);
        });
    }
};
