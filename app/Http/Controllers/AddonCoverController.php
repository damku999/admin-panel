<?php

namespace App\Http\Controllers;

use App\Contracts\Services\AddonCoverServiceInterface;
use App\Models\AddonCover;
use App\Traits\ExportableTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

/**
 * Addon Cover Controller
 *
 * Handles AddonCover CRUD operations.
 * Inherits middleware setup and common utilities from AbstractBaseCrudController.
 */
class AddonCoverController extends AbstractBaseCrudController
{
    use ExportableTrait;

    public function __construct(
        private AddonCoverServiceInterface $addonCoverService
    ) {
        $this->setupPermissionMiddleware('addon-cover');
    }

    /**
     * List AddonCover
     *
     * @param Nill
     * @return array $addon_covers
     *
     * @author Darshan Baraiya
     */
    public function index(Request $request)
    {
        $addon_covers = $this->addonCoverService->getAddonCovers($request);

        return view('addon_covers.index', ['addon_covers' => $addon_covers]);
    }

    /**
     * Create AddonCover
     *
     * @param Nill
     * @return array $addon_cover
     *
     * @author Darshan Baraiya
     */
    public function create()
    {
        return view('addon_covers.add');
    }

    /**
     * Store AddonCover
     *
     * @return View AddonCovers
     *
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

            return $this->redirectWithSuccess('addon-covers.index',
                $this->getSuccessMessage('Add-on Cover', 'created'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Add-on Cover', 'create').': '.$th->getMessage())
                ->withInput();
        }
    }

    /**
     * Update Status Of AddonCover
     *
     * @param  int  $status
     * @return List Page With Success
     *
     * @author Darshan Baraiya
     */
    public function updateStatus($addon_cover_id, $status)
    {
        $validate = Validator::make([
            'addon_cover_id' => $addon_cover_id,
            'status' => $status,
        ], [
            'addon_cover_id' => 'required|exists:addon_covers,id',
            'status' => 'required|in:0,1',
        ]);

        if ($validate->fails()) {
            return $this->redirectWithError($validate->errors()->first());
        }

        try {
            $this->addonCoverService->updateStatus($addon_cover_id, $status);

            return $this->redirectWithSuccess('addon-covers.index',
                $this->getSuccessMessage('Add-on Cover status', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Add-on Cover status', 'update').': '.$th->getMessage());
        }
    }

    /**
     * Edit AddonCover
     *
     * @param  int  $addon_cover
     * @return Collection $addon_cover
     *
     * @author Darshan Baraiya
     */
    public function edit(AddonCover $addon_cover)
    {
        return view('addon_covers.edit')->with([
            'addon_cover' => $addon_cover,
        ]);
    }

    /**
     * Update AddonCover
     *
     * @param  Request  $request,  AddonCover $addon_cover
     * @return View AddonCovers
     *
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

            return $this->redirectWithSuccess('addon-covers.index',
                $this->getSuccessMessage('Add-on Cover', 'updated'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Add-on Cover', 'update').': '.$th->getMessage())
                ->withInput();
        }
    }

    /**
     * Delete AddonCover
     *
     * @return Index AddonCovers
     *
     * @author Darshan Baraiya
     */
    public function delete(AddonCover $addon_cover)
    {
        try {
            $this->addonCoverService->deleteAddonCover($addon_cover);

            return $this->redirectWithSuccess('addon-covers.index',
                $this->getSuccessMessage('Add-on Cover', 'deleted'));
        } catch (\Throwable $th) {
            return $this->redirectWithError(
                $this->getErrorMessage('Add-on Cover', 'delete').': '.$th->getMessage());
        }
    }

    /**
     * Import AddonCovers
     *
     * @param null
     * @return View File
     */
    public function importAddonCovers()
    {
        return view('addon_covers.import');
    }

    protected function getExportRelations(): array
    {
        return [];
    }

    protected function getSearchableFields(): array
    {
        return ['name', 'description'];
    }

    protected function getExportConfig(Request $request): array
    {
        return [
            'format' => $request->get('format', 'xlsx'),
            'filename' => 'addon_covers',
            'with_headings' => true,
            'auto_size' => true,
            'relations' => $this->getExportRelations(),
            'order_by' => ['column' => 'order_no', 'direction' => 'asc'],
            'headings' => ['ID', 'Name', 'Description', 'Order No', 'Status', 'Created Date'],
            'mapping' => function ($model) {
                return [
                    $model->id,
                    $model->name,
                    $model->description ?? 'N/A',
                    $model->order_no,
                    $model->status ? 'Active' : 'Inactive',
                    $model->created_at->format('Y-m-d H:i:s'),
                ];
            },
            'with_mapping' => true,
        ];
    }
}
