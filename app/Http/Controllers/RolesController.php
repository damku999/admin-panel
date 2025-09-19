<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

/**
 * Roles Controller
 *
 * Handles Role CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class RolesController extends AbstractBaseCrudController
{
    public function __construct()
    {
        $this->setupCustomPermissionMiddleware([
            ['permission' => 'role-list|role-create|role-edit|role-delete', 'only' => ['index']],
            ['permission' => 'role-create', 'only' => ['create','store']],
            ['permission' => 'role-edit', 'only' => ['edit','update']],
            ['permission' => 'role-delete', 'only' => ['destroy']]
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $roles = Role::paginate(10);

        return view('roles.index', [
            'roles' => $roles
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = Permission::all();

        return view('roles.add', ['permissions' => $permissions]);
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
            Role::create($request->all());
            DB::commit();

            return redirect()->back()->with('success',
                $this->getSuccessMessage('Role', 'created'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Role', 'create') . ': ' . $th->getMessage());
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
        $role = Role::whereId($id)->with('permissions')->first();

        $permissions = Permission::all();

        return view('roles.edit', ['role' => $role, 'permissions' => $permissions]);
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
            $role = Role::whereId($id)->first();
            $role->name = $request->name;
            $role->guard_name = $request->guard_name;
            $role->save();

            // Sync Permissions
            $permissions = $request->permissions;
            $role->syncPermissions($permissions);
            DB::commit();

            return redirect()->back()->with('success',
                $this->getSuccessMessage('Role', 'updated'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Role', 'update') . ': ' . $th->getMessage());
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
            Role::whereId($id)->delete();
            DB::commit();

            return redirect()->back()->with('success',
                $this->getSuccessMessage('Role', 'deleted'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Role', 'delete') . ': ' . $th->getMessage());
        }
    }
}
