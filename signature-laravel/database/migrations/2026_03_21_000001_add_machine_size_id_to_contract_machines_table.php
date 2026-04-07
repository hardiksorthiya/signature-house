<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contract_machines', function (Blueprint $table) {
            if (!Schema::hasColumn('contract_machines', 'machine_size_id')) {
                $table->foreignId('machine_size_id')
                    ->nullable()
                    ->after('machine_model_id')
                    ->constrained('machine_sizes')
                    ->nullOnDelete();
            }
        });

        // Backfill from legacy JSON (lead conversion stored size only there)
        if (Schema::hasColumn('contracts', 'machine_details')) {
            $contracts = DB::table('contracts')
                ->whereNotNull('machine_details')
                ->select('id', 'machine_details')
                ->get();

            foreach ($contracts as $contract) {
                $details = json_decode($contract->machine_details, true);
                if (!is_array($details)) {
                    continue;
                }
                $rows = DB::table('contract_machines')
                    ->where('contract_id', $contract->id)
                    ->orderBy('id')
                    ->get(['id']);
                if ($rows->count() !== count($details)) {
                    continue;
                }
                foreach ($rows as $i => $row) {
                    $sizeId = $details[$i]['machine_size_id'] ?? null;
                    if ($sizeId !== null && $sizeId !== '') {
                        DB::table('contract_machines')
                            ->where('id', $row->id)
                            ->update(['machine_size_id' => (int) $sizeId]);
                    }
                }
            }
        }
    }

    public function down(): void
    {
        Schema::table('contract_machines', function (Blueprint $table) {
            if (Schema::hasColumn('contract_machines', 'machine_size_id')) {
                $table->dropForeign(['machine_size_id']);
                $table->dropColumn('machine_size_id');
            }
        });
    }
};
