<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PIDocument extends Model
{
    protected $table = 'pi_documents';

    protected $fillable = [
        'proforma_invoice_id',
        'pi_delivery_detail_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
        'row_number',
    ];

    protected $casts = [
        'file_size' => 'integer',
        'row_number' => 'integer',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function deliveryDetail()
    {
        return $this->belongsTo(PIDeliveryDetail::class, 'pi_delivery_detail_id');
    }
}
