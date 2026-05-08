<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OldDataMachine extends Model
{
    use HasFactory;

    protected $fillable = [
        'old_data_id',
        'machine_category_id',
        'machine_model',
        'serial_number',
        'khata_number',
        'date_of_manufacturing',
    ];

    public function oldData()
    {
        return $this->belongsTo(OldData::class);
    }

    public function machineCategory()
    {
        return $this->belongsTo(MachineCategory::class);
    }
}
