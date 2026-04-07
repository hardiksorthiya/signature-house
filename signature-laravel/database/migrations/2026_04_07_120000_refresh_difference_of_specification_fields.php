<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private function specColumns(): array
    {
        return [
            'spec_color_8_to_12_selectors',
            'spec_extra_feeder_per_pc',
            'spec_extra_warp_beam_per_pc',
            'spec_reed_reduction_per_20cm',
            'spec_reed_increase_per_20cm',
            'spec_increase_380_to_480cm',
            'spec_electronic_weft_cutter',
            'spec_hooks_5376_to_6144',
            'spec_hooks_5376_to_10240',
            'spec_hooks_5376_to_2688',
            'spec_changshu_to_sns_cam',
            'spec_changshu_to_sns_chain_24',
            'spec_changshu_to_sns_chain_16',
            'spec_changshu_to_jkd_or_changfang',
            'spec_changshu_to_wumu',
        ];
    }

    private function oldContractColumns(): array
    {
        return [
            'cam_jacquard_chain_jacquard',
            'hooks_5376_to_6144_jacquard',
            'warp_beam',
            'reed_space_380_to_420_cm',
            'color_selector_8_to_12',
            'hooks_5376_to_2688_jacquard',
            'extra_feeder',
        ];
    }

    private function oldGlobalColumns(): array
    {
        return [
            'global_cam_jacquard_chain_jacquard',
            'global_hooks_5376_to_6144_jacquard',
            'global_warp_beam',
            'global_reed_space_380_to_420_cm',
            'global_color_selector_8_to_12',
            'global_hooks_5376_to_2688_jacquard',
            'global_extra_feeder',
        ];
    }

    private function globalSpecColumns(): array
    {
        return [
            'global_spec_color_8_to_12_selectors',
            'global_spec_extra_feeder_per_pc',
            'global_spec_extra_warp_beam_per_pc',
            'global_spec_reed_reduction_per_20cm',
            'global_spec_reed_increase_per_20cm',
            'global_spec_increase_380_to_480cm',
            'global_spec_electronic_weft_cutter',
            'global_spec_hooks_5376_to_6144',
            'global_spec_hooks_5376_to_10240',
            'global_spec_hooks_5376_to_2688',
            'global_spec_changshu_to_sns_cam',
            'global_spec_changshu_to_sns_chain_24',
            'global_spec_changshu_to_sns_chain_16',
            'global_spec_changshu_to_jkd_or_changfang',
            'global_spec_changshu_to_wumu',
        ];
    }

    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // --- contracts: add new columns, copy, drop old ---
        Schema::table('contracts', function (Blueprint $table) {
            foreach ($this->specColumns() as $col) {
                $table->string($col)->nullable();
            }
        });

        if (Schema::hasColumn('contracts', 'color_selector_8_to_12')) {
            DB::table('contracts')->orderBy('id')->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('contracts')->where('id', $row->id)->update([
                        'spec_color_8_to_12_selectors' => $row->color_selector_8_to_12,
                        'spec_extra_feeder_per_pc' => $row->extra_feeder,
                        'spec_extra_warp_beam_per_pc' => $row->warp_beam,
                        'spec_reed_reduction_per_20cm' => null,
                        'spec_reed_increase_per_20cm' => null,
                        'spec_increase_380_to_480cm' => $row->reed_space_380_to_420_cm,
                        'spec_electronic_weft_cutter' => null,
                        'spec_hooks_5376_to_6144' => $row->hooks_5376_to_6144_jacquard,
                        'spec_hooks_5376_to_10240' => null,
                        'spec_hooks_5376_to_2688' => $row->hooks_5376_to_2688_jacquard,
                        'spec_changshu_to_sns_cam' => null,
                        'spec_changshu_to_sns_chain_24' => null,
                        'spec_changshu_to_sns_chain_16' => null,
                        'spec_changshu_to_jkd_or_changfang' => null,
                        'spec_changshu_to_wumu' => null,
                    ]);
                }
            });
        }

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn($this->oldContractColumns());
        });

        // --- settings: add new columns, copy, drop old ---
        Schema::table('settings', function (Blueprint $table) {
            foreach ($this->globalSpecColumns() as $col) {
                $table->string($col)->nullable();
            }
        });

        if (Schema::hasColumn('settings', 'global_color_selector_8_to_12')) {
            $settings = DB::table('settings')->where('id', 1)->first();
            if ($settings) {
                DB::table('settings')->where('id', 1)->update([
                    'global_spec_color_8_to_12_selectors' => $settings->global_color_selector_8_to_12,
                    'global_spec_extra_feeder_per_pc' => $settings->global_extra_feeder,
                    'global_spec_extra_warp_beam_per_pc' => $settings->global_warp_beam,
                    'global_spec_reed_reduction_per_20cm' => null,
                    'global_spec_reed_increase_per_20cm' => null,
                    'global_spec_increase_380_to_480cm' => $settings->global_reed_space_380_to_420_cm,
                    'global_spec_electronic_weft_cutter' => null,
                    'global_spec_hooks_5376_to_6144' => $settings->global_hooks_5376_to_6144_jacquard,
                    'global_spec_hooks_5376_to_10240' => null,
                    'global_spec_hooks_5376_to_2688' => $settings->global_hooks_5376_to_2688_jacquard,
                    'global_spec_changshu_to_sns_cam' => null,
                    'global_spec_changshu_to_sns_chain_24' => null,
                    'global_spec_changshu_to_sns_chain_16' => null,
                    'global_spec_changshu_to_jkd_or_changfang' => null,
                    'global_spec_changshu_to_wumu' => null,
                ]);
            }
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn($this->oldGlobalColumns());
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('contracts', function (Blueprint $table) {
            $table->string('cam_jacquard_chain_jacquard')->nullable();
            $table->string('hooks_5376_to_6144_jacquard')->nullable();
            $table->string('warp_beam')->nullable();
            $table->string('reed_space_380_to_420_cm')->nullable();
            $table->string('color_selector_8_to_12')->nullable();
            $table->string('hooks_5376_to_2688_jacquard')->nullable();
            $table->string('extra_feeder')->nullable();
        });

        if (Schema::hasColumn('contracts', 'spec_color_8_to_12_selectors')) {
            DB::table('contracts')->orderBy('id')->chunkById(100, function ($rows) {
                foreach ($rows as $row) {
                    DB::table('contracts')->where('id', $row->id)->update([
                        'color_selector_8_to_12' => $row->spec_color_8_to_12_selectors,
                        'extra_feeder' => $row->spec_extra_feeder_per_pc,
                        'warp_beam' => $row->spec_extra_warp_beam_per_pc,
                        'reed_space_380_to_420_cm' => $row->spec_increase_380_to_480cm,
                        'hooks_5376_to_6144_jacquard' => $row->spec_hooks_5376_to_6144,
                        'hooks_5376_to_2688_jacquard' => $row->spec_hooks_5376_to_2688,
                        'cam_jacquard_chain_jacquard' => null,
                    ]);
                }
            });
        }

        Schema::table('contracts', function (Blueprint $table) {
            $table->dropColumn($this->specColumns());
        });

        Schema::table('settings', function (Blueprint $table) {
            $table->string('global_cam_jacquard_chain_jacquard')->nullable();
            $table->string('global_hooks_5376_to_6144_jacquard')->nullable();
            $table->string('global_warp_beam')->nullable();
            $table->string('global_reed_space_380_to_420_cm')->nullable();
            $table->string('global_color_selector_8_to_12')->nullable();
            $table->string('global_hooks_5376_to_2688_jacquard')->nullable();
            $table->string('global_extra_feeder')->nullable();
        });

        if (Schema::hasColumn('settings', 'global_spec_color_8_to_12_selectors')) {
            $settings = DB::table('settings')->where('id', 1)->first();
            if ($settings) {
                DB::table('settings')->where('id', 1)->update([
                    'global_color_selector_8_to_12' => $settings->global_spec_color_8_to_12_selectors,
                    'global_extra_feeder' => $settings->global_spec_extra_feeder_per_pc,
                    'global_warp_beam' => $settings->global_spec_extra_warp_beam_per_pc,
                    'global_reed_space_380_to_420_cm' => $settings->global_spec_increase_380_to_480cm,
                    'global_hooks_5376_to_6144_jacquard' => $settings->global_spec_hooks_5376_to_6144,
                    'global_hooks_5376_to_2688_jacquard' => $settings->global_spec_hooks_5376_to_2688,
                    'global_cam_jacquard_chain_jacquard' => null,
                ]);
            }
        }

        Schema::table('settings', function (Blueprint $table) {
            $table->dropColumn($this->globalSpecColumns());
        });
    }
};
