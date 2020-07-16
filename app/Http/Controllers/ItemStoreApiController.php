<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemStoreRequest;
use App\Models\ItemStore;
use App\Ninja\Repositories\ItemStoreRepository;

/**
 * Class WarehouseApiController.
 */
class ItemStoreApiController extends BaseAPIController
{

    protected $entityType = ENTITY_ITEM_STORE;


    protected $itemStoreRepo;

    /**
     * WarehouseApiController constructor.
     *
     * @param ItemStoreRepository $itemStoreRepo
     */
    public function __construct(ItemStoreRepository $itemStoreRepo)
    {
        parent::__construct();

        $this->itemStoreRepo = $itemStoreRepo;
    }

    /**
     * @SWG\Get(
     *   path="/warehouses",
     *   summary="List warehouses",
     *   operationId="listStores",
     *   tags={"store"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of warehouses",
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
        $itemStores = ItemStore::scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemStores);
    }

    /**
     * @SWG\Get(
     *   path="/warehouses/{store_id}",
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
     * @param ItemStoreRequest $request
     * @return
     */
    public function show(ItemStoreRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/warehouses",
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
     * @param ItemStoreRequest $request
     * @return
     */
    public function store(ItemStoreRequest $request)
    {
        $itemStore = $this->itemStoreRepo->save($request->input());

        return $this->itemResponse($itemStore);
    }

    /**
     * @SWG\Put(
     *   path="/warehouses/{store_id}",
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
     * @param ItemStoreRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemStoreRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemStore = $this->itemStoreRepo->save($data, $request->entity());

        return $this->itemResponse($itemStore);
    }

    /**
     * @SWG\Delete(
     *   path="/warehouses/{store_id}",
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
     * @param ItemStoreRequest $request
     * @return
     */
    public function destroy(ItemStoreRequest $request)
    {
        $itemStore = $request->entity();

        $this->itemStoreRepo->delete($itemStore);

        return $this->itemResponse($itemStore);
    }
}
