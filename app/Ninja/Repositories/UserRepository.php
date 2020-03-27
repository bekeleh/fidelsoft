<?php

namespace App\Ninja\Repositories;

use App\Events\UserWasCreated;
use App\Events\UserWasUpdated;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    private $model;

    public function __construct(User $model)
    {
        $this->model = $model;
    }

    public function getById($id)
    {
        return $this->model->findOrFail($id);
    }

    public function getModel()
    {
        return $this->model;
    }

    public function setModel($model)
    {
        $this->model = $model;
        return $this;
    }

    public function getClassName()
    {
        return 'App\Models\User';
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('users')
            ->join('locations', 'locations.id', '=', 'users.location_id')
//            ->join('users_groups', 'users_groups.user_id', '=', 'users.id')
//            ->where('users.deleted_at', '=', null)
//            ->where('users.account_id', '=', $accountId)
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
                'users.is_deleted',
                'users.notes',
                'users.is_admin',
                'users.permissions',
                'users.created_at',
                'users.updated_at',
                'users.deleted_at',
                'users.created_by',
                'users.updated_by',
                'users.deleted_by',
                'users.last_login',
                'locations.name as location_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('users.name', 'like', '%' . $filter . '%')
                    ->orWhere('locations.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_USER);

        return $query;
    }

    public function findLocation($locationPublicId)
    {
        $locationId = Location::getPrivateId($locationPublicId);

        $query = $this->find()->where('locations.location_id', '=', $locationId);

        return $query;
    }

    public function save($data, $user = null)
    {
        $publicId = isset($data['public_id']) ? $data['public_id'] : false;

        if ($user) {
            $user->updated_by = Auth::user()->username;
        } elseif ($publicId) {
            $user = User::scope($publicId)->withArchived()->firstOrFail();
            \Log::warning('Entity not set in user repo save');
        } else {
            $user = User::createNew();
            $lastUser = User::withTrashed()->where('account_id', '=', Auth::user()->account_id)
                ->orderBy('public_id', 'DESC')->first();

            $user->public_id = $lastUser->public_id + 1;
            $user->created_by = auth::user()->username;
        }

        $user->fill($data);

        $user->account_id = Auth::user()->account_id;
        $user->first_name = isset($data['first_name']) ? ucfirst(trim($data['first_name'])) : '';
        $user->last_name = isset($data['last_name']) ? ucfirst(trim($data['last_name'])) : '';
        $user->username = isset($data['username']) ? trim($data['username']) : '';
        $user->email = isset($data['email']) ? trim($data['email']) : '';
        $user->registered = true;
        $user->password = strtolower(str_random(RANDOM_KEY_LENGTH));
        $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));

        if (Auth::user()->hasFeature(FEATURE_USER_PERMISSIONS)) {
            $user->is_admin = isset($data['permissions']) ? boolval($data['is_admin']) : 0;
            $user->permissions = isset($data['permissions']) ? self::formatUserPermissions($data['permissions']) : '';
        }

        $user->save();

        if ($publicId) {
            event(new UserWasUpdated($user, $data));
        } else {
            event(new UserWasCreated($user, $data));
        }

        return $user;
    }

    private function formatUserPermissions(array $permissions)
    {

        return json_encode(array_diff(array_values($permissions), [0]));
    }

    public function decodePermissions()
    {
        return $this->model->decodePermissions();
    }

    public function decodeGroups()
    {
        return $this->model->decodeGroups();
    }
}
