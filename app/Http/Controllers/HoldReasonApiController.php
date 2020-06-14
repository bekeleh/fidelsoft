<?php

namespace App\Http\Controllers;

use App\Http\Requests\HoldReasonRequest;
use App\Models\HoldReason;
use App\Ninja\Repositories\HoldReasonRepository;

/**
 * Class HoldReasonApiController.
 */
class HoldReasonApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_HOLD_REASON;

    /**
     * @var HoldReasonRepository
     */
    protected $holdReasonRepo;

    /**
     * HoldReasonApiController constructor.
     *
     * @param HoldReasonRepository $holdReasonRepo
     */
    public function __construct(HoldReasonRepository $holdReasonRepo)
    {
        parent::__construct();

        $this->holdReasonRepo = $holdReasonRepo;
    }

    /**
     * @SWG\Get(
     *   path="/hold_reasons",
     *   summary="List hold_reasons",
     *   operationId="listHoldReasons",
     *   tags={"hold reason"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of hold_reasons",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/HoldReason"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $itemCategories = HoldReason::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemCategories);
    }

    /**
     * @SWG\Get(
     *   path="/hold_reasons/{hold_reason_id}",
     *   summary="Retrieve a hold reason",
     *   operationId="getHoldReason",
     *   tags={"hold reason"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="hold_reason_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single hold reason",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/HoldReason"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param HoldReasonRequest $request
     * @return
     */
    public function show(HoldReasonRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/hold_reasons",
     *   summary="Create a hold reason",
     *   operationId="createHoldReason",
     *   tags={"hold reason"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/HoldReason")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New hold reason",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/HoldReason"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param HoldReasonRequest $request
     * @return
     */
    public function store(HoldReasonRequest $request)
    {
        $holdReason = $this->holdReasonRepo->save($request->input());

        return $this->itemResponse($holdReason);
    }

    /**
     * @SWG\Put(
     *   path="/hold_reasons/{hold_reason_id}",
     *   summary="Update a hold reason",
     *   operationId="updateHoldReason",
     *   tags={"hold reason"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="hold_reason_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="hold reason",
     *     @SWG\Schema(ref="#/definitions/HoldReason")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated hold reason",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/HoldReason"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param HoldReasonRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(HoldReasonRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $holdReason = $this->holdReasonRepo->save($data, $request->entity());

        return $this->itemResponse($holdReason);
    }

    /**
     * @SWG\Delete(
     *   path="/hold_reasons/{hold_reason_id}",
     *   summary="Delete a hold reason",
     *   operationId="deleteHoldReason",
     *   tags={"hold reason"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="hold_reason_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted hold reason",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/HoldReason"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param HoldReasonRequest $request
     * @return
     */
    public function destroy(HoldReasonRequest $request)
    {
        $holdReason = $request->entity();

        $this->holdReasonRepo->delete($holdReason);

        return $this->itemResponse($holdReason);
    }
}
