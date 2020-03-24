<?php

namespace App\Ninja\Repositories;

use App\Events\UserWasCreated;
use App\Events\UserWasUpdated;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class UserRepository extends BaseRepository
{
    public function getClassName()
    {
        return 'App\Models\User';
    }

    public function find($accountId, $filter = null)
    {
        $query = DB::table('users')
//            ->join('users', 'users.id', '=', 'users.user_id')
//            ->where('users.deleted_at', '=', null)
//            ->where('users.account_id', '=', $accountId)
            ->select(
                'users.public_id',
                'users.first_name',
                'users.last_name',
                'users.username',
                'users.email',
                'users.confirmed',
                'users.public_id',
                'users.deleted_at',
                'users.is_admin',
                'users.permissions');

        if ($filter) {
            $query->where(function ($query) use ($filter) {
                $query->where('users.name', 'like', '%' . $filter . '%');
            });
        }

        $this->applyFilters($query, ENTITY_USER);

        return $query;
    }

    public function save($data, $user)
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
        $user->first_name = ucfirst(trim($data['first_name']));
        $user->last_name = ucfirst(trim($data['last_name']));
        $user->username = trim($data['username']);
        $user->email = trim($data['email']);
        $user->registered = true;
        $user->password = strtolower(str_random(RANDOM_KEY_LENGTH));
        $user->confirmation_code = strtolower(str_random(RANDOM_KEY_LENGTH));

        if (Auth::user()->hasFeature(FEATURE_USER_PERMISSIONS)) {
            $user->is_admin = boolval($data['is_admin']);
            $user->permissions = self::formatUserPermissions($data['permissions']);
        }

        dd($user);

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
