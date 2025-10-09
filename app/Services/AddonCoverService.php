<?php

namespace App\Services;

use App\Contracts\Repositories\AddonCoverRepositoryInterface;
use App\Contracts\Services\AddonCoverServiceInterface;
use App\Exports\AddonCoverExport;
use App\Models\AddonCover;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Addon Cover Service
 *
 * Handles AddonCover business logic operations.
 * Inherits transaction management from BaseService.
 */
class AddonCoverService extends BaseService implements AddonCoverServiceInterface
{
    public function __construct(
        private AddonCoverRepositoryInterface $addonCoverRepository
    ) {}

    public function getAddonCovers(Request $request): LengthAwarePaginator
    {
        return $this->addonCoverRepository->getPaginated($request);
    }

    public function createAddonCover(array $data): AddonCover
    {
        return $this->createInTransaction(
            fn () => $this->addonCoverRepository->create($data)
        );
    }

    public function updateAddonCover(AddonCover $addonCover, array $data): AddonCover
    {
        return $this->updateInTransaction(
            fn () => $this->addonCoverRepository->update($addonCover, $data)
        );
    }

    public function deleteAddonCover(AddonCover $addonCover): bool
    {
        return $this->deleteInTransaction(
            fn () => $this->addonCoverRepository->delete($addonCover)
        );
    }

    public function updateStatus(int $addonCoverId, int $status): bool
    {
        return $this->executeInTransaction(
            fn () => $this->addonCoverRepository->updateStatus($addonCoverId, $status)
        );
    }

    public function exportAddonCovers(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new AddonCoverExport, 'addon_covers.xlsx');
    }

    public function getActiveAddonCovers(): Collection
    {
        return $this->addonCoverRepository->getActive();
    }

    public function getStoreValidationRules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:addon_covers,name',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'status' => 'boolean',
        ];
    }

    public function getUpdateValidationRules(AddonCover $addonCover): array
    {
        return [
            'name' => 'required|string|max:255|unique:addon_covers,name,'.$addonCover->id,
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'status' => 'boolean',
        ];
    }
}
