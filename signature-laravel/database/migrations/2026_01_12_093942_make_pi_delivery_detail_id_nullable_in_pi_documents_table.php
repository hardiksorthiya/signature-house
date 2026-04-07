<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Check if column exists first
        if (Schema::hasColumn('pi_documents', 'pi_delivery_detail_id')) {
            // Try to drop foreign key if it exists
            try {
                Schema::table('pi_documents', function (Blueprint $table) {
                    $table->dropForeign(['pi_delivery_detail_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist, continue
            }
            
            // Make pi_delivery_detail_id nullable since images can be uploaded without linking to a specific delivery detail
            Schema::table('pi_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('pi_delivery_detail_id')->nullable()->change();
            });
            
            // Re-add foreign key constraint if pi_delivery_details table exists
            if (Schema::hasTable('pi_delivery_details')) {
                Schema::table('pi_documents', function (Blueprint $table) {
                    $table->foreign('pi_delivery_detail_id')->references('id')->on('pi_delivery_details')->onDelete('set null');
                });
            }
        } else {
            // Column doesn't exist, create it as nullable
            Schema::table('pi_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('pi_delivery_detail_id')->nullable()->after('proforma_invoice_id');
            });
            
            if (Schema::hasTable('pi_delivery_details')) {
                Schema::table('pi_documents', function (Blueprint $table) {
                    $table->foreign('pi_delivery_detail_id')->references('id')->on('pi_delivery_details')->onDelete('set null');
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('pi_documents', 'pi_delivery_detail_id')) {
            try {
                Schema::table('pi_documents', function (Blueprint $table) {
                    $table->dropForeign(['pi_delivery_detail_id']);
                });
            } catch (\Exception $e) {
                // Foreign key might not exist
            }
            
            Schema::table('pi_documents', function (Blueprint $table) {
                $table->unsignedBigInteger('pi_delivery_detail_id')->nullable(false)->change();
            });
            
            if (Schema::hasTable('pi_delivery_details')) {
                Schema::table('pi_documents', function (Blueprint $table) {
                    $table->foreign('pi_delivery_detail_id')->references('id')->on('pi_delivery_details')->onDelete('cascade');
                });
            }
        }
    }
};
