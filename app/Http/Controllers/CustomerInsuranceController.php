<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Broker;
use App\Models\Customer;
use Illuminate\Http\Request;
use App\Models\InsuranceCompany;
use App\Models\CustomerInsurance;
use Illuminate\Support\Facades\DB;
use App\Models\RelationshipManager;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CustomerInsurancesExport;
use App\Models\FuelType;
use App\Models\PolicyType;
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
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $customer_insurance_obj = CustomerInsurance::select(['customer_insurances.*', 'customers.name as customer_name', 'branches.name as branch_name', 'brokers.name as broker_name', 'relationship_managers.name as relationship_manager_name'])
            ->join('customers', 'customers.id', 'customer_insurances.customer_id')
            ->leftJoin('branches', 'branches.id', 'customer_insurances.branch_id')
            ->leftJoin('brokers', 'brokers.id', 'customer_insurances.broker_id')
            ->leftJoin('relationship_managers', 'relationship_managers.id', 'customer_insurances.relationship_manager_id');
        if (!empty($request->search)) {
            $customer_insurance_obj->where('registration_no', 'LIKE', '%' . trim($request->search) . '%')
                ->orWhere('policy_no', 'LIKE', '%' . trim($request->search) . '%')
                ->orWhere('customers.name', 'LIKE', '%' . trim($request->search) . '%')
                ->orWhere('customers.mobile_number', 'LIKE', '%' . trim($request->search) . '%');
        }
        if (!empty($request->customer_id)) {
            $customer_insurance_obj->where('customer_insurances.customer_id', $request->customer_id);
        }

        $customer_insurances = $customer_insurance_obj->paginate(10);
        $customers = Customer::select('id', 'name')->get();

        return view('customer_insurances.index', ['customer_insurances' => $customer_insurances, 'customers' => $customers]);
    }

    /**
     * Create CustomerInsurance 
     * @param Nill
     * @return Array $customer_insurance
     * @author Darshan Baraiya
     */
    public function create()
    {
        $response  = [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
        ];
        return view('customer_insurances.add', $response);
    }

    /**
     * Store CustomerInsurance
     * @param Request $request
     * @return View CustomerInsurances
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        // Validations
        $validation_array = [
            'customer_id' => 'required|exists:brokers,id',
            'branch_id' => 'required|exists:branches,id',
            'broker_id' => 'required|exists:brokers,id',
            'relationship_manager_id' => 'required|exists:relationship_managers,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_type_id' => 'required|exists:policy_types,id',
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'registration_no' => 'required'
        ];

        if (!empty($request->issue_date)) {
            $validation_array['issue_date'] = 'date_format:Y-m-d';
        }

        if (!empty($request->start_date)) {
            $validation_array['start_date'] = 'date_format:Y-m-d';
        }
        if (!empty($request->expired_date)) {
            $validation_array['expired_date'] = 'date_format:Y-m-d';
        }
        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            $data_to_store = [];
            $data_to_store['customer_id'] = $request->customer_id;
            $data_to_store['branch_id'] = $request->branch_id;
            $data_to_store['broker_id'] = $request->broker_id;
            $data_to_store['relationship_manager_id'] = $request->relationship_manager_id;
            $data_to_store['insurance_company_id'] = $request->insurance_company_id;

            if (isset($request->issue_date))
                $data_to_store['issue_date'] = $request->issue_date;

            if (isset($request->policy_type_id))
                $data_to_store['policy_type_id'] = $request->policy_type_id;

            if (isset($request->fuel_type_id))
                $data_to_store['fuel_type_id'] = $request->fuel_type_id;

            if (isset($request->policy_no))
                $data_to_store['policy_no'] = $request->policy_no;

            if (isset($request->registration_no))
                $data_to_store['registration_no'] = $request->registration_no;

            if (isset($request->rto))
                $data_to_store['rto'] = $request->rto;

            if (isset($request->make_model))
                $data_to_store['make_model'] = $request->make_model;

            if (isset($request->start_date))
                $data_to_store['start_date'] = $request->start_date;

            if (isset($request->expired_date))
                $data_to_store['expired_date'] = $request->expired_date;

            if (isset($request->od_premium))
                $data_to_store['od_premium'] = $request->od_premium;

            if (isset($request->tp_premium))
                $data_to_store['tp_premium'] = $request->tp_premium;

            if (isset($request->rsa))
                $data_to_store['rsa'] = $request->rsa;

            if (isset($request->net_premium))
                $data_to_store['net_premium'] = $request->net_premium;

            if (isset($request->gst))
                $data_to_store['gst'] = $request->gst;

            if (isset($request->final_premium_with_gst))
                $data_to_store['final_premium_with_gst'] = $request->final_premium_with_gst;

            if (isset($request->mode_of_payment))
                $data_to_store['mode_of_payment'] = $request->mode_of_payment;

            if (isset($request->cheque_no))
                $data_to_store['cheque_no'] = $request->cheque_no;

            if (isset($request->premium))
                $data_to_store['premium'] = $request->premium;

            if (isset($request->issued_by))
                $data_to_store['issued_by'] = $request->issued_by;

            // Store Data
            $customer_insurance = CustomerInsurance::create($data_to_store);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'Customer Insurance Created Successfully.');
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
     * @author Darshan Baraiya
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
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            CustomerInsurance::whereId($customer_insurance_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            // return redirect()->back()->with('success', 'CustomerInsurance Status Updated Successfully!');
            return redirect()->back()->with('success', 'CustomerInsurance Status Updated Successfully!');
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
     * @author Darshan Baraiya
     */
    public function edit(CustomerInsurance $customer_insurance)
    {
        $response  = [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'customer_insurance'  => $customer_insurance,
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
        ];
        // dd($response);
        return view('customer_insurances.edit')->with($response);
    }

    /**
     * Update CustomerInsurance
     * @param Request $request, CustomerInsurance $customer_insurance
     * @return View CustomerInsurances
     * @author Darshan Baraiya
     */
    public function update(Request $request, CustomerInsurance $customer_insurance)
    {
        $validation_array = [
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'broker_id' => 'required|exists:brokers,id',
            'relationship_manager_id' => 'required|exists:relationship_managers,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_type_id' => 'required|exists:policy_types,id',
            'registration_no' => 'required',
            'fuel_type_id' => 'required|exists:fuel_types,id',
        ];

        if (!empty($request->issue_date)) {
            $validation_array['issue_date'] = 'date_format:Y-m-d';
        }

        if (!empty($request->start_date)) {
            $validation_array['start_date'] = 'date_format:Y-m-d';
        }
        if (!empty($request->expired_date)) {
            $validation_array['expired_date'] = 'date_format:Y-m-d';
        }
        $request->validate($validation_array);
        DB::beginTransaction($validation_array);
        try {
            $data_to_store = [];
            $data_to_store['customer_id'] = $request->customer_id;
            $data_to_store['branch_id'] = $request->branch_id;
            $data_to_store['broker_id'] = $request->broker_id;
            $data_to_store['relationship_manager_id'] = $request->relationship_manager_id;
            $data_to_store['insurance_company_id'] = $request->insurance_company_id;
            if (isset($request->policy_type_id))
                $data_to_store['policy_type_id'] = $request->policy_type_id;

            if (isset($request->fuel_type_id))
                $data_to_store['fuel_type_id'] = $request->fuel_type_id;

            if (isset($request->issue_date))
                $data_to_store['issue_date'] = $request->issue_date;

            if (isset($request->policy_no))
                $data_to_store['policy_no'] = $request->policy_no;

            if (isset($request->registration_no))
                $data_to_store['registration_no'] = $request->registration_no;

            if (isset($request->rto))
                $data_to_store['rto'] = $request->rto;

            if (isset($request->make_model))
                $data_to_store['make_model'] = $request->make_model;

            if (isset($request->start_date))
                $data_to_store['start_date'] = $request->start_date;

            if (isset($request->expired_date))
                $data_to_store['expired_date'] = $request->expired_date;

            if (isset($request->od_premium))
                $data_to_store['od_premium'] = $request->od_premium;

            if (isset($request->tp_premium))
                $data_to_store['tp_premium'] = $request->tp_premium;

            if (isset($request->rsa))
                $data_to_store['rsa'] = $request->rsa;

            if (isset($request->net_premium))
                $data_to_store['net_premium'] = $request->net_premium;

            if (isset($request->gst))
                $data_to_store['gst'] = $request->gst;

            if (isset($request->final_premium_with_gst))
                $data_to_store['final_premium_with_gst'] = $request->final_premium_with_gst;

            if (isset($request->mode_of_payment))
                $data_to_store['mode_of_payment'] = $request->mode_of_payment;

            if (isset($request->cheque_no))
                $data_to_store['cheque_no'] = $request->cheque_no;

            if (isset($request->premium))
                $data_to_store['premium'] = $request->premium;

            if (isset($request->issued_by))
                $data_to_store['issued_by'] = $request->issued_by;

            // dd($data_to_store);
            // Store Data
            $customer_insurance_updated = CustomerInsurance::whereId($customer_insurance->id)->update($data_to_store);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'CustomerInsurance Updated Successfully.');
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
     * @author Darshan Baraiya
     */
    public function delete(CustomerInsurance $customer_insurance)
    {
        DB::beginTransaction();
        try {
            // Delete CustomerInsurance
            CustomerInsurance::whereId($customer_insurance->id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'CustomerInsurance Deleted Successfully!.');
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
