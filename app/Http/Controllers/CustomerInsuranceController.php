<?php

namespace App\Http\Controllers;

use App\Exports\CustomerInsurancesExport;
use App\Models\Branch;
use App\Models\Broker;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Models\FuelType;
use App\Models\InsuranceCompany;
use App\Models\PolicyType;
use App\Models\PremiumType;
use App\Models\ReferenceUser;
use App\Models\RelationshipManager;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerInsuranceController extends Controller
{
    use WhatsAppApiTrait;
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
        $customer_insurance_obj = CustomerInsurance::select(['customer_insurances.*', 'customers.name as customer_name', 'branches.name as branch_name', 'brokers.name as broker_name', 'relationship_managers.name as relationship_manager_name', 'premium_types.name AS policy_type_name'])
            ->join('customers', 'customers.id', 'customer_insurances.customer_id')
            ->leftJoin('branches', 'branches.id', 'customer_insurances.branch_id')
            ->leftJoin('premium_types', 'premium_types.id', 'customer_insurances.premium_type_id')
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
        // New code for expiring date range filter
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $start_date = Carbon::parse($request->input('start_date'))->startOfDay();
            $end_date = Carbon::parse($request->input('end_date'))->endOfDay();
            $customer_insurance_obj->whereBetween('expired_date', [$start_date, $end_date]);
        }

        $sort = $request->input('sort', 'updated_at');
        $direction = $request->input('direction', 'desc');
        $customer_insurance_obj->orderBy($sort, $direction);
        $customer_insurances = $customer_insurance_obj->paginate(10);
        $customers = Customer::select('id', 'name')->get();

        return view('customer_insurances.index', [
            'customer_insurances' => $customer_insurances,
            'customers' => $customers,
            'sort' => $sort,
            'direction' => $direction,
        ]);
    }

    /**
     * Create CustomerInsurance
     * @param Nill
     * @return Array $customer_insurance
     * @author Darshan Baraiya
     */
    public function create()
    {
        $response = [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
            'premium_types' => PremiumType::select('id', 'name', 'is_vehicle', 'is_life_insurance_policies')->get(),
            'reference_by_user' => ReferenceUser::select('id', 'name')->get(),
            'life_insurance_payment_mode' => Config::get('constants.LIFE_INSURANCE_PAYMENT_MODE'),
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
            'customer_id' => 'required|exists:customers,id',
            'branch_id' => 'required|exists:branches,id',
            'broker_id' => 'required|exists:brokers,id',
            'relationship_manager_id' => 'required|exists:relationship_managers,id',
            'insurance_company_id' => 'required|exists:insurance_companies,id',
            'policy_type_id' => 'required|exists:policy_types,id',
            'fuel_type_id' => 'nullable|exists:fuel_types,id',
            'premium_type_id' => 'required|exists:premium_types,id',
            'issue_date' => 'required|date_format:Y-m-d',
            'expired_date' => 'required|date_format:Y-m-d',
            'start_date' => 'required|date_format:Y-m-d',
            'tp_expiry_date' => 'nullable|date_format:Y-m-d',
            'policy_no' => 'required',
            'net_premium' => 'nullable|numeric',
            'premium_amount' => 'nullable|numeric',
            'gst' => 'nullable|numeric',
            'final_premium_with_gst' => 'required|numeric',
            'mode_of_payment' => 'nullable|string',
            'cheque_no' => 'nullable|string',
            'rto' => 'nullable|string',
            'registration_no' => 'nullable|string',
            'make_model' => 'nullable|string',
            'od_premium' => 'nullable|numeric',
            'tp_premium' => 'nullable|numeric',
            'cgst1' => 'nullable|numeric',
            'sgst1' => 'nullable|numeric',
            'cgst2' => 'nullable|numeric',
            'sgst2' => 'nullable|numeric',
            'commission_on' => 'nullable|in:net_premium,od_premium,tp_premium',
            'my_commission_percentage' => 'nullable|numeric',
            'my_commission_amount' => 'nullable|numeric',
            'transfer_commission_percentage' => 'nullable|numeric',
            'transfer_commission_amount' => 'nullable|numeric',
            'reference_commission_percentage' => 'nullable|numeric',
            'reference_commission_amount' => 'nullable|numeric',
            'actual_earnings' => 'nullable|numeric',
            'ncb_percentage' => 'nullable|numeric',
            'gross_vehicle_weight' => 'nullable|numeric',
            'mfg_year' => 'nullable|numeric',
            // 'reference_by' => 'nullable|exists:reference_users,id',
            'plan_name' => 'nullable|string',
            'premium_paying_term' => 'nullable|string',
            'policy_term' => 'nullable|string',
            'sum_insured' => 'nullable|string',
            'pension_amount_yearly' => 'nullable|string',
            'approx_maturity_amount' => 'nullable|string',
            'remarks' => 'nullable|string',
        ];
        $request->validate($validation_array);

        DB::beginTransaction();

        try {
            // Retrieve only the validated fields from the request
            $data_to_store = $request->only([
                'customer_id',
                'branch_id',
                'broker_id',
                'relationship_manager_id',
                'insurance_company_id',
                'premium_type_id',
                'policy_type_id',
                'fuel_type_id',
                'issue_date',
                'expired_date',
                'start_date',
                'tp_expiry_date',
                'policy_no',
                'net_premium',
                'gst',
                'final_premium_with_gst',
                'mode_of_payment',
                'cheque_no',
                'rto',
                'registration_no',
                'make_model',
                'od_premium',
                'premium_amount',
                'tp_premium',
                'cgst1',
                'sgst1',
                'cgst2',
                'sgst2',
                'commission_on',
                'my_commission_percentage',
                'my_commission_amount',
                'transfer_commission_percentage',
                'transfer_commission_amount',
                'actual_earnings',
                'ncb_percentage',
                'gross_vehicle_weight',
                'mfg_year',
                'reference_commission_percentage',
                'reference_commission_amount',
                'plan_name',
                'premium_paying_term',
                'policy_term',
                'sum_insured',
                'pension_amount_yearly',
                'approx_maturity_amount',
                'remarks',
                'maturity_date',
                'life_insurance_payment_mode',
                'reference_by',
            ]);
            if (!empty($request->reference_by)) {
                $data_to_store['reference_by'] = $request->reference_by;
            }

            // Store Data
            $customer_insurance = CustomerInsurance::create($data_to_store);
            $this->whatsAppSendMessageWithAttachment($this->insuranceAdded($customer_insurance->customer), $customer_insurance->customer->mobile_number, Storage::path('public' . DIRECTORY_SEPARATOR . $customer_insurance->policy_document_path));

            // Handle file uploads
            $this->handleFileUpload($request, $customer_insurance);

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
     * Handle file upload.
     * @param Request $request
     * @param CustomerInsurance $customer_insurance
     * @return void
     */
    private function handleFileUpload(Request $request, CustomerInsurance $customer_insurance)
    {
        if ($request->hasFile('policy_document_path')) {
            $file = $request->file('policy_document_path');
            $path = $file->store('customer_insurances/' . $customer_insurance->id . '/policy_document_path', 'public');
            $customer_insurance->policy_document_path = $path;
            $customer_insurance->save();
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
            'customer_insurance_id' => $customer_insurance_id,
            'status' => $status,
        ], [
            'customer_insurance_id' => 'required|exists:customer_insurances,id',
            'status' => 'required|in:0,1',
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
        $response = [
            'customers' => Customer::select('id', 'name')->get(),
            'brokers' => Broker::select('id', 'name')->get(),
            'relationship_managers' => RelationshipManager::select('id', 'name')->get(),
            'branches' => Branch::select('id', 'name')->get(),
            'insurance_companies' => InsuranceCompany::select('id', 'name')->get(),
            'customer_insurance' => $customer_insurance,
            'policy_type' => PolicyType::select('id', 'name')->get(),
            'fuel_type' => FuelType::select('id', 'name')->get(),
            'premium_types' => PremiumType::select('id', 'name', 'is_vehicle', 'is_life_insurance_policies')->get(),
            'reference_by_user' => ReferenceUser::select('id', 'name')->get(),
            'life_insurance_payment_mode' => Config::get('constants.LIFE_INSURANCE_PAYMENT_MODE'),
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
            'customer_id',
            'branch_id',
            'broker_id',
            'relationship_manager_id',
            'insurance_company_id',
            'premium_type_id',
            'policy_type_id',
            'fuel_type_id',
            'issue_date',
            'expired_date',
            'start_date',
            'tp_expiry_date',
            'policy_no',
            'net_premium',
            'gst',
            'final_premium_with_gst',
            'mode_of_payment',
            'cheque_no',
            'rto',
            'registration_no',
            'make_model',
            'od_premium',
            'premium_amount',
            'tp_premium',
            'cgst1',
            'sgst1',
            'cgst2',
            'sgst2',
            'commission_on',
            'my_commission_percentage',
            'my_commission_amount',
            'transfer_commission_percentage',
            'transfer_commission_amount',
            'actual_earnings',
            'ncb_percentage',
            'gross_vehicle_weight',
            'mfg_year',
            'reference_commission_percentage',
            'reference_commission_amount',
            'plan_name',
            'premium_paying_term',
            'policy_term',
            'sum_insured',
            'pension_amount_yearly',
            'approx_maturity_amount',
            'remarks',
            'maturity_date',
            'life_insurance_payment_mode',
            'reference_by',
        ];
        $request->validate($validation_array);
        DB::beginTransaction();
        try {
            // Retrieve only the validated fields from the request
            $data_to_store = $request->only([
                'customer_id',
                'branch_id',
                'broker_id',
                'relationship_manager_id',
                'insurance_company_id',
                'premium_type_id',
                'policy_type_id',
                'fuel_type_id',
                'issue_date',
                'expired_date',
                'start_date',
                'tp_expiry_date',
                'policy_no',
                'net_premium',
                'gst',
                'final_premium_with_gst',
                'mode_of_payment',
                'cheque_no',
                'rto',
                'registration_no',
                'make_model',
                'od_premium',
                'tp_premium',
                'premium_amount',
                'cgst1',
                'sgst1',
                'cgst2',
                'sgst2',
                'commission_on',
                'my_commission_percentage',
                'my_commission_amount',
                'transfer_commission_percentage',
                'transfer_commission_amount',
                'actual_earnings',
                'ncb_percentage',
                'gross_vehicle_weight',
                'mfg_year',
                'reference_commission_percentage',
                'reference_commission_amount',
                'plan_name',
                'premium_paying_term',
                'policy_term',
                'sum_insured',
                'pension_amount_yearly',
                'approx_maturity_amount',
                'remarks',
                'life_insurance_payment_mode',
            ]);
            if (!empty($request->reference_by)) {
                $data_to_store['reference_by'] = $request->reference_by;
            }

            // Store Data
            CustomerInsurance::whereId($customer_insurance->id)->update($data_to_store);
            // Handle file uploads
            $this->handleFileUpload($request, $customer_insurance);
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
