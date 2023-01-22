<?php

namespace App\Http\Controllers;

use App\Models\CustomerInsurance;
use Illuminate\Http\Request;
use App\Exports\CustomerInsurancesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class CustomerInsuranceController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-insurance-list|customer-insurance-create|customer-insurance-edit|customer-insurance-delete', ['only' => ['index']]);
        $this->middleware('permission:customer-insurance-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:customer-insurance-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-insurance-delete', ['only' => ['delete']]);
    }


    /**
     * List CustomerInsurance 
     * @param Nill
     * @return Array $customer_insurance
     * @author Shani Singh
     */
    public function index(Request $request)
    {
        $customer_insurance_obj = CustomerInsurance::select('*');
        // dd($request->toArray());
        if (!empty($request->search)) {
            $customer_insurance_obj->where('name', 'LIKE', '%' . trim($request->search) . '%')->orWhere('email', 'LIKE', '%' . trim($request->search) . '%')->orWhere('mobile_number', 'LIKE', '%' . trim($request->search) . '%');
        }

        $customer_insurances = $customer_insurance_obj->paginate(10);
        return view('customer_insurances.index', ['customer_insurances' => $customer_insurances]);
    }

    /**
     * Create CustomerInsurance 
     * @param Nill
     * @return Array $customer_insurance
     * @author Shani Singh
     */
    public function create()
    {
        return view('customer_insurances.add');
    }

    /**
     * Store CustomerInsurance
     * @param Request $request
     * @return View CustomerInsurances
     * @author Shani Singh
     */
    public function store(Request $request)
    {
        // Validations
        $validation_array = [
            'name' => 'required',
            'email' => 'required|unique:customer_insurances,email',
            'mobile_number' => 'required|numeric|digits:10',
            'status' => 'required|numeric|in:0,1',
        ];

        if (!empty($request->date_of_birth)) {
            $validation_array['date_of_birth'] = 'date_format:Y-m-d';
        }

        if (!empty($request->wedding_anniversary_date)) {
            $validation_array['wedding_anniversary_date'] = 'date_format:Y-m-d';
        }
        if (!empty($request->engagement_anniversary_date)) {
            $validation_array['engagement_anniversary_date'] = 'date_format:Y-m-d';
        }
        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            // Store Data
            $customer_insurance = CustomerInsurance::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => $request->status,
                'wedding_anniversary_date' => $request->wedding_anniversary_date,
                'engagement_anniversary_date' => $request->engagement_anniversary_date,
                'date_of_birth' => $request->date_of_birth,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->route('customer_insurances.index')->with('success', 'CustomerInsurance Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Update Status Of CustomerInsurance
     * @param Integer $status
     * @return List Page With Success
     * @author Shani Singh
     */
    public function updateStatus($customer_insurance_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'customer_insurance_id'   => $customer_insurance_id,
            'status' => $status
        ], [
            'customer_insurance_id'   =>  'required|exists:customer_insurances,id',
            'status' =>  'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->route('customer_insurances.index')->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            CustomerInsurance::whereId($customer_insurance_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return redirect()->route('customer_insurances.index')->with('success', 'CustomerInsurance Status Updated Successfully!');
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Edit CustomerInsurance
     * @param Integer $customer_insurance
     * @return Collection $customer_insurance
     * @author Shani Singh
     */
    public function edit(CustomerInsurance $customer_insurance)
    {
        return view('customer_insurances.edit')->with([
            'customer_insurance'  => $customer_insurance
        ]);
    }

    /**
     * Update CustomerInsurance
     * @param Request $request, CustomerInsurance $customer_insurance
     * @return View CustomerInsurances
     * @author Shani Singh
     */
    public function update(Request $request, CustomerInsurance $customer_insurance)
    {
        // Validations
        $validation_array = [
            'name' => 'required',
            'email' => 'required|unique:customer_insurances,email,' . $customer_insurance->id . ',id',
            'mobile_number' => 'required|numeric|digits:10',
            'status' => 'required|numeric|in:0,1',
        ];

        if (!empty($request->date_of_birth)) {
            $validation_array['date_of_birth'] = 'date_format:Y-m-d';
        }

        if (!empty($request->wedding_anniversary_date)) {
            $validation_array['wedding_anniversary_date'] = 'date_format:Y-m-d';
        }
        if (!empty($request->engagement_anniversary_date)) {
            $validation_array['engagement_anniversary_date'] = 'date_format:Y-m-d';
        }

        $request->validate($validation_array);

        DB::beginTransaction($validation_array);
        try {
            // Store Data
            $customer_insurance_updated = CustomerInsurance::whereId($customer_insurance->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => $request->status,
                'wedding_anniversary_date' => $request->wedding_anniversary_date,
                'engagement_anniversary_date' => $request->engagement_anniversary_date,
                'date_of_birth' => $request->date_of_birth,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->route('customer_insurances.index')->with('success', 'CustomerInsurance Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Delete CustomerInsurance
     * @param CustomerInsurance $customer_insurance
     * @return Index CustomerInsurances
     * @author Shani Singh
     */
    public function delete(CustomerInsurance $customer_insurance)
    {
        DB::beginTransaction();
        try {
            // Delete CustomerInsurance
            CustomerInsurance::whereId($customer_insurance->id)->delete();

            DB::commit();
            return redirect()->route('customer_insurances.index')->with('success', 'CustomerInsurance Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import CustomerInsurances 
     * @param Null
     * @return View File
     */
    public function importCustomerInsurances()
    {
        return view('customer_insurances.import');
    }


    public function export()
    {
        return Excel::download(new CustomerInsurancesExport, 'customer_insurances.xlsx');
    }
}
