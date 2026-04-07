<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineDropin extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'machine_dropin_machine_category');
    }
}
