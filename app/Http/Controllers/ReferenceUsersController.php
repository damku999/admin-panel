<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferenceUser;
use App\Services\ReferenceUserService;
use App\Traits\ExportableTrait;
use Illuminate\Support\Facades\Validator;
use Throwable;

/**
 * Reference Users Controller
 *
 * Handles ReferenceUser CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class ReferenceUsersController extends AbstractBaseCrudController
{
    use ExportableTrait;

    public function __construct(
        private ReferenceUserService $referenceUserService
    ) {
        $this->setupPermissionMiddleware('reference-user');
    }

    /**
     * List ReferenceUsers
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        $query = ReferenceUser::query();

        if ($request->filled('search')) {
            $searchTerm = '%' . trim($request->search) . '%';
            $query->where('name', 'LIKE', $searchTerm)
                ->orWhere('email', 'LIKE', $searchTerm)
                ->orWhere('mobile_number', 'LIKE', $searchTerm);
        }

        $reference_users = $query->paginate(config('app.pagination_default', 15));

        return view('reference_users.index', compact('reference_users'));
    }

    /**
     * Create ReferenceUser
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        return view('reference_users.add');
    }

    /**
     * Store ReferenceUser
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:100',
            'mobile_number' => 'nullable|string|max:15',
        ];

        $validated = $request->validate($validationRules);

        try {
            $this->referenceUserService->createReferenceUser($validated);
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User', 'created'));
        } catch (Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Reference User', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of ReferenceUser
     *
     * @param Integer $reference_user_id
     * @param Integer $status
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateStatus($reference_user_id, $status)
    {
        $validationRules = [
            'reference_user_id' => 'required|exists:reference_users,id',
            'status' => 'required|in:0,1',
        ];

        $validator = Validator::make([
            'reference_user_id' => $reference_user_id,
            'status' => $status,
        ], $validationRules);

        if ($validator->fails()) {
            return $this->redirectWithError($validator->errors()->first());
        }

        try {
            $this->referenceUserService->updateStatus($reference_user_id, $status);
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User Status', 'updated'));
        } catch (Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Reference User Status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit ReferenceUser
     *
     * @param ReferenceUser $reference_user
     * @return \Illuminate\View\View
     */
    public function edit(ReferenceUser $reference_user)
    {
        return view('reference_users.edit', compact('reference_user'));
    }

    /**
     * Update ReferenceUser
     *
     * @param Request $request
     * @param ReferenceUser $reference_user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(Request $request, ReferenceUser $reference_user)
    {
        $validationRules = [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:100',
            'mobile_number' => 'nullable|string|max:15',
        ];

        $validated = $request->validate($validationRules);

        try {
            $this->referenceUserService->updateReferenceUser($reference_user, $validated);
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User', 'updated'));
        } catch (Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Reference User', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete ReferenceUser
     *
     * @param ReferenceUser $reference_user
     * @return \Illuminate\Http\RedirectResponse
     */
    public function delete(ReferenceUser $reference_user)
    {
        try {
            $this->referenceUserService->deleteReferenceUser($reference_user);
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User', 'deleted'));
        } catch (Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Reference User', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Import ReferenceUsers
     *
     * @return \Illuminate\View\View
     */
    public function importReferenceUsers()
    {
        return view('reference_users.import');
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
            'filename' => 'reference_users',
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
