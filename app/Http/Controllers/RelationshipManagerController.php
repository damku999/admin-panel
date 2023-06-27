<?php

namespace App\Http\Controllers;

use App\Models\RelationshipManager;
use Illuminate\Http\Request;
use App\Exports\RelationshipManagersExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class RelationshipManagerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:relationship_manager-list|relationship_manager-create|relationship_manager-edit|relationship_manager-delete', ['only' => ['index']]);
        $this->middleware('permission:relationship_manager-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:relationship_manager-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:relationship_manager-delete', ['only' => ['delete']]);
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

        $relationship_managers = $relationship_manager_obj->paginate(10);
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


        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            // Store Data
            $relationship_manager = RelationshipManager::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'RelationshipManager Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            RelationshipManager::whereId($relationship_manager_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return redirect()->back()->with('success', 'RelationshipManager Status Updated Successfully!');
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
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

        $request->validate($validation_array);

        DB::beginTransaction($validation_array);
        try {
            // Store Data
            $relationship_manager_updated = RelationshipManager::whereId($relationship_manager->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'RelationshipManager Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
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
        DB::beginTransaction();
        try {
            // Delete RelationshipManager
            RelationshipManager::whereId($relationship_manager->id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'RelationshipManager Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
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


    public function export()
    {
        return Excel::download(new RelationshipManagersExport, 'relationship_managers.xlsx');
    }
}
