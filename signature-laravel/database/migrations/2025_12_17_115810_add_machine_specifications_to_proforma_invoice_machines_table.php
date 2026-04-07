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
        Schema::table('proforma_invoice_machines', function (Blueprint $table) {
            $table->foreignId('machine_category_id')->nullable()->after('contract_machine_id')->constrained()->onDelete('set null');
            $table->foreignId('brand_id')->nullable()->after('machine_category_id')->constrained()->onDelete('set null');
            $table->foreignId('machine_model_id')->nullable()->after('brand_id')->constrained()->onDelete('set null');
            $table->foreignId('feeder_id')->nullable()->after('machine_model_id')->constrained()->onDelete('set null');
            $table->foreignId('machine_hook_id')->nullable()->after('feeder_id')->constrained('machine_hooks')->onDelete('set null');
            $table->foreignId('machine_e_read_id')->nullable()->after('machine_hook_id')->constrained('machine_e_reads')->onDelete('set null');
            $table->foreignId('color_id')->nullable()->after('machine_e_read_id')->constrained()->onDelete('set null');
            $table->foreignId('machine_nozzle_id')->nullable()->after('color_id')->constrained('machine_nozzles')->onDelete('set null');
            $table->foreignId('machine_dropin_id')->nullable()->after('machine_nozzle_id')->constrained('machine_dropins')->onDelete('set null');
            $table->foreignId('machine_beam_id')->nullable()->after('machine_dropin_id')->constrained('machine_beams')->onDelete('set null');
            $table->foreignId('machine_cloth_roller_id')->nullable()->after('machine_beam_id')->constrained('machine_cloth_rollers')->onDelete('set null');
            $table->foreignId('machine_software_id')->nullable()->after('machine_cloth_roller_id')->constrained('machine_softwares')->onDelete('set null');
            $table->foreignId('hsn_code_id')->nullable()->after('machine_software_id')->constrained('hsn_codes')->onDelete('set null');
            $table->foreignId('wir_id')->nullable()->after('hsn_code_id')->constrained('wirs')->onDelete('set null');
            $table->foreignId('machine_shaft_id')->nullable()->after('wir_id')->constrained('machine_shafts')->onDelete('set null');
            $table->foreignId('machine_lever_id')->nullable()->after('machine_shaft_id')->constrained('machine_levers')->onDelete('set null');
            $table->foreignId('machine_chain_id')->nullable()->after('machine_lever_id')->constrained('machine_chains')->onDelete('set null');
            $table->foreignId('machine_heald_wire_id')->nullable()->after('machine_chain_id')->constrained('machine_heald_wires')->onDelete('set null');
            $table->foreignId('delivery_term_id')->nullable()->after('machine_heald_wire_id')->constrained('delivery_terms')->onDelete('set null');
            $table->text('description')->nullable()->after('delivery_term_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('proforma_invoice_machines', function (Blueprint $table) {
            $table->dropForeign(['machine_category_id']);
            $table->dropForeign(['brand_id']);
            $table->dropForeign(['machine_model_id']);
            $table->dropForeign(['feeder_id']);
            $table->dropForeign(['machine_hook_id']);
            $table->dropForeign(['machine_e_read_id']);
            $table->dropForeign(['color_id']);
            $table->dropForeign(['machine_nozzle_id']);
            $table->dropForeign(['machine_dropin_id']);
            $table->dropForeign(['machine_beam_id']);
            $table->dropForeign(['machine_cloth_roller_id']);
            $table->dropForeign(['machine_software_id']);
            $table->dropForeign(['hsn_code_id']);
            $table->dropForeign(['wir_id']);
            $table->dropForeign(['machine_shaft_id']);
            $table->dropForeign(['machine_lever_id']);
            $table->dropForeign(['machine_chain_id']);
            $table->dropForeign(['machine_heald_wire_id']);
            $table->dropForeign(['delivery_term_id']);
            $table->dropColumn([
                'machine_category_id', 'brand_id', 'machine_model_id', 'feeder_id',
                'machine_hook_id', 'machine_e_read_id', 'color_id', 'machine_nozzle_id',
                'machine_dropin_id', 'machine_beam_id', 'machine_cloth_roller_id',
                'machine_software_id', 'hsn_code_id', 'wir_id', 'machine_shaft_id',
                'machine_lever_id', 'machine_chain_id', 'machine_heald_wire_id',
                'delivery_term_id', 'description'
            ]);
        });
    }
};
