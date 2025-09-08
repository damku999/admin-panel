<?php

namespace App\Services;

use App\Contracts\Services\InsuranceCompanyServiceInterface;
use App\Contracts\Repositories\InsuranceCompanyRepositoryInterface;
use App\Exports\InsuranceCompanyExport;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;

class InsuranceCompanyService implements InsuranceCompanyServiceInterface
{
    public function __construct(
        private InsuranceCompanyRepositoryInterface $insuranceCompanyRepository
    ) {}
    
    public function getInsuranceCompanies(Request $request): LengthAwarePaginator
    {
        return $this->insuranceCompanyRepository->getPaginated($request);
    }
    
    public function createInsuranceCompany(array $data): InsuranceCompany
    {
        DB::beginTransaction();
        try {
            $insuranceCompany = $this->insuranceCompanyRepository->create($data);
            DB::commit();
            return $insuranceCompany;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateInsuranceCompany(InsuranceCompany $insuranceCompany, array $data): InsuranceCompany
    {
        DB::beginTransaction();
        try {
            $updatedInsuranceCompany = $this->insuranceCompanyRepository->update($insuranceCompany, $data);
            DB::commit();
            return $updatedInsuranceCompany;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function deleteInsuranceCompany(InsuranceCompany $insuranceCompany): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->insuranceCompanyRepository->delete($insuranceCompany);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function updateStatus(int $insuranceCompanyId, int $status): bool
    {
        DB::beginTransaction();
        try {
            $result = $this->insuranceCompanyRepository->updateStatus($insuranceCompanyId, $status);
            DB::commit();
            return $result;
        } catch (\Throwable $th) {
            DB::rollBack();
            throw $th;
        }
    }
    
    public function exportInsuranceCompanies(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        return Excel::download(new InsuranceCompanyExport, 'insurance_companies.xlsx');
    }
    
    public function getActiveInsuranceCompanies(): Collection
    {
        return $this->insuranceCompanyRepository->getActive();
    }
}