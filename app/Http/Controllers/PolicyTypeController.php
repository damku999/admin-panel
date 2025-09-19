<?php

namespace App\Http\Controllers;

use App\Models\PolicyType;
use App\Services\PolicyTypeService;
use Illuminate\Http\Request;
use App\Exports\PolicyTypesExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

/**
 * Policy Type Controller
 *
 * Handles PolicyType CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class PolicyTypeController extends AbstractBaseCrudController
{
    public function __construct(
        private PolicyTypeService $policyTypeService
    ) {
        $this->setupPermissionMiddleware('policy-type');
    }


    /**
     * List PolicyType 
     * @param Nill
     * @return Array $policy_type
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $policy_type_obj = PolicyType::select('*');
        if (!empty($request->search)) {
            $policy_type_obj->where('name', 'LIKE', '%' . trim($request->search) . '%');
        }

        $policy_type = $policy_type_obj->paginate(10);
        return view('policy_type.index', ['policy_type' => $policy_type]);
    }

    /**
     * Create PolicyType 
     * @param Nill
     * @return Array $policy_type
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('policy_type.add');
    }

    /**
     * Store PolicyType
     * @param Request $request
     * @return View PolicyTypes
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
            $this->policyTypeService->createPolicyType($validated);
            return $this->redirectWithSuccess('policy_type.index', $this->getSuccessMessage('Policy Type', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Policy Type', 'create') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of PolicyType
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($policy_type_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'policy_type_id'   => $policy_type_id,
            'status' => $status
        ], [
            'policy_type_id'   =>  'required|exists:policy_types,id',
            'status' =>  'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return $this->redirectWithError($validate->errors()->first());
        }

        try {
            $this->policyTypeService->updateStatus($policy_type_id, $status);
            return $this->redirectWithSuccess('policy_type.index', $this->getSuccessMessage('Policy Type Status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Policy Type Status', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Edit PolicyType
     * @param Integer $policy_type
     * @return Collection $policy_type
     * @author Darshan Baraiya
     */
    public function edit(PolicyType $policy_type)
    {
        return view('policy_type.edit')->with([
            'policy_type'  => $policy_type
        ]);
    }

    /**
     * Update PolicyType
     * @param Request $request, PolicyType $policy_type
     * @return View PolicyTypes
     * @author Darshan Baraiya
     */
    public function update(Request $request, PolicyType $policy_type)
    {
        // Validations
        $validation_array = [
            'name' => 'required',
        ];

        $validated = $request->validate($validation_array);

        try {
            $this->policyTypeService->updatePolicyType($policy_type, $validated);
            return $this->redirectWithSuccess('policy_type.index', $this->getSuccessMessage('Policy Type', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Policy Type', 'update') . ': ' . $th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete PolicyType
     * @param PolicyType $policy_type
     * @return Index PolicyTypes
     * @author Darshan Baraiya
     */
    public function delete(PolicyType $policy_type)
    {
        try {
            $this->policyTypeService->deletePolicyType($policy_type);
            return $this->redirectWithSuccess('policy_type.index', $this->getSuccessMessage('Policy Type', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError($this->getErrorMessage('Policy Type', 'delete') . ': ' . $th->getMessage());
        }
    }

    /**
     * Import PolicyTypes 
     * @param Null
     * @return View File
     */
    public function importPolicyTypes()
    {
        return view('policy_type.import');
    }

    public function export()
    {
        return Excel::download(new PolicyTypesExport, 'policy_type.xlsx');
    }
}
