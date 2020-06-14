<?php

namespace App\Http\Controllers;

use App\Http\Requests\StatusRequest;
use App\Models\Status;
use App\Ninja\Repositories\StatusRepository;

/**
 * Class StatusApiController.
 */
class StatusApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_STATUS;

    /**
     * @var StatusRepository
     */
    protected $StatusRepo;

    /**
     * StatusApiController constructor.
     *
     * @param StatusRepository $StatusRepo
     */
    public function __construct(StatusRepository $StatusRepo)
    {
        parent::__construct();

        $this->StatusRepo = $StatusRepo;
    }

    /**
     * @SWG\Get(
     *   path="/statuses",
     *   summary="List statuses",
     *   operationId="listStatuss",
     *   tags={"approval status"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of statuses",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Status"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $Statuses = Status::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($Statuses);
    }

    /**
     * @SWG\Get(
     *   path="/statuses/{status_id}",
     *   summary="Retrieve a approval status",
     *   operationId="getStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="status_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Status"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param StatusRequest $request
     * @return
     */
    public function show(StatusRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/statuses",
     *   summary="Create a approval status",
     *   operationId="createStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Status")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Status"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param StatusRequest $request
     * @return
     */
    public function store(StatusRequest $request)
    {
        $Status = $this->StatusRepo->save($request->input());

        return $this->itemResponse($Status);
    }

    /**
     * @SWG\Put(
     *   path="/statuses/{status_id}",
     *   summary="Update a approval status",
     *   operationId="updateStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="status_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="approval status",
     *     @SWG\Schema(ref="#/definitions/Status")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Status"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param StatusRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(StatusRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $Status = $this->StatusRepo->save($data, $request->entity());

        return $this->itemResponse($Status);
    }

    /**
     * @SWG\Delete(
     *   path="/statuses/{status_id}",
     *   summary="Delete a approval status",
     *   operationId="deleteStatus",
     *   tags={"approval status"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="status_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted approval status",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Status"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param StatusRequest $request
     * @return
     */
    public function destroy(StatusRequest $request)
    {
        $Status = $request->entity();

        $this->StatusRepo->delete($Status);

        return $this->itemResponse($Status);
    }
}
