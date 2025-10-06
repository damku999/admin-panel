<?php

namespace App\Http\Controllers;

use App\Contracts\Services\BrokerServiceInterface;
use App\Http\Requests\StoreBrokerRequest;
use App\Http\Requests\UpdateBrokerRequest;
use App\Models\Broker;
use App\Traits\ExportableTrait;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Broker Controller
 *
 * Handles Broker CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class BrokerController extends AbstractBaseCrudController
{
    use ExportableTrait;

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
            return $this->redirectWithSuccess('brokers.index',
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
            return $this->redirectWithSuccess('brokers.index',
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
            return $this->redirectWithSuccess('brokers.index',
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

    protected function getExportRelations(): array
    {
        return [];
    }

    protected function getSearchableFields(): array
    {
        return ['name', 'email', 'mobile_number'];
    }

    protected function getExportConfig(Request $request): array
    {
        return [
            'format' => $request->get('format', 'xlsx'),
            'filename' => 'brokers',
            'with_headings' => true,
            'auto_size' => true,
            'relations' => $this->getExportRelations(),
            'order_by' => ['column' => 'created_at', 'direction' => 'desc'],
            'headings' => ['ID', 'Name', 'Email', 'Mobile Number', 'Status', 'Created Date'],
            'mapping' => function($model) {
                return [
                    $model->id,
                    $model->name,
                    $model->email ?? 'N/A',
                    $model->mobile_number ?? 'N/A',
                    $model->status ? 'Active' : 'Inactive',
                    $model->created_at->format('Y-m-d H:i:s')
                ];
            },
            'with_mapping' => true
        ];
    }
}
