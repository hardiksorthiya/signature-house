<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContractMachine extends Model
{
    protected $fillable = [
        'contract_id',
        'machine_category_id',
        'brand_id',
        'machine_model_id',
        'machine_size_id',
        'seller_id',
        'quantity',
        'amount',
        'description',
        'feeder_id',
        'machine_hook_id',
        'machine_e_read_id',
        'color_id',
        'machine_nozzle_id',
        'machine_dropin_id',
        'machine_beam_id',
        'machine_cloth_roller_id',
        'machine_software_id',
        'hsn_code_id',
        'wir_id',
        'machine_shaft_id',
        'machine_lever_id',
        'machine_chain_id',
        'machine_heald_wire_id',
        'delivery_term_id',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function machineCategory()
    {
        return $this->belongsTo(MachineCategory::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function machineModel()
    {
        return $this->belongsTo(MachineModel::class);
    }

    public function machineSize()
    {
        return $this->belongsTo(MachineSize::class);
    }

    public function getMachineSizeNameAttribute(): ?string
    {
        return $this->machineSize?->name;
    }

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }

    public function feeder()
    {
        return $this->belongsTo(Feeder::class);
    }

    public function machineHook()
    {
        return $this->belongsTo(MachineHook::class);
    }

    public function machineERead()
    {
        return $this->belongsTo(MachineERead::class);
    }

    public function color()
    {
        return $this->belongsTo(Color::class);
    }

    public function machineNozzle()
    {
        return $this->belongsTo(MachineNozzle::class);
    }

    public function machineDropin()
    {
        return $this->belongsTo(MachineDropin::class);
    }

    public function machineBeam()
    {
        return $this->belongsTo(MachineBeam::class);
    }

    public function machineClothRoller()
    {
        return $this->belongsTo(MachineClothRoller::class);
    }

    public function machineSoftware()
    {
        return $this->belongsTo(MachineSoftware::class);
    }

    public function hsnCode()
    {
        return $this->belongsTo(HsnCode::class);
    }

    public function wir()
    {
        return $this->belongsTo(Wir::class);
    }

    public function machineShaft()
    {
        return $this->belongsTo(MachineShaft::class);
    }

    public function machineLever()
    {
        return $this->belongsTo(MachineLever::class);
    }

    public function machineChain()
    {
        return $this->belongsTo(MachineChain::class);
    }

    public function machineHealdWire()
    {
        return $this->belongsTo(MachineHealdWire::class);
    }

    public function deliveryTerm()
    {
        return $this->belongsTo(DeliveryTerm::class);
    }
}
