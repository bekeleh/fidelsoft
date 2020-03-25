<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class UserDatatable extends EntityDatatable
{
    public $entityType = ENTITY_USER;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'username',
                function ($model) {
                    return $model->username;
                },
            ],
            [
                'email',
                function ($model) {
                    return $model->email;
                },
            ],
            [
                'phone',
                function ($model) {
                    return $model->phone;
                },
            ],
            [
                'confirmed',
                function ($model) {
                    if (!$model->public_id) {
                        return self::getStatusLabel(USER_STATE_OWNER);
                    } elseif ($model->deleted_at) {
                        return self::getStatusLabel(USER_STATE_DISABLED);
                    } elseif ($model->confirmed) {
                        if ($model->is_admin) {
                            return self::getStatusLabel(USER_STATE_ADMIN);
                        } else {
                            return self::getStatusLabel(USER_STATE_ACTIVE);
                        }
                    } else {
                        return self::getStatusLabel(USER_STATE_PENDING);
                    }
                },
            ],
            [
                'notes',
                function ($model) {
                    return $model->notes;
                },
            ],
            [
                'created_by',
                function ($model) {
                    return $model->created_by;
                },
            ],
            [
                'updated_by',
                function ($model) {
                    return $model->updated_by;
                },
            ],
            [
                'created_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->created_at));
                },
            ],
            [
                'updated_at',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->updated_at));
                },
            ],
//            [
//                'date_deleted',
//                function ($model) {
//                    return Utils::timestampToDateString(strtotime($model->deleted_at));
//                },
//            ],
        ];
    }

    public function actions()
    {
        return [
            [
                uctrans('texts.edit_user'),
                function ($model) {
                    return URL::to("users/{$model->public_id}/edit");
                },
                function ($model) {
                    return $model->public_id;
                },
            ],
            [
                trans('texts.clone_user'),
                function ($model) {
                    return URL::to("users/{$model->public_id}/clone");
                },
                function ($model) {
                    return Auth::user()->can('create', ENTITY_USER);
                },
            ],
            [
                uctrans('texts.send_invite'),
                function ($model) {
                    return URL::to("send_confirmation/{$model->public_id}");
                },
                function ($model) {
                    return $model->public_id && !$model->confirmed;
                },
            ],
        ];
    }

    private function getStatusLabel($state)
    {
        $label = trans("texts.{$state}");
        $class = 'default';
        switch ($state) {
            case USER_STATE_PENDING:
                $class = 'default';
                break;
            case USER_STATE_ACTIVE:
                $class = 'info';
                break;
            case USER_STATE_DISABLED:
                $class = 'warning';
                break;
            case USER_STATE_OWNER:
                $class = 'success';
                break;
            case USER_STATE_ADMIN:
                $class = 'primary';
                break;
        }

        return "<h4><div class=\"label label-{$class}\">$label</div></h4>";
    }
}
