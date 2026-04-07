<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProformaInvoiceMachine extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'contract_machine_id',
        'machine_category_id',
        'brand_id',
        'machine_model_id',
        'quantity',
        'amount',
        'amc_price',
        'pi_price_plus_amc',
        'total_pi_price',
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

    protected $casts = [
        'quantity' => 'integer',
        'amount' => 'decimal:2',
        'amc_price' => 'decimal:2',
        'pi_price_plus_amc' => 'decimal:2',
        'total_pi_price' => 'decimal:2',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function contractMachine()
    {
        return $this->belongsTo(ContractMachine::class);
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

    public function serialNumbers()
    {
        return $this->hasMany(SerialNumber::class);
    }
}
