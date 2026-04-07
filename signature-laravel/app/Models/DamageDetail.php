<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'proforma_invoice_id',
        'title',
        'detail',
    ];

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function images()
    {
        return $this->hasMany(DamageImage::class)->orderBy('created_at', 'desc');
    }
}
