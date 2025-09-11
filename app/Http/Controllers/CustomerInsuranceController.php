<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CustomerInsuranceServiceInterface;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerInsuranceController extends Controller
{
    use WhatsAppApiTrait;

    /**
     * Create a new controller instance.
     */
    public function __construct(
        private CustomerInsuranceServiceInterface $customerInsuranceService
    ) {
        $this->middleware('auth');
        $this->middleware('permission:customer-insurance-list|customer-insurance-create|customer-insurance-edit|customer-insurance-delete', ['only' => ['index']]);
        $this->middleware('permission:customer-insurance-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:customer-insurance-edit', ['only' => ['edit', 'update', 'renew', 'storeRenew']]);
        $this->middleware('permission:customer-insurance-delete', ['only' => ['delete']]);
    }

    /**
     * List CustomerInsurance
     * @param Request $request
     * @return View
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $customer_insurances = $this->customerInsuranceService->getCustomerInsurances($request);
        $customers = Customer::select('id', 'name')->get();
        
        return view('customer_insurances.index', [
            'customer_insurances' => $customer_insurances,
            'customers' => $customers,
            'sort' => $request->input('sort', 'id'),
            'direction' => $request->input('direction', 'desc'),
            'request' => $request->all(),
        ]);
    }

    /**
     * Create CustomerInsurance
     * @return View
     * @author Darshan Baraiya
     */
    public function create()
    {
        $formData = $this->customerInsuranceService->getFormData();
        return view('customer_insurances.add', $formData);
    }

    /**
     * Store CustomerInsurance
     * @param Request $request
     * @return RedirectResponse
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        $validationRules = $this->customerInsuranceService->getStoreValidationRules();
        $request->validate($validationRules);

        try {
            $data = $this->customerInsuranceService->prepareStorageData($request);
            $customer_insurance = $this->customerInsuranceService->createCustomerInsurance($data);

            // Handle file uploads
            $this->customerInsuranceService->handleFileUpload($request, $customer_insurance);
            
            // Send WhatsApp document if uploaded
            if (!empty($customer_insurance->policy_document_path)) {
                $this->customerInsuranceService->sendWhatsAppDocument($customer_insurance);
            }

            return redirect()->route('customer_insurances.index')->with('success', 'Customer Insurance Created Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Update Status Of CustomerInsurance
     * @param int $customer_insurance_id
     * @param int $status
     * @return RedirectResponse
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

        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            $this->customerInsuranceService->updateStatus($customer_insurance_id, $status);
            return redirect()->back()->with('success', 'CustomerInsurance Status Updated Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Edit CustomerInsurance
     * @param CustomerInsurance $customer_insurance
     * @return View
     * @author Darshan Baraiya
     */
    public function edit(CustomerInsurance $customer_insurance)
    {
        $formData = $this->customerInsuranceService->getFormData();
        $formData['customer_insurance'] = $customer_insurance;
        
        return view('customer_insurances.edit', $formData);
    }

    /**
     * Send WhatsApp Document
     * @param CustomerInsurance $customer_insurance
     * @return RedirectResponse
     * @author Darshan Baraiya
     */
    public function sendWADocument(CustomerInsurance $customer_insurance)
    {
        try {
            $sent = $this->customerInsuranceService->sendWhatsAppDocument($customer_insurance);
            
            if ($sent) {
                return redirect()->back()->with('success', 'Document Sent Successfully!');
            } else {
                return redirect()->back()->with('error', 'Document Not Sent!');
            }
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Send Renewal Reminder via WhatsApp
     * @param CustomerInsurance $customer_insurance
     * @return RedirectResponse
     */
    public function sendRenewalReminderWA(CustomerInsurance $customer_insurance)
    {
        try {
            $this->customerInsuranceService->sendRenewalReminderWhatsApp($customer_insurance);
            return redirect()->back()->with('success', 'Renewal Reminder Sent Successfully!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Update CustomerInsurance
     * @param Request $request
     * @param CustomerInsurance $customer_insurance
     * @return RedirectResponse
     * @author Darshan Baraiya
     */
    public function update(Request $request, CustomerInsurance $customer_insurance)
    {
        $validationRules = $this->customerInsuranceService->getUpdateValidationRules();
        $request->validate($validationRules);

        try {
            $data = $this->customerInsuranceService->prepareStorageData($request);
            $this->customerInsuranceService->updateCustomerInsurance($customer_insurance, $data);

            // Handle file uploads
            $this->customerInsuranceService->handleFileUpload($request, $customer_insurance);

            return redirect()->back()->with('success', 'CustomerInsurance Updated Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Delete CustomerInsurance
     * @param CustomerInsurance $customer_insurance
     * @return RedirectResponse
     * @author Darshan Baraiya
     */
    public function delete(CustomerInsurance $customer_insurance)
    {
        try {
            $this->customerInsuranceService->deleteCustomerInsurance($customer_insurance);
            return redirect()->back()->with('success', 'CustomerInsurance Deleted Successfully!.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import CustomerInsurances
     * @return View
     */
    public function importCustomerInsurances()
    {
        return view('customer_insurances.import');
    }

    /**
     * Export CustomerInsurances
     * @return BinaryFileResponse
     */
    public function export()
    {
        return $this->customerInsuranceService->exportCustomerInsurances();
    }

    /**
     * Renew CustomerInsurance
     * @param CustomerInsurance $customer_insurance
     * @return View
     * @author Darshan Baraiya
     */
    public function renew(CustomerInsurance $customer_insurance)
    {
        $formData = $this->customerInsuranceService->getFormData();
        $formData['customer_insurance'] = $customer_insurance;
        
        return view('customer_insurances.renew', $formData);
    }

    /**
     * Store Renew CustomerInsurance
     * @param Request $request
     * @param CustomerInsurance $customer_insurance
     * @return RedirectResponse
     * @author Darshan Baraiya
     */
    public function storeRenew(Request $request, CustomerInsurance $customer_insurance)
    {
        $validationRules = $this->customerInsuranceService->getRenewalValidationRules();
        $request->validate($validationRules);

        try {
            $data = $this->customerInsuranceService->prepareStorageData($request);
            $renewedPolicy = $this->customerInsuranceService->renewPolicy($customer_insurance, $data);

            // Handle file uploads
            $this->customerInsuranceService->handleFileUpload($request, $renewedPolicy);
            
            // Send WhatsApp document if uploaded
            if (!empty($renewedPolicy->policy_document_path)) {
                $this->customerInsuranceService->sendWhatsAppDocument($renewedPolicy);
            }

            return redirect()->route('customer_insurances.index')->with('success', 'Customer Insurance Renewed Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }
}