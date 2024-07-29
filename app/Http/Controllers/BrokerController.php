<?php

namespace App\Http\Controllers;

use App\Exports\BrokersExport;
use App\Models\Broker;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class BrokerController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:broker-list|broker-create|broker-edit|broker-delete', ['only' => ['index']]);
        $this->middleware('permission:broker-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:broker-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:broker-delete', ['only' => ['delete']]);
    }

    /**
     * List Broker
     * @param Nill
     * @return Array $broker
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $broker_obj = Broker::select('*');
        if (!empty($request->search)) {
            $broker_obj->where('name', 'LIKE', '%' . trim($request->search) . '%')->orWhere('email', 'LIKE', '%' . trim($request->search) . '%')->orWhere('mobile_number', 'LIKE', '%' . trim($request->search) . '%');
        }

        $brokers = $broker_obj->paginate(10);
        return view('brokers.index', ['brokers' => $brokers, 'request' => $request->all()]);
    }

    /**
     * Create Broker
     * @param Nill
     * @return Array $broker
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('brokers.add');
    }

    /**
     * Store Broker
     * @param Request $request
     * @return View Brokers
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
            $broker = Broker::create([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'Broker Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Update Status Of Broker
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($broker_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'broker_id' => $broker_id,
            'status' => $status,
        ], [
            'broker_id' => 'required|exists:brokers,id',
            'status' => 'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            Broker::whereId($broker_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return redirect()->back()->with('success', 'Broker Status Updated Successfully!');
        } catch (\Throwable $th) {

            // Rollback & Return Error Message
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Edit Broker
     * @param Integer $broker
     * @return Collection $broker
     * @author Darshan Baraiya
     */
    public function edit(Broker $broker)
    {
        return view('brokers.edit')->with([
            'broker' => $broker,
        ]);
    }

    /**
     * Update Broker
     * @param Request $request, Broker $broker
     * @return View Brokers
     * @author Darshan Baraiya
     */
    public function update(Request $request, Broker $broker)
    {
        // Validations
        $validation_array = [
            'name' => 'required',
        ];

        $request->validate($validation_array);

        DB::beginTransaction($validation_array);
        try {
            // Store Data
            $broker_updated = Broker::whereId($broker->id)->update([
                'name' => $request->name,
                'email' => $request->email,
                'mobile_number' => $request->mobile_number,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->back()->with('success', 'Broker Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Delete Broker
     * @param Broker $broker
     * @return Index Brokers
     * @author Darshan Baraiya
     */
    public function delete(Broker $broker)
    {
        DB::beginTransaction();
        try {
            // Delete Broker
            Broker::whereId($broker->id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Broker Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import Brokers
     * @param Null
     * @return View File
     */
    public function importBrokers()
    {
        return view('brokers.import');
    }

    public function export()
    {
        return Excel::download(new BrokersExport, 'brokers.xlsx');
    }
}
