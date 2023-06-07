<?php

namespace App\Http\Controllers;

use App\Models\PolicyType;
use Illuminate\Http\Request;
use App\Exports\PolicyTypesExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class PolicyTypeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:policy-type-list|policy-type-create|policy-type-edit|policy-type-delete', ['only' => ['index']]);
        $this->middleware('permission:policy-type-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:policy-type-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:policy-type-delete', ['only' => ['delete']]);
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

        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            // Store Data
            PolicyType::create([
                'name' => $request->name,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->route('policy_type.index')->with('success', 'PolicyType Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
            return redirect()->route('policy_type.index')->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            PolicyType::whereId($policy_type_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return redirect()->route('policy_type.index')->with('success', 'PolicyType Status Updated Successfully!');
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
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

        $request->validate($validation_array);

        DB::beginTransaction($validation_array);
        try {
            // Store Data
            PolicyType::whereId($policy_type->id)->update([
                'name' => $request->name,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->route('policy_type.index')->with('success', 'PolicyType Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
        DB::beginTransaction();
        try {
            // Delete PolicyType
            PolicyType::whereId($policy_type->id)->delete();

            DB::commit();
            return redirect()->route('policy_type.index')->with('success', 'PolicyType Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
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
