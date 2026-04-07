<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DamageImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'damage_detail_id',
        'file_name',
        'file_path',
        'file_type',
        'file_size',
    ];

    protected $casts = [
        'file_size' => 'integer',
    ];

    public function damageDetail()
    {
        return $this->belongsTo(DamageDetail::class);
    }
}
