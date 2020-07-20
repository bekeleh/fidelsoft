<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemPriceRequest;
use App\Models\ItemPrice;
use App\Ninja\Repositories\ItemPriceRepository;

/**
 * Class WarehouseApiController.
 */
class ItemPriceApiController extends BaseAPIController
{
    /**
     * @var string
     */
    protected $entityType = ENTITY_ITEM_PRICE;

    /**
     * @var ItemPriceRepository
     */
    protected $itemPriceRepo;

    /**
     * WarehouseApiController constructor.
     *
     * @param ItemPriceRepository $itemPriceRepo
     */
    public function __construct(ItemPriceRepository $itemPriceRepo)
    {
        parent::__construct();

        $this->itemPriceRepo = $itemPriceRepo;
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
        $itemPrices = ItemPrice::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemPrices);
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
     * @param ItemPriceRequest $request
     * @return
     */
    public function show(ItemPriceRequest $request)
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
     * @param ItemPriceRequest $request
     * @return mixed|null
     */
    public function store(ItemPriceRequest $request)
    {
        $itemPrice = $this->itemPriceRepo->save($request->input());

        return $this->itemResponse($itemPrice);
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
     * @param ItemPriceRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemPriceRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemPrice = $this->itemPriceRepo->save($data, $request->entity());

        return $this->itemResponse($itemPrice);
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
     * @param ItemPriceRequest $request
     * @return
     */
    public function destroy(ItemPriceRequest $request)
    {
        $itemPrice = $request->entity();

        $this->itemPriceRepo->delete($itemPrice);

        return $this->itemResponse($itemPrice);
    }
}
