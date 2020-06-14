<?php

namespace App\Http\Controllers;

use App\Http\Requests\StoreRequest;
use App\Models\Store;
use App\Ninja\Repositories\StoreRepository;

/**
 * Class StoreApiController.
 */
class StoreApiController extends BaseAPIController
{

    protected $entityType = ENTITY_STORE;
    protected $storeRepo;

    public function __construct(StoreRepository $storeRepo)
    {
        parent::__construct();

        $this->storeRepo = $storeRepo;
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
        $stores = Store::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($stores);
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
     * @param StoreRequest $request
     * @return
     */
    public function show(StoreRequest $request)
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
     * @param StoreRequest $request
     * @return
     */
    public function store(StoreRequest $request)
    {
        $store = $this->storeRepo->save($request->input());

        return $this->itemResponse($store);
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
     *     description="Updated store",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/Store"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param StoreRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(StoreRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $store = $this->storeRepo->save($data, $request->entity());

        return $this->itemResponse($store);
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
     * @param StoreRequest $request
     * @return
     */
    public function destroy(StoreRequest $request)
    {
        $store = $request->entity();

        $this->storeRepo->delete($store);

        return $this->itemResponse($store);
    }
}
