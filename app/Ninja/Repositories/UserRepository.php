<?php

namespace App\Ninja\Repositories;

use App\Events\User\UserWasCreatedEvent;
use App\Events\User\UserWasUpdatedEvent;
use App\Models\EntityModel;
use App\Models\Location;
use App\Models\Warehouse;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    private $model;

    /**
     * UserRepository constructor.
     * @param User $model
     */
    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getClassName()
    {
        return 'App\Models\User';
    }

    /**
     * @param $publicId
     * @param $accountId
     * @return mixed
     */
    public function getById($publicId, $accountId)
    {
        return $this->model->withTrashed()
            ->where('public_id', $publicId)
            ->where('account_id', $accountId)
            ->first();
    }

    /**
     * @return mixed
     */
    public function all()
    {
        return User::scope()->with('contacts', 'country')
            ->withTrashed()
            ->where('is_deleted', false)
            ->get();
    }

    /**
     * @param bool $accountId
     * @param null $filter
     * @return \Illuminate\Database\Query\Builder
     */
    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('users')
            ->leftJoin('accounts', 'accounts.id', '=', 'users.account_id')
            ->leftJoin('users as admin', 'admin.id', '=', 'users.user_id')
            ->leftJoin('locations', 'locations.id', '=', 'users.location_id')
            ->leftJoin('branches', 'branches.id', '=', 'users.branch_id')
            ->where('users.account_id', $accountId)
            ->whereNotNull('users.public_id')
//            ->where('users.deleted_at', null)
            ->select(
                'users.id',
                'users.public_id',
                'users.location_id',
                'users.first_name',
                'users.last_name',
                'users.username',
                'users.email',
                'users.phone',
                'users.confirmed',
                'users.activated',
                'users.is_admin',
                'users.is_deleted',
                'users.notes',
                'users.permissions',
                'users.created_at',
                'users.updated_at',
                'users.deleted_at',
                'users.created_by',
                'users.updated_by',
                'users.deleted_by',
                'users.last_login',
                'branches.public_id as branch_public_id',
                'branches.name as branch_name',
                'locations.public_id as location_public_id',
                'locations.name as location_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('users.first_name', 'like', '%' . $filter . '%')
                    ->orWhere('users.username', 'like', '%' . $filter . '%')
                    ->orWhere('users.email', 'like', '%' . $filter . '%')
                    ->orWhere('users.phone', 'like', '%' . $filter . '%')
                    ->orWhere('users.confirmed', 'like', '%' . $filter . '%')
                    ->orWhere('users.activated', 'like', '%' . $filter . '%')
                    ->orWhere('branches.name', 'like', '%' . $filter . '%')
                    ->orWhere('locations.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_USER);

        return $query;
    }

    /**
     * @param $locationPublicId
     * @return \Illuminate\Database\Query\Builder
     */
    public function findLocation($locationPublicId)
    {
        $locationId = Location::getPrivateId($locationPublicId);

        $query = $this->find()->where('users.location_id', $locationId);

        return $query;
    }

    /**
     * @param $branchPublicId
     * @return \Illuminate\Database\Query\Builder
     */
    public function findBranch($branchPublicId)
    {
        $branchId = Warehouse::getPrivateId($branchPublicId);

        $query = $this->find()->where('users.branch_id', $branchId);

        return $query;
    }

    /**
     * @param $data
     * @param null $user
     * @return EntityModel|Builder|Model|mixed|null
     */
    public function save($data, $user = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($user) {
            $user->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $user = User::scope($publicId)->withArchived()->firstOrFail();
        } else {
            $user = User::createNew();
            $lastUser = User::withTrashed()
                ->where('account_id', Auth::user()->account_id)
                ->orderBy('public_id', 'DESC')->first();

            $user->public_id = $lastUser->public_id + 1;
            $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));
            $user->registered = true;
            $user->created_by = auth::user()->username;
        }

        $user->fill($data);
        $user->account_id = Auth::user()->account_id;
        $user->first_name = isset($data['first_name']) ? trim($data['first_name']) : null;
        $user->last_name = isset($data['last_name']) ? trim($data['last_name']) : null;
        $user->username = isset($data['username']) ? trim($data['username']) : null;
        $user->email = isset($data['email']) ? trim($data['email']) : null;
        $user->activated = isset($data['activated']) ? boolval($data['activated']) : 0;

        $user->save();

        if (!empty($data['permission_groups'])) {
            $user->groups()->sync($data['permission_groups']);
        } else {
            $user->groups()->sync(array());
        }

        if ($publicId) {
            event(new UserWasUpdatedEvent($user));
        } else {
            event(new UserWasCreatedEvent($user));
        }

        return $user;
    }

    private function formatUserPermissions(array $permissions)
    {
        return json_encode(array_diff(array_values($permissions), [0]));
    }
}
