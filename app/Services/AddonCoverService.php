<?php

namespace App\Services;

use App\Contracts\Services\AddonCoverServiceInterface;
use App\Contracts\Repositories\AddonCoverRepositoryInterface;
use App\Exports\AddonCoverExport;
use App\Models\AddonCover;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class AddonCoverService implements AddonCoverServiceInterface
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
        DB::beginTransaction();
        try {
            $addonCover = $this->addonCoverRepository->create($data);
            DB::commit();
            return $addonCover;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateAddonCover(AddonCover $addonCover, array $data): AddonCover
    {
        DB::beginTransaction();
        try {
            $updatedAddonCover = $this->addonCoverRepository->update($addonCover, $data);
            DB::commit();
            return $updatedAddonCover;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function deleteAddonCover(AddonCover $addonCover): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->addonCoverRepository->delete($addonCover);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateStatus(int $addonCoverId, int $status): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->addonCoverRepository->updateStatus($addonCoverId, $status);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
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
            'name' => 'required|string|max:255|unique:addon_covers,name,' . $addonCover->id,
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0',
            'status' => 'boolean',
        ];
    }
}