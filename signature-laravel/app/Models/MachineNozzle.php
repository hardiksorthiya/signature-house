<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineNozzle extends Model
{
    use HasFactory;

    protected $fillable = [
        'nozzle',
    ];

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'machine_nozzle_machine_category');
    }
}
