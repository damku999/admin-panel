<?php

namespace App\Http\Controllers;

use App\Models\RelationshipManager;
use App\Services\RelationshipManagerService;
use App\Traits\ExportableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Relationship Manager Controller
 *
 * Handles RelationshipManager CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class RelationshipManagerController extends AbstractBaseCrudController
{
    use ExportableTrait;

    public function __construct(
        private RelationshipManagerService $relationshipManagerService
    ) {
        $this->setupPermissionMiddleware('relationship_manager');
    }


    /**
     * List RelationshipManager
     * @param Nill
     * @return Array $relationship_manager
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $relationship_manager_obj = RelationshipManager::select('*');
        if (!empty($request->search)) {
            $relationship_manager_obj->where('name', 'LIKE', '%' . trim($request->search) . '%')->orWhere('email', 'LIKE', '%' . trim($request->search) . '%')->orWhere('mobile_number', 'LIKE', '%' . trim($request->search) . '%');
        }

        $relationship_managers = $relationship_manager_obj->paginate(config('app.pagination_default', 15));
        return view('relationship_managers.index', ['relationship_managers' => $relationship_managers]);
    }

    /**
     * Create RelationshipManager
     * @param Nill
     * @return Array $relationship_manager
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('relationship_managers.add');
    }

    /**
     * Store RelationshipManager
     * @param Request $request
     * @return View RelationshipManagers
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
            $this->relationshipManagerService->createRelationshipManager($validated);
            return $this->redirectWithSuccess('relationship_managers.index', $this->getSuccessMessage('Relationship Manager', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Relationship Manager', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of RelationshipManager
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($relationship_manager_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'relationship_manager_id'   => $relationship_manager_id,
            'status' => $status
        ], [
            'relationship_manager_id'   =>  'required|exists:relationship_managers,id',
            'status' =>  'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return $this->redirectWithError($validate->errors()->first());
        }

        try {
            $this->relationshipManagerService->updateStatus($relationship_manager_id, $status);
            return $this->redirectWithSuccess('relationship_managers.index', $this->getSuccessMessage('Relationship Manager Status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Relationship Manager Status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit RelationshipManager
     * @param Integer $relationship_manager
     * @return Collection $relationship_manager
     * @author Darshan Baraiya
     */
    public function edit(RelationshipManager $relationship_manager)
    {
        return view('relationship_managers.edit')->with([
            'relationship_manager'  => $relationship_manager
        ]);
    }

    /**
     * Update RelationshipManager
     * @param Request $request, RelationshipManager $relationship_manager
     * @return View RelationshipManagers
     * @author Darshan Baraiya
     */
    public function update(Request $request, RelationshipManager $relationship_manager)
    {
        // Validations
        $validation_array = [
            'name' => 'required',
        ];

        $validated = $request->validate($validation_array);

        try {
            $this->relationshipManagerService->updateRelationshipManager($relationship_manager, $validated);
            return $this->redirectWithSuccess('relationship_managers.index', $this->getSuccessMessage('Relationship Manager', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Relationship Manager', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete RelationshipManager
     * @param RelationshipManager $relationship_manager
     * @return Index RelationshipManagers
     * @author Darshan Baraiya
     */
    public function delete(RelationshipManager $relationship_manager)
    {
        try {
            $this->relationshipManagerService->deleteRelationshipManager($relationship_manager);
            return $this->redirectWithSuccess('relationship_managers.index', $this->getSuccessMessage('Relationship Manager', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Relationship Manager', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Import RelationshipManagers
     * @param Null
     * @return View File
     */
    public function importRelationshipManagers()
    {
        return view('relationship_managers.import');
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
            'filename' => 'relationship_managers',
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
