<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferenceUser;
use App\Services\ReferenceUserService;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ReferenceUsersExport;
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

        $reference_users = $query->paginate(10);

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
            'name' => 'required',
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
            'name' => 'required',
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

    /**
     * Export ReferenceUsers
     *
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new ReferenceUsersExport, 'reference_users.xlsx');
    }
}
