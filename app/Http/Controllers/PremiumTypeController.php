<?php

namespace App\Http\Controllers;

use App\Models\PremiumType;
use Illuminate\Http\Request;
use App\Exports\PremiumTypesExport;
use Illuminate\Support\Facades\DB;
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
    public function __construct()
    {
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

        $request->validate($validation_array);
        if ($request->input('is_vehicle') && $request->input('is_life_insurance_policies')) {
            return $this->redirectWithError('Both "Is it for Vehicle?" and "Is Life Insurance Policies?" cannot be true at the same time.');
        }
        DB::beginTransaction();

        try {
            // Store Data
            PremiumType::create([
                'name' => $request->name,
                'is_vehicle' => $request->is_vehicle,
                'is_life_insurance_policies' => $request->is_life_insurance_policies,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type', 'created'));
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return $this->redirectWithError($this->getErrorMessage('Premium Type', 'create') . ': ' . $th->getMessage());
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
            DB::beginTransaction();

            // Update Status
            PremiumType::whereId($premium_type_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type Status', 'updated'));
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
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

        $request->validate($validation_array);
        if ($request->input('is_vehicle') && $request->input('is_life_insurance_policies')) {
            return $this->redirectWithError('Both "Is it for Vehicle?" and "Is Life Insurance Policies?" cannot be true at the same time.');
        }
        DB::beginTransaction();
        try {
            // Store Data
            PremiumType::whereId($premium_type->id)->update([
                'is_vehicle' => $request->is_vehicle,
                'name' => $request->name,
                'is_life_insurance_policies' => $request->is_life_insurance_policies,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type', 'updated'));
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return $this->redirectWithError($this->getErrorMessage('Premium Type', 'update') . ': ' . $th->getMessage());
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
        DB::beginTransaction();
        try {
            // Delete PremiumType
            PremiumType::whereId($premium_type->id)->delete();

            DB::commit();
            return $this->redirectWithSuccess('premium_type.index', $this->getSuccessMessage('Premium Type', 'deleted'));
        } catch (\Throwable $th) {
            DB::rollBack();
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
}
