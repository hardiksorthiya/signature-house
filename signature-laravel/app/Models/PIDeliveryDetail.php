<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PIDeliveryDetail extends Model
{
    protected $table = 'pi_delivery_details';

    protected $fillable = [
        'proforma_invoice_id',
        'document_name',
        'date',
        'document_number',
        'no_of_copies',
        'is_received',
        'sort_order',
    ];

    protected $casts = [
        'date' => 'date',
        'no_of_copies' => 'integer',
        'is_received' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
