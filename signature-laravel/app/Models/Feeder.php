<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Feeder extends Model
{
    use HasFactory;

    protected $fillable = [
        'feeder',
        'feeder_brand_id',
    ];

    public function feederBrand()
    {
        return $this->belongsTo(FeederBrand::class);
    }

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'feeder_machine_category');
    }
}
