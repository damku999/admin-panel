<?php

namespace App\Models;

use App\Models\CustomerInsurance;
use Spatie\Activitylog\LogOptions;
use App\Traits\TableRecordObserver;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Notifications\Notifiable;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;

class PolicyType extends Authenticatable
{
    use  HasFactory, Notifiable, HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;
    protected static $logAttributes = ['*'];
    protected static $logOnlyDirty = true;
    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
    ];

    public function customerInsurances()
    {
        return $this->hasMany(CustomerInsurance::class, 'policy_type_id');
    }
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }
}
