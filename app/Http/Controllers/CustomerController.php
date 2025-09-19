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

/**
 * Customer Controller
 *
 * Handles Customer CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class CustomerController extends AbstractBaseCrudController
{
    public function __construct(private CustomerServiceInterface $customerService)
    {
        $this->setupPermissionMiddleware('customer');
    }

    /**
     * List Customer
     * @param Nill
     * @return Array $customer
     * @author Darshan Baraiya
     */
    public function index(Request $request): View
    {
        try {
            $customers = $this->customerService->getCustomers($request);

            return view('customers.index', [
                'customers' => $customers,
                'sortField' => $request->input('sort_field', 'name'),
                'sortOrder' => $request->input('sort_order', 'asc'),
                'request' => $request->all()
            ]);
        } catch (\Throwable $th) {
            // Create empty paginated result to maintain view compatibility
            $emptyPaginator = new \Illuminate\Pagination\LengthAwarePaginator(
                collect(), // empty collection
                0, // total count
                10, // per page (matches CustomerService default)
                1, // current page
                ['path' => request()->url(), 'pageName' => 'page']
            );

            return view('customers.index', [
                'customers' => $emptyPaginator,
                'sortField' => 'name',
                'sortOrder' => 'asc',
                'request' => $request->all(),
                'error' => 'Failed to load customers: ' . $th->getMessage()
            ]);
        }
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
            return $this->redirectWithSuccess('customers.index',
                $this->getSuccessMessage('Customer', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Customer', 'create') . ': ' . $th->getMessage())
                ->withInput();
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
                return redirect()->back()->with('success',
                    $this->getSuccessMessage('Customer status', 'updated'));
            }

            return $this->redirectWithError('Failed to update customer status.');
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Customer status', 'update') . ': ' . $th->getMessage());
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
                return redirect()->back()->with('success',
                    $this->getSuccessMessage('Customer', 'updated'));
            }

            return $this->redirectWithError('Failed to update customer.');
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Customer', 'update') . ': ' . $th->getMessage())
                ->withInput();
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
                return redirect()->back()->with('success',
                    $this->getSuccessMessage('Customer', 'deleted'));
            }

            return $this->redirectWithError('Failed to delete customer.');
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Customer', 'delete') . ': ' . $th->getMessage());
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
            return redirect()->back()->with('success',
                $this->getSuccessMessage('Onboarding message', 'sent'));
        }

        return $this->redirectWithError('Failed to send onboarding message.');
    }

}
