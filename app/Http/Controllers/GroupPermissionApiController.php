<?php

namespace App\Http\Controllers;

use App\Http\Requests\PermissionGroupRequest;
use App\Models\PermissionGroup;
use App\Ninja\Repositories\PermissionGroupRepository;

/**
 * Class GroupPermissionApiController.
 */
class GroupPermissionApiController extends BaseAPIController
{

    protected $entityType = ENTITY_PERMISSION_GROUP;

    protected $groupRepo;

    public function __construct(PermissionGroupRepository $groupRepo)
    {
        parent::__construct();

        $this->groupRepo = $groupRepo;
    }

    /**
     * @SWG\Get(
     *   path="/permission_groups",
     *   summary="List permission_groups",
     *   operationId="listGroups",
     *   tags={"group"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of permission_groups",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/PermissionGroup"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $groups = PermissionGroup::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($groups);
    }

    /**
     * @SWG\Get(
     *   path="/permission_groups/{group_id}",
     *   summary="Retrieve a group",
     *   operationId="getGroup",
     *   tags={"group"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="group_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/PermissionGroup"))
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
     *   summary="Create a group",
     *   operationId="createGroup",
     *   tags={"group"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/PermissionGroup")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/PermissionGroup"))
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
        $group = $this->groupRepo->save($request->input());

        return $this->itemResponse($group);
    }

    /**
     * @SWG\Put(
     *   path="/permission_groups/{group_id}",
     *   summary="Update a group",
     *   operationId="updateGroup",
     *   tags={"group"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="group_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="group",
     *     @SWG\Schema(ref="#/definitions/PermissionGroup")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/PermissionGroup"))
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
        $group = $this->groupRepo->save($data, $request->entity());

        return $this->itemResponse($group);
    }

    /**
     * @SWG\Delete(
     *   path="/permission_groups/{group_id}",
     *   summary="Delete a group",
     *   operationId="deleteGroup",
     *   tags={"group"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="group_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/PermissionGroup"))
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
        $group = $request->entity();

        $this->groupRepo->delete($group);

        return $this->itemResponse($group);
    }
}
