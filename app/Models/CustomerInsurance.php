<?php

namespace App\Models;

use App\Models\Branch;
use App\Models\Broker;
use App\Models\Customer;
use App\Models\FuelType;
use App\Models\PolicyType;
use App\Models\PremiumType;
use App\Models\InsuranceCompany;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Activitylog\LogOptions;
use App\Models\RelationshipManager;
use App\Traits\TableRecordObserver;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class CustomerInsurance extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;
    protected $guarded = [];
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    // Define the relationships here
    public function branch()
    {
        return $this->belongsTo(Branch::class, 'branch_id');
    }

    public function broker()
    {
        return $this->belongsTo(Broker::class, 'broker_id');
    }

    public function relationshipManager()
    {
        return $this->belongsTo(RelationshipManager::class, 'relationship_manager_id');
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class, 'customer_id');
    }

    public function insuranceCompany()
    {
        return $this->belongsTo(InsuranceCompany::class, 'insurance_company_id');
    }

    public function premiumType()
    {
        return $this->belongsTo(PremiumType::class, 'premium_type_id');
    }

    public function policyType()
    {
        return $this->belongsTo(PolicyType::class, 'policy_type_id');
    }

    public function fuelType()
    {
        return $this->belongsTo(FuelType::class, 'fuel_type_id');
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
