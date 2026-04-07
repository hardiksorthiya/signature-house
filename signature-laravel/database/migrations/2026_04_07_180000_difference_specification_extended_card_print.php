<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function perRowInPrintColumns(): array
    {
        return [
            'spec_bintian_8_shaft_cam_heald_in_print',
            'spec_bintian_10_shaft_cam_heald_in_print',
            'spec_changshu_16_shaft_2861_dobby_in_print',
            'spec_staubli_16_shaft_dobby_in_print',
            'spec_increase_1_nozzle_in_print',
            'spec_increase_1_feeder_in_print',
            'spec_reed_increase_per_10cm_in_print',
            'spec_bintian_cam_plate_extra_in_print',
            'spec_bintian_gear_extra_in_print',
        ];
    }

    private function globalPerRowInPrintColumns(): array
    {
        return [
            'global_spec_bintian_8_shaft_cam_heald_in_print',
            'global_spec_bintian_10_shaft_cam_heald_in_print',
            'global_spec_changshu_16_shaft_2861_dobby_in_print',
            'global_spec_staubli_16_shaft_dobby_in_print',
            'global_spec_increase_1_nozzle_in_print',
            'global_spec_increase_1_feeder_in_print',
            'global_spec_reed_increase_per_10cm_in_print',
            'global_spec_bintian_cam_plate_extra_in_print',
            'global_spec_bintian_gear_extra_in_print',
        ];
    }

    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn($this->perRowInPrintColumns());
            $table->boolean('difference_specification_extended_in_print')->default(false)->after('spec_bintian_gear_extra');
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn($this->globalPerRowInPrintColumns());
            $table->boolean('global_difference_specification_extended_in_print')->default(false)->after('global_spec_bintian_gear_extra');
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn('difference_specification_extended_in_print');
            foreach ($this->perRowInPrintColumns() as $col) {
                $table->boolean($col)->default(false);
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn('global_difference_specification_extended_in_print');
            foreach ($this->globalPerRowInPrintColumns() as $col) {
                $table->boolean($col)->default(false);
            }
        });
    }
};
