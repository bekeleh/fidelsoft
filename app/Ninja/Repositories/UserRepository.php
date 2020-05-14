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

    public function getClassName()
    {
        return 'App\Models\User';
    }

    public function getById($publicId, $accountId)
    {
        return $this->model->withTrashed()->where('public_id', $publicId)->where('account_id', $accountId)->first();
    }

    public function all()
    {
        return User::scope()->with('contacts', 'country')
            ->withTrashed()
            ->where('is_deleted', '=', false)
            ->get();
    }

    public function find($accountId = false, $filter = null)
    {
        $query = DB::table('users')
            ->join('locations', 'locations.id', '=', 'users.location_id')
            ->where('users.account_id', '=', $accountId)
//            ->where('users.deleted_at', '=', null)
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
                'locations.name as location_name'
            );

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('users.first_name', 'like', '%' . $filter . '%')
                    ->where('users.username', 'like', '%' . $filter . '%')
                    ->where('users.email', 'like', '%' . $filter . '%')
                    ->where('users.confirmed', 'like', '%' . $filter . '%')
                    ->where('users.activated', 'like', '%' . $filter . '%')
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
            $lastUser = User::withTrashed()
                ->where('account_id', '=', Auth::user()->account_id)
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
        $user->confirmed = isset($data['confirmed']) ? boolval($data['confirmed']) : 0;
        $user->activated = isset($data['activated']) ? boolval($data['activated']) : 0;

        $user->save();

        if (!empty($data['permission_groups'])) {
            $user->groups()->sync($data['permission_groups']);
        } else {
            $user->groups()->sync(array());
        }
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
}
