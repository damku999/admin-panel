<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use App\Services\FuelTypeService;
use App\Traits\ExportableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Fuel Type Controller
 *
 * Handles FuelType CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class FuelTypeController extends AbstractBaseCrudController
{
    use ExportableTrait;

    public function __construct(
        private FuelTypeService $fuelTypeService
    ) {
        $this->setupPermissionMiddleware('fuel-type');
    }

    /**
     * List FuelType
     * @param Nill
     * @return Array $fuel_type
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $fuel_type_obj = FuelType::select('*');
        if (!empty($request->search)) {
            $fuel_type_obj->where('name', 'LIKE', '%' . trim($request->search) . '%');
        }

        $fuel_type = $fuel_type_obj->paginate(10);
        return view('fuel_type.index', ['fuel_type' => $fuel_type, 'request' => $request->all()]);
    }

    /**
     * Create FuelType
     * @param Nill
     * @return Array $fuel_type
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('fuel_type.add');
    }

    /**
     * Store FuelType
     * @param Request $request
     * @return View FuelTypes
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        // Validations
        $validation_array = [
            'name' => 'required',
        ];

        $validated = $request->validate($validation_array);

        try {
            $this->fuelTypeService->createFuelType($validated);
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Fuel Type', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of FuelType
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($fuel_type_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'fuel_type_id' => $fuel_type_id,
            'status' => $status,
        ], [
            'fuel_type_id' => 'required|exists:fuel_types,id',
            'status' => 'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return $this->redirectWithError($validate->errors()->first());
        }

        try {
            $this->fuelTypeService->updateStatus($fuel_type_id, $status);
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type Status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Fuel Type Status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit FuelType
     * @param Integer $fuel_type
     * @return Collection $fuel_type
     * @author Darshan Baraiya
     */
    public function edit(FuelType $fuel_type)
    {
        return view('fuel_type.edit')->with([
            'fuel_type' => $fuel_type,
        ]);
    }

    /**
     * Update FuelType
     * @param Request $request, FuelType $fuel_type
     * @return View FuelTypes
     * @author Darshan Baraiya
     */
    public function update(Request $request, FuelType $fuel_type)
    {
        $validation_array = [
            'name' => 'required',
        ];

        $validated = $request->validate($validation_array);

        try {
            $this->fuelTypeService->updateFuelType($fuel_type, $validated);
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Fuel Type', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete FuelType
     * @param FuelType $fuel_type
     * @return Index FuelTypes
     * @author Darshan Baraiya
     */
    public function delete(FuelType $fuel_type)
    {
        try {
            $this->fuelTypeService->deleteFuelType($fuel_type);
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Fuel Type', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Import FuelTypes
     * @param Null
     * @return View File
     */
    public function importFuelTypes()
    {
        return view('fuel_type.import');
    }

    protected function getExportRelations(): array
    {
        return [];
    }

    protected function getSearchableFields(): array
    {
        return ['name'];
    }

    protected function getExportConfig(Request $request): array
    {
        return [
            'format' => $request->get('format', 'xlsx'),
            'filename' => 'fuel_types',
            'with_headings' => true,
            'auto_size' => true,
            'relations' => $this->getExportRelations(),
            'order_by' => ['column' => 'created_at', 'direction' => 'desc'],
            'headings' => ['ID', 'Name', 'Status', 'Created Date'],
            'mapping' => function($model) {
                return [
                    $model->id,
                    $model->name,
                    $model->status ? 'Active' : 'Inactive',
                    $model->created_at->format('Y-m-d H:i:s')
                ];
            },
            'with_mapping' => true
        ];
    }
}
