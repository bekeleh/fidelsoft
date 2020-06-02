<?php

namespace App\Http\Controllers;

use App\Http\Requests\CreateBranchRequest;
use App\Http\Requests\BranchRequest;
use App\Http\Requests\UpdateBranchRequest;
use App\Libraries\Utils;
use App\Models\Branch;
use App\Ninja\Datatables\BranchDatatable;
use App\Ninja\Repositories\BranchRepository;
use App\Services\BranchService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\View;
use Redirect;

/**
 * Class BranchController.
 */
class BranchController extends BaseController
{

    protected $brachService;

    protected $branchRepo;

    /**
     * BranchController constructor.
     *
     * @param BranchService $brachService
     * @param BranchRepository $branchRepo
     */
    public function __construct(BranchService $brachService, BranchRepository $branchRepo)
    {
        //parent::__construct();
        $this->brachService = $brachService;
        $this->branchRepo = $branchRepo;
    }

    public function index()
    {
        return View::make('list_wrapper', [
            'entityType' => ENTITY_BRANCH,
            'datatable' => new BranchDatatable(),
            'title' => trans('texts.branches'),
            'statuses' => Branch::getStatuses(),
        ]);
    }

    public function show($publicId)
    {
        Session::reflash();

        return Redirect::to("branches/$publicId/edit");
    }

    public function getDatatable()
    {
        $accountId = Auth::user()->account_id;
        $search = Input::get('sSearch');

        return $this->brachService->getDatatable($accountId, $search);
    }

    public function create(BranchRequest $request)
    {
        Auth::user()->can('create', [ENTITY_BRANCH, $request->entity()]);
        $data = [
            'branch' => null,
            'method' => 'POST',
            'url' => 'branches',
            'title' => trans('texts.create_branch'),
        ];

        $data = array_merge($data, self::getViewModel());

        return View::make('branches.edit', $data);
    }

    public function store(CreateBranchRequest $request)
    {
        $data = $request->input();

        $branch = $this->brachService->save($data);

        return redirect()->to("branches/{$branch->public_id}/edit")->with('success', trans('texts.created_branch'));
    }

    public function edit(BranchRequest $request, $publicId, $clone = false)
    {
        Auth::user()->can('edit', [ENTITY_BRANCH, $request->entity()]);

        $branch = Branch::scope($publicId)->withTrashed()->firstOrFail();

        if ($clone) {
            $branch->id = null;
            $branch->public_id = null;
            $branch->deleted_at = null;
            $method = 'POST';
            $url = 'branches';
        } else {
            $method = 'PUT';
            $url = 'branches/' . $branch->public_id;
        }

        $data = [
            'branch' => $branch,
            'entity' => $branch,
            'method' => $method,
            'url' => $url,
            'title' => trans('texts.edit_branch'),
        ];

        $data = array_merge($data, self::getViewModel($branch));

        return View::make('branches.edit', $data);
    }

    public function update(UpdateBranchRequest $request, $publicId)
    {
        $data = $request->input();
        $branch = $this->brachService->save($data, $request->entity());

        $action = Input::get('action');
        if (in_array($action, ['archive', 'delete', 'restore', 'invoice', 'add_to_invoice'])) {
            return self::bulk();
        }

        if ($action == 'clone') {
            return redirect()->to(sprintf('branches/%s/clone', $branch->public_id))->with('success', trans('texts.clone_branch'));
        } else {
            return redirect()->to("branches/{$branch->public_id}/edit")->with('success', trans('texts.updated_branch'));
        }
    }

    public function bulk()
    {
        $action = Input::get('action');
        $ids = Input::get('public_id') ? Input::get('public_id') : Input::get('ids');

        if ($action == 'invoice') {
            $branches = Branch::scope($ids)->get();
            $data = [];
            foreach ($branches as $branch) {
                $data[] = $branch->branch_key;
            }

            return redirect("invoices/create")->with('branches', $data);
        } else {
            $count = $this->brachService->bulk($ids, $action);
        }

        $message = Utils::pluralize($action . 'd_branch', $count);

        return $this->returnBulk(ENTITY_BRANCH, $action, $ids)->with('success', $message);
    }

    public function cloneBranch(BranchRequest $request, $publicId)
    {
        return self::edit($request, $publicId, true);
    }

    private static function getViewModel($branch = false)
    {
        return [
            'data' => Input::old('data'),
            'account' => Auth::user()->account,
        ];
    }
}
