<?php

namespace App\Http\Controllers;

use App\Contracts\Services\BrokerServiceInterface;
use App\Http\Requests\StoreBrokerRequest;
use App\Http\Requests\UpdateBrokerRequest;
use App\Models\Broker;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BrokersExport;

/**
 * Broker Controller
 *
 * Handles Broker CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class BrokerController extends AbstractBaseCrudController
{
    public function __construct(
        private BrokerServiceInterface $brokerService
    ) {
        $this->setupPermissionMiddleware('broker');
    }

    /**
     * List Broker
     * @param Nill
     * @return Array $broker
     * @author Darshan Baraiya
     */
    public function index(Request $request): View
    {
        $brokers = $this->brokerService->getBrokers($request);
        return view('brokers.index', ['brokers' => $brokers, 'request' => $request->all()]);
    }

    /**
     * Create Broker
     * @param Nill
     * @return Array $broker
     * @author Darshan Baraiya
     */
    public function create(): View
    {
        return view('brokers.add');
    }

    /**
     * Store Broker
     * @param Request $request
     * @return View Brokers
     * @author Darshan Baraiya
     */
    public function store(StoreBrokerRequest $request): RedirectResponse
    {
        try {
            $broker = $this->brokerService->createBroker($request->validated());
            return $this->redirectWithSuccess('brokers.index',
                $this->getSuccessMessage('Broker', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Broker', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of Broker
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus(int $broker_id, int $status): RedirectResponse
    {
        try {
            $this->brokerService->updateStatus($broker_id, $status);
            return redirect()->back()->with('success',
                $this->getSuccessMessage('Broker status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Broker status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit Broker
     * @param Integer $broker
     * @return Collection $broker
     * @author Darshan Baraiya
     */
    public function edit(Broker $broker): View
    {
        return view('brokers.edit')->with([
            'broker' => $broker,
        ]);
    }

    /**
     * Update Broker
     * @param Request $request, Broker $broker
     * @return View Brokers
     * @author Darshan Baraiya
     */
    public function update(UpdateBrokerRequest $request, Broker $broker): RedirectResponse
    {
        try {
            $this->brokerService->updateBroker($broker, $request->validated());
            return redirect()->back()->with('success',
                $this->getSuccessMessage('Broker', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Broker', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete Broker
     * @param Broker $broker
     * @return Index Brokers
     * @author Darshan Baraiya
     */
    public function delete(Broker $broker): RedirectResponse
    {
        try {
            $this->brokerService->deleteBroker($broker);
            return redirect()->back()->with('success',
                $this->getSuccessMessage('Broker', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Broker', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Import Brokers
     * @param Null
     * @return View File
     */
    public function importBrokers(): View
    {
        return view('brokers.import');
    }

    public function export()
    {
        return Excel::download(new BrokersExport, 'brokers.xlsx');
    }
}
