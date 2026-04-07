<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PILayout extends Model
{
    protected $table = 'pi_layouts';
    
    protected $fillable = [
        'name',
        'description',
        'template_html',
        'is_active',
        'is_default',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'is_default' => 'boolean',
    ];

    public function sellers()
    {
        return $this->hasMany(Seller::class, 'pi_layout_id');
    }
}
