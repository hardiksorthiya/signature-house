<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Complaint extends Model
{
    protected $fillable = [
        'contract_id',
        'complain_type_id',
        'machine_category_id',
        'machine_khata_number',
        'other_detail',
        'status',
        'assigned_at',
        'completed_at',
        'remarks',
        'feedback_status',
        'feedback_remarks',
        'feedback_at',
        'feedback_by',
        'created_by',
    ];

    protected $casts = [
        'assigned_at' => 'datetime',
        'completed_at' => 'datetime',
        'feedback_at' => 'datetime',
    ];

    public function contract()
    {
        return $this->belongsTo(Contract::class);
    }

    public function complainType()
    {
        return $this->belongsTo(ComplainType::class);
    }

    public function machineCategory()
    {
        return $this->belongsTo(MachineCategory::class);
    }

    public function assignees()
    {
        return $this->belongsToMany(User::class, 'complaint_assignees', 'complaint_id', 'user_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function feedbackBy()
    {
        return $this->belongsTo(User::class, 'feedback_by');
    }

    public function spares()
    {
        return $this->belongsToMany(Spare::class, 'complaint_spare')
            ->withPivot('quantity', 'used_at')
            ->withTimestamps();
    }
}
