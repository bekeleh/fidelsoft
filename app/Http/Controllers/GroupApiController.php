<?php

namespace App\Http\Controllers;

use App\Http\Requests\GroupRequest;
use App\Models\Group;
use App\Ninja\Repositories\GroupRepository;

/**
 * Class GroupApiController.
 */
class GroupApiController extends BaseAPIController
{

    protected $entityType = ENTITY_GROUP;

    protected $groupRepo;

    public function __construct(GroupRepository $groupRepo)
    {
        parent::__construct();

        $this->groupRepo = $groupRepo;
    }

    /**
     * @SWG\Get(
     *   path="/groups",
     *   summary="List groups",
     *   operationId="listGroups",
     *   tags={"group"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of groups",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Group"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $groups = Group::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($groups);
    }

    /**
     * @SWG\Get(
     *   path="/groups/{group_id}",
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
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Group"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param GroupRequest $request
     * @return
     */
    public function show(GroupRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/groups",
     *   summary="Create a group",
     *   operationId="createGroup",
     *   tags={"group"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Group")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Group"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param GroupRequest $request
     * @return
     */
    public function store(GroupRequest $request)
    {
        $group = $this->groupRepo->save($request->input());

        return $this->itemResponse($group);
    }

    /**
     * @SWG\Put(
     *   path="/groups/{group_id}",
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
     *     @SWG\Schema(ref="#/definitions/Group")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated group",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Group"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param GroupRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(GroupRequest $request, $publicId)
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
     *   path="/groups/{group_id}",
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
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Group"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param GroupRequest $request
     * @return
     */
    public function destroy(GroupRequest $request)
    {
        $group = $request->entity();

        $this->groupRepo->delete($group);

        return $this->itemResponse($group);
    }
}
