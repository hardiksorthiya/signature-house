<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'proforma_invoice_id',
        'purchase_order_number',
        'buyer_name',
        'address',
        'no_of_bill',
        'no_of_container',
        'size_of_container',
        'port_of_destination_id',
        'created_by',
        'notes',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function portOfDestination()
    {
        return $this->belongsTo(PortOfDestination::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function attachments()
    {
        return $this->hasMany(PurchaseOrderAttachment::class);
    }
}
