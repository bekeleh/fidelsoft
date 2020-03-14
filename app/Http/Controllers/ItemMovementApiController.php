<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemMovementRequest;
use App\Models\ItemMovement;
use App\Ninja\Repositories\ItemMovementRepository;

/**
 * Class StoreApiController.
 */
class ItemMovementApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_ITEM_MOVEMENT;

    /**
     * @var ItemMovementRepository
     */
    protected $itemMovementRepo;

    /**
     * StoreApiController constructor.
     *
     * @param ItemMovementRepository $itemMovementRepo
     */
    public function __construct(ItemMovementRepository $itemMovementRepo)
    {
        parent::__construct();

        $this->itemMovementRepo = $itemMovementRepo;
    }

    /**
     * @SWG\Get(
     *   path="/stores",
     *   summary="List stores",
     *   operationId="listStores",
     *   tags={"store"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of stores",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Store"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $itemMovements = ItemMovement::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemMovements);
    }

    /**
     * @SWG\Get(
     *   path="/stores/{store_id}",
     *   summary="Retrieve a store",
     *   operationId="getStore",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="store_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Store"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemMovementRequest $request
     * @return
     */
    public function show(ItemMovementRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/stores",
     *   summary="Create a store",
     *   operationId="createStore",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Store")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Store"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemMovementRequest $request
     * @return
     */
    public function store(ItemMovementRequest $request)
    {
        $itemMovement = $this->itemMovementRepo->save($request->input());

        return $this->itemResponse($itemMovement);
    }

    /**
     * @SWG\Put(
     *   path="/stores/{store_id}",
     *   summary="Update a store",
     *   operationId="updateStore",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="store_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="store",
     *     @SWG\Schema(ref="#/definitions/Store")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated item store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Store"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ItemMovementRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemMovementRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemMovement = $this->itemMovementRepo->save($data, $request->entity());

        return $this->itemResponse($itemMovement);
    }

    /**
     * @SWG\Delete(
     *   path="/stores/{store_id}",
     *   summary="Delete a store",
     *   operationId="deleteStore",
     *   tags={"store"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="store_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Store"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemMovementRequest $request
     * @return
     */
    public function destroy(ItemMovementRequest $request)
    {
        $itemMovement = $request->entity();

        $this->itemMovementRepo->delete($itemMovement);

        return $this->itemResponse($itemMovement);
    }
}
