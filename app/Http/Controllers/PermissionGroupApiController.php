<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionGroupRequest;
use App\Models\PermissionGroup;
use App\Ninja\Repositories\PermissionGroupRepository;

/**
 * Class PermissionGroupApiController.
 */
class PermissionGroupApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_PERMISSION_GROUP;

    /**
     * @var PermissionGroupRepository
     */
    protected $permissionGroupRepo;

    /**
     * permissionGroupApiController constructor.
     *
     * @param PermissionGroupRepository $permissionGroupRepo
     */
    public function __construct(PermissionGroupRepository $permissionGroupRepo)
    {
        parent::__construct();

        $this->permissionGroupRepo = $permissionGroupRepo;
    }

    /**
     * @SWG\Get(
     *   path="/permission_groups",
     *   summary="List permission_groups",
     *   operationId="listpermissionGroups",
     *   tags={"permission group"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of permission_groups",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/permissionGroup"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $permissionGroups = PermissionGroup::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($permissionGroups);
    }

    /**
     * @SWG\Get(
     *   path="/permission_groups/{permission_group_id}",
     *   summary="Retrieve a permission group",
     *   operationId="getpermissionGroup",
     *   tags={"permission group"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="permission_group_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single permission group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/permissionGroup"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PermissionGroupRequest $request
     * @return
     */
    public function show(PermissionGroupRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/permission_groups",
     *   summary="Create a permission group",
     *   operationId="createpermissionGroup",
     *   tags={"permission group"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/permissionGroup")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New permission group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/permissionGroup"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PermissionGroupRequest $request
     * @return
     */
    public function store(PermissionGroupRequest $request)
    {
        $permissionGroup = $this->permissionGroupRepo->save($request->input());

        return $this->itemResponse($permissionGroup);
    }

    /**
     * @SWG\Put(
     *   path="/permission_groups/{permission_group_id}",
     *   summary="Update a permission group",
     *   operationId="updatepermissionGroup",
     *   tags={"permission group"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="permission_group_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="permission group",
     *     @SWG\Schema(ref="#/definitions/permissionGroup")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated permission group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/permissionGroup"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param PermissionGroupRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(PermissionGroupRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $permissionGroup = $this->permissionGroupRepo->save($data, $request->entity());

        return $this->itemResponse($permissionGroup);
    }

    /**
     * @SWG\Delete(
     *   path="/permission_groups/{permission_group_id}",
     *   summary="Delete a permission group",
     *   operationId="deletePermissionGroup",
     *   tags={"permission group"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="permission_group_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted permission group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/permissionGroup"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param PermissionGroupRequest $request
     * @return
     */
    public function destroy(PermissionGroupRequest $request)
    {
        $permissionGroup = $request->entity();

        $this->permissionGroupRepo->delete($permissionGroup);

        return $this->itemResponse($permissionGroup);
    }
}
