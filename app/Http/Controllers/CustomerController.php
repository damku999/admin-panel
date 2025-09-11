<?php

namespace App\Http\Controllers;

use App\Contracts\Services\CustomerServiceInterface;
use App\Exports\CustomersExport;
use App\Http\Requests\StoreCustomerRequest;
use App\Http\Requests\UpdateCustomerRequest;
use App\Models\Customer;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function __construct(private CustomerServiceInterface $customerService)
    {
        $this->middleware('auth');
        $this->middleware('permission:customer-list|customer-create|customer-edit|customer-delete', ['only' => ['index']]);
        $this->middleware('permission:customer-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:customer-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:customer-delete', ['only' => ['delete']]);
    }

    /**
     * List Customer
     * @param Nill
     * @return Array $customer
     * @author Darshan Baraiya
     */
    public function index(Request $request): View
    {
        $customers = $this->customerService->getCustomers($request);
        
        return view('customers.index', [
            'customers' => $customers,
            'sortField' => $request->input('sort_field', 'name'),
            'sortOrder' => $request->input('sort_order', 'asc'),
            'request' => $request->all()
        ]);
    }

    /**
     * Create Customer
     * @param Nill
     * @return Array $customer
     * @author Darshan Baraiya
     */
    public function create(): View
    {
        return view('customers.add');
    }

    /**
     * Store Customer
     * @param Request $request
     * @return View Customers
     * @author Darshan Baraiya
     */
    public function store(StoreCustomerRequest $request): RedirectResponse
    {
        try {
            $customer = $this->customerService->createCustomer($request);
            return redirect()->route('customers.index')->with('success', 'Customer Created Successfully.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }


    /**
     * Update Status Of Customer
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus(int $customer_id, int $status): RedirectResponse
    {
        try {
            $updated = $this->customerService->updateCustomerStatus($customer_id, $status);
            
            if ($updated) {
                return redirect()->back()->with('success', 'Customer Status Updated Successfully!');
            }
            
            return redirect()->back()->with('error', 'Failed to update customer status.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Edit Customer
     * @param Integer $customer
     * @return Collection $customer
     * @author Darshan Baraiya
     */
    public function edit(Customer $customer): View
    {
        return view('customers.edit')->with([
            'customer' => $customer,
            'customer_insurances' => $customer->insurance,
        ]);
    }

    /**
     * Update Customer
     * @param Request $request, Customer $customer
     * @return View Customers
     * @author Darshan Baraiya
     */
    public function update(UpdateCustomerRequest $request, Customer $customer): RedirectResponse
    {
        try {
            $updated = $this->customerService->updateCustomer($request, $customer);
            
            if ($updated) {
                return redirect()->back()->with('success', 'Customer Updated Successfully.');
            }
            
            return redirect()->back()->with('error', 'Failed to update customer.');
        } catch (\Throwable $th) {
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Delete Customer
     * @param Customer $customer
     * @return Index Customers
     * @author Darshan Baraiya
     */
    public function delete(Customer $customer): RedirectResponse
    {
        try {
            $deleted = $this->customerService->deleteCustomer($customer);
            
            if ($deleted) {
                return redirect()->back()->with('success', 'Customer Deleted Successfully!');
            }
            
            return redirect()->back()->with('error', 'Failed to delete customer.');
        } catch (\Throwable $th) {
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import Customers
     * @param Null
     * @return View File
     */
    public function importCustomers(): View
    {
        return view('customers.import');
    }

    public function export()
    {
        return Excel::download(new CustomersExport, 'customers.xlsx');
    }

    public function resendOnBoardingWA(Customer $customer): RedirectResponse
    {
        $sent = $this->customerService->sendOnboardingMessage($customer);
        
        if ($sent) {
            return redirect()->back()->with('success', 'Onboarding Message Sent Successfully!');
        }
        
        return redirect()->back()->with('error', 'Failed to send onboarding message.');
    }

}
