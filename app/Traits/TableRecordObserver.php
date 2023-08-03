<?php

namespace App\Traits;

trait TableRecordObserver
{
    protected static function boot()
    {
        parent::boot();
        static::creating(function ($model) {
            $model->created_by = auth()->user()->id ?? 0;
            $model->updated_by = auth()->user()->id ?? 0;
        });

        static::updating(function ($model) {
            $model->updated_by = auth()->user()->id ?? 0;
        });

        static::deleting(function ($model) {
            $model->deleted_by = auth()->user()->id ?? 0;
            $model->save();
        });

        //  retrieved, creating, created, updating, updated, saving, saved, deleting, deleted, restoring, restored, and replicating.
    }
}
