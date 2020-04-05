<?php

namespace App\Ninja\Datatables;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;
use App\Libraries\Utils;

class UserDatatable extends EntityDatatable
{
    public $entityType = ENTITY_USER;
    public $sortCol = 1;

    public function columns()
    {
        return [
            [
                'first_name',
                function ($model) {
                    if (Auth::user()->can('view', [ENTITY_USER]))
                        return $model->public_id ? link_to("users/{$model->public_id}", Utils::getUserDisplayName($model))->toHtml() : '';
                    else
                        return Utils::getUserDisplayName($model);

                },
            ],
            [
                'username',
                function ($model) {
                    return link_to("users/{$model->public_id}", $model->username ?: '')->toHtml();
                },
            ],
            [
                'email',
                function ($model) {
                    return link_to("users/{$model->public_id}", $model->email ?: '')->toHtml();
                },
            ],
            [
                'phone',
                function ($model) {
                    return link_to("users/{$model->public_id}", $model->phone ?: '')->toHtml();
                },
            ],
            [
                'location_name',
                function ($model) {
                    if ($model->location_id) {
                        if (Auth::user()->can('view', [ENTITY_LOCATION]))
                            return link_to("locations/{$model->location_id}", $model->location_name)->toHtml();
                        else
                            return $model->location_name;
                    } else {
                        return '';
                    }
                }
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
                'last_login',
                function ($model) {
                    return Utils::timestampToDateString(strtotime($model->last_login));
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
//                'deleted_at',
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
                trans('texts.edit_user'),
                function ($model) {
                    if (Auth::user()->public_id != $model->public_id) {
                        if (Auth::user()->can('edit', [ENTITY_USER]))
                            return URL::to("users/{$model->public_id}/edit");
                        elseif (Auth::user()->can('view', [ENTITY_USER]))
                            return URL::to("users/{$model->public_id}");
                    } else
                        return false;
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
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_USER]);
                },
            ],
            [
                trans('texts.edit_permission'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_USER]))
                        return URL::to("users/{$model->public_id}");
                },
            ],
            [
                trans('texts.reset_pwd'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_USER]))
                        return URL::to("reset_password/{$model->public_id}");
                },
            ],
            [
                trans('texts.send_invite'),
                function ($model) {
                    if (Auth::user()->can('edit', [ENTITY_USER]))
                        return URL::to("send_confirmation/{$model->public_id}");
                },
            ],
            [
                '--divider--', function () {
                return false;
            },
                function ($model) {
                    return Auth::user()->can('edit', [ENTITY_USER]);
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
