<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Traits\TableRecordObserver;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;

class Report extends Authenticatable
{
    use  HasRoles, SoftDeletes, TableRecordObserver, LogsActivity;

    protected $guarded = [];

    protected static $logName = 'User Reports';

    protected static $logAttributes = ['*'];

    protected static $logOnlyDirty = true;

    protected $casts = ['selected_columns' => 'array'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
