<?php

namespace App\Observers;

use App\Services\CacheService;
use Illuminate\Database\Eloquent\Model;

/**
 * Observer to handle automatic cache invalidation when models are updated
 */
class CacheInvalidationObserver
{
    public function __construct(
        private CacheService $cacheService
    ) {}

    /**
     * Handle the model "created" event.
     */
    public function created(Model $model): void
    {
        $this->invalidateModelCache($model);
    }

    /**
     * Handle the model "updated" event.
     */
    public function updated(Model $model): void
    {
        $this->invalidateModelCache($model);
    }

    /**
     * Handle the model "deleted" event.
     */
    public function deleted(Model $model): void
    {
        $this->invalidateModelCache($model);
    }

    /**
     * Handle the model "restored" event.
     */
    public function restored(Model $model): void
    {
        $this->invalidateModelCache($model);
    }

    /**
     * Invalidate relevant cache entries for the model
     */
    private function invalidateModelCache(Model $model): void
    {
        $modelClass = get_class($model);
        $this->cacheService->invalidateModelCache($modelClass, $model->id ?? null);
    }
}