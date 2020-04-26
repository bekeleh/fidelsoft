<?php

namespace App\Http\Controllers;

use App\Http\Requests\ApprovalStatusRequest;
use App\Models\ApprovalStatus;
use App\Ninja\Repositories\ApprovalStatusRepository;

/**
 * Class ApprovalStatusApiController.
 */
class ApprovalStatusApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_APPROVAL_STATUS;

    /**
     * @var ApprovalStatusRepository
     */
    protected $approvalStatusRepo;

    /**
     * ApprovalStatusApiController constructor.
     *
     * @param ApprovalStatusRepository $approvalStatusRepo
     */
    public function __construct(ApprovalStatusRepository $approvalStatusRepo)
    {
        parent::__construct();

        $this->approvalStatusRepo = $approvalStatusRepo;
    }

    /**
     * @SWG\Get(
     *   path="/approval_statuses",
     *   summary="List approval_statuses",
     *   operationId="listApprovalStatuss",
     *   tags={"approval status"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of approval_statuses",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ApprovalStatus"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $approvalStatuses = ApprovalStatus::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($approvalStatuses);
    }

    /**
     * @SWG\Get(
     *   path="/approval_statuses/{approval_status_id}",
     *   summary="Retrieve a approval status",
     *   operationId="getApprovalStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="approval_status_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ApprovalStatus"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ApprovalStatusRequest $request
     * @return
     */
    public function show(ApprovalStatusRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/approval_statuses",
     *   summary="Create a approval status",
     *   operationId="createApprovalStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/ApprovalStatus")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ApprovalStatus"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ApprovalStatusRequest $request
     * @return
     */
    public function store(ApprovalStatusRequest $request)
    {
        $approvalStatus = $this->approvalStatusRepo->save($request->input());

        return $this->itemResponse($approvalStatus);
    }

    /**
     * @SWG\Put(
     *   path="/approval_statuses/{approval_status_id}",
     *   summary="Update a approval status",
     *   operationId="updateApprovalStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="approval_status_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="approval status",
     *     @SWG\Schema(ref="#/definitions/ApprovalStatus")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ApprovalStatus"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ApprovalStatusRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ApprovalStatusRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $approvalStatus = $this->approvalStatusRepo->save($data, $request->entity());

        return $this->itemResponse($approvalStatus);
    }

    /**
     * @SWG\Delete(
     *   path="/approval_statuses/{approval_status_id}",
     *   summary="Delete a approval status",
     *   operationId="deleteApprovalStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="approval_status_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ApprovalStatus"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ApprovalStatusRequest $request
     * @return
     */
    public function destroy(ApprovalStatusRequest $request)
    {
        $approvalStatus = $request->entity();

        $this->approvalStatusRepo->delete($approvalStatus);

        return $this->itemResponse($approvalStatus);
    }
}
