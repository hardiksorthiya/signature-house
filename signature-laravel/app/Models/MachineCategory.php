<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_machine_category');
    }

    public function machineModels()
    {
        return $this->belongsToMany(MachineModel::class, 'machine_category_machine_model');
    }

    public function machineSizes()
    {
        return $this->belongsToMany(MachineSize::class, 'machine_size_machine_category');
    }

    public function flangeSizes()
    {
        return $this->belongsToMany(FlangeSize::class, 'flange_size_machine_category');
    }

    public function feeders()
    {
        return $this->belongsToMany(Feeder::class, 'feeder_machine_category');
    }

    public function machineHooks()
    {
        return $this->belongsToMany(MachineHook::class, 'machine_hook_machine_category');
    }

    public function colors()
    {
        return $this->belongsToMany(Color::class, 'color_machine_category');
    }

    public function machineNozzles()
    {
        return $this->belongsToMany(MachineNozzle::class, 'machine_nozzle_machine_category');
    }

    public function machineDropins()
    {
        return $this->belongsToMany(MachineDropin::class, 'machine_dropin_machine_category');
    }

    public function machineBeams()
    {
        return $this->belongsToMany(MachineBeam::class, 'machine_beam_machine_category');
    }

    public function machineClothRollers()
    {
        return $this->belongsToMany(MachineClothRoller::class, 'machine_cloth_roller_machine_category');
    }

    public function machineSoftwares()
    {
        return $this->belongsToMany(MachineSoftware::class, 'machine_software_machine_category');
    }

    public function hsnCodes()
    {
        return $this->belongsToMany(HsnCode::class, 'hsn_code_machine_category');
    }

    public function wirs()
    {
        return $this->belongsToMany(Wir::class, 'wir_machine_category');
    }

    public function machineShafts()
    {
        return $this->belongsToMany(MachineShaft::class, 'machine_shaft_machine_category');
    }

    public function machineLevers()
    {
        return $this->belongsToMany(MachineLever::class, 'machine_lever_machine_category');
    }

    public function machineChains()
    {
        return $this->belongsToMany(MachineChain::class, 'machine_chain_machine_category');
    }

    public function machineHealdWires()
    {
        return $this->belongsToMany(MachineHealdWire::class, 'machine_heald_wire_machine_category');
    }

    public function machineEReads()
    {
        return $this->belongsToMany(MachineERead::class, 'machine_e_read_machine_category');
    }

    public function spares()
    {
        return $this->belongsToMany(Spare::class, 'spare_machine_category');
    }
}
