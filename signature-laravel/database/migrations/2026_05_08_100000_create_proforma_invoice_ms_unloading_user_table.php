<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('proforma_invoice_ms_unloading_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('proforma_invoice_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();

            $table->unique(['proforma_invoice_id', 'user_id'], 'pi_ms_unloading_user_unique');
        });

        if (Schema::hasColumn('proforma_invoices', 'ms_unloading_assigned_to')) {
            DB::table('proforma_invoices')
                ->whereNotNull('ms_unloading_assigned_to')
                ->orderBy('id')
                ->each(function ($pi) {
                    DB::table('proforma_invoice_ms_unloading_user')->insertOrIgnore([
                        'proforma_invoice_id' => $pi->id,
                        'user_id' => $pi->ms_unloading_assigned_to,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                });

            Schema::table('proforma_invoices', function (Blueprint $table) {
                $table->dropForeign(['ms_unloading_assigned_to']);
                $table->dropColumn('ms_unloading_assigned_to');
            });
        }
    }

    public function down(): void
    {
        Schema::table('proforma_invoices', function (Blueprint $table) {
            $table->foreignId('ms_unloading_assigned_to')
                ->nullable()
                ->after('created_by')
                ->constrained('users')
                ->nullOnDelete();
        });

        $firstAssignments = DB::table('proforma_invoice_ms_unloading_user')
            ->select('proforma_invoice_id', DB::raw('MIN(user_id) as user_id'))
            ->groupBy('proforma_invoice_id')
            ->get();

        foreach ($firstAssignments as $row) {
            DB::table('proforma_invoices')
                ->where('id', $row->proforma_invoice_id)
                ->update(['ms_unloading_assigned_to' => $row->user_id]);
        }

        Schema::dropIfExists('proforma_invoice_ms_unloading_user');
    }
};
