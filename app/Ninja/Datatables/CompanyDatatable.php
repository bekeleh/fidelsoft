<?php

namespace App\Ninja\Datatables;

use App\Libraries\Utils;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\URL;

class CompanyDatatable extends EntityDatatable
{
  public $entityType = ENTITY_COMPANY;
  public $sortCol = 1;

  public function columns()
  {

    return [

      [
        'plan',
        function ($model) {
          return $model->plan;
        },
      ],
      [
        'plan_term',
        function ($model) {
         return $model->plan_term;
       },
     ],     
     [
      'plan_started',
      function ($model) {
       return Utils::timestampToDateString(strtotime($model->plan_started));
     },
   ],
   [
    'plan_paid',
    function ($model) {
     return Utils::timestampToDateString(strtotime($model->plan_paid ));
   },
 ], 
 [
  'plan_expires',
  function ($model) {
    return Utils::timestampToDateString(strtotime($model->plan_expires ));
  },
],
[
  'trial_started',
  function ($model) {
    return Utils::timestampToDateString(strtotime($model->trial_started ));
  },
],
[
  'trial_plan',
  function ($model) {
   return $model->trial_plan ;
 },
],
[
  'pending_plan',
  function ($model) {
   return $model->pending_plan ;
 },
],
[
  'pending_term',
  function ($model) {
   return $model->pending_term;
 },
],
[
  'plan_price',
  function ($model) {
   return $model->plan_price;
 },
],
[
  'pending_plan_price',
  function ($model) {
   return $model->pending_plan_price;
 },
],
[
  'num_users',
  function ($model) {
   return $model->num_users;
 },
],
[
  'pending_num_users',
  function ($model) {
   return $model->pending_num_users;
 },
],
[
  'utm_source',
  function ($model) {
   return $model->utm_source;
 },
],
[
  'utm_medium',
  function ($model) {
   return $model->utm_medium;
 },
],
[
  'utm_campaign',
  function ($model) {
   return $model->utm_campaign;
 },
],
[
  'utm_term',
  function ($model) {
   return $model->utm_term;
 },
],
[
  'utm_content',
  function ($model) {
   return $model->utm_content;
 },
],
[
  'discount',
  function ($model) {
   return $model->discount;
 },
],
[
  'discount_expires',
  function ($model) {
   return $model->discount_expires;
 },
],
[
  'promo_expires',
  function ($model) {
   return $model->promo_expires;
 },
],
[
  'bluevine_status',
  function ($model) {
   return $model->bluevine_status;
 },
],
[
  'referral_code',
  function ($model) {
   return $model->referral_code;
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






];

}

public function actions()
{
  return [
    [
      uctrans('texts.edit_company'),
      function ($model) {
        return URL::to("companies/{$model->public_id}/edit");
      },
      function ($model) {
        return Auth::user()->can('edit', ENTITY_COMPANY);
      },
    ],
    [
      uctrans('texts.clone_company'),
      function ($model) {
        return URL::to("companies/{$model->public_id}/clone");
      },
      function ($model) {
        return Auth::user()->can('create', ENTITY_COMPANY);
      },
    ],
    [
      '--divider--', function () {
        return false;
      },
      function ($model) {
        return Auth::user()->can('edit', [ENTITY_COMPANY]);
      },
    ],
  ];
}
}
