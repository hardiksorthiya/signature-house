<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SellerBankDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'seller_id',
        'bank_name',
        'account_number',
        'ifsc_code',
        'branch_name',
        'bank_address',
        'account_holder_name',
    ];

    public function seller()
    {
        return $this->belongsTo(Seller::class);
    }
}
