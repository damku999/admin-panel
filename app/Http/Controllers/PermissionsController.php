<?php

namespace App\Http\Controllers;

use App\Contracts\Repositories\PermissionRepositoryInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/**
 * Permissions Controller
 *
 * Handles Permission CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class PermissionsController extends AbstractBaseCrudController
{
    /**
     * Permission Repository instance
     */
    private PermissionRepositoryInterface $permissionRepository;

    public function __construct(PermissionRepositoryInterface $permissionRepository)
    {
        $this->permissionRepository = $permissionRepository;
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
    public function index(Request $request)
    {
        $permissions = $this->permissionRepository->getPermissionsWithFilters($request, 10);

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
            $this->permissionRepository->create($request->all());
            DB::commit();

            return $this->redirectWithSuccess('permissions.index',
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
        $permission = $this->permissionRepository->getPermissionWithRoles($id);

        if (!$permission) {
            return $this->redirectWithError(
                $this->getErrorMessage('Permission', 'find'));
        }

        return view('permissions.show', ['permission' => $permission]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $permission = $this->permissionRepository->findById($id);

        if (!$permission) {
            return $this->redirectWithError(
                $this->getErrorMessage('Permission', 'find'));
        }

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

            $permission = $this->permissionRepository->findById($id);
            if (!$permission) {
                return $this->redirectWithError(
                    $this->getErrorMessage('Permission', 'find'));
            }

            DB::beginTransaction();
            $this->permissionRepository->update($permission, $request->only(['name', 'guard_name']));
            DB::commit();

            return $this->redirectWithSuccess('permissions.index',
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
            $permission = $this->permissionRepository->findById($id);
            if (!$permission) {
                return $this->redirectWithError(
                    $this->getErrorMessage('Permission', 'find'));
            }

            DB::beginTransaction();
            $this->permissionRepository->delete($permission);
            DB::commit();

            return $this->redirectWithSuccess('permissions.index',
                $this->getSuccessMessage('Permission', 'deleted'));
        } catch (\Throwable $th) {
            DB::rollback();
            return $this->redirectWithError(
                $this->getErrorMessage('Permission', 'delete') . ': ' . $th->getMessage());
        }
    }
}
