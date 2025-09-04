<?php

namespace App\Http\Controllers;

use App\Models\AddonCover;
use Illuminate\Http\Request;
use App\Exports\AddonCoversExport;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Validator;

class AddonCoverController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:addon-cover-list|addon-cover-create|addon-cover-edit|addon-cover-delete', ['only' => ['index']]);
        $this->middleware('permission:addon-cover-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:addon-cover-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:addon-cover-delete', ['only' => ['delete']]);
    }

    /**
     * List AddonCover 
     * @param Nill
     * @return Array $addon_covers
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $addon_cover_obj = AddonCover::select('*');
        if (!empty($request->search)) {
            $addon_cover_obj->where('name', 'LIKE', '%' . trim($request->search) . '%')
                           ->orWhere('description', 'LIKE', '%' . trim($request->search) . '%');
        }

        // Order by order_no first, then by name
        $addon_covers = $addon_cover_obj->orderBy('order_no', 'asc')
                                      ->orderBy('name', 'asc')
                                      ->paginate(10);
        return view('addon_covers.index', ['addon_covers' => $addon_covers]);
    }

    /**
     * Create AddonCover 
     * @param Nill
     * @return Array $addon_cover
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('addon_covers.add');
    }

    /**
     * Store AddonCover
     * @param Request $request
     * @return View AddonCovers
     * @author Darshan Baraiya
     */
    public function store(Request $request)
    {
        // Validations
        $validation_array = [
            'name' => 'required|string|max:255|unique:addon_covers,name',
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0', // 0 = auto-assign next available
            'status' => 'boolean',
        ];

        $request->validate($validation_array);
        DB::beginTransaction();

        try {
            // Store Data
            AddonCover::create([
                'name' => $request->name,
                'description' => $request->description,
                'order_no' => $request->order_no,
                'status' => $request->has('status') ? 1 : 0,
            ]);

            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->route('addon-covers.index')->with('success', 'Add-on Cover Created Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Update Status Of AddonCover
     * @param Integer $status
     * @return List Page With Success
     * @author Darshan Baraiya
     */
    public function updateStatus($addon_cover_id, $status)
    {
        // Validation
        $validate = Validator::make([
            'addon_cover_id' => $addon_cover_id,
            'status' => $status
        ], [
            'addon_cover_id' => 'required|exists:addon_covers,id',
            'status' => 'required|in:0,1',
        ]);

        // If Validations Fails
        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            DB::beginTransaction();

            // Update Status
            AddonCover::whereId($addon_cover_id)->update(['status' => $status]);

            // Commit And Redirect on index with Success Message
            DB::commit();
            return redirect()->back()->with('success', 'Add-on Cover Status Updated Successfully!');
        } catch (\Throwable $th) {
            // Rollback & Return Error Message
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Edit AddonCover
     * @param Integer $addon_cover
     * @return Collection $addon_cover
     * @author Darshan Baraiya
     */
    public function edit(AddonCover $addon_cover)
    {
        return view('addon_covers.edit')->with([
            'addon_cover' => $addon_cover
        ]);
    }

    /**
     * Update AddonCover
     * @param Request $request, AddonCover $addon_cover
     * @return View AddonCovers
     * @author Darshan Baraiya
     */
    public function update(Request $request, AddonCover $addon_cover)
    {
        // Validations
        $validation_array = [
            'name' => 'required|string|max:255|unique:addon_covers,name,' . $addon_cover->id,
            'description' => 'nullable|string',
            'order_no' => 'required|integer|min:0', // 0 = auto-assign next available
            'status' => 'boolean',
        ];

        $request->validate($validation_array);

        DB::beginTransaction();
        try {
            // Store Data
            AddonCover::whereId($addon_cover->id)->update([
                'name' => $request->name,
                'description' => $request->description,
                'order_no' => $request->order_no,
                'status' => $request->has('status') ? 1 : 0,
            ]);
            // Commit And Redirected To Listing
            DB::commit();
            return redirect()->route('addon-covers.index')->with('success', 'Add-on Cover Updated Successfully.');
        } catch (\Throwable $th) {
            // Rollback and return with Error
            DB::rollBack();
            return redirect()->back()->withInput()->with('error', $th->getMessage());
        }
    }

    /**
     * Delete AddonCover
     * @param AddonCover $addon_cover
     * @return Index AddonCovers
     * @author Darshan Baraiya
     */
    public function delete(AddonCover $addon_cover)
    {
        DB::beginTransaction();
        try {
            // Delete AddonCover
            AddonCover::whereId($addon_cover->id)->delete();

            DB::commit();
            return redirect()->back()->with('success', 'Add-on Cover Deleted Successfully!.');
        } catch (\Throwable $th) {
            DB::rollBack();
            return redirect()->back()->with('error', $th->getMessage());
        }
    }

    /**
     * Import AddonCovers 
     * @param Null
     * @return View File
     */
    public function importAddonCovers()
    {
        return view('addon_covers.import');
    }

    public function export()
    {
        return Excel::download(new AddonCoversExport, 'addon_covers.xlsx');
    }

}