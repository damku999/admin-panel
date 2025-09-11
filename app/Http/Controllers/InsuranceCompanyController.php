<?php

namespace App\Http\Controllers;

use App\Contracts\Services\InsuranceCompanyServiceInterface;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;

class InsuranceCompanyController extends Controller
{
    public function __construct(
        private InsuranceCompanyServiceInterface $insuranceCompanyService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:insurance_company-list|insurance_company-create|insurance_company-edit|insurance_company-delete', ['only' => ['index']]);
        $this->middleware('permission:insurance_company-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:insurance_company-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:insurance_company-delete', ['only' => ['delete']]);
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

            return redirect()->route('insurance_companies.index')->with('success', 'InsuranceCompany Created Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
            return redirect()->back()->with('success', 'InsuranceCompany Status Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
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

            return redirect()->back()->with('success', 'InsuranceCompany Updated Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
            return redirect()->back()->with('success', 'InsuranceCompany Deleted Successfully!.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
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
