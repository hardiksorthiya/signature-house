<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MachineErectionDetail extends Model
{
    protected $fillable = [
        'proforma_invoice_id',
        'machine_category_id',
        'point_to_follow',
        'machine_number',
        'date',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
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
