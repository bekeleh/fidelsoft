<?php

namespace App\Http\Controllers;

use App\Http\Requests\ItemMovementRequest;
use App\Libraries\Utils;
use App\Ninja\Datatables\StoreDatatable;
use App\Ninja\Repositories\ItemMovementRepository;
use App\Services\ItemMovementService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

class ItemMovementController extends BaseController
{
    // Stores
    protected $itemMovementRepo;
    protected $itemMovementService;
    protected $entityType = ENTITY_ITEM_MOVEMENT;

    public function __construct(ItemMovementRepository $itemMovementRepo, ItemMovementService $itemMovementService)
    {
        // parent::__construct();

        $this->itemMovementRepo = $itemMovementRepo;
        $this->itemMovementService = $itemMovementService;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_ITEM_MOVEMENT,
            'datatable' => new StoreDatatable(),
            'title' => trans('texts.item_movement'),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("item_movements/{$publicId}/edit");
    }

    public function getDatatable($itemMovementPublicId = null)
    {
        return $this->itemMovementService->getDatatable(Auth::user()->account_id, Input::get('sSearch'));
    }

    public function create(ItemMovementRequest $request)
    {

        $data = [
            '' => null,
            'method' => 'POST',
            'url' => 'item_movements',
            'title' => trans('texts.new_item_movement'),
        ];

        $data = array_merge($data, self::getViewModel());
        return View::make('item_movements.edit', $data);
    }

    public function edit(ItemMovementRequest $request, $publicId = false, $clone = false)
    {
        $itemMovement = $request->entity();
        if ($clone) {
            $itemMovement->id = null;
            $itemMovement->public_id = null;
            $itemMovement->deleted_at = null;
            $method = 'POST';
            $url = 'item_movements';
        } else {
            $method = 'PUT';
            $url = 'item_movements/' . $itemMovement->public_id;
        }

        $data = [
            '' => $itemMovement,
            'entity' => $itemMovement,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.item_movement.edit'),
        ];

        $data = array_merge($data, self::getViewModel($itemMovement));

        return View::make('item_movements.edit', $data);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param ItemMovementRequest $request
     * @return Response
     */
    public function update(ItemMovementRequest $request)
    {
        $data = $request->input();

        $itemMovement = $this->itemMovementService->save($data, $request->entity());

        Session::flash('message', trans('texts.updated_item_movement'));

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 're', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('item_movements/%s/clone', $itemMovement->public_id));
        } else {
            return redirect()->to("item_movements/{$itemMovement->public_id}/edit");
        }
    }

    public function store(ItemMovementRequest $request)
    {
        $data = $request->input();
        $itemMovement = $this->itemMovementService->save($data);

        Session::flash('message', trans('texts.created_'));

        return redirect()->to("item_movements/{$itemMovement->public_id}/edit");
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        $count = $this->itemMovementService->bulk($ids, $action);

        $message = Utils::pluralize($action . 'd_', $count);
        Session::flash('message', $message);

        return $this->returnBulk(ENTITY_ITEM_MOVEMENT, $action, $ids);
    }

    public function cloneItemMovement(ItemMovementRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($itemMovement = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
