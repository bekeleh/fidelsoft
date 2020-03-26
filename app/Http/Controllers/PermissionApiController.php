<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionRequest;
use App\Models\Permission;
use App\Ninja\Repositories\PermissionRepository;
use App\Services\PermissionService;

class PermissionApiController extends BaseAPIController
{
    protected $permissionService;
    protected $permissionRepo;

    protected $entityType = ENTITY_USER;

    public function __construct(PermissionService $permissionService, PermissionRepository $permissionRepo)
    {
        parent::__construct();

        $this->permissionService = $permissionService;
        $this->permissionRepo = $permissionRepo;
    }

    /**
     * @SWG\Get(
     *   path="/permissions",
     *   summary="List permissions",
     *   operationId="listPermissions",
     *   tags={"user"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of permissions",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Permission"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $permissions = Permission::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($permissions);
    }

    /**
     * @SWG\Get(
     *   path="/permissions/{permission_id}",
     *   summary="Retrieve a user",
     *   operationId="getPermission",
     *   tags={"client"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="permission_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Permission"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PermissionRequest $request
     * @return
     */
    public function show(PermissionRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/permissions",
     *   summary="Create a user",
     *   operationId="createPermission",
     *   tags={"user"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="user",
     *     @SWG\Schema(ref="#/definitions/Permission")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Permission"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PermissionRequest $request
     * @return
     */
    public function store(PermissionRequest $request)
    {
        $unit = $this->permissionRepo->save($request->input());

        return $this->itemResponse($unit);
    }

    /**
     * @SWG\Put(
     *   path="/permissions/{permission_id}",
     *   summary="Update a user",
     *   operationId="updatePermission",
     *   tags={"user"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="permission_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="user",
     *     @SWG\Schema(ref="#/definitions/Permission")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Permission"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param PermissionRequest $request
     * @param $publicId
     * @return
     */
    public function update(PermissionRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $unit = $this->permissionRepo->save($data, $request->entity());

        return $this->itemResponse($unit);
    }

    /**
     * @SWG\Delete(
     *   path="/permissions/{permission_id}",
     *   summary="Delete a user",
     *   operationId="deletePermission",
     *   tags={"user"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="permission_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted user",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Permission"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PermissionRequest $request
     * @return
     */
    public function destroy(PermissionRequest $request)
    {
        $entity = $request->entity();

        $this->permissionRepo->delete($entity);

        return $this->itemResponse($entity);
    }
}
