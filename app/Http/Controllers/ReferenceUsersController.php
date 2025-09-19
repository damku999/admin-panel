<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ReferenceUser;
use Illuminate\Support\Facades\DB;
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
    public function __construct()
    {
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

        $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Store Data
            ReferenceUser::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User', 'created'));
        } catch (Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return $this->redirectWithError($this->getErrorMessage('Reference User', 'create') . ': ' . $th->getMessage());
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
            DB::beginTransaction();

            // Update Status
            ReferenceUser::whereId($reference_user_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User Status', 'updated'));
        } catch (Throwable $th) {
            // Rollback & Return Error Message
            DB::rollBack();
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

        $request->validate($validationRules);

        try {
            DB::beginTransaction();

            // Update Data
            $reference_user->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User', 'updated'));
        } catch (Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return $this->redirectWithError($this->getErrorMessage('Reference User', 'update') . ': ' . $th->getMessage());
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
            DB::beginTransaction();

            // Delete ReferenceUser
            $reference_user->delete();

            DB::commit();
            return $this->redirectWithSuccess('reference_users.index', $this->getSuccessMessage('Reference User', 'deleted'));
        } catch (Throwable $th) {
            DB::rollBack();
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
