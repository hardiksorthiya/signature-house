<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SerialNumber extends Model
{
    use HasFactory;

    protected $fillable = [
        'proforma_invoice_id',
        'proforma_invoice_machine_id',
        'machine_category_id',
        'serial_number',
        'khata_number',
        'production_date',
        'name_plate_path',
    ];

    protected $casts = [
        'production_date' => 'date',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function proformaInvoiceMachine()
    {
        return $this->belongsTo(ProformaInvoiceMachine::class);
    }

    public function machineCategory()
    {
        return $this->belongsTo(MachineCategory::class);
    }
}
