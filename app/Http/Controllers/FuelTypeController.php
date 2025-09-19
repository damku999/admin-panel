<?php

namespace App\Http\Controllers;

use App\Exports\FuelTypesExport;
use App\Models\FuelType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

/**
 * Fuel Type Controller
 *
 * Handles FuelType CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class FuelTypeController extends AbstractBaseCrudController
{
    public function __construct()
    {
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

        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            // Store Data
            FuelType::create([
                'name' => $request->name,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type', 'created'));
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return $this->redirectWithError($this->getErrorMessage('Fuel Type', 'create') . ': ' . $th->getMessage());
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
            DB::beginTransaction();
            FuelType::whereId($fuel_type_id)->update(['status' => $status]);
            DB::commit();
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type Status', 'updated'));
        } catch (\Throwable $th) {
            DB::rollBack();
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

        $request->validate($validation_array);

        DB::beginTransaction();
        try {
            FuelType::whereId($fuel_type->id)->update([
                'name' => $request->name,
            ]);
            DB::commit();
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type', 'updated'));
        } catch (\Throwable $th) {
            DB::rollBack();
            return $this->redirectWithError($this->getErrorMessage('Fuel Type', 'update') . ': ' . $th->getMessage());
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
        DB::beginTransaction();
        try {
            FuelType::whereId($fuel_type->id)->delete();
            DB::commit();
            return $this->redirectWithSuccess('fuel_type.index', $this->getSuccessMessage('Fuel Type', 'deleted'));
        } catch (\Throwable $th) {
            DB::rollBack();
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

    public function export()
    {
        return Excel::download(new FuelTypesExport, 'fuel_type.xlsx');
    }
}
