<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ComplainType extends Model
{
    protected $fillable = ['name', 'sort_order'];

    public function complaints()
    {
        return $this->hasMany(Complaint::class);
    }
}
