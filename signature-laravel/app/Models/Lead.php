<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Lead extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'business_id',
        'name',
        'phone_number',
        'state_id',
        'city_id',
        'area_id',
        'quantity',
        'planning_month',
        'planning_year',
        'status_id',
        'brand_id',
        'brand_name',
        'machine_quantity',
        'running_since',
        'needs_scheduling',
        'scheduled_date',
        'scheduled_time',
        'created_by',
    ];

    public function business()
    {
        return $this->belongsTo(Business::class);
    }

    public function state()
    {
        return $this->belongsTo(State::class);
    }

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function area()
    {
        return $this->belongsTo(Area::class);
    }

    public function status()
    {
        return $this->belongsTo(Status::class);
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class);
    }

    public function machineCategories()
    {
        return $this->belongsToMany(MachineCategory::class, 'lead_machine_category');
    }

    public function contract()
    {
        return $this->hasOne(Contract::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function tasks()
    {
        return $this->hasMany(Task::class);
    }
}
