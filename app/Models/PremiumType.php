<?php

namespace App\Models;

use App\Models\CustomerInsurance;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PremiumType extends Authenticatable
{
    use  HasFactory, Notifiable, HasRoles, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'is_vehicle',
    ];

    public function customerInsurances()
    {
        return $this->hasMany(CustomerInsurance::class, 'premium_type_id');
    }
}
