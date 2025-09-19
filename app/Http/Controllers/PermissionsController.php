<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;

/**
 * Permissions Controller
 *
 * Handles Permission CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class PermissionsController extends AbstractBaseCrudController
{
    public function __construct()
    {
        $this->setupCustomPermissionMiddleware([
            ['permission' => 'permission-list|permission-create|permission-edit|permission-delete', 'only' => ['index']],
            ['permission' => 'permission-create', 'only' => ['create', 'store']],
            ['permission' => 'permission-edit', 'only' => ['edit', 'update']],
            ['permission' => 'permission-delete', 'only' => ['destroy']]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $permissions = Permission::paginate(10);

        return view('permissions.index', [
            'permissions' => $permissions
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('permissions.add');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'guard_name' => 'required'
            ]);

            DB::beginTransaction();
            Permission::create($request->all());
            DB::commit();

            return redirect()->back()->with('success',
                $this->getSuccessMessage('Permission', 'created'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Permission', 'create') . ': ' . $th->getMessage());
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = Permission::whereId($id)->first();

        return view('permissions.edit', ['permission' => $permission]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'guard_name' => 'required'
            ]);

            DB::beginTransaction();
            $permission = Permission::whereId($id)->first();
            $permission->name = $request->name;
            $permission->guard_name = $request->guard_name;
            $permission->save();
            DB::commit();

            return redirect()->back()->with('success',
                $this->getSuccessMessage('Permission', 'updated'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Permission', 'update') . ': ' . $th->getMessage());
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            Permission::whereId($id)->delete();
            DB::commit();

            return redirect()->back()->with('success',
                $this->getSuccessMessage('Permission', 'deleted'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Permission', 'delete') . ': ' . $th->getMessage());
        }
    }
}
