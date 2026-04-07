<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('spec_3_niupai_10_shaft_410_cam_heald_frames')->nullable();
            $table->string('spec_3_niupai_12_shaft_411_cam_heald_frames')->nullable();
            $table->string('spec_3_niupai_16_electronic_dobby_5400d_invertor')->nullable();
            $table->string('spec_3_sanhe_s650_controller_accessories')->nullable();
            $table->string('spec_3_increase_1_colour')->nullable();
            $table->string('spec_3_increase_1_feeder')->nullable();
            $table->string('spec_3_reed_increase_per_20cm')->nullable();
            $table->string('spec_3_single_pump_to_double_pump')->nullable();
            $table->boolean('difference_specification_3_in_print')->default(false);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('global_spec_3_niupai_10_shaft_410_cam_heald_frames')->nullable();
            $table->string('global_spec_3_niupai_12_shaft_411_cam_heald_frames')->nullable();
            $table->string('global_spec_3_niupai_16_electronic_dobby_5400d_invertor')->nullable();
            $table->string('global_spec_3_sanhe_s650_controller_accessories')->nullable();
            $table->string('global_spec_3_increase_1_colour')->nullable();
            $table->string('global_spec_3_increase_1_feeder')->nullable();
            $table->string('global_spec_3_reed_increase_per_20cm')->nullable();
            $table->string('global_spec_3_single_pump_to_double_pump')->nullable();
            $table->boolean('global_difference_specification_3_in_print')->default(false);
        });
    }

    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn([
                'spec_3_niupai_10_shaft_410_cam_heald_frames',
                'spec_3_niupai_12_shaft_411_cam_heald_frames',
                'spec_3_niupai_16_electronic_dobby_5400d_invertor',
                'spec_3_sanhe_s650_controller_accessories',
                'spec_3_increase_1_colour',
                'spec_3_increase_1_feeder',
                'spec_3_reed_increase_per_20cm',
                'spec_3_single_pump_to_double_pump',
                'difference_specification_3_in_print',
            ]);
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn([
                'global_spec_3_niupai_10_shaft_410_cam_heald_frames',
                'global_spec_3_niupai_12_shaft_411_cam_heald_frames',
                'global_spec_3_niupai_16_electronic_dobby_5400d_invertor',
                'global_spec_3_sanhe_s650_controller_accessories',
                'global_spec_3_increase_1_colour',
                'global_spec_3_increase_1_feeder',
                'global_spec_3_reed_increase_per_20cm',
                'global_spec_3_single_pump_to_double_pump',
                'global_difference_specification_3_in_print',
            ]);
        });
    }
};
