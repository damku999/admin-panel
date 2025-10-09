<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\PermissionRepositoryInterface;
use App\Contracts\Repositories\RoleRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Roles Controller
 *
 * Handles Role CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class RolesController extends AbstractBaseCrudController
{
    /**
     * Role Repository instance
     */
    private RoleRepositoryInterface $roleRepository;

    /**
     * Permission Repository instance
     */
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(
        RoleRepositoryInterface $roleRepository,
        PermissionRepositoryInterface $permissionRepository
    ) {
        $this->roleRepository = $roleRepository;
        $this->permissionRepository = $permissionRepository;
        $this->setupCustomPermissionMiddleware([
            ['permission' => 'role-list|role-create|role-edit|role-delete', 'only' => ['index']],
            ['permission' => 'role-create', 'only' => ['create', 'store']],
            ['permission' => 'role-edit', 'only' => ['edit', 'update']],
            ['permission' => 'role-delete', 'only' => ['destroy']],
        ]);
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $roles = $this->roleRepository->getRolesWithFilters($request, 10);

        return view('roles.index', [
            'roles' => $roles,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $permissions = $this->permissionRepository->getPermissionsByGuard();

        return view('roles.add', ['permissions' => $permissions]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        try {
            $request->validate([
                'name' => 'required',
                'guard_name' => 'required',
            ]);

            DB::beginTransaction();
            $this->roleRepository->create($request->all());
            DB::commit();

            return $this->redirectWithSuccess('roles.index',
                $this->getSuccessMessage('Role', 'created'));
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->redirectWithError(
                $this->getErrorMessage('Role', 'create').': '.$th->getMessage());
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
        $role = $this->roleRepository->getRoleWithPermissions($id);

        if (! $role) {
            return $this->redirectWithError('Role not found.');
        }

        return view('roles.show', ['role' => $role]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $role = $this->roleRepository->getRoleWithPermissions($id);

        if (! $role) {
            return $this->redirectWithError('Role not found.');
        }

        $permissions = $this->permissionRepository->getPermissionsByGuard($role->guard_name);

        return view('roles.edit', ['role' => $role, 'permissions' => $permissions]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        try {
            $request->validate([
                'name' => 'required',
                'guard_name' => 'required',
            ]);

            DB::beginTransaction();
            $role = $this->roleRepository->findById($id);

            if (! $role) {
                DB::rollback();

                return $this->redirectWithError('Role not found.');
            }

            // Update role data
            $roleData = [
                'name' => $request->name,
                'guard_name' => $request->guard_name,
            ];
            $this->roleRepository->update($role, $roleData);

            // Sync Permissions - using the model's native method
            $permissions = $request->permissions ?? [];
            $role->syncPermissions($permissions);
            DB::commit();

            return $this->redirectWithSuccess('roles.index',
                $this->getSuccessMessage('Role', 'updated'));
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->redirectWithError(
                $this->getErrorMessage('Role', 'update').': '.$th->getMessage());
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
            $role = $this->roleRepository->findById($id);

            if (! $role) {
                DB::rollback();

                return $this->redirectWithError('Role not found.');
            }

            $this->roleRepository->delete($role);
            DB::commit();

            return $this->redirectWithSuccess('roles.index',
                $this->getSuccessMessage('Role', 'deleted'));
        } catch (\Throwable $th) {
            DB::rollback();

            return $this->redirectWithError(
                $this->getErrorMessage('Role', 'delete').': '.$th->getMessage());
        }
    }
}
