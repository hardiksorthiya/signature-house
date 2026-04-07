<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class IAFittingDetail extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'machine_category_id',
        'machine_number',
        'detail_name',
        'value',
        'value_type',
        'sort_order',
    ];

    protected $casts = [
        'machine_number' => 'integer',
        'sort_order' => 'integer',
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
