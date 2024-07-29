<?php

namespace App\Http\Controllers;

use App\Exports\InsuranceCompanyExport;
use App\Models\InsuranceCompany;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class InsuranceCompanyController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
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
        $insurance_company_obj = InsuranceCompany::select('*');
        if (!empty($request->search)) {
            $insurance_company_obj->where('name', 'LIKE', '%' . trim($request->search) . '%')->orWhere('email', 'LIKE', '%' . trim($request->search) . '%')->orWhere('mobile_number', 'LIKE', '%' . trim($request->search) . '%');
        }

        $insurance_companies = $insurance_company_obj->paginate(10);
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
        // Validations
        $validation_array = [
            'name' => 'required',
        ];

        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            // Store Data
            $insurance_company = InsuranceCompany::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'InsuranceCompany Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
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
        // Validation
        $validate = Validator::make([
            'insurance_company_id' => $insurance_company_id,
            'status' => $status,
        ], [
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'status' => 'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            InsuranceCompany::whereId($insurance_company_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return redirect()->back()->with('success', 'InsuranceCompany Status Updated Successfully!');
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
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
        // Validations
        $validation_array = [
            'name' => 'required',
        ];

        $request->validate($validation_array);

        DB::beginTransaction($validation_array);
        try {
            // Store Data
            $insurance_company_updated = InsuranceCompany::whereId($insurance_company->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'InsuranceCompany Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
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
        DB::beginTransaction();
        try {
            // Delete InsuranceCompany
            InsuranceCompany::whereId($insurance_company->id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'InsuranceCompany Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import InsuranceCompanys
     * @param Null
     * @return View File
     */
    public function importInsuranceCompanys()
    {
        return view('insurance_companies.import');
    }

    public function export()
    {
        return Excel::download(new InsuranceCompanyExport, 'insurance_companies.xlsx');
    }
}
