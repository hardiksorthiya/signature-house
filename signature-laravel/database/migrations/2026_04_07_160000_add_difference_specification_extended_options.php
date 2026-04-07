<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function extendedValueColumns(): array
    {
        return [
            'spec_bintian_8_shaft_cam_heald',
            'spec_bintian_10_shaft_cam_heald',
            'spec_changshu_16_shaft_2861_dobby',
            'spec_staubli_16_shaft_dobby',
            'spec_increase_1_nozzle',
            'spec_increase_1_feeder',
            'spec_reed_increase_per_10cm',
            'spec_bintian_cam_plate_extra',
            'spec_bintian_gear_extra',
        ];
    }

    private function extendedInPrintColumns(): array
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

    private function globalExtendedValueColumns(): array
    {
        return [
            'global_spec_bintian_8_shaft_cam_heald',
            'global_spec_bintian_10_shaft_cam_heald',
            'global_spec_changshu_16_shaft_2861_dobby',
            'global_spec_staubli_16_shaft_dobby',
            'global_spec_increase_1_nozzle',
            'global_spec_increase_1_feeder',
            'global_spec_reed_increase_per_10cm',
            'global_spec_bintian_cam_plate_extra',
            'global_spec_bintian_gear_extra',
        ];
    }

    private function globalExtendedInPrintColumns(): array
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
            foreach ($this->extendedValueColumns() as $col) {
                $table->string($col)->nullable();
            }
            foreach ($this->extendedInPrintColumns() as $col) {
                $table->boolean($col)->default(false);
            }
        });

        Schema::table('settings', function (Blueprint $table) {
            foreach ($this->globalExtendedValueColumns() as $col) {
                $table->string($col)->nullable();
            }
            foreach ($this->globalExtendedInPrintColumns() as $col) {
                $table->boolean($col)->default(false);
            }
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn(array_merge($this->extendedValueColumns(), $this->extendedInPrintColumns()));
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn(array_merge($this->globalExtendedValueColumns(), $this->globalExtendedInPrintColumns()));
        });
    }
};
