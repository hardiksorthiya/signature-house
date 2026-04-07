<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'contract_id',
        'proforma_invoice_id',
        'amount',
        'payment_date',
        'payment_method',
        'payment_by',
        'payee_country_id',
        'payment_to_seller_id',
        'bank_detail_id',
        'transaction_id',
        'swift_copy',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'payment_date' => 'date',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function proformaInvoice()
    {
        return $this->belongsTo(ProformaInvoice::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function payeeCountry()
    {
        return $this->belongsTo(Country::class, 'payee_country_id');
    }

    public function paymentToSeller()
    {
        return $this->belongsTo(Seller::class, 'payment_to_seller_id');
    }

    public function bankDetail()
    {
        return $this->belongsTo(SellerBankDetail::class, 'bank_detail_id');
    }
}
