<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineStatus extends Model
{
    protected $fillable = [
        'contract_id',
        'proforma_invoice_id',
        'contract_date',
        'contract_date_completed',
        'proforma_invoice_date',
        'proforma_invoice_completed',
        'china_payment_date',
        'china_payment_completed',
        'actual_dispatch_date',
        'actual_dispatch_completed',
        'expected_arrival_date',
        'expected_arrival_completed',
        'actual_arrival_date',
        'actual_arrival_completed',
    ];

    protected $casts = [
        'contract_date' => 'date',
        'contract_date_completed' => 'boolean',
        'proforma_invoice_date' => 'date',
        'proforma_invoice_completed' => 'boolean',
        'china_payment_date' => 'date',
        'china_payment_completed' => 'boolean',
        'actual_dispatch_date' => 'date',
        'actual_dispatch_completed' => 'boolean',
        'expected_arrival_date' => 'date',
        'expected_arrival_completed' => 'boolean',
        'actual_arrival_date' => 'date',
        'actual_arrival_completed' => 'boolean',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
