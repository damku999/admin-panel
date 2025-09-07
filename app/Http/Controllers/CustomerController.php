<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use App\Services\FileUploadService;
use App\Traits\WhatsAppApiTrait;
use App\Traits\NotificationControlTrait;
use App\Traits\ExportableTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CustomerController extends Controller
{
    use WhatsAppApiTrait, NotificationControlTrait, ExportableTrait;

    public function __construct(private FileUploadService $fileUploadService)
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index']]);
        $this->middleware('permission:customer-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-delete', ['only' => ['delete']]);
    }

    public function index(Request $request): View
    {
        $customer_obj = Customer::select('*');
        
        // Sorting - support both old and new parameter names
        $sortField = $request->input('sort', $request->input('sort_field', 'name')); // Default sort by name
        $sortOrder = $request->input('direction', $request->input('sort_order', 'asc')); // Default sort order ascending
        
        // Validate sort field to prevent SQL injection
        $allowedSortFields = ['name', 'email', 'mobile_number', 'type', 'status', 'created_at'];
        if (!in_array($sortField, $allowedSortFields)) {
            $sortField = 'name';
        }
        
        // Validate sort order
        if (!in_array(strtolower($sortOrder), ['asc', 'desc'])) {
            $sortOrder = 'asc';
        }

        $customer_obj->orderBy($sortField, $sortOrder);
        // Apply search filter
        if (!empty($request->search)) {
            $customer_obj->where('name', 'LIKE', '%' . trim($request->search) . '%')
                ->orWhere('email', 'LIKE', '%' . trim($request->search) . '%')
                ->orWhere('mobile_number', 'LIKE', '%' . trim($request->search) . '%');
        }

        // Apply type filter
        if (!empty($request->type)) {
            $customer_obj->where('type', $request->type);
        }

        // Apply date range filter
        if (!empty($request->from_date) && !empty($request->to_date)) {
            $customer_obj->whereBetween('created_at', [$request->from_date, $request->to_date]);
        }

        $customers = $customer_obj->paginate(10);
        return view('customers.index', ['customers' => $customers, 'sortField' => $sortField, 'sortOrder' => $sortOrder, 'request' => $request->all()]);
    }
    public function create(): View
    {
        return view('customers.add');
    }
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        DB::beginTransaction();

        try {
            // Store Data
            $customer = Customer::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => true,
                'wedding_anniversary_date' => $request->wedding_anniversary_date,
                'engagement_anniversary_date' => $request->engagement_anniversary_date,
                'date_of_birth' => $request->date_of_birth,
                'type' => $request->type,
                'pan_card_number' => $request->pan_card_number,
                'aadhar_card_number' => $request->aadhar_card_number,
                'gst_number' => $request->gst_number,
            ]);
            // Handle file uploads
            $this->handleFileUpload($request, $customer);

            // Commit And Redirected To Listing
            DB::commit();
            
            // Send WhatsApp notification if enabled
            $this->sendWhatsAppIfEnabled($this->newCustomerAdd($customer), $customer->mobile_number);
            
            return redirect()->back()->with('success', 'Customer Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    private function handleFileUpload(StoreCustomerRequest|UpdateCustomerRequest $request, Customer $customer): void
    {
        if ($request->hasFile('pan_card_path')) {
            $customer->pan_card_path = $this->fileUploadService->uploadCustomerDocument(
                $request->file('pan_card_path'),
                $customer->id,
                'pan_card',
                $customer->name
            );
        }

        if ($request->hasFile('aadhar_card_path')) {
            $customer->aadhar_card_path = $this->fileUploadService->uploadCustomerDocument(
                $request->file('aadhar_card_path'),
                $customer->id,
                'aadhar_card',
                $customer->name
            );
        }

        if ($request->hasFile('gst_path')) {
            $customer->gst_path = $this->fileUploadService->uploadCustomerDocument(
                $request->file('gst_path'),
                $customer->id,
                'gst',
                $customer->name
            );
        }

        $customer->save();
    }

   
    public function updateStatus(int $customer_id, int $status): RedirectResponse
    {
        $validate = Validator::make([
            'customer_id' => $customer_id,
            'status' => $status,
        ], [
            'customer_id' => 'required|exists:customers,id',
            'status' => 'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            Customer::whereId($customer_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();

            return redirect()->back()->with('success', 'Customer Status Updated Successfully!');
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

   
    public function edit(Customer $customer): View
    {
        return view('customers.edit')->with([
            'customer' => $customer,
            'customer_insurances' => $customer->insurance,
        ]);
    }

    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {

        DB::beginTransaction();
        try {
            // Store Data
            $customer_updated = Customer::whereId($customer->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
                'status' => $request->status,
                'wedding_anniversary_date' => $request->wedding_anniversary_date,
                'engagement_anniversary_date' => $request->engagement_anniversary_date,
                'date_of_birth' => $request->date_of_birth,
                'type' => $request->type,
                'pan_card_number' => $request->pan_card_number,
                'aadhar_card_number' => $request->aadhar_card_number,
                'gst_number' => $request->gst_number,
            ]);

            $this->handleFileUpload($request, $customer);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'Customer Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    
    public function delete(Customer $customer): RedirectResponse
    {
        DB::beginTransaction();
        try {
            // Delete Customer
            Customer::whereId($customer->id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Customer Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    public function importCustomers(): View
    {
        return view('customers.import');
    }

    // Export method is now provided by ExportableTrait with advanced features
    
    protected function getExportRelations(): array
    {
        return ['familyGroup'];
    }
    
    protected function getSearchableFields(): array
    {
        return ['name', 'email', 'mobile_number', 'pan_card_number', 'aadhar_card_number'];
    }
    
    protected function getExportConfig(Request $request): array
    {
        return array_merge($this->getBaseExportConfig($request), [
            'headings' => [
                'ID', 'Customer Name', 'Email Address', 'Mobile Number', 'Status',
                'Date of Birth', 'PAN Number', 'Aadhar Number', 'GST Number',
                'Family Group', 'Customer Type', 'Registration Date'
            ],
            'mapping' => function($customer) {
                return [
                    $customer->id,
                    $customer->name,
                    $customer->email,
                    $customer->mobile_number,
                    ucfirst($customer->status),
                    $customer->date_of_birth ? $customer->date_of_birth->format('d-m-Y') : '',
                    $customer->pan_card_number,
                    $customer->aadhar_card_number,
                    $customer->gst_number,
                    $customer->familyGroup ? $customer->familyGroup->name : 'Individual',
                    ucfirst($customer->type),
                    $customer->created_at->format('d-m-Y H:i:s')
                ];
            },
            'with_headings' => true,
            'with_mapping' => true
        ]);
    }

    public function resendOnBoardingWA(Customer $customer): RedirectResponse
    {
        // Send WhatsApp notification if enabled
        $sent = $this->sendWhatsAppIfEnabled($this->newCustomerAdd($customer), $customer->mobile_number);
        $message = $sent ? 'Onboarding message sent successfully!' : 'WhatsApp notifications are disabled. Message not sent.';

        return redirect()->back()->with('success', $message);
    }

}
