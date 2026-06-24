<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'password',
        'department_id',
        'created_by',
        'profile_image',
        'signature',
        'is_active',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get the department that the user belongs to.
     */
    public function department()
    {
        return $this->belongsTo(Department::class);
    }

    /**
     * Get the user who created this user.
     */
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the contracts created by this user.
     */
    public function createdContracts()
    {
        return $this->hasMany(Contract::class, 'created_by');
    }

    /**
     * Get the proforma invoices created by this user.
     */
    public function createdProformaInvoices()
    {
        return $this->hasMany(ProformaInvoice::class, 'created_by');
    }

    /**
     * Get location updates for this user.
     */
    public function userLocations()
    {
        return $this->hasMany(UserLocation::class);
    }

    /**
     * Get the latest recorded location for this user.
     */
    public function latestLocation()
    {
        return $this->hasOne(UserLocation::class)->latestOfMany('recorded_at');
    }

    public function complaintAreas()
    {
        return $this->belongsToMany(Area::class, 'user_complaint_areas', 'user_id', 'area_id');
    }
}
