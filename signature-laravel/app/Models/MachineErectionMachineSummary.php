<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineErectionMachineSummary extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'machine_category_id',
        'machine_number',
        'machine_erection_date',
        'installation_completed_date',
        'certificate_received',
    ];

    protected $casts = [
        'machine_number' => 'integer',
        'machine_erection_date' => 'date',
        'installation_completed_date' => 'date',
        'certificate_received' => 'boolean',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function machineCategory()
    {
        return $this->belongsTo(MachineCategory::class);
    }
}
