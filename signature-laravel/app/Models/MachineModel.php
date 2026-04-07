<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MachineModel extends Model
{
    use HasFactory;

    protected $fillable = [
        'model_no',
        'brand_id', // Keep for backward compatibility, but not used for new records
    ];

    // Many-to-many relationship with brands
    public function brands()
    {
        return $this->belongsToMany(Brand::class, 'brand_machine_model');
    }

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'machine_category_machine_model');
    }

    // Backward compatibility: return first brand (or null)
    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }
}
