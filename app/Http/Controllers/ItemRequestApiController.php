<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemRequestRequest;
use App\Models\ItemRequest;
use App\Ninja\Repositories\ItemRequestRepository;

/**
 * Class ItemRequestApiController.
 */
class ItemRequestApiController extends BaseAPIController
{

    protected $entityType = ENTITY_ITEM_REQUEST;


    protected $itemRequestRepo;

    /**
     * ItemRequestApiController constructor.
     *
     * @param ItemRequestRepository $itemRequestRepo
     */
    public function __construct(ItemRequestRepository $itemRequestRepo)
    {
        parent::__construct();

        $this->itemRequestRepo = $itemRequestRepo;
    }

    /**
     * @SWG\Get(
     *   path="/item_requests",
     *   summary="List item_requests",
     *   operationId="listItemRequests",
     *   tags={"item_request"},
     *   @SWG\Response(
     *     response=200,
     *     description="A list of item_requests",
     *      @SWG\Schema(type="array", @SWG\Items(ref="#/definitions/ItemRequest"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     */
    public function index()
    {
        $itemRequests = ItemRequest::Scope()->withTrashed()->orderBy('created_at', 'desc');

        return $this->listResponse($itemRequests);
    }

    /**
     * @SWG\Get(
     *   path="/item_requests/{item_request_id}",
     *   summary="Retrieve a item_request",
     *   operationId="getItemRequest",
     *   tags={"item_request"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_request_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="A single item_request",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemRequest"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemRequestRequest $request
     * @return
     */
    public function show(ItemRequestRequest $request)
    {
        return $this->itemResponse($request->entity());
    }

    /**
     * @SWG\Post(
     *   path="/item_requests",
     *   summary="Create a item_request",
     *   operationId="createItemRequest",
     *   tags={"item_request"},
     *   @SWG\Parameter(
     *     in="body",
     *     name="body",
     *     @SWG\Schema(ref="#/definitions/ItemRequest")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="New item_request",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemRequest"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemRequestRequest $request
     * @return
     */
    public function store(ItemRequestRequest $request)
    {
        $itemRequest = $this->itemRequestRepo->save($request->input());

        return $this->itemResponse($itemRequest);
    }

    /**
     * @SWG\Put(
     *   path="/item_requests/{item_request_id}",
     *   summary="Update a item_request",
     *   operationId="updateItemRequest",
     *   tags={"item_request"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_request_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Parameter(
     *     in="body",
     *     name="item_request",
     *     @SWG\Schema(ref="#/definitions/ItemRequest")
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Updated item item_request",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemRequest"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     *
     * @param ItemRequestRequest $request
     * @param mixed $publicId
     * @return
     */
    public function update(ItemRequestRequest $request, $publicId)
    {
        if ($request->action) {
            return $this->handleAction($request);
        }

        $data = $request->input();
        $data['public_id'] = $publicId;
        $itemRequest = $this->itemRequestRepo->save($data, $request->entity());

        return $this->itemResponse($itemRequest);
    }

    /**
     * @SWG\Delete(
     *   path="/item_requests/{item_request_id}",
     *   summary="Delete a item_request",
     *   operationId="deleteItemRequest",
     *   tags={"item_request"},
     *   @SWG\Parameter(
     *     in="path",
     *     name="item_request_id",
     *     type="integer",
     *     required=true
     *   ),
     *   @SWG\Response(
     *     response=200,
     *     description="Deleted item_request",
     *      @SWG\Schema(type="object", @SWG\Items(ref="#/definitions/ItemRequest"))
     *   ),
     *   @SWG\Response(
     *     response="default",
     *     description="an ""unexpected"" error"
     *   )
     * )
     * @param ItemRequestRequest $request
     * @return
     */
    public function destroy(ItemRequestRequest $request)
    {
        $itemRequest = $request->entity();

        $this->itemRequestRepo->delete($itemRequest);

        return $this->itemResponse($itemRequest);
    }
}
