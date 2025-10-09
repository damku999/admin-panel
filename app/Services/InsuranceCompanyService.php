<?php

namespace App\Services;

use App\Contracts\Repositories\InsuranceCompanyRepositoryInterface;
use App\Contracts\Services\InsuranceCompanyServiceInterface;
use App\Exports\InsuranceCompanyExport;
use App\Models\InsuranceCompany;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Insurance Company Service
 *
 * Handles InsuranceCompany business logic.
 * Inherits transaction management from BaseService.
 */
class InsuranceCompanyService extends BaseService implements InsuranceCompanyServiceInterface
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
        return $this->createInTransaction(
            fn () => $this->insuranceCompanyRepository->create($data)
        );
    }

    public function updateInsuranceCompany(InsuranceCompany $insuranceCompany, array $data): InsuranceCompany
    {
        return $this->updateInTransaction(
            fn () => $this->insuranceCompanyRepository->update($insuranceCompany, $data)
        );
    }

    public function deleteInsuranceCompany(InsuranceCompany $insuranceCompany): bool
    {
        return $this->deleteInTransaction(
            fn () => $this->insuranceCompanyRepository->delete($insuranceCompany)
        );
    }

    public function updateStatus(int $insuranceCompanyId, int $status): bool
    {
        return $this->updateInTransaction(
            fn () => $this->insuranceCompanyRepository->updateStatus($insuranceCompanyId, $status)
        );
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
