<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AddonCoverServiceInterface;
use App\Models\AddonCover;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class AddonCoverController extends Controller
{
    /**
     * Create a new controller instance.
     */
    public function __construct(
        private AddonCoverServiceInterface $addonCoverService
    ) {
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
        $addon_covers = $this->addonCoverService->getAddonCovers($request);
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
        $validationRules = $this->addonCoverService->getStoreValidationRules();
        $request->validate($validationRules);

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'order_no' => $request->order_no,
                'status' => $request->has('status') ? 1 : 0,
            ];
            
            $this->addonCoverService->createAddonCover($data);
            return redirect()->route('addon-covers.index')->with('success', 'Add-on Cover Created Successfully.');
        } catch (\Throwable $th) {
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
        $validate = Validator::make([
            'addon_cover_id' => $addon_cover_id,
            'status' => $status
        ], [
            'addon_cover_id' => 'required|exists:addon_covers,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validate->fails()) {
            return redirect()->back()->with('error', $validate->errors()->first());
        }

        try {
            $this->addonCoverService->updateStatus($addon_cover_id, $status);
            return redirect()->back()->with('success', 'Add-on Cover Status Updated Successfully!');
        } catch (\Throwable $th) {
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
        $validationRules = $this->addonCoverService->getUpdateValidationRules($addon_cover);
        $request->validate($validationRules);

        try {
            $data = [
                'name' => $request->name,
                'description' => $request->description,
                'order_no' => $request->order_no,
                'status' => $request->has('status') ? 1 : 0,
            ];
            
            $this->addonCoverService->updateAddonCover($addon_cover, $data);
            return redirect()->route('addon-covers.index')->with('success', 'Add-on Cover Updated Successfully.');
        } catch (\Throwable $th) {
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
        try {
            $this->addonCoverService->deleteAddonCover($addon_cover);
            return redirect()->back()->with('success', 'Add-on Cover Deleted Successfully!.');
        } catch (\Throwable $th) {
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
        return $this->addonCoverService->exportAddonCovers();
    }

}