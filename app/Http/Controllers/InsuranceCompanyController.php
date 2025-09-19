<?php

namespace App\Http\Controllers;

use App\Contracts\Services\InsuranceCompanyServiceInterface;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;

/**
 * Insurance Company Controller
 *
 * Handles InsuranceCompany CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class InsuranceCompanyController extends AbstractBaseCrudController
{
    public function __construct(
        private InsuranceCompanyServiceInterface $insuranceCompanyService
    ) {
        $this->setupPermissionMiddleware('insurance_company');
    }

    /**
     * List InsuranceCompany
     * @param Nill
     * @return Array $insurance_company
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $insurance_companies = $this->insuranceCompanyService->getInsuranceCompanies($request);
        return view('insurance_companies.index', ['insurance_companies' => $insurance_companies, 'request' => $request->all()]);
    }

    /**
     * Create InsuranceCompany
     * @param Nill
     * @return Array $insurance_company
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('insurance_companies.add');
    }

    /**
     * Store InsuranceCompany
     * @param Request $request
     * @return View InsuranceCompanys
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required',
        ]);

        try {
            $this->insuranceCompanyService->createInsuranceCompany([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            return $this->redirectWithSuccess('insurance_companies.index',
                $this->getSuccessMessage('Insurance Company', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Insurance Company', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of InsuranceCompany
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($insurance_company_id, $status)
    {
        try {
            $this->insuranceCompanyService->updateStatus($insurance_company_id, $status);
            return $this->redirectWithSuccess('insurance_companies.index',
                $this->getSuccessMessage('Insurance Company status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Insurance Company status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit InsuranceCompany
     * @param Integer $insurance_company
     * @return Collection $insurance_company
     * @author Darshan Baraiya
     */
    public function edit(InsuranceCompany $insurance_company)
    {
        return view('insurance_companies.edit')->with([
            'insurance_company' => $insurance_company,
        ]);
    }

    /**
     * Update InsuranceCompany
     * @param Request $request, InsuranceCompany $insurance_company
     * @return View InsuranceCompanys
     * @author Darshan Baraiya
     */
    public function update(Request $request, InsuranceCompany $insurance_company)
    {
        $request->validate([
            'name' => 'required',
        ]);

        try {
            $this->insuranceCompanyService->updateInsuranceCompany($insurance_company, [
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            return $this->redirectWithSuccess('insurance_companies.index',
                $this->getSuccessMessage('Insurance Company', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Insurance Company', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete InsuranceCompany
     * @param InsuranceCompany $insurance_company
     * @return Index InsuranceCompanys
     * @author Darshan Baraiya
     */
    public function delete(InsuranceCompany $insurance_company)
    {
        try {
            $this->insuranceCompanyService->deleteInsuranceCompany($insurance_company);
            return $this->redirectWithSuccess('insurance_companies.index',
                $this->getSuccessMessage('Insurance Company', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Insurance Company', 'delete') . ': ' . $th->getMessage());
        }
    }

    public function importInsuranceCompanys()
    {
        return view('insurance_companies.import');
    }

    public function export()
    {
        return $this->insuranceCompanyService->exportInsuranceCompanies();
    }
}
