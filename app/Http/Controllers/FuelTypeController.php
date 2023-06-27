<?php

namespace App\Http\Controllers;

use App\Models\FuelType;
use Illuminate\Http\Request;
use App\Exports\FuelTypesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class FuelTypeController extends Controller
{
    /**
     * Create a new controller instance.
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:fuel-type-list|fuel-type-create|fuel-type-edit|fuel-type-delete', ['only' => ['index']]);
        $this->middleware('permission:fuel-type-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:fuel-type-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:fuel-type-delete', ['only' => ['delete']]);
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
        return view('fuel_type.index', ['fuel_type' => $fuel_type]);
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
            return redirect()->back()->with('success', 'Fuel Type Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
            'fuel_type_id'   => $fuel_type_id,
            'status' => $status
        ], [
            'fuel_type_id'   =>  'required|exists:fuel_types,id',
            'status' =>  'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();
            FuelType::whereId($fuel_type_id)->update(['status' => $status]);
            DB::commit();
            return redirect()->back()->with('success', 'Fuel Type Status Updated Successfully!');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
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
            'fuel_type'  => $fuel_type
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

        DB::beginTransaction($validation_array);
        try {
            FuelType::whereId($fuel_type->id)->update([
                'name' => $request->name,
            ]);
            DB::commit();
            return redirect()->back()->with('success', 'Fuel Type Updated Successfully.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
            return redirect()->back()->with('success', 'Fuel Type Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
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
