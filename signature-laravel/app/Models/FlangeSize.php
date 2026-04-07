<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FlangeSize extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
    ];

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'flange_size_machine_category');
    }
}
