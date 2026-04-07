<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PiSpareList extends Model
{
    protected $table = 'pi_spare_lists';

    protected $fillable = [
        'proforma_invoice_id',
        'document_name',
        'quantity',
        'is_custom',
        'is_fulfilled',
        'sort_order',
    ];

    protected $casts = [
        'is_custom' => 'boolean',
        'is_fulfilled' => 'boolean',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
