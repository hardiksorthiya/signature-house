<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Brand extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'brand_machine_category');
    }

    // Many-to-many relationship with machine models
    public function machineModels()
    {
        return $this->belongsToMany(MachineModel::class, 'brand_machine_model');
    }
}
