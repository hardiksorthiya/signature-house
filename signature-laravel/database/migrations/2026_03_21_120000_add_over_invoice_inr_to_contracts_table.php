<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (!Schema::hasColumn('contracts', 'over_invoice_usd_inr_rate')) {
                $table->decimal('over_invoice_usd_inr_rate', 18, 6)->nullable()->after('approval_notes');
            }
            if (!Schema::hasColumn('contracts', 'over_invoice_difference_inr')) {
                $table->decimal('over_invoice_difference_inr', 18, 2)->nullable()->after('over_invoice_usd_inr_rate');
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            if (Schema::hasColumn('contracts', 'over_invoice_difference_inr')) {
                $table->dropColumn('over_invoice_difference_inr');
            }
            if (Schema::hasColumn('contracts', 'over_invoice_usd_inr_rate')) {
                $table->dropColumn('over_invoice_usd_inr_rate');
            }
        });
    }
};
