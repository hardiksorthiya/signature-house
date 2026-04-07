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
        Schema::table('payments', function (Blueprint $table) {
            $table->string('payment_by')->nullable()->after('payment_method');
            $table->foreignId('payee_country_id')->nullable()->constrained('countries')->onDelete('set null')->after('payment_by');
            $table->foreignId('payment_to_seller_id')->nullable()->constrained('sellers')->onDelete('set null')->after('payee_country_id');
            $table->foreignId('bank_detail_id')->nullable()->constrained('seller_bank_details')->onDelete('set null')->after('payment_to_seller_id');
            $table->string('transaction_id')->nullable()->after('bank_detail_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            $table->dropForeign(['payee_country_id']);
            $table->dropForeign(['payment_to_seller_id']);
            $table->dropForeign(['bank_detail_id']);
            $table->dropColumn(['payment_by', 'payee_country_id', 'payment_to_seller_id', 'bank_detail_id', 'transaction_id']);
        });
    }
};
