<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldData extends Model
{
    use HasFactory;

    protected $table = 'old_data';

    protected $fillable = [
        'firm_name',
        'client_name',
        'phone_number_1',
        'phone_number_2',
        'city',
        'area',
    ];

    public function machines()
    {
        return $this->hasMany(OldDataMachine::class)->orderBy('id');
    }
}
