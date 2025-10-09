<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CustomerInsuranceServiceInterface;
use App\Models\Customer;
use App\Models\CustomerInsurance;
use App\Traits\WhatsAppApiTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Customer Insurance Controller
 *
 * Handles CustomerInsurance CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class CustomerInsuranceController extends AbstractBaseCrudController
{
    use WhatsAppApiTrait;

    public function __construct(
        private CustomerInsuranceServiceInterface $customerInsuranceService
    ) {
        $this->setupCustomPermissionMiddleware([
            ['permission' => 'customer-insurance-list|customer-insurance-create|customer-insurance-edit|customer-insurance-delete', 'only' => ['index']],
            ['permission' => 'customer-insurance-create', 'only' => ['create', 'store', 'updateStatus']],
            ['permission' => 'customer-insurance-edit', 'only' => ['edit', 'update', 'renew', 'storeRenew']],
            ['permission' => 'customer-insurance-delete', 'only' => ['delete']],
        ]);
    }

    /**
     * List CustomerInsurance
     *
     * @return View
     *
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        // Handle AJAX requests for Select2 autocomplete
        if ($request->ajax() || $request->has('ajax')) {
            $search = $request->input('q', $request->input('search', ''));

            $query = \App\Models\CustomerInsurance::with('customer:id,name')
                ->select('id', 'policy_no', 'customer_id', 'registration_no');

            if ($search) {
                $query->where(function ($q) use ($search) {
                    $q->where('policy_no', 'LIKE', "%{$search}%")
                        ->orWhere('registration_no', 'LIKE', "%{$search}%")
                        ->orWhereHas('customer', function ($cq) use ($search) {
                            $cq->where('name', 'LIKE', "%{$search}%");
                        });
                });
            }

            $insurances = $query->orderBy('created_at', 'desc')
                ->limit(20)
                ->get();

            // Format for Select2
            return response()->json([
                'results' => $insurances->map(function ($insurance) {
                    return [
                        'id' => $insurance->id,
                        'text' => $insurance->policy_no.' - '.($insurance->customer?->name ?? 'Unknown'),
                    ];
                }),
            ]);
        }

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
     *
     * @return View
     *
     * @author Darshan Baraiya
     */
    public function create()
    {
        $formData = $this->customerInsuranceService->getFormData();

        return view('customer_insurances.add', $formData);
    }

    /**
     * Store CustomerInsurance
     *
     * @return RedirectResponse
     *
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
            if (! empty($customer_insurance->policy_document_path)) {
                $this->customerInsuranceService->sendWhatsAppDocument($customer_insurance);
            }

            return $this->redirectWithSuccess('customer_insurances.index', $this->getSuccessMessage('Customer Insurance', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'create').': '.$th->getMessage());
        }
    }

    /**
     * Update Status Of CustomerInsurance
     *
     * @param  int  $customer_insurance_id
     * @param  int  $status
     * @return RedirectResponse
     *
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
            return $this->redirectWithError($validate->errors()->first());
        }

        try {
            $this->customerInsuranceService->updateStatus($customer_insurance_id, $status);

            return $this->redirectWithSuccess('customer_insurances.index', $this->getSuccessMessage('Customer Insurance Status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'operation').': '.$th->getMessage());
        }
    }

    /**
     * Edit CustomerInsurance
     *
     * @return View
     *
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
     *
     * @return RedirectResponse
     *
     * @author Darshan Baraiya
     */
    public function sendWADocument(CustomerInsurance $customer_insurance)
    {
        try {
            $sent = $this->customerInsuranceService->sendWhatsAppDocument($customer_insurance);

            if ($sent) {
                return $this->redirectWithSuccess('customer_insurances.index', 'Document Sent Successfully!');
            } else {
                return $this->redirectWithError('Document Not Sent!');
            }
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'operation').': '.$th->getMessage());
        }
    }

    /**
     * Send Renewal Reminder via WhatsApp
     *
     * @return RedirectResponse
     */
    public function sendRenewalReminderWA(CustomerInsurance $customer_insurance)
    {
        try {
            $this->customerInsuranceService->sendRenewalReminderWhatsApp($customer_insurance);

            return $this->redirectWithSuccess('customer_insurances.index', 'Renewal Reminder Sent Successfully!');
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'operation').': '.$th->getMessage());
        }
    }

    /**
     * Update CustomerInsurance
     *
     * @return RedirectResponse
     *
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

            return $this->redirectWithSuccess('customer_insurances.index', $this->getSuccessMessage('Customer Insurance', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'create').': '.$th->getMessage());
        }
    }

    /**
     * Delete CustomerInsurance
     *
     * @return RedirectResponse
     *
     * @author Darshan Baraiya
     */
    public function delete(CustomerInsurance $customer_insurance)
    {
        try {
            $this->customerInsuranceService->deleteCustomerInsurance($customer_insurance);

            return $this->redirectWithSuccess('customer_insurances.index', $this->getSuccessMessage('Customer Insurance', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'operation').': '.$th->getMessage());
        }
    }

    /**
     * Import CustomerInsurances
     *
     * @return View
     */
    public function importCustomerInsurances()
    {
        return view('customer_insurances.import');
    }

    /**
     * Export CustomerInsurances
     *
     * @return BinaryFileResponse
     */
    public function export()
    {
        return $this->customerInsuranceService->exportCustomerInsurances();
    }

    /**
     * Renew CustomerInsurance
     *
     * @return View
     *
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
     *
     * @return RedirectResponse
     *
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
            if (! empty($renewedPolicy->policy_document_path)) {
                $this->customerInsuranceService->sendWhatsAppDocument($renewedPolicy);
            }

            return $this->redirectWithSuccess('customer_insurances.index', $this->getSuccessMessage('Customer Insurance', 'renewed'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Customer Insurance', 'create').': '.$th->getMessage());
        }
    }
}
