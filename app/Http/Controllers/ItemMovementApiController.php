<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemMovementRequest;
use App\Models\ItemMovement;
use App\Ninja\Repositories\ItemMovementRepository;

/**
 * Class WarehouseApiController.
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
     * WarehouseApiController constructor.
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
     *   path="/warehouses",
     *   summary="List warehouses",
     *   operationId="listWarehouses",
     *   tags={"warehouse"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of warehouses",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/Warehouse"))
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
     *   path="/warehouses/{warehouse_id}",
     *   summary="Retrieve a warehouse",
     *   operationId="getWarehouse",
     *   tags={"warehouse"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="warehouse_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single warehouse",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
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
     *   path="/warehouses",
     *   summary="Create a warehouse",
     *   operationId="createWarehouse",
     *   tags={"warehouse"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/Warehouse")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New warehouse",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
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
     *   path="/warehouses/{warehouse_id}",
     *   summary="Update a warehouse",
     *   operationId="updateWarehouse",
     *   tags={"warehouse"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="warehouse_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="warehouse",
     *     @SWG\Schema(ref="#/definitions/Warehouse")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated item warehouse",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
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
     *   path="/warehouses/{warehouse_id}",
     *   summary="Delete a warehouse",
     *   operationId="deleteWarehouse",
     *   tags={"warehouse"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="warehouse_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted warehouse",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Warehouse"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemMovementRequest $request
     * @return mixed
     */
    public function destroy(ItemMovementRequest $request)
    {
        $itemMovement = $request->entity();

        $this->itemMovementRepo->delete($itemMovement);

        return $this->itemResponse($itemMovement);
    }
}
