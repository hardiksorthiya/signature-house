<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PreErectionDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'proforma_invoice_id',
        'technical_specification',
        'details',
        'is_completed',
        'sort_order',
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }
}
