<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemPriceRequest;
use App\Models\ItemPrice;
use App\Ninja\Repositories\ItemPriceRepository;

/**
 * Class StoreApiController.
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
     * StoreApiController constructor.
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
        $itemPrices = ItemPrice::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemPrices);
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
     * @param ItemPriceRequest $request
     * @return
     */
    public function show(ItemPriceRequest $request)
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
     * @param ItemPriceRequest $request
     * @return
     */
    public function store(ItemPriceRequest $request)
    {
        $itemPrice = $this->itemPriceRepo->save($request->input());

        return $this->itemResponse($itemPrice);
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
