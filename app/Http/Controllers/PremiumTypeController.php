<?php

namespace App\Http\Controllers;

use App\Models\PremiumType;
use App\Services\PremiumTypeService;
use App\Traits\ExportableTrait;
use Illuminate\Http\Request;
use App\Exports\PremiumTypesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

/**
 * Premium Type Controller
 *
 * Handles PremiumType CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class PremiumTypeController extends AbstractBaseCrudController
{
    use ExportableTrait;

    public function __construct(
        private PremiumTypeService $premiumTypeService
    ) {
        $this->setupPermissionMiddleware('premium-type');
    }


    /**
     * List PremiumType 
     * @param Nill
     * @return Array $premium_type
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $premium_type_obj = PremiumType::select('*');
        if (!empty($request->search)) {
            $premium_type_obj->where('name', 'LIKE', '%' . trim($request->search) . '%');
        }

        $premium_type = $premium_type_obj->paginate(10);
        return view('premium_type.index', ['premium_type' => $premium_type]);
    }

    /**
     * Create PremiumType 
     * @param Nill
     * @return Array $premium_type
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('premium_type.add');
    }

    /**
     * Store PremiumType
     * @param Request $request
     * @return View PremiumTypes
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        // Validations
        $validation_array = [
            'name' => 'required|unique:premium_types',
            'is_vehicle' => 'required',
        ];

        $validated = $request->validate($validation_array);

        try {
            $this->premiumTypeService->createPremiumType($validated);
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type', 'created'));
        } catch (\InvalidArgumentException $e) {
            return $this->redirectWithError($e->getMessage())
                ->withInput();
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Premium Type', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of PremiumType
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($premium_type_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'premium_type_id'   => $premium_type_id,
            'status' => $status
        ], [
            'premium_type_id'   =>  'required|exists:premium_types,id',
            'status' =>  'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return $this->redirectWithError($validate->errors()->first());
        }

        try {
            $this->premiumTypeService->updateStatus($premium_type_id, $status);
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type Status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Premium Type Status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit PremiumType
     * @param Integer $premium_type
     * @return Collection $premium_type
     * @author Darshan Baraiya
     */
    public function edit(PremiumType $premium_type)
    {
        return view('premium_type.edit')->with([
            'premium_type'  => $premium_type
        ]);
    }

    /**
     * Update PremiumType
     * @param Request $request, PremiumType $premium_type
     * @return View PremiumTypes
     * @author Darshan Baraiya
     */
    public function update(Request $request, PremiumType $premium_type)
    {
        // Validations
        $validation_array = [
            'name' => 'required|unique:premium_types,name,' . $premium_type->id,
            'is_vehicle' => 'required|boolean',
            'is_life_insurance_policies' => 'required|boolean',
        ];

        $validated = $request->validate($validation_array);

        try {
            $this->premiumTypeService->updatePremiumType($premium_type, $validated);
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type', 'updated'));
        } catch (\InvalidArgumentException $e) {
            return $this->redirectWithError($e->getMessage())
                ->withInput();
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Premium Type', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete PremiumType
     * @param PremiumType $premium_type
     * @return Index PremiumTypes
     * @author Darshan Baraiya
     */
    public function delete(PremiumType $premium_type)
    {
        try {
            $this->premiumTypeService->deletePremiumType($premium_type);
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Premium Type', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Import PremiumTypes 
     * @param Null
     * @return View File
     */
    public function importPremiumTypes()
    {
        return view('premium_type.import');
    }

    public function export()
    {
        return Excel::download(new PremiumTypesExport, 'premium_type.xlsx');
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
            'filename' => 'premium_types',
            'with_headings' => true,
            'auto_size' => true,
            'relations' => $this->getExportRelations(),
            'order_by' => ['column' => 'created_at', 'direction' => 'desc'],
            'headings' => ['ID', 'Name', 'Is Vehicle', 'Is Life Insurance', 'Status', 'Created Date'],
            'mapping' => function($model) {
                return [
                    $model->id,
                    $model->name ?? 'N/A',
                    $model->is_vehicle ? 'Yes' : 'No',
                    $model->is_life_insurance_policies ? 'Yes' : 'No',
                    $model->status ? 'Active' : 'Inactive',
                    $model->created_at->format('Y-m-d H:i:s')
                ];
            },
            'with_mapping' => true
        ];
    }
}
