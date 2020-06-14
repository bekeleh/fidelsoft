<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemTransferRequest;
use App\Models\ItemTransfer;
use App\Ninja\Repositories\ItemTransferRepository;

/**
 * Class ItemTransferApiController.
 */
class ItemTransferApiController extends BaseAPIController
{

    protected $entityType = ENTITY_ITEM_TRANSFER;


    protected $itemTransferRepo;

    /**
     * ItemTransferApiController constructor.
     *
     * @param ItemTransferRepository $itemTransferRepo
     */
    public function __construct(ItemTransferRepository $itemTransferRepo)
    {
        parent::__construct();

        $this->itemTransferRepo = $itemTransferRepo;
    }

    /**
     * @SWG\Get(
     *   path="/item_transfers",
     *   summary="List item_transfers",
     *   operationId="listItemTransfers",
     *   tags={"item_transfer"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of item_transfers",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ItemTransfer"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $itemTransfers = ItemTransfer::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemTransfers);
    }

    /**
     * @SWG\Get(
     *   path="/item_transfers/{item_transfer_id}",
     *   summary="Retrieve a item_transfer",
     *   operationId="getItemTransfer",
     *   tags={"item_transfer"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_transfer_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single item_transfer",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemTransfer"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemTransferRequest $request
     * @return
     */
    public function show(ItemTransferRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/item_transfers",
     *   summary="Create a item_transfer",
     *   operationId="createItemTransfer",
     *   tags={"item_transfer"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/ItemTransfer")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New item_transfer",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemTransfer"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemTransferRequest $request
     * @return
     */
    public function store(ItemTransferRequest $request)
    {
        $itemTransfer = $this->itemTransferRepo->save($request->input());

        return $this->itemResponse($itemTransfer);
    }

    /**
     * @SWG\Put(
     *   path="/item_transfers/{item_transfer_id}",
     *   summary="Update a item_transfer",
     *   operationId="updateItemTransfer",
     *   tags={"item_transfer"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_transfer_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="item_transfer",
     *     @SWG\Schema(ref="#/definitions/ItemTransfer")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated item item_transfer",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemTransfer"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ItemTransferRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemTransferRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemTransfer = $this->itemTransferRepo->save($data, $request->entity());

        return $this->itemResponse($itemTransfer);
    }

    /**
     * @SWG\Delete(
     *   path="/item_transfers/{item_transfer_id}",
     *   summary="Delete a item_transfer",
     *   operationId="deleteItemTransfer",
     *   tags={"item_transfer"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_transfer_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted item_transfer",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemTransfer"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemTransferRequest $request
     * @return
     */
    public function destroy(ItemTransferRequest $request)
    {
        $itemTransfer = $request->entity();

        $this->itemTransferRepo->delete($itemTransfer);

        return $this->itemResponse($itemTransfer);
    }
}
