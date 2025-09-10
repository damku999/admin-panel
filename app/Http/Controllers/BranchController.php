<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\BranchesExport;

class BranchController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:branch-list|branch-create|branch-edit|branch-delete', ['only' => ['index']]);
        $this->middleware('permission:branch-create', ['only' => ['create', 'store', 'updateStatus']]);
        $this->middleware('permission:branch-edit', ['only' => ['edit', 'update']]);
        $this->middleware('permission:branch-delete', ['only' => ['delete']]);
    }

    /**
     * List Branches
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $search = $request->get('search');
        $branches = Branch::when($search, function ($query, $search) {
                return $query->where('name', 'LIKE', "%{$search}%");
            })
            ->orderBy('name')
            ->paginate(10);
            
        return view('branches.index', ['branches' => $branches, 'request' => $request->all()]);
    }

    /**
     * Create Branch
     * @return View
     */
    public function create(): View
    {
        return view('branches.add');
    }

    /**
     * Store Branch
     * @param Request $request
     * @return RedirectResponse
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name',
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        Branch::create([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'status' => 1,
            'created_by' => auth()->id(),
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch created successfully.');
    }

    /**
     * Edit Branch
     * @param Branch $branch
     * @return View
     */
    public function edit(Branch $branch): View
    {
        return view('branches.edit', compact('branch'));
    }

    /**
     * Update Branch
     * @param Request $request
     * @param Branch $branch
     * @return RedirectResponse
     */
    public function update(Request $request, Branch $branch): RedirectResponse
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:branches,name,' . $branch->id,
            'email' => 'nullable|email|max:255',
            'mobile_number' => 'nullable|string|max:20',
        ]);

        $branch->update([
            'name' => $request->name,
            'email' => $request->email,
            'mobile_number' => $request->mobile_number,
            'updated_by' => auth()->id(),
        ]);

        return redirect()->route('branches.index')->with('success', 'Branch updated successfully.');
    }

    /**
     * Update Branch Status
     * @param int $branch_id
     * @param int $status
     * @return RedirectResponse
     */
    public function updateStatus($branch_id, $status): RedirectResponse
    {
        $branch = Branch::findOrFail($branch_id);
        $branch->update([
            'status' => $status,
            'updated_by' => auth()->id(),
        ]);

        $message = $status == 1 ? 'Branch activated successfully.' : 'Branch deactivated successfully.';
        return redirect()->route('branches.index')->with('success', $message);
    }

    /**
     * Export Branches to Excel
     * @return \Symfony\Component\HttpFoundation\BinaryFileResponse
     */
    public function export()
    {
        return Excel::download(new BranchesExport, 'branches.xlsx');
    }
}