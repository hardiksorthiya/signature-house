<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Area extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'city_id',
    ];

    public function city()
    {
        return $this->belongsTo(City::class);
    }

    public function leads()
    {
        return $this->hasMany(Lead::class);
    }

    public function complaintUsers()
    {
        return $this->belongsToMany(User::class, 'user_complaint_areas', 'area_id', 'user_id');
    }
}
